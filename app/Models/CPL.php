<?php

namespace App\Models;

use App\Enums\CPKategori;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property int $program_studi_id
 * @property string $code
 * @property string $deskripsi
 * @property string $kategori
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read ProgramStudi $programStudi
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProfilLulusan> $profilLulusan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, MataKuliah> $mataKuliah
 */
class CPL extends Model
{
    use SoftDeletes;

    protected $table = 'cpls';

    protected $fillable = [
        'program_studi_id',
        'code',
        'deskripsi',
        'kategori',
    ];

    protected function casts(): array
    {
        return [
            'kategori' => CPKategori::class,
        ];
    }

    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function profilLulusan(): BelongsToMany
    {
        return $this->belongsToMany(ProfilLulusan::class, 'profil_lulusan_cpl', 'cpl_id', 'profil_lulusan_id')
            ->withTimestamps();
    }

    public function mataKuliah(): BelongsToMany
    {
        return $this->belongsToMany(MataKuliah::class, 'mata_kuliah_cpl', 'cpl_id', 'mata_kuliah_id')
            ->withTimestamps();
    }

    public function scopeByKategori(Builder $query, CPKategori|string $kategori): void
    {
        $value = $kategori instanceof CPKategori ? $kategori->value : $kategori;
        $query->where('kategori', $value);
    }
}

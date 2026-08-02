<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property int $kurikulum_id
 * @property string $name
 * @property string $code
 * @property int $sks
 * @property int $semester
 * @property string $jenis
 * @property string|null $deskripsi
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read Kurikulum $kurikulum
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Dosen> $dosens
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CPL> $cpls
 */
class MataKuliah extends Model
{
    use SoftDeletes;

    protected $table = 'mata_kuliah';

    protected $fillable = [
        'kurikulum_id',
        'name',
        'code',
        'sks',
        'semester',
        'jenis',
        'deskripsi',
    ];

    public function kurikulum(): BelongsTo
    {
        return $this->belongsTo(Kurikulum::class);
    }

    public function dosens(): BelongsToMany
    {
        return $this->belongsToMany(Dosen::class, 'mata_kuliah_dosen')
            ->withTimestamps();
    }

    public function cpls(): BelongsToMany
    {
        return $this->belongsToMany(CPL::class, 'mata_kuliah_cpl')
            ->withTimestamps();
    }

    public function scopeWajib(Builder $query): void
    {
        $query->where('jenis', 'wajib');
    }

    public function scopePilihan(Builder $query): void
    {
        $query->where('jenis', 'pilihan');
    }
}

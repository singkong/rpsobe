<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $program_studi_id
 * @property string $name
 * @property int $tahun_mulai
 * @property int $tahun_selesai
 * @property int $total_sks
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read ProgramStudi $programStudi
 * @property-read \Illuminate\Database\Eloquent\Collection<int, MataKuliah> $mataKuliah
 */
class Kurikulum extends Model
{
    use SoftDeletes;

    protected $table = 'kurikulum';

    protected $fillable = [
        'program_studi_id',
        'name',
        'tahun_mulai',
        'tahun_selesai',
        'total_sks',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'tahun_mulai' => 'integer',
            'tahun_selesai' => 'integer',
        ];
    }

    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function mataKuliah(): HasMany
    {
        return $this->hasMany(MataKuliah::class);
    }
}

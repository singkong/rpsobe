<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $rps_id
 * @property int $pertemuan_ke
 * @property int|null $sub_cpmk_id
 * @property string $materi
 * @property string|null $indikator
 * @property array|null $referensi_ids
 * @property array|null $metode_pembelajaran
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read RPS $rps
 * @property-read SubCPMK|null $subCpmk
 */
class MateriPertemuan extends Model
{
    use SoftDeletes;

    protected $table = 'materi_pertemuan';

    protected $fillable = [
        'rps_id',
        'pertemuan_ke',
        'sub_cpmk_id',
        'materi',
        'indikator',
        'referensi_ids',
        'metode_pembelajaran',
    ];

    protected function casts(): array
    {
        return [
            'referensi_ids' => 'array',
            'metode_pembelajaran' => 'array',
        ];
    }

    public function rps(): BelongsTo
    {
        return $this->belongsTo(RPS::class, 'rps_id');
    }

    public function subCpmk(): BelongsTo
    {
        return $this->belongsTo(SubCPMK::class, 'sub_cpmk_id');
    }
}

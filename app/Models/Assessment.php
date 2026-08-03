<?php

namespace App\Models;

use App\Enums\AssessmentJenis;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $rps_id
 * @property string $nama
 * @property float $bobot_persen
 * @property AssessmentJenis $jenis
 * @property string|null $deskripsi
 * @property string|null $rubrik
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read RPS $rps
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SubCPMK> $subCpmk
 */
class Assessment extends Model
{
    use SoftDeletes;

    protected $table = 'assessments';

    protected $fillable = [
        'rps_id',
        'nama',
        'bobot_persen',
        'jenis',
        'deskripsi',
        'rubrik',
    ];

    protected function casts(): array
    {
        return [
            'jenis' => AssessmentJenis::class,
            'bobot_persen' => 'float',
        ];
    }

    public function rps(): BelongsTo
    {
        return $this->belongsTo(RPS::class, 'rps_id');
    }

    public function subCpmk(): BelongsToMany
    {
        return $this->belongsToMany(SubCPMK::class, 'assessment_sub_cpmk', 'assessment_id', 'sub_cpmk_id')
            ->withTimestamps();
    }
}

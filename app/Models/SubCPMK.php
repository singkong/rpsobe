<?php

namespace App\Models;

use App\Enums\TaksonomiLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $cpml_id
 * @property string $code
 * @property string $deskripsi
 * @property TaksonomiLevel|null $level_taksonomi
 * @property array|null $pertemuan_terkait
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read CPMK $cpmk
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Assessment> $assessments
 */
class SubCPMK extends Model
{
    use SoftDeletes;

    protected $table = 'sub_cpmk';

    protected $fillable = [
        'cpml_id',
        'code',
        'deskripsi',
        'level_taksonomi',
        'pertemuan_terkait',
    ];

    protected function casts(): array
    {
        return [
            'pertemuan_terkait' => 'array',
            'level_taksonomi' => TaksonomiLevel::class,
        ];
    }

    public function cpmk(): BelongsTo
    {
        return $this->belongsTo(CPMK::class, 'cpml_id');
    }

    public function assessments(): BelongsToMany
    {
        return $this->belongsToMany(Assessment::class, 'assessment_sub_cpmk')
            ->withTimestamps();
    }
}

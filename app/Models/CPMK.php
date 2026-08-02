<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $rps_id
 * @property string $code
 * @property string $deskripsi
 * @property string|null $level_taksonomi
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read RPS $rps
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SubCPMK> $subCpmk
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CPL> $cpl
 */
class CPMK extends Model
{
    use SoftDeletes;

    protected $table = 'cpml';

    protected $fillable = [
        'rps_id',
        'code',
        'deskripsi',
        'level_taksonomi',
    ];

    public function rps(): BelongsTo
    {
        return $this->belongsTo(RPS::class);
    }

    public function subCpmk(): HasMany
    {
        return $this->hasMany(SubCPMK::class, 'cpml_id');
    }

    public function cpl(): BelongsToMany
    {
        return $this->belongsToMany(CPL::class, 'cpml_cpl')
            ->withTimestamps();
    }
}

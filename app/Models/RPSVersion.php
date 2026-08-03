<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $rps_id
 * @property string $version_label
 * @property array $snapshot_data
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read RPS $rps
 * @property-read User $createdBy
 */
class RPSVersion extends Model
{
    protected $table = 'rps_versions';

    protected $fillable = [
        'rps_id',
        'version_label',
        'snapshot_data',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'snapshot_data' => 'array',
        ];
    }

    public function rps(): BelongsTo
    {
        return $this->belongsTo(RPS::class, 'rps_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

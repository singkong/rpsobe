<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $rps_id
 * @property int $reviewer_id
 * @property int|null $skor_total
 * @property array|null $skor_per_komponen
 * @property array|null $komentar
 * @property string|null $status
 * @property string|null $catatan
 * @property string|null $reviewed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read RPS $rps
 * @property-read User $reviewer
 */
class RPSReview extends Model
{
    protected $table = 'rps_reviews';

    protected $fillable = [
        'rps_id',
        'reviewer_id',
        'skor_total',
        'skor_per_komponen',
        'komentar',
        'status',
        'catatan',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'skor_per_komponen' => 'array',
            'komentar' => 'array',
        ];
    }

    public function rps(): BelongsTo
    {
        return $this->belongsTo(RPS::class, 'rps_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}

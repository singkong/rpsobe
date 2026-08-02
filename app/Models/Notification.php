<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
        'actionable_type',
        'actionable_id',
        'action_url',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread(Builder $query): void
    {
        $query->whereNull('read_at');
    }

    public function scopeByType(Builder $query, string $type): void
    {
        $query->where('type', $type);
    }

    public function markAsRead(): void
    {
        if (is_null($this->read_at)) {
            $this->read_at = now();
            $this->save();
        }
    }

    public function getIsUnreadAttribute(): bool
    {
        return is_null($this->read_at);
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'rps_submitted' => 'send',
            'rps_reviewed' => 'clipboard-check',
            'rps_revision_requested' => 'pencil',
            'rps_approved' => 'check',
            'rps_published' => 'book',
            'reviewer_assigned' => 'user-plus',
            'deadline_reminder' => 'clock',
            'system' => 'info-circle',
            default => 'bell',
        };
    }

    public function getIconClassAttribute(): string
    {
        return match ($this->type) {
            'rps_submitted' => 'text-blue',
            'rps_reviewed' => 'text-green',
            'rps_revision_requested' => 'text-orange',
            'rps_approved' => 'text-success',
            'rps_published' => 'text-primary',
            'reviewer_assigned' => 'text-cyan',
            'deadline_reminder' => 'text-red',
            'system' => 'text-secondary',
            default => 'text-muted',
        };
    }
}

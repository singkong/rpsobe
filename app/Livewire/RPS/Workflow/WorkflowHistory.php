<?php

use function Livewire\Volt\{state, mount};
use App\Models\RPS;
use App\Models\RPSReview;
use App\Models\RPSVersion;

state('rpsId');
state('rps');
state('timeline', []);

mount(function ($rpsId) {
    $this->rpsId = $rpsId;
    $this->rps = RPS::with(['mataKuliah', 'semester', 'user'])->findOrFail($rpsId);
    $this->loadTimeline();
});

$loadTimeline = function () {
    $auditLogs = \App\Models\AuditLog::with('user')
        ->where('model_type', RPS::class)
        ->where('model_id', $this->rps->id)
        ->where('action', 'status_changed')
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($log) {
            return [
                'type' => 'status_change',
                'actor' => $log->user?->name ?? 'System',
                'action' => $log->action,
                'changes' => $log->changes ?: [],
                'old_values' => $log->old_values ?: [],
                'new_values' => $log->new_values ?: [],
                'created_at' => $log->created_at,
            ];
        });

    $reviews = RPSReview::with('reviewer')
        ->where('rps_id', $this->rps->id)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($review) {
            return [
                'type' => 'review',
                'actor' => $review->reviewer?->name ?? 'Unknown',
                'skor_total' => $review->skor_total,
                'skor_per_komponen' => $review->skor_per_komponen ?: [],
                'komentar' => $review->komentar ?: [],
                'status' => $review->status,
                'catatan' => $review->catatan,
                'created_at' => $review->created_at,
            ];
        });

    $versions = RPSVersion::with('createdBy')
        ->where('rps_id', $this->rps->id)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($version) {
            return [
                'type' => 'version',
                'version_label' => $version->version_label,
                'actor' => $version->createdBy?->name ?? 'System',
                'created_at' => $version->created_at,
            ];
        });

    $allTimeline = $auditLogs->concat($reviews)->concat($versions)
        ->sortByDesc('created_at')
        ->values()
        ->toArray();

    $this->timeline = $allTimeline;
};

$statusIcon = function ($status): string {
    return match ($status) {
        'draft' => 'pencil',
        'review' => 'search',
        'revision' => 'refresh',
        'approved' => 'check',
        'published' => 'world',
        'archived' => 'archive',
        default => 'circle',
    };
};

$statusColor = function ($status): string {
    return match ($status) {
        'draft' => 'gray',
        'review' => 'yellow',
        'revision' => 'orange',
        'approved' => 'green',
        'published' => 'blue',
        'archived' => 'red',
        default => 'gray',
    };
};

return view('livewire.rps.workflow.workflow-history');

<?php

use function Livewire\Volt\{state, mount, withPagination};
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

withPagination();

state('filterType', '');
state('filterRead', '');
state('perPage', 15);

mount(function () {
    //
});

$notificationList = function () {
    $user = Auth::user();

    $query = Notification::where('user_id', $user->id);

    if ($this->filterType) {
        $query->byType($this->filterType);
    }

    if ($this->filterRead === 'unread') {
        $query->unread();
    } elseif ($this->filterRead === 'read') {
        $query->whereNotNull('read_at');
    }

    return $query->latest()->paginate($this->perPage);
};

$notificationTypes = function () {
    return [
        '' => 'Semua Tipe',
        'rps_submitted' => 'RPS Diajukan',
        'rps_reviewed' => 'RPS Direview',
        'rps_revision_requested' => 'Revisi Diminta',
        'rps_approved' => 'RPS Disetujui',
        'rps_published' => 'RPS Dipublikasi',
        'reviewer_assigned' => 'Reviewer Ditugaskan',
        'deadline_reminder' => 'Pengingat Deadline',
        'system' => 'Sistem',
    ];
};

$markAsRead = function ($id) {
    $notification = Notification::find($id);
    if ($notification && $notification->user_id === Auth::id()) {
        $notification->markAsRead();

        if ($notification->action_url) {
            $this->redirect($notification->action_url, navigate: true);
        }
    }
};

$markAllAsRead = function () {
    $service = app(\App\Services\NotificationService::class);
    $service->markAllAsRead(Auth::user());
};

$getUnreadCount = function () {
    return Notification::where('user_id', Auth::id())
        ->whereNull('read_at')
        ->count();
};

return view('livewire.notification.notification-list');

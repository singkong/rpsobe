<?php

use function Livewire\Volt\{state, mount};
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

state('notifications');
state('unreadCount');

mount(function () {
    $this->refreshNotifications();
});

$refreshNotifications = function () {
    $user = Auth::user();
    $this->notifications = Notification::where('user_id', $user->id)
        ->latest()
        ->take(10)
        ->get();
    $this->unreadCount = Notification::where('user_id', $user->id)
        ->whereNull('read_at')
        ->count();
};

$markAsRead = function ($id) {
    $notification = Notification::find($id);
    if ($notification && $notification->user_id === Auth::id()) {
        $notification->markAsRead();
        $this->refreshNotifications();

        if ($notification->action_url) {
            $this->redirect($notification->action_url, navigate: true);
        }
    }
};

$markAllAsRead = function () {
    \App\Services\NotificationService::class;
    $service = app(\App\Services\NotificationService::class);
    $service->markAllAsRead(Auth::user());
    $this->refreshNotifications();
};

return view('livewire.notification.notification-center');
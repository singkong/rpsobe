<?php

namespace App\Livewire\Notification;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationCenter extends Component
{
    public $notifications;
    public $unreadCount;

    public function mount(): void
    {
        $this->refreshNotifications();
    }

    public function refreshNotifications(): void
    {
        $user = Auth::user();
        $this->notifications = Notification::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();
        $this->unreadCount = Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();
    }

    public function markAsRead(int $id): void
    {
        $notification = Notification::find($id);
        if ($notification && $notification->user_id === Auth::id()) {
            $notification->markAsRead();
            $this->refreshNotifications();

            if ($notification->action_url) {
                $this->redirect($notification->action_url, navigate: true);
            }
        }
    }

    public function markAllAsRead(): void
    {
        $service = app(\App\Services\NotificationService::class);
        $service->markAllAsRead(Auth::user());
        $this->refreshNotifications();
    }

    public function render()
    {
        return view('livewire.notification.notification-center');
    }
}

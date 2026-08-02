<?php

namespace App\Livewire\Notification;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationList extends Component
{
    use WithPagination;

    public string $filterType = '';
    public string $filterRead = '';
    public int $perPage = 15;

    public function mount(): void
    {
        //
    }

    public function getNotificationsProperty()
    {
        return Notification::where('user_id', Auth::id())
            ->when($this->filterRead === 'unread', fn($q) => $q->whereNull('read_at'))
            ->when($this->filterRead === 'read', fn($q) => $q->whereNotNull('read_at'))
            ->latest()
            ->paginate($this->perPage);
    }

    public function notificationList()
    {
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
    }

    public function notificationTypes(): array
    {
        return [
            '' => 'Semua Tipe', 'rps_submitted' => 'RPS Diajukan',
            'rps_reviewed' => 'RPS Direview', 'rps_revision_requested' => 'Revisi Diminta',
            'rps_approved' => 'RPS Disetujui', 'rps_published' => 'RPS Dipublikasi',
            'reviewer_assigned' => 'Reviewer Ditugaskan', 'deadline_reminder' => 'Pengingat Deadline',
            'system' => 'Sistem',
        ];
    }

    public function markAsRead(int $id): void
    {
        $notification = Notification::find($id);
        if ($notification && $notification->user_id === Auth::id()) {
            $notification->markAsRead();
            if ($notification->action_url) {
                $this->redirect($notification->action_url, navigate: true);
            }
        }
    }

    public function markAllAsRead(): void
    {
        $service = app(\App\Services\NotificationService::class);
        $service->markAllAsRead(Auth::user());
    }

    public function getUnreadCount(): int
    {
        return Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();
    }

    public function render()
    {
        return view('livewire.notification.notification-list');
    }
}

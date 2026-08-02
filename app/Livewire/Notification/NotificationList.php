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

?>

<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Notifikasi</h2>
                </div>
                <div class="col-auto">
                    <button wire:click="markAllAsRead" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12l5 5l10 -10"/></svg>
                        Tandai Semua Dibaca
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-body border-bottom py-3">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <select wire:model.live="filterType" class="form-select">
                                @foreach($this->notificationTypes() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select wire:model.live="filterRead" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="unread">Belum Dibaca</option>
                                <option value="read">Sudah Dibaca</option>
                            </select>
                        </div>
                    </div>
                </div>
                @php $list = $this->notificationList(); @endphp
                <div class="list-group list-group-flush">
                    @forelse($list as $notification)
                        <div class="list-group-item {{ $notification->is_unread ? 'list-group-item-blue' : '' }}"
                             wire:click="markAsRead({{ $notification->id }})"
                             style="cursor: pointer;">
                            <div class="row align-items-center g-3">
                                <div class="col-auto">
                                    <span class="avatar rounded bg-{{ $notification->is_unread ? 'blue-lt' : 'white' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="{{ $notification->icon_class }}">
                                            @switch($notification->icon)
                                                @case('send')
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 14l11 -11"/><path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5"/>
                                                    @break
                                                @case('clipboard-check')
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/><path d="M9 3a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/><path d="M9 14l2 2l4 -4"/>
                                                    @break
                                                @case('pencil')
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4"/><path d="M13.5 6.5l4 4"/>
                                                    @break
                                                @case('check')
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/>
                                                    @break
                                                @case('book')
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6l0 13"/><path d="M12 6l0 13"/><path d="M21 6l0 13"/>
                                                    @break
                                                @case('user-plus')
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/><path d="M16 19h6"/><path d="M19 16v6"/><path d="M6 21v-2a4 4 0 0 1 4 -4h4"/>
                                                    @break
                                                @case('clock')
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 9 -9a9 9 0 0 0 -9 9"/><path d="M12 7v5l3 3"/>
                                                    @break
                                                @default
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"/><path d="M9 17v1a3 3 0 0 0 6 0v-1"/>
                                            @endswitch
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong class="{{ $notification->is_unread ? '' : 'text-secondary' }}">
                                                {{ $notification->title }}
                                            </strong>
                                            @if($notification->is_unread)
                                                <span class="badge bg-blue ms-2">Baru</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $notification->time_ago }}</small>
                                    </div>
                                    <p class="text-secondary mb-1">{{ $notification->message }}</p>
                                    <small class="text-muted">
                                        <span class="badge bg-{{ match($notification->type) {
                                            'rps_submitted' => 'blue',
                                            'rps_reviewed' => 'green',
                                            'rps_revision_requested' => 'orange',
                                            'rps_approved' => 'success',
                                            'rps_published' => 'primary',
                                            'reviewer_assigned' => 'cyan',
                                            'deadline_reminder' => 'red',
                                            default => 'secondary',
                                        } }}-lt">{{ str_replace('_', ' ', ucfirst($notification->type)) }}</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center py-5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="text-secondary mb-3">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"/>
                            </svg>
                            <p class="text-secondary mb-1 fw-bold">Tidak ada notifikasi</p>
                            <p class="text-secondary">Notifikasi akan muncul di sini saat ada aktivitas yang berkaitan dengan Anda.</p>
                        </div>
                    @endforelse
                </div>
                <div class="card-footer d-flex align-items-center">
                    <p class="m-0 text-secondary">Menampilkan {{ $list->firstItem() }} - {{ $list->lastItem() }} dari {{ $list->total() }}</p>
                    <div class="ms-auto">{{ $list->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="nav-item dropdown" wire:poll.30s="refreshNotifications">
    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Notifications" role="button">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-bell">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"/>
            <path d="M9 17v1a3 3 0 0 0 6 0v-1"/>
        </svg>
        @if($unreadCount > 0)
            <span class="badge bg-red">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow dropdown-menu-card" style="min-width: 360px;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Notifikasi</h3>
                @if($unreadCount > 0)
                    <div class="card-actions">
                        <a href="#" wire:click.prevent="markAllAsRead" class="btn btn-sm btn-ghost-secondary">
                            Tandai Semua Dibaca
                        </a>
                    </div>
                @endif
            </div>
            <div class="list-group list-group-flush" style="max-height: 360px; overflow-y: auto;">
                @forelse($notifications as $notification)
                    <div class="list-group-item cursor-pointer {{ $notification->is_unread ? 'list-group-item-blue' : '' }}"
                         wire:click="markAsRead({{ $notification->id }})"
                         style="cursor: pointer;">
                        <div class="row align-items-center g-2">
                            <div class="col-auto">
                                <span class="avatar rounded" style="background-color: {{ $notification->is_unread ? '#e9ecef' : '#f8fafc' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="{{ $notification->icon_class }}">
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
                            <div class="col text-truncate">
                                <div class="text-body d-flex justify-content-between">
                                    <strong class="{{ $notification->is_unread ? '' : 'text-secondary' }}">{{ $notification->title }}</strong>
                                    @if($notification->is_unread)
                                        <span class="badge bg-blue badge-pill ms-2" style="width:8px; height:8px; padding:0; min-width:8px;"></span>
                                    @endif
                                </div>
                                <div class="text-secondary text-truncate small">{{ \Illuminate\Support\Str::limit($notification->message, 80) }}</div>
                                <div class="text-muted small">{{ $notification->time_ago }}</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="list-group-item text-center py-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="text-secondary mb-2">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"/>
                        </svg>
                        <p class="text-secondary m-0">Tidak ada notifikasi</p>
                    </div>
                @endforelse
            </div>
            <div class="card-footer text-center">
                <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-ghost-secondary w-100" wire:navigate>
                    Lihat Semua Notifikasi
                </a>
            </div>
        </div>
    </div>
</div>

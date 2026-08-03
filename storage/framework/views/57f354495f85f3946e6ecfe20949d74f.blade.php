<div class="nav-item dropdown">
    <a href="#" class="nav-link px-2" data-bs-toggle="dropdown" aria-label="Notifikasi">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"/><path d="M9 17v1a3 3 0 0 0 6 0v-1"/>
        </svg>
        @if($unreadCount > 0)
            <span class="badge bg-red">{{ $unreadCount }}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow dropdown-menu-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Notifikasi</h3>
            </div>
            <div class="list-group list-group-flush">
                <a href="{{ route('notifications.index') }}" class="list-group-item text-center text-primary">
                    Lihat Semua Notifikasi
                </a>
            </div>
        </div>
    </div>
</div>
<?php

use function Livewire\Volt\{state, computed, mount};
use App\Models\RPS;
use App\Models\RPSReview;
use App\Enums\RPSStatus;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

$user = fn() => Auth::user();

$stats = function () {
    return app(DashboardService::class)->getDosenStats($this->user);
};

$recentRps = fn() => $this->stats['recentRps'];
$notifications = fn() => $this->stats['notifications'];

$getStatusBadge = function (RPSStatus $status): string {
    return match ($status) {
        RPSStatus::Draft => 'bg-yellow-lt',
        RPSStatus::Review => 'bg-blue-lt',
        RPSStatus::Revision => 'bg-orange-lt',
        RPSStatus::Approved => 'bg-green-lt',
        RPSStatus::Published => 'bg-teal-lt',
        RPSStatus::Archived => 'bg-red-lt',
    };
};

?>

<div>
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Dashboard Dosen</h2>
                <div class="text-secondary">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Dosen</li>
                    </ol>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('rps.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/>
                    </svg>
                    Buat RPS Baru
                </a>
            </div>
        </div>
    </div>

    @if(!$stats)
        <div class="empty">
            <div class="empty-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="icon text-muted">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/>
                </svg>
            </div>
            <p class="empty-title">Tidak ada data RPS</p>
            <p class="empty-subtitle text-secondary">Mulai buat RPS pertama Anda</p>
            <div class="empty-action">
                <a href="{{ route('rps.create') }}" class="btn btn-primary">Buat RPS Baru</a>
            </div>
        </div>
    @else
        {{-- Stats Cards --}}
        <div class="row mb-3">
            <div class="col-md-2 col-sm-4 col-6 mb-2">
                <a href="{{ route('rps.index') }}" class="card card-link card-link-pop">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $stats['total'] }}</div>
                        <div class="text-secondary">Total RPS</div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-sm-4 col-6 mb-2">
                <a href="{{ route('rps.index') }}" class="card card-link card-link-pop">
                    <div class="card-status-start bg-yellow"></div>
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $stats['draft'] }}</div>
                        <div class="text-secondary">Draft</div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-sm-4 col-6 mb-2">
                <a href="{{ route('rps.index') }}" class="card card-link card-link-pop">
                    <div class="card-status-start bg-blue"></div>
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $stats['review'] }}</div>
                        <div class="text-secondary">Dalam Review</div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-sm-4 col-6 mb-2">
                <a href="{{ route('rps.index') }}" class="card card-link card-link-pop">
                    <div class="card-status-start bg-green"></div>
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $stats['approved'] }}</div>
                        <div class="text-secondary">Disetujui</div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-sm-4 col-6 mb-2">
                <a href="{{ route('rps.index') }}" class="card card-link card-link-pop">
                    <div class="card-status-start bg-orange"></div>
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $stats['revision'] }}</div>
                        <div class="text-secondary">Perlu Revisi</div>
                    </div>
                </a>
            </div>
            <div class="col-md-2 col-sm-4 col-6 mb-2">
                <a href="{{ route('rps.index') }}" class="card card-link card-link-pop">
                    <div class="card-status-start bg-teal"></div>
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $stats['published'] }}</div>
                        <div class="text-secondary">Published</div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row">
            {{-- RPS Terbaru --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">RPS Terbaru</h3>
                        <div class="card-actions">
                            <a href="{{ route('rps.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Mata Kuliah</th>
                                        <th>Semester</th>
                                        <th>Status</th>
                                        <th>Diperbarui</th>
                                        <th class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($this->recentRps as $rps)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $rps->mataKuliah?->name ?? '-' }}</div>
                                                <div class="text-secondary small">{{ $rps->mataKuliah?->code ?? '' }}</div>
                                            </td>
                                            <td class="text-secondary">{{ $rps->semester?->name ?? '-' }}</td>
                                            <td>
                                                <span class="badge {{ $this->getStatusBadge($rps->status) }}">
                                                    {{ $rps->status->label() }}
                                                </span>
                                            </td>
                                            <td class="text-secondary small">{{ $rps->updated_at->diffForHumans() }}</td>
                                            <td>
                                                <a href="{{ route('rps.edit', ['rpsId' => $rps->id]) }}" class="btn btn-sm btn-icon btn-ghost-primary" title="Edit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/>
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-secondary py-4">Belum ada RPS</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Links & Notifications --}}
            <div class="col-lg-4">
                {{-- Quick Links --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Akses Cepat</h3>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('rps.create') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon me-2 text-primary"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                            Buat RPS Baru
                        </a>
                        <a href="{{ route('rps.index') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon me-2 text-yellow"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.066 2.573c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.573 1.066c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.066 -2.573c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"/><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"/></svg>
                            Lanjutkan Draft
                        </a>
                        <a href="{{ route('review.list') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon me-2 text-blue"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 12l2 2l4 -4"/><path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z"/></svg>
                            Review Saya
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon me-2 text-teal"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/></svg>
                            RPS Published
                        </a>
                    </div>
                </div>

                {{-- Notifications --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Notifikasi</h3>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($this->notifications as $notification)
                            <div class="list-group-item">
                                <div class="d-flex">
                                    <div class="me-2">
                                        <span class="avatar avatar-xs bg-blue-lt">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/><path d="M12 8v4l2 2"/></svg>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="small text-secondary">{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $notification->action)) }}</div>
                                        <div class="text-muted small">{{ $notification->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-secondary py-4">Belum ada notifikasi</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>


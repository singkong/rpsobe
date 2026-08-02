<?php

use function Livewire\Volt\{state, computed, mount};
use App\Models\Fakultas;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

$user = fn() => Auth::user();

$fakultasId = fn() => $this->user->fakultas_id;

$fakultas = function () {
    if (!$this->fakultasId) {
        return null;
    }
    return Fakultas::find($this->fakultasId);
};

$stats = function () {
    if (!$this->fakultas) {
        return null;
    }
    return app(DashboardService::class)->getFakultasStats($this->fakultas);
};

$prodiStats = fn() => $this->stats['prodiStats'] ?? [];

?>

<div>
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Dashboard Fakultas</h2>
                <div class="text-secondary">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Fakultas</li>
                    </ol>
                </div>
            </div>
            @if($fakultas)
                <div class="col-auto">
                    <span class="badge bg-azure-lt fs-5">{{ $fakultas->name }} ({{ $fakultas->code }})</span>
                </div>
            @endif
        </div>
    </div>

    @if(!$stats)
        <div class="empty">
            <div class="empty-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="icon text-muted">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 8v4l2 2"/><path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5"/>
                </svg>
            </div>
            <p class="empty-title">Fakultas belum dikonfigurasi</p>
            <p class="empty-subtitle text-secondary">Hubungi administrator</p>
        </div>
    @else
        {{-- Fakultas Stats Cards --}}
        <div class="row mb-3">
            <div class="col-lg-3 col-sm-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $stats['totalProdi'] }}</div>
                        <div class="text-secondary">Program Studi</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $stats['totalRps'] }}</div>
                        <div class="text-secondary">Total RPS</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $stats['totalPublished'] }}</div>
                        <div class="text-secondary">Published</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-2">
                <div class="card">
                    <div class="card-status-top bg-green"></div>
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $stats['completionRate'] }}%</div>
                        <div class="text-secondary">Completion Rate</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Overall Progress Bar --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <strong>Progress Fakultas</strong>
                    <span class="ms-auto">{{ $stats['totalPublished'] }} / {{ $stats['totalMk'] }} RPS</span>
                </div>
                <div class="progress">
                    <div class="progress-bar bg-green" style="width: {{ min($stats['completionRate'], 100) }}%" role="progressbar">
                        <span>{{ $stats['completionRate'] }}%</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Per Prodi Comparison Chart --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Perbandingan per Program Studi</h3>
            </div>
            <div class="card-body">
                @if(empty($prodiStats))
                    <div class="text-center text-secondary py-4">Belum ada data prodi</div>
                @else
                    @php
                        $maxProdi = max(array_column($prodiStats, 'totalRps')) ?: 1;
                        $barColors = ['bg-primary', 'bg-azure', 'bg-green', 'bg-orange', 'bg-purple', 'bg-teal', 'bg-yellow', 'bg-pink'];
                    @endphp
                    @foreach($prodiStats as $index => $ps)
                        @php
                            $barPct = ($ps['totalRps'] / max($maxProdi, 1)) * 100;
                            $colorClass = $barColors[$index % count($barColors)];
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="fw-bold">{{ $ps['name'] }} ({{ $ps['code'] }})</span>
                                <span>{{ $ps['published'] }}/{{ $ps['totalMk'] }} — {{ $ps['completionRate'] }}%</span>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar {{ $colorClass }}" style="width: {{ $ps['completionRate'] }}%" role="progressbar">
                                    {{ $ps['completionRate'] }}%
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Per Prodi Table --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detail per Program Studi</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Program Studi</th>
                                <th>Kode</th>
                                <th>Total MK</th>
                                <th>Total RPS</th>
                                <th>Published</th>
                                <th>Completion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($prodiStats as $ps)
                                <tr>
                                    <td class="fw-bold">{{ $ps['name'] }}</td>
                                    <td class="text-secondary">{{ $ps['code'] }}</td>
                                    <td>{{ $ps['totalMk'] }}</td>
                                    <td>{{ $ps['totalRps'] }}</td>
                                    <td>{{ $ps['published'] }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress progress-xs flex-grow-1 me-2" style="max-width: 80px;">
                                                <div class="progress-bar {{ $ps['completionRate'] >= 80 ? 'bg-green' : ($ps['completionRate'] >= 50 ? 'bg-yellow' : 'bg-red') }}" style="width: {{ $ps['completionRate'] }}%" role="progressbar"></div>
                                            </div>
                                            <span class="small">{{ $ps['completionRate'] }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-secondary py-4">Belum ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>


<x-layouts.app title="Dashboard Universitas">
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Dashboard Universitas</h2>
                <div class="text-secondary">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Universitas</li>
                    </ol>
                </div>
            </div>
            @if($tenant)
                <div class="col-auto">
                    <span class="badge bg-azure-lt fs-5">{{ $tenant->name }}</span>
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
            <p class="empty-title">Data universitas belum tersedia</p>
            <p class="empty-subtitle text-secondary">Hubungi administrator</p>
        </div>
    @else
        {{-- University Stats Cards --}}
        <div class="row mb-3">
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $stats['totalFakultas'] }}</div>
                        <div class="text-secondary">Fakultas</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $stats['totalUsers'] }}</div>
                        <div class="text-secondary">Pengguna</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $stats['totalRps'] }}</div>
                        <div class="text-secondary">Total RPS</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $stats['totalPublished'] }}</div>
                        <div class="text-secondary">Published</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $stats['totalMk'] }}</div>
                        <div class="text-secondary">Mata Kuliah</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-2">
                <div class="card">
                    <div class="card-status-top bg-green"></div>
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $stats['completionRate'] }}%</div>
                        <div class="text-secondary">Completion</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Overall Progress --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <strong>Progress Universitas</strong>
                    <span class="ms-auto">{{ $stats['totalPublished'] }} / {{ $stats['totalMk'] }} RPS Completed</span>
                </div>
                <div class="progress">
                    <div class="progress-bar bg-green" style="width: {{ min($stats['completionRate'], 100) }}%" role="progressbar">
                        <span>{{ $stats['completionRate'] }}%</span>
                    </div>
                </div>
            </div>
        </div>

        @if($stats['activeSemester'])
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon me-2 text-green"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z"/><path d="M16 3v4"/><path d="M8 3v4"/><path d="M4 11h16"/><path d="M11 15h1"/><path d="M12 15v3"/></svg>
                    <div>
                        <strong>Semester Aktif</strong>
                        <span class="badge bg-green-lt ms-2">{{ $stats['activeSemester']->name }} - {{ $stats['activeSemester']->tahun_akademik }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Per Fakultas Stats --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Statistik per Fakultas</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Fakultas</th>
                                <th>Kode</th>
                                <th>Prodi</th>
                                <th>Total MK</th>
                                <th>RPS</th>
                                <th>Published</th>
                                <th>Completion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($this->fakultasStats as $fs)
                                <tr>
                                    <td class="fw-bold">{{ $fs['name'] }}</td>
                                    <td class="text-secondary">{{ $fs['code'] }}</td>
                                    <td>{{ $fs['totalProdi'] }}</td>
                                    <td>{{ $fs['totalMk'] }}</td>
                                    <td>{{ $fs['totalRps'] }}</td>
                                    <td>{{ $fs['published'] }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress progress-xs flex-grow-1 me-2" style="max-width: 80px;">
                                                <div class="progress-bar {{ $fs['completionRate'] >= 80 ? 'bg-green' : ($fs['completionRate'] >= 50 ? 'bg-yellow' : 'bg-red') }}" style="width: {{ $fs['completionRate'] }}%" role="progressbar"></div>
                                            </div>
                                            <span class="small">{{ $fs['completionRate'] }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-secondary py-4">Belum ada data fakultas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</x-layouts.app>

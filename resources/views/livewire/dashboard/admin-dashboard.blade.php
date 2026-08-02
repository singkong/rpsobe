<div>
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Dashboard Super Admin</h2>
                <div class="text-secondary">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if (!$this->stats())
        <div class="empty">
            <p class="empty-title">Data tidak tersedia</p>
        </div>
    @else
        <div class="row mb-3">
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $this->stats()['totalTenants'] }}</div>
                        <div class="text-secondary">Total Tenants</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-2">
                <div class="card">
                    <div class="card-status-top bg-green"></div>
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $this->stats()['activeTenants'] }}</div>
                        <div class="text-secondary">Active Tenants</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-2">
                <div class="card">
                    <div class="card-status-top bg-red"></div>
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $this->stats()['inactiveTenants'] }}</div>
                        <div class="text-secondary">Inactive Tenants</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $this->stats()['totalUsers'] }}</div>
                        <div class="text-secondary">Total Users</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $this->stats()['totalRps'] }}</div>
                        <div class="text-secondary">Total RPS</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0">{{ $this->stats()['totalPublished'] }}</div>
                        <div class="text-secondary">Published RPS</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon me-2 text-success"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/><path d="M12 8v4l2 2"/></svg>
                            <strong>System Status</strong>
                            <span class="ms-auto badge bg-green-lt">Operational</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <small class="text-secondary flex-grow-1">Active Tenants</small>
                            <span class="badge bg-green-lt">{{ $this->stats()['activeTenants'] }}/{{ $this->stats()['totalTenants'] }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <small class="text-secondary flex-grow-1">Total Users</small>
                            <span class="fw-bold">{{ $this->stats()['totalUsers'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon me-2 text-primary"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0"/></svg>
                            <strong>RPS Statistics</strong>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <small class="text-secondary flex-grow-1">Total RPS</small>
                            <span class="fw-bold">{{ $this->stats()['totalRps'] }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <small class="text-secondary flex-grow-1">Published</small>
                            <span class="fw-bold">{{ $this->stats()['totalPublished'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon me-2 text-azure"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0"/><path d="M3 7v1a4 4 0 0 0 4 4h10a4 4 0 0 0 4 -4v-1"/><path d="M12 11l0 10"/></svg>
                            <strong>Quick Actions</strong>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-primary">Reports</a>
                            <a href="{{ route('master-data.dashboard') }}" class="btn btn-sm btn-outline-azure">Master Data</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Aktivitas Tenant Terbaru</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Tenant</th>
                                <th>Kode</th>
                                <th>Status</th>
                                <th>Fakultas</th>
                                <th>Prodi</th>
                                <th>Users</th>
                                <th>RPS</th>
                                <th>Terakhir Aktif</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($this->recentTenants() as $tenant)
                                <tr>
                                    <td class="fw-bold">{{ $tenant['name'] }}</td>
                                    <td class="text-secondary">{{ $tenant['code'] }}</td>
                                    <td>
                                        <span class="badge {{ $tenant['is_active'] ? 'bg-green-lt' : 'bg-red-lt' }}">
                                            {{ $tenant['is_active'] ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ $tenant['fakultas_count'] }}</td>
                                    <td>{{ $tenant['prodi_count'] }}</td>
                                    <td>{{ $tenant['users_count'] }}</td>
                                    <td>{{ $tenant['rps_count'] }}</td>
                                    <td class="text-secondary small">{{ \Carbon\Carbon::parse($tenant['updated_at'])->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                            @if (empty($this->recentTenants()))
                                <tr>
                                    <td colspan="8" class="text-center text-secondary py-4">Belum ada data tenant</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

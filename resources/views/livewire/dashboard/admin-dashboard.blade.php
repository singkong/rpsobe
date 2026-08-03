<div>
    @php $stats = $this->stats(); @endphp
    <div class="row row-deck row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card"><div class="card-body text-center"><div class="h1 m-0">{{ $stats['totalTenants'] ?? 0 }}</div><div class="text-secondary">Total Universitas</div></div></div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card"><div class="card-body text-center"><div class="h1 m-0 text-green">{{ $stats['activeTenants'] ?? 0 }}</div><div class="text-secondary">Aktif</div></div></div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card"><div class="card-body text-center"><div class="h1 m-0">{{ $stats['totalUsers'] ?? 0 }}</div><div class="text-secondary">Total Pengguna</div></div></div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card"><div class="card-body text-center"><div class="h1 m-0 text-teal">{{ $stats['totalPublished'] ?? 0 }}</div><div class="text-secondary">RPS Published</div></div></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Tenant Terbaru</h3></div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead><tr><th>Nama</th><th>Fakultas</th><th>Prodi</th><th>Users</th><th>RPS</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($stats['recentTenants'] ?? [] as $tenant)
                        <tr>
                            <td><strong>{{ $tenant['name'] }}</strong></td>
                            <td>{{ $tenant['fakultas_count'] }}</td>
                            <td>{{ $tenant['prodi_count'] }}</td>
                            <td>{{ $tenant['users_count'] }}</td>
                            <td>{{ $tenant['rps_count'] }}</td>
                            <td><span class="badge {{ $tenant['is_active'] ? 'bg-green' : 'bg-red' }}-lt">{{ $tenant['is_active'] ? 'Aktif' : 'Nonaktif' }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-3 text-secondary">Belum ada tenant</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

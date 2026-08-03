<div>
    <div class="row row-deck row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card"><div class="card-body text-center"><div class="h1 m-0">{{ $this->stats()['totalFakultas'] ?? 0 }}</div><div class="text-secondary">Fakultas</div></div></div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card"><div class="card-body text-center"><div class="h1 m-0">{{ $this->stats()['totalUsers'] ?? 0 }}</div><div class="text-secondary">Pengguna</div></div></div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card"><div class="card-body text-center"><div class="h1 m-0">{{ $this->stats()['totalRps'] ?? 0 }}</div><div class="text-secondary">Total RPS</div></div></div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card"><div class="card-body text-center"><div class="h1 m-0">{{ $this->stats()['completionRate'] ?? 0 }}%</div><div class="text-secondary">Penyelesaian</div></div></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3 class="card-title">Per Fakultas</h3></div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead><tr><th>Fakultas</th><th>Prodi</th><th>RPS</th><th>Published</th><th>Penyelesaian</th></tr></thead>
                <tbody>
                    @forelse($this->stats()['fakultasStats'] ?? [] as $fs)
                        <tr>
                            <td><strong>{{ $fs['name'] }}</strong></td>
                            <td>{{ $fs['totalProdi'] }}</td>
                            <td>{{ $fs['totalRps'] }}</td>
                            <td>{{ $fs['published'] }}</td>
                            <td><div class="progress" style="height:6px"><div class="progress-bar bg-teal" style="width:{{ $fs['completionRate'] }}%"></div></div><small>{{ $fs['completionRate'] }}%</small></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-3">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

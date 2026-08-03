<div>
    <div class="row row-deck row-cards mb-4">
        <div class="col-sm-6 col-lg-4">
            <div class="card"><div class="card-body text-center"><div class="h1 m-0">{{ $this->stats()['totalProdi'] ?? 0 }}</div><div class="text-secondary">Program Studi</div></div></div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card"><div class="card-body text-center"><div class="h1 m-0">{{ $this->stats()['totalRps'] ?? 0 }}</div><div class="text-secondary">Total RPS</div></div></div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card"><div class="card-body text-center"><div class="h1 m-0">{{ $this->stats()['completionRate'] ?? 0 }}%</div><div class="text-secondary">Penyelesaian</div></div></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3 class="card-title">Per Program Studi</h3></div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead><tr><th>Prodi</th><th>Total RPS</th><th>Published</th><th>Penyelesaian</th></tr></thead>
                <tbody>
                    @forelse($this->stats()['prodiStats'] ?? [] as $ps)
                        <tr>
                            <td><strong>{{ $ps['name'] }}</strong> <small class="text-secondary">{{ $ps['code'] }}</small></td>
                            <td>{{ $ps['totalRps'] }}</td>
                            <td>{{ $ps['published'] }}</td>
                            <td><div class="progress" style="height:6px"><div class="progress-bar bg-teal" style="width:{{ $ps['completionRate'] }}%"></div></div><small>{{ $ps['completionRate'] }}%</small></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-3">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

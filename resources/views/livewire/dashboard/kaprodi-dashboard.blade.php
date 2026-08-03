<div>
    @php $stats = $this->stats(); @endphp
    <!-- Stats Cards -->
    <div class="row row-deck row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h1 m-0">{{ $stats['total'] ?? 0 }}</div>
                    <div class="text-secondary">Total RPS</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h1 m-0 text-green">{{ $stats['published'] ?? 0 }}</div>
                    <div class="text-secondary">Published</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h1 m-0 text-blue">{{ $stats['review'] ?? 0 }}</div>
                    <div class="text-secondary">Menunggu Review</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h1 m-0">{{ $stats['completionRate'] ?? 0 }}%</div>
                    <div class="text-secondary">Tingkat Penyelesaian</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Menunggu Review -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex">
                    <h3 class="card-title">Menunggu Review</h3>
                    <span class="badge bg-blue ms-2">{{ count($stats['rpsMenungguReview'] ?? []) }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead><tr><th>MK</th><th>Dosen</th><th></th></tr></thead>
                        <tbody>
                            @forelse($stats['rpsMenungguReview'] ?? [] as $rps)
                                <tr>
                                    <td><strong>{{ $rps->mataKuliah->name ?? '-' }}</strong></td>
                                    <td>{{ $rps->user->name ?? '-' }}</td>
                                    <td class="text-end"><a href="{{ route('rps.review', $rps->id) }}" class="btn btn-sm btn-primary">Review</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center py-3 text-secondary">Tidak ada RPS menunggu review</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Progress Card -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Status RPS</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2"><span>Draft</span> <strong class="text-yellow">{{ $stats['draft'] ?? 0 }}</strong></div>
                    <div class="progress mb-3" style="height:6px"><div class="progress-bar bg-yellow" style="width:{{ ($stats['total'] > 0) ? ($stats['draft'] / $stats['total'] * 100) : 0 }}%"></div></div>
                    <div class="d-flex justify-content-between mb-2"><span>Review</span> <strong class="text-blue">{{ $stats['review'] ?? 0 }}</strong></div>
                    <div class="progress mb-3" style="height:6px"><div class="progress-bar bg-blue" style="width:{{ ($stats['total'] > 0) ? ($stats['review'] / $stats['total'] * 100) : 0 }}%"></div></div>
                    <div class="d-flex justify-content-between mb-2"><span>Approved</span> <strong class="text-green">{{ $stats['approved'] ?? 0 }}</strong></div>
                    <div class="progress mb-3" style="height:6px"><div class="progress-bar bg-green" style="width:{{ ($stats['total'] > 0) ? ($stats['approved'] / $stats['total'] * 100) : 0 }}%"></div></div>
                    <div class="d-flex justify-content-between mb-2"><span>Published</span> <strong class="text-teal">{{ $stats['published'] ?? 0 }}</strong></div>
                    <div class="progress" style="height:6px"><div class="progress-bar bg-teal" style="width:{{ ($stats['total'] > 0) ? ($stats['published'] / $stats['total'] * 100) : 0 }}%"></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Audit Log</h3>
        </div>
        <div class="card-body border-bottom py-3">
            <div class="row g-2">
                <div class="col-md-3">
                    <input type="text" wire:model.live="search" class="form-control" placeholder="Cari...">
                </div>
                <div class="col-md-2">
                    <select wire:model.live="filterAction" class="form-select">
                        <option value="">Semua Aksi</option>
                        <option value="created">Dibuat</option>
                        <option value="updated">Diperbarui</option>
                        <option value="deleted">Dihapus</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Aksi</th>
                        <th>Model</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->audits as $audit)
                        <tr>
                            <td class="text-muted">{{ $audit->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $audit->user->name ?? 'System' }}</td>
                            <td><span class="badge bg-blue-lt">{{ $audit->action }}</span></td>
                            <td>{{ $audit->model_type }} #{{ $audit->model_id }}</td>
                            <td>
                                @if($audit->changes)
                                    @foreach($audit->changes as $field => $change)
                                        <small class="d-block">{{ $field }}: <span class="text-red">{{ $change['old'] ?? '' }}</span> → <span class="text-green">{{ $change['new'] ?? '' }}</span></small>
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-secondary">Tidak ada log audit</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $this->audits->links() }}
        </div>
    </div>
</div>

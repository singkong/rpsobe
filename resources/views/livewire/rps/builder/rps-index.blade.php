<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar RPS</h3>
            <div class="card-actions">
                <a href="{{ route('rps.create') }}" class="btn btn-primary" wire:navigate>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                    Buat RPS Baru
                </a>
            </div>
        </div>
        <div class="card-body border-bottom py-3">
            <div class="row g-2">
                <div class="col-md-4">
                    <div class="input-icon">
                        <input type="text" wire:model.live="search" class="form-control" placeholder="Cari mata kuliah...">
                        <span class="input-icon-addon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><circle cx="10" cy="10" r="7"/><path d="M21 21l-6-6"/></svg>
                        </span>
                    </div>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="filterStatus" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach(App\Enums\RPSStatus::cases() as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="filterSemester" class="form-select">
                        <option value="">Semua Semester</option>
                        @foreach($semesterList as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="filterMk" class="form-select">
                        <option value="">Semua MK</option>
                        @foreach($mataKuliahList as $mk)
                            <option value="{{ $mk->id }}">{{ $mk->code }} - {{ $mk->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-striped">
                <thead>
                    <tr>
                        <th wire:click="sortBy('mata_kuliah_id')" style="cursor:pointer">Mata Kuliah @if($sortField === 'mata_kuliah_id') {{ $sortDirection === 'asc' ? '&#9650;' : '&#9660;' }} @endif</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th>Terakhir Diupdate</th>
                        <th>Progress</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->rpsList() as $item)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $item->mataKuliah->name ?? '-' }}</div>
                                <div class="text-secondary small">{{ $item->mataKuliah->code ?? '-' }}</div>
                            </td>
                            <td>{{ $item->semester->name ?? '-' }}</td>
                            <td>
                                @php $color = $item->status?->color() ?? 'gray'; @endphp
                                <span class="badge bg-{{ $color }}-lt text-{{ $color }}">
                                    {{ $item->status?->label() ?? '-' }}
                                </span>
                            </td>
                            <td class="text-secondary small">{{ $item->updated_at->diffForHumans() }}</td>
                            <td>
                                @php $progress = $this->getProgress($item->id); @endphp
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress progress-sm flex-grow-1" style="min-width: 100px;">
                                        <div class="progress-bar bg-primary" style="width: {{ $progress }}%" role="progressbar"></div>
                                    </div>
                                    <span class="small text-secondary">{{ $progress }}%</span>
                                </div>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    @if($item->isEditable() || ($item->user_id === Auth::id()))
                                        <a href="{{ route('rps.edit', ['rpsId' => $item->id]) }}" class="btn btn-sm btn-icon btn-outline-primary" title="{{ $item->isEditable() ? 'Lanjutkan' : 'Lihat' }}" wire:navigate>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/></svg>
                                        </a>
                                    @else
                                        <a href="{{ route('rps.edit', ['rpsId' => $item->id]) }}" class="btn btn-sm btn-icon btn-outline-info" title="Lihat" wire:navigate>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M10 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/></svg>
                                        </a>
                                    @endif
                                    <button wire:click="duplicate({{ $item->id }})" class="btn btn-sm btn-icon btn-outline-success" title="Duplikasi">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M7 7m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z"/><path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1"/></svg>
                                    </button>
                                    @if($item->user_id === Auth::id())
                                        <button wire:click="confirmDelete({{ $item->id }})" class="btn btn-sm btn-icon btn-outline-danger" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-secondary">Menampilkan {{ $this->rpsList()->firstItem() }} - {{ $this->rpsList()->lastItem() }} dari {{ $this->rpsList()->total() }}</p>
            <div class="ms-auto">{{ $this->rpsList()->links() }}</div>
        </div>
    </div>

    @if($showDeleteConfirm)
        <div class="modal modal-blur fade show" tabindex="-1" style="display:block" role="dialog">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-status bg-danger"></div>
                    <div class="modal-body text-center py-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-danger mb-2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                        <h3>Konfirmasi Hapus</h3>
                        <p class="text-secondary">Apakah Anda yakin ingin menghapus RPS ini? Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <div class="modal-footer">
                        <div class="w-100">
                            <div class="row">
                                <div class="col"><button class="btn btn-ghost-secondary w-100" wire:click="cancelDelete">Batal</button></div>
                                <div class="col"><button class="btn btn-danger w-100" wire:click="delete">Hapus</button></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    @if(session()->has('message'))
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
            <div class="toast show" role="alert">
                <div class="toast-header">
                    <strong class="me-auto">Berhasil</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">{{ session('message') }}</div>
            </div>
        </div>
    @endif
</div>

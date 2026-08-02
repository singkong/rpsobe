<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Capaian Pembelajaran Mata Kuliah (CPMK)</h3>
            <button wire:click="startAdd" class="btn btn-sm btn-primary" @if($addingNew) disabled @endif>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                Tambah CPMK
            </button>
        </div>
        <div class="card-body">
            @if($addingNew)
                <div class="card card-sm bg-primary-lt mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Tambah CPMK Baru</h5>
                        <div class="row mb-2">
                            <div class="col-md-3">
                                <label class="form-label required">Kode</label>
                                <input type="text" wire:model="newCode" class="form-control" placeholder="CPMK-01">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Level Taksonomi</label>
                                <select wire:model="newLevelTaksonomi" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    @foreach($this->taksonomiOptions() as $opt)
                                        <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">CPL Terkait</label>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($cplList as $cpl)
                                        <label class="form-check form-check-inline">
                                            <input type="checkbox" class="form-check-input"
                                                   wire:model="newCplIds" value="{{ $cpl->id }}">
                                            <span class="form-check-label small">{{ $cpl->code }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label required">Deskripsi</label>
                            <textarea wire:model="newDeskripsi" class="form-control @error('newDeskripsi') is-invalid @enderror" rows="2" placeholder="Deskripsi CPMK..."></textarea>
                            @error('newDeskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="d-flex gap-2">
                            <button wire:click="saveNew" class="btn btn-sm btn-primary">Simpan</button>
                            <button wire:click="cancelAdd" class="btn btn-sm btn-ghost-secondary">Batal</button>
                        </div>
                    </div>
                </div>
            @endif

            @if(count($cpmlList) === 0)
                <div class="alert alert-info">Belum ada CPMK yang ditambahkan. Silakan pilih CPL terlebih dahulu pada Step 2.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th style="width: 40px">#</th>
                                <th>Kode</th>
                                <th>Deskripsi</th>
                                <th>CPL Terkait</th>
                                <th>Taksonomi</th>
                                <th style="width: 120px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cpmlList as $index => $cpml)
                                @if($editingId === $cpml['id'])
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><input type="text" wire:model="editCode" class="form-control form-control-sm"></td>
                                        <td><textarea wire:model="editDeskripsi" class="form-control form-control-sm @error('editDeskripsi') is-invalid @enderror" rows="2"></textarea></td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($cplList as $cpl)
                                                    <label class="form-check form-check-inline small">
                                                        <input type="checkbox" class="form-check-input" wire:model="editCplIds" value="{{ $cpl->id }}">
                                                        {{ $cpl->code }}
                                                    </label>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <select wire:model="editLevelTaksonomi" class="form-select form-select-sm">
                                                <option value="">--</option>
                                                @foreach($this->taksonomiOptions() as $opt)
                                                    <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <div class="btn-list">
                                                <button wire:click="saveEdit({{ $cpml['id'] }})" class="btn btn-sm btn-icon btn-outline-success" title="Simpan">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M5 12l5 5l10 -10"/></svg>
                                                </button>
                                                <button wire:click="cancelEdit" class="btn btn-sm btn-icon btn-outline-secondary" title="Batal">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M18 6l-12 12"/><path d="M6 6l12 12"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button wire:click="moveUp({{ $index }})" class="btn btn-sm btn-icon btn-ghost-secondary" title="Naik" @if($index === 0) disabled @endif>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M18 15l-6 -6l-6 6h12"/></svg>
                                                </button>
                                                <button wire:click="moveDown({{ $index }})" class="btn btn-sm btn-icon btn-ghost-secondary" title="Turun" @if($index === count($cpmlList) - 1) disabled @endif>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M6 9l6 6l6 -6h-12"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-primary-lt">{{ $cpml['code'] }}</span></td>
                                        <td>{{ $cpml['deskripsi'] }}</td>
                                        <td><span class="small">{{ $cpml['cpl_labels'] ?: '-' }}</span></td>
                                        <td>{{ $cpml['level_taksonomi'] ?: '-' }}</td>
                                        <td>
                                            <div class="btn-list">
                                                <button wire:click="startEdit({{ $cpml['id'] }})" class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/></svg>
                                                </button>
                                                <button wire:click="deleteCpml({{ $cpml['id'] }})" class="btn btn-sm btn-icon btn-outline-danger" title="Hapus">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

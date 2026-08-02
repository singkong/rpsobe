<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Sub-CPMK</h3>
            <button wire:click="startAdd" class="btn btn-sm btn-primary" @if($addingNew) disabled @endif>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                Tambah Sub-CPMK
            </button>
        </div>
        <div class="card-body">
            @if($addingNew)
                <div class="card card-sm bg-primary-lt mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Tambah Sub-CPMK Baru</h5>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <label class="form-label required">CPMK Induk</label>
                                <select wire:model.live="newCpmlId" class="form-select @error('newCpmlId') is-invalid @enderror">
                                    <option value="">-- Pilih CPMK --</option>
                                    @foreach($cpmlList as $cpml)
                                        <option value="{{ $cpml->id }}">{{ $cpml->code }} - {{ $cpml->deskripsi }}</option>
                                    @endforeach
                                </select>
                                @error('newCpmlId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kode</label>
                                <input type="text" wire:model="newCode" class="form-control" placeholder="SCPMK-01">
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
                        </div>
                        <div class="mb-2">
                            <label class="form-label required">Deskripsi</label>
                            <textarea wire:model="newDeskripsi" class="form-control @error('newDeskripsi') is-invalid @enderror" rows="2" placeholder="Deskripsi Sub-CPMK..."></textarea>
                            @error('newDeskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Pertemuan Terkait</label>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($this->pertemuanRange() as $p)
                                    <button type="button"
                                            wire:click="togglePertemuan({{ $p }})"
                                            class="btn btn-sm {{ in_array($p, $newPertemuanTerkait ?? []) ? 'btn-primary' : 'btn-outline-secondary' }}"
                                            style="min-width: 38px">
                                        {{ $p }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button wire:click="saveNew" class="btn btn-sm btn-primary">Simpan</button>
                            <button wire:click="cancelAdd" class="btn btn-sm btn-ghost-secondary">Batal</button>
                        </div>
                    </div>
                </div>
            @endif

            @if(count($subCpmkList) === 0)
                <div class="alert alert-info">Belum ada Sub-CPMK. Silakan tambahkan CPMK terlebih dahulu pada Step 3.</div>
            @else
                @foreach($subCpmkList as $group)
                    <div class="card card-sm mb-3">
                        <div class="card-header bg-light">
                            <strong>{{ $group['cpml_code'] }}</strong> - {{ $group['cpml_deskripsi'] }}
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Deskripsi</th>
                                        <th>Taksonomi</th>
                                        <th>Pertemuan</th>
                                        <th style="width: 100px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($group['items']) === 0)
                                        <tr>
                                            <td colspan="5" class="text-secondary text-center py-2">Belum ada Sub-CPMK</td>
                                        </tr>
                                    @else
                                        @foreach($group['items'] as $item)
                                            @if($editingId === $item['id'])
                                                <tr>
                                                    <td><input type="text" wire:model="editCode" class="form-control form-control-sm"></td>
                                                    <td><textarea wire:model="editDeskripsi" class="form-control form-control-sm @error('editDeskripsi') is-invalid @enderror" rows="2"></textarea></td>
                                                    <td>
                                                        <select wire:model="editLevelTaksonomi" class="form-select form-select-sm">
                                                            <option value="">--</option>
                                                            @foreach($this->taksonomiOptions() as $opt)
                                                                <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-wrap gap-1">
                                                            @foreach($this->pertemuanRange() as $p)
                                                                <button type="button"
                                                                        wire:click="toggleEditPertemuan({{ $p }})"
                                                                        class="btn btn-xs {{ in_array($p, $editPertemuanTerkait ?? []) ? 'btn-primary' : 'btn-outline-secondary' }}"
                                                                        style="padding: 1px 6px; font-size: 11px;">
                                                                    {{ $p }}
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="btn-list">
                                                            <button wire:click="saveEdit({{ $item['id'] }})" class="btn btn-sm btn-icon btn-outline-success" title="Simpan">
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
                                                    <td><span class="badge bg-primary-lt">{{ $item['code'] }}</span></td>
                                                    <td>{{ $item['deskripsi'] }}</td>
                                                    <td>{{ $item['level_taksonomi'] ?: '-' }}</td>
                                                    <td>
                                                        @if(!empty($item['pertemuan_terkait']))
                                                            @foreach($item['pertemuan_terkait'] as $p)
                                                                <span class="badge bg-secondary-lt me-1">{{ $p }}</span>
                                                            @endforeach
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-list">
                                                            <button wire:click="startEdit({{ $item['id'] }})" class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/></svg>
                                                            </button>
                                                            <button wire:click="deleteSubCpmk({{ $item['id'] }})" class="btn btn-sm btn-icon btn-outline-danger" title="Hapus">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Assessment</h3>
            <div class="ms-auto d-flex gap-3 align-items-center">
                <div>
                    <span class="small text-secondary">Total Bobot:</span>
                    <strong class="{{ $this->bobotClass() }} ms-1">{{ $this->totalBobot() }}%</strong>
                    @if(!$bobotClass() === 'text-green')
                        <span class="badge bg-danger-lt ms-1">Harus 100%</span>
                    @else
                        <span class="badge bg-green-lt ms-1">OK</span>
                    @endif
                </div>
                <button wire:click="startAdd" class="btn btn-sm btn-primary" @if($addingNew) disabled @endif>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                    Tambah Assessment
                </button>
            </div>
        </div>
        <div class="card-body">
            @if(abs($this->totalBobot() - 100) > 0.01 && $this->totalBobot() > 0)
                <div class="alert alert-warning alert-dismissible" role="alert">
                    <strong>Perhatian!</strong> Total bobot assessment harus 100%. Saat ini: {{ $this->totalBobot() }}%.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($addingNew)
                <div class="card card-sm bg-primary-lt mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Tambah Assessment Baru</h5>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <label class="form-label required">Nama Assessment</label>
                                <input type="text" wire:model="newNama" class="form-control @error('newNama') is-invalid @enderror" placeholder="UTS, UAS, Tugas 1, Kuis 1...">
                                @error('newNama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label required">Jenis</label>
                                <select wire:model="newJenis" class="form-select @error('newJenis') is-invalid @enderror">
                                    <option value="">-- Pilih --</option>
                                    @foreach($this->jenisOptions() as $opt)
                                        <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                                    @endforeach
                                </select>
                                @error('newJenis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-2">
                                <label class="form-label required">Bobot (%)</label>
                                <input type="number" wire:model="newBobot" class="form-control @error('newBobot') is-invalid @enderror" step="1" min="1" max="100">
                                @error('newBobot') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Sub-CPMK Terkait</label>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($allSubCpmk as $sub)
                                    <label class="form-check form-check-inline">
                                        <input type="checkbox" class="form-check-input" wire:model="newSubCpmkIds" value="{{ $sub['id'] }}">
                                        <span class="form-check-label small">{{ $sub['code'] }} ({{ $sub['cpml_code'] }})</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Deskripsi</label>
                            <input type="text" wire:model="newDeskripsi" class="form-control" placeholder="Deskripsi singkat...">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Rubrik</label>
                            <textarea wire:model="newRubrik" class="form-control" rows="3" placeholder="Rubrik penilaian (opsional)..."></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button wire:click="saveNew" class="btn btn-sm btn-primary">Simpan</button>
                            <button wire:click="cancelAdd" class="btn btn-sm btn-ghost-secondary">Batal</button>
                        </div>
                    </div>
                </div>
            @endif

            @if(count($assessmentList) === 0)
                <div class="alert alert-info">Belum ada assessment. Tambahkan assessment untuk memulai.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Jenis</th>
                                <th style="width: 80px">Bobot</th>
                                <th>Sub-CPMK</th>
                                <th style="width: 140px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assessmentList as $item)
                                @if($editingId === $item['id'])
                                    <tr>
                                        <td><input type="text" wire:model="editNama" class="form-control form-control-sm @error('editNama') is-invalid @enderror"></td>
                                        <td>
                                            <select wire:model="editJenis" class="form-select form-select-sm">
                                                @foreach($this->jenisOptions() as $opt)
                                                    <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" wire:model="editBobot" class="form-control form-control-sm" step="1" min="1" max="100"></td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($allSubCpmk as $sub)
                                                    <label class="form-check form-check-inline small">
                                                        <input type="checkbox" class="form-check-input" wire:model="editSubCpmkIds" value="{{ $sub['id'] }}">
                                                        {{ $sub['code'] }}
                                                    </label>
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
                                        <td>
                                            <strong>{{ $item['nama'] }}</strong>
                                            @if(!empty($item['deskripsi']))
                                                <div class="text-secondary small">{{ $item['deskripsi'] }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $item['jenis'] === 'formatif' ? 'bg-blue-lt' : 'bg-orange-lt' }}">
                                                {{ $item['jenis_label'] }}
                                            </span>
                                        </td>
                                        <td><strong>{{ $item['bobot'] }}%</strong></td>
                                        <td>
                                            @if(!empty($item['sub_cpmk_codes']))
                                                @foreach(explode(', ', $item['sub_cpmk_codes']) as $code)
                                                    <span class="badge bg-primary-lt me-1">{{ $code }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-secondary">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-list">
                                                @if(!empty($item['rubrik']))
                                                    <button wire:click="toggleRubrik({{ $item['id'] }})" class="btn btn-sm btn-icon btn-outline-info" title="Lihat Rubrik">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/></svg>
                                                    </button>
                                                @endif
                                                <button wire:click="startEdit({{ $item['id'] }})" class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/></svg>
                                                </button>
                                                <button wire:click="deleteAssessment({{ $item['id'] }})" onclick="confirm('Hapus assessment ini?') || event.stopImmediatePropagation()" class="btn btn-sm btn-icon btn-outline-danger" title="Hapus">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @if(in_array($item['id'], $expandedRubrik ?? []) && !empty($item['rubrik']))
                                        <tr>
                                            <td colspan="5">
                                                <div class="card card-sm bg-light">
                                                    <div class="card-body">
                                                        <h6>Rubrik: {{ $item['nama'] }}</h6>
                                                        <pre class="mb-0" style="white-space: pre-wrap;">{{ $item['rubrik'] }}</pre>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="text-end"><strong>Total Bobot</strong></td>
                                <td><strong class="{{ $this->bobotClass() }}">{{ $this->totalBobot() }}%</strong></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

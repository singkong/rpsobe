<x-layouts.app title="Review RPS">
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Review RPS</h2>
                    <div class="text-secondary mt-1">
                        <span class="badge bg-{{ $rps->status->color() }} me-2">{{ $rps->status->label() }}</span>
                        {{ $rps->mataKuliah->code }} - {{ $rps->mataKuliah->name }}
                    </div>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('review.list') }}" class="btn btn-ghost-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12l14 0"/><path d="M5 12l4 4"/><path d="M5 12l4 -4"/></svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Pratinjau RPS</h3>
                        </div>
                        <div class="card-body">
                            <div class="accordion" id="rpsPreview">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#sec-info">
                                            Informasi Mata Kuliah
                                        </button>
                                    </h2>
                                    <div id="sec-info" class="accordion-collapse collapse show" data-bs-parent="#rpsPreview">
                                        <div class="accordion-body">
                                            <dl class="row">
                                                <dt class="col-sm-4">Mata Kuliah</dt>
                                                <dd class="col-sm-8">{{ $rps->mataKuliah->code }} - {{ $rps->mataKuliah->name }} ({{ $rps->mataKuliah->sks }} SKS)</dd>
                                                <dt class="col-sm-4">Program Studi</dt>
                                                <dd class="col-sm-8">{{ $rps->mataKuliah->kurikulum->programStudi->name ?? '-' }}</dd>
                                                <dt class="col-sm-4">Semester</dt>
                                                <dd class="col-sm-8">{{ $rps->semester->name ?? '-' }}</dd>
                                                <dt class="col-sm-4">Dosen</dt>
                                                <dd class="col-sm-8">{{ $rps->user->name ?? '-' }}</dd>
                                                <dt class="col-sm-4">Versi</dt>
                                                <dd class="col-sm-8"><span class="badge bg-primary-lt">{{ $rps->version_label }}</span></dd>
                                            </dl>
                                            @if($rps->deskripsi)
                                            <div class="mt-3">
                                                <strong>Deskripsi:</strong>
                                                <p class="text-secondary mt-1">{{ $rps->deskripsi }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-cpl">
                                            CPL &amp; CPMK
                                        </button>
                                    </h2>
                                    <div id="sec-cpl" class="accordion-collapse collapse" data-bs-parent="#rpsPreview">
                                        <div class="accordion-body">
                                            <h5 class="mb-2">Capaian Pembelajaran Lulusan (CPL)</h5>
                                            @foreach($rps->cpl as $cpl)
                                            <div class="mb-2"><span class="badge bg-blue-lt me-2">{{ $cpl->code }}</span> {{ $cpl->deskripsi }}</div>
                                            @endforeach
                                            <hr>
                                            <h5 class="mb-2">Capaian Pembelajaran Mata Kuliah (CPMK)</h5>
                                            @foreach($rps->cpml as $cpml)
                                            <div class="mb-2 p-2 border rounded">
                                                <strong>{{ $cpml->code }}</strong>: {{ $cpml->deskripsi }}
                                                @if($cpml->cpl->isNotEmpty())
                                                <div class="mt-1">
                                                    <small class="text-secondary">Terkait CPL: {{ $cpml->cpl->pluck('code')->join(', ') }}</small>
                                                </div>
                                                @endif
                                                @if($cpml->subCpmk->isNotEmpty())
                                                <div class="mt-2">
                                                    <small class="text-secondary">Sub-CPMK:</small>
                                                    <ul class="mb-0 small">
                                                        @foreach($cpml->subCpmk as $sub)
                                                        <li>{{ $sub->code }}: {{ $sub->deskripsi }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                @endif
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-materi">
                                            Materi Pertemuan &amp; Metode
                                        </button>
                                    </h2>
                                    <div id="sec-materi" class="accordion-collapse collapse" data-bs-parent="#rpsPreview">
                                        <div class="accordion-body">
                                            <div class="table-responsive">
                                                <table class="table table-vcenter">
                                                    <thead>
                                                        <tr>
                                                            <th>Minggu</th>
                                                            <th>Sub-CPMK</th>
                                                            <th>Materi</th>
                                                            <th>Metode</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($rps->materiPertemuan as $materi)
                                                        <tr>
                                                            <td>{{ $materi->pertemuan_ke }}</td>
                                                            <td><small>{{ $materi->subCpmk->code ?? '-' }}</small></td>
                                                            <td>{{ Str::limit($materi->materi, 60) }}</td>
                                                            <td>
                                                                @if($materi->metode_pembelajaran)
                                                                    @foreach($materi->metode_pembelajaran as $m)
                                                                        <span class="badge bg-azure-lt me-1">{{ $m }}</span>
                                                                    @endforeach
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sec-assessment">
                                            Assessment
                                        </button>
                                    </h2>
                                    <div id="sec-assessment" class="accordion-collapse collapse" data-bs-parent="#rpsPreview">
                                        <div class="accordion-body">
                                            <div class="table-responsive">
                                                <table class="table table-vcenter">
                                                    <thead>
                                                        <tr>
                                                            <th>Nama</th>
                                                            <th>Jenis</th>
                                                            <th>Bobot</th>
                                                            <th>Sub-CPMK</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($rps->assessment as $as)
                                                        <tr>
                                                            <td>{{ $as->nama }}</td>
                                                            <td><span class="badge bg-purple-lt">{{ $as->jenis->value }}</span></td>
                                                            <td>{{ $as->bobot_persen }}%</td>
                                                            <td>
                                                                @foreach($as->subCpmk as $sub)
                                                                    <span class="badge bg-green-lt me-1">{{ $sub->code }}</span>
                                                                @endforeach
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Penilaian Review</h3>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="">
                                <h5 class="mb-3">Skor Per Komponen (1-10)</h5>

                                @foreach($this->komponenLabels as $key => $label)
                                <div class="mb-3">
                                    <label class="form-label">{{ $label }}</label>
                                    <div class="row g-2">
                                        <div class="col-7">
                                            <input type="range" class="form-range" min="1" max="10"
                                                wire:model.live="skorPerKomponen.{{ $key }}"
                                                value="{{ $skorPerKomponen[$key] ?? 5 }}">
                                        </div>
                                        <div class="col-5">
                                            <input type="number" class="form-control form-control-sm"
                                                wire:model.live="skorPerKomponen.{{ $key }}"
                                                min="1" max="10"
                                                value="{{ $skorPerKomponen[$key] ?? '' }}"
                                                placeholder="1-10">
                                        </div>
                                    </div>
                                    @error('skorPerKomponen.' . $key)
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                    <textarea class="form-control form-control-sm mt-1"
                                        wire:model="komentar.{{ $key }}"
                                        rows="2"
                                        placeholder="Komentar {{ $label }}..."></textarea>
                                </div>
                                @endforeach

                                <div class="card bg-light mb-3">
                                    <div class="card-body text-center">
                                        <div class="h3 mb-0">
                                            Total: <span class="text-{{ $this->skorTotal >= ($this->skorMax * 0.7) ? 'success' : 'danger' }}">
                                                {{ $this->skorTotal }} / {{ $this->skorMax }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Catatan Keseluruhan</label>
                                    <textarea class="form-control" wire:model="catatan" rows="4"
                                        placeholder="Catatan atau rekomendasi keseluruhan..."></textarea>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-success" wire:click="confirmApprove">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12l5 5l10 -10"/></svg>
                                        Setujui
                                    </button>
                                    <button type="button" class="btn btn-warning" wire:click="confirmRevision">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h-9"/><path d="M16.793 3.293a1 1 0 0 1 1.414 0l2.5 2.5a1 1 0 0 1 0 1.414l-9 9h-3v-3z"/></svg>
                                        Minta Revisi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($showConfirmApprove)
        <div class="modal modal-blur fade show" tabindex="-1" style="display:block" role="dialog">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-status bg-success"></div>
                    <div class="modal-body text-center py-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-success mb-2"><path d="M5 12l5 5l10 -10"/></svg>
                        <h3>Konfirmasi Persetujuan</h3>
                        <p class="text-secondary">Apakah Anda yakin ingin menyetujui RPS ini?</p>
                        <p class="small text-secondary">Total Skor: {{ $this->skorTotal }} / {{ $this->skorMax }}</p>
                    </div>
                    <div class="modal-footer">
                        <div class="w-100">
                            <div class="row">
                                <div class="col"><button class="btn btn-ghost-secondary w-100" wire:click="cancelConfirm">Batal</button></div>
                                <div class="col"><button class="btn btn-success w-100" wire:click="executeApprove">Ya, Setujui</button></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    @if($showConfirmRevision)
        <div class="modal modal-blur fade show" tabindex="-1" style="display:block" role="dialog">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-status bg-warning"></div>
                    <div class="modal-body text-center py-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-warning mb-2"><path d="M12 20h-9"/><path d="M16.793 3.293a1 1 0 0 1 1.414 0l2.5 2.5a1 1 0 0 1 0 1.414l-9 9h-3v-3z"/></svg>
                        <h3>Konfirmasi Revisi</h3>
                        <p class="text-secondary">Apakah Anda yakin ingin meminta revisi untuk RPS ini?</p>
                    </div>
                    <div class="modal-footer">
                        <div class="w-100">
                            <div class="row">
                                <div class="col"><button class="btn btn-ghost-secondary w-100" wire:click="cancelConfirm">Batal</button></div>
                                <div class="col"><button class="btn btn-warning w-100" wire:click="executeRevision">Ya, Minta Revisi</button></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    @if(session()->has('error'))
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
            <div class="toast show bg-danger text-white" role="alert">
                <div class="toast-header"><strong class="me-auto">Error</strong><button type="button" class="btn-close" data-bs-dismiss="toast"></button></div>
                <div class="toast-body">{{ session('error') }}</div>
            </div>
        </div>
    @endif
</x-layouts.app>

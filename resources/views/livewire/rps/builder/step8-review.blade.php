<div>
    @if(empty($rpsData))
        <div class="alert alert-warning">Data RPS tidak tersedia. Silakan lengkapi step sebelumnya.</div>
    @else
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card card-sm">
                    <div class="card-body text-center">
                        <div class="text-secondary small">Total CPMK</div>
                        <div class="h2 mb-0">{{ $this->totalCpmk() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-sm">
                    <div class="card-body text-center">
                        <div class="text-secondary small">Total Sub-CPMK</div>
                        <div class="h2 mb-0">{{ $this->totalSubCpmk() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card card-sm">
                    <div class="card-body text-center">
                        <div class="text-secondary small">Pertemuan</div>
                        <div class="h2 mb-0">{{ $this->totalPertemuan() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card card-sm">
                    <div class="card-body text-center">
                        <div class="text-secondary small">Assessment</div>
                        <div class="h2 mb-0">{{ $this->totalAssessment() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card card-sm">
                    <div class="card-body text-center">
                        <div class="text-secondary small">Bobot</div>
                        <div class="h2 mb-0 {{ $this->bobotOk() ? 'text-green' : 'text-red' }}">
                            {{ $this->totalBobot() }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="card card-sm">
                    <div class="card-body text-center">
                        <div class="text-secondary small">CPL Coverage (CPMK)</div>
                        <div class="h2 mb-0 text-blue">{{ $this->cplCoverage() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-sm">
                    <div class="card-body text-center">
                        <div class="text-secondary small">Referensi</div>
                        <div class="h2 mb-0">{{ $this->totalReferensi() }}</div>
                    </div>
                </div>
            </div>
        </div>

        @if($validationResult)
            <div class="card mb-3">
                <div class="card-status-top {{ $validationResult['pass'] ? 'bg-green' : 'bg-red' }}"></div>
                <div class="card-body">
                    <h4>Hasil Validasi (Skor: {{ $validationResult['score'] }}/{{ $validationResult['max_score'] }})</h4>

                    @if(!empty($validationResult['errors']))
                        <div class="alert alert-danger">
                            <strong>Error:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach($validationResult['errors'] as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(!empty($validationResult['warnings']))
                        <div class="alert alert-warning">
                            <strong>Peringatan:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach($validationResult['warnings'] as $warning)
                                    <li>{{ $warning }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($validationResult['pass'] && empty($validationResult['warnings']))
                        <div class="alert alert-success mb-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M5 12l5 5l10 -10"/></svg>
                            Semua validasi lulus! RPS siap diajukan untuk review.
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pratinjau RPS</h3>
                <div class="ms-auto">
                    <span class="badge bg-primary-lt me-2">{{ $rpsData['version_label'] ?? 'v0.1' }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h4 class="text-primary">1. Identitas Mata Kuliah</h4>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td style="width: 180px;"><strong>Nama MK</strong></td>
                            <td>: {{ $rpsData['mata_kuliah_name'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>Kode MK</strong></td>
                            <td>: {{ $rpsData['mata_kuliah_code'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>SKS</strong></td>
                            <td>: {{ $rpsData['sks'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>Semester</strong></td>
                            <td>: {{ $rpsData['semester_name'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>Program Studi</strong></td>
                            <td>: {{ $rpsData['program_studi_name'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>Dosen Pengampu</strong></td>
                            <td>: {{ $this->getDosenPengampu() }}</td>
                        </tr>
                        @if(!empty($rpsData['deskripsi']))
                            <tr>
                                <td><strong>Deskripsi</strong></td>
                                <td>: {{ $rpsData['deskripsi'] }}</td>
                            </tr>
                        @endif
                    </table>
                </div>

                <hr>

                <div class="mb-4">
                    <h4 class="text-primary">2. CPL yang Didukung</h4>
                    @if(empty($rpsData['cpl']))
                        <div class="text-secondary">Belum ada CPL yang dipilih.</div>
                    @else
                        @foreach($rpsData['cpl'] as $cpl)
                            <div class="mb-1">
                                <span class="badge bg-blue-lt me-2">{{ $cpl['code'] }}</span>
                                {{ $cpl['deskripsi'] }}
                            </div>
                        @endforeach
                    @endif
                </div>

                <hr>

                <div class="mb-4">
                    <h4 class="text-primary">3. CPMK dengan Mapping CPL</h4>
                    @if(empty($rpsData['cpml']))
                        <div class="text-secondary">Belum ada CPMK.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Deskripsi</th>
                                        <th>CPL Terkait</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rpsData['cpml'] as $cpml)
                                        <tr>
                                            <td><span class="badge bg-primary-lt">{{ $cpml['code'] }}</span></td>
                                            <td>{{ $cpml['deskripsi'] }}</td>
                                            <td><span class="small">{{ $cpml['cpl_codes'] ?? '-' }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <hr>

                <div class="mb-4">
                    <h4 class="text-primary">4. Sub-CPMK dengan Mapping CPMK</h4>
                    @if(empty($rpsData['sub_cpmk_flat']))
                        <div class="text-secondary">Belum ada Sub-CPMK.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Deskripsi</th>
                                        <th>CPMK</th>
                                        <th>CPL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rpsData['sub_cpmk_flat'] as $sub)
                                        <tr>
                                            <td><span class="badge bg-secondary-lt">{{ $sub['code'] }}</span></td>
                                            <td>{{ $sub['deskripsi'] }}</td>
                                            <td>{{ $sub['cpmk_code'] }}</td>
                                            <td><span class="small">{{ $sub['cpl_codes'] ?? '-' }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <hr>

                <div class="mb-4">
                    <h4 class="text-primary">5. Tabel Pertemuan</h4>
                    @if(empty($rpsData['pertemuan']))
                        <div class="text-secondary">Belum ada materi pertemuan.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Ke-</th>
                                        <th>Sub-CPMK</th>
                                        <th>Materi</th>
                                        <th>Metode</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rpsData['pertemuan'] as $p)
                                        <tr>
                                            <td><span class="badge bg-primary">{{ $p['pertemuan_ke'] }}</span></td>
                                            <td><span class="badge bg-primary-lt">{{ $p['sub_cpmk_code'] }}</span></td>
                                            <td class="small">{{ Str::limit($p['materi'], 80) }}</td>
                                            <td>
                                                @if(!empty($p['metode']))
                                                    @foreach($p['metode'] as $m)
                                                        <span class="badge bg-secondary-lt me-1">{{ $m }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-secondary">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <hr>

                <div class="mb-4">
                    <h4 class="text-primary">6. Assessment</h4>
                    @if(empty($rpsData['assessments']))
                        <div class="text-secondary">Belum ada assessment.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Jenis</th>
                                        <th>Bobot</th>
                                        <th>Sub-CPMK</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rpsData['assessments'] as $a)
                                        <tr>
                                            <td><strong>{{ $a['nama'] }}</strong></td>
                                            <td>
                                                <span class="badge {{ $a['jenis'] === 'formatif' ? 'bg-blue-lt' : 'bg-orange-lt' }}">
                                                    {{ $a['jenis_label'] }}
                                                </span>
                                            </td>
                                            <td><strong>{{ $a['bobot'] }}%</strong></td>
                                            <td>
                                                @if(!empty($a['sub_cpmk_codes']))
                                                    <span class="small">{{ $a['sub_cpmk_codes'] }}</span>
                                                @else
                                                    <span class="text-secondary">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-end"><strong>Total Bobot</strong></td>
                                        <td><strong class="{{ $this->bobotOk() ? 'text-green' : 'text-red' }}">{{ $this->totalBobot() }}%</strong></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>

                <hr>

                <div class="mb-4">
                    <h4 class="text-primary">7. Referensi</h4>
                    @if($this->totalReferensi() === 0)
                        <div class="text-secondary">Belum ada referensi pada materi pertemuan.</div>
                    @else
                        <span class="badge bg-primary-lt">{{ $this->totalReferensi() }} referensi digunakan</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <div>
                <button wire:click="simpanDraft" class="btn btn-outline-primary" wire:loading.attr="disabled">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"/><path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/><path d="M14 4l0 4h-6l0 -4"/></svg>
                    Simpan Draft
                </button>
            </div>
            <div class="d-flex gap-2">
                <button wire:click="validasi" class="btn btn-outline-info" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="validasi">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M10 24h-6v-6h6z"/><path d="M20.998 12.998v-7.998h-12.998l-2 2v1.998"/><path d="M5 8h-5v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1 -1v-7.5"/><path d="M10 13l4 -4"/><path d="M8 21l0 -3"/></svg>
                        Validasi RPS
                    </span>
                    <span wire:loading wire:target="validasi" class="spinner-border spinner-border-sm me-1"></span>
                    <span wire:loading wire:target="validasi">Memvalidasi...</span>
                </button>
                <button wire:click="confirmSubmit" class="btn btn-primary" wire:loading.attr="disabled">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M5 12l5 5l10 -10"/></svg>
                    Ajukan Review
                </button>
            </div>
        </div>

        @if($showSubmitModal)
            <div class="modal modal-blur fade show" style="display: block;" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Konfirmasi Ajukan Review</h5>
                            <button type="button" class="btn-close" wire:click="cancelSubmit"></button>
                        </div>
                        <div class="modal-body">
                            <p>Apakah Anda yakin ingin mengajukan RPS ini untuk review?</p>
                            <p class="text-secondary small">
                                RPS akan berubah status menjadi <strong>Dalam Review</strong> dan tidak dapat diedit hingga reviewer memberikan keputusan.
                            </p>
                            @if(!empty($validationResult) && !$validationResult['pass'])
                                <div class="alert alert-warning mt-2 mb-0">
                                    Masih terdapat error validasi. Lanjutkan?
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button wire:click="cancelSubmit" class="btn btn-ghost-secondary">Batal</button>
                            <button wire:click="ajukanReview" class="btn btn-primary">Ya, Ajukan Review</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>
        @endif
    @endif
</div>

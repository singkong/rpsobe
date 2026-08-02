<?php

use function Livewire\Volt\{state, mount, rules, computed};
use App\Enums\RPSStatus;
use App\Models\RPS;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

state('rpsId');
state('rps');

state('skorPerKomponen', [
    'cpl_cpmk' => null,
    'sub_cpmk' => null,
    'materi' => null,
    'metode' => null,
    'assessment' => null,
    'referensi' => null,
    'alignment' => null,
]);

state('komentar', [
    'cpl_cpmk' => '',
    'sub_cpmk' => '',
    'materi' => '',
    'metode' => '',
    'assessment' => '',
    'referensi' => '',
    'alignment' => '',
]);

state('catatan', '');
state('showConfirmApprove', false);
state('showConfirmRevision', false);
state('confirmAction', '');

rules([
    'skorPerKomponen.cpl_cpmk' => ['required', 'integer', 'min:1', 'max:10'],
    'skorPerKomponen.sub_cpmk' => ['required', 'integer', 'min:1', 'max:10'],
    'skorPerKomponen.materi' => ['required', 'integer', 'min:1', 'max:10'],
    'skorPerKomponen.metode' => ['required', 'integer', 'min:1', 'max:10'],
    'skorPerKomponen.assessment' => ['required', 'integer', 'min:1', 'max:10'],
    'skorPerKomponen.referensi' => ['required', 'integer', 'min:1', 'max:10'],
    'skorPerKomponen.alignment' => ['required', 'integer', 'min:1', 'max:10'],
]);

mount(function ($rpsId) {
    $this->rpsId = $rpsId;
    $this->rps = RPS::with([
        'mataKuliah.kurikulum.programStudi',
        'semester',
        'user',
        'cpl',
        'cpml.cpl',
        'cpml.subCpmk',
        'materiPertemuan.subCpmk',
        'assessment.subCpmk',
    ])->findOrFail($rpsId);
});

$skorTotal = computed(function () {
    $total = 0;
    foreach ($this->skorPerKomponen as $skor) {
        $total += (int) ($skor ?? 0);
    }
    return $total;
});

$skorMax = computed(function () {
    return count($this->skorPerKomponen) * 10;
});

$komponenLabels = computed(function () {
    return [
        'cpl_cpmk' => 'CPL & CPMK',
        'sub_cpmk' => 'Sub-CPMK',
        'materi' => 'Materi',
        'metode' => 'Metode Pembelajaran',
        'assessment' => 'Assessment',
        'referensi' => 'Referensi',
        'alignment' => 'Alignment',
    ];
});

$confirmApprove = function () {
    $this->validate();
    $this->confirmAction = 'approve';
    $this->showConfirmApprove = true;
};

$confirmRevision = function () {
    if (empty(trim($this->catatan))) {
        session()->flash('error', 'Catatan alasan revisi wajib diisi.');
        return;
    }
    $this->validate();
    $this->confirmAction = 'revision';
    $this->showConfirmRevision = true;
};

$executeApprove = function () {
    $user = Auth::user();
    $service = app(WorkflowService::class);

    $reviewData = [
        'rps_id' => $this->rps->id,
        'skor_total' => $this->skorTotal,
        'skor_per_komponen' => $this->skorPerKomponen,
        'komentar' => array_filter($this->komentar),
        'status' => 'approved',
        'catatan' => $this->catatan ?: 'Disetujui',
    ];

    $service->review($this->rps, $user, $reviewData);

    $this->showConfirmApprove = false;
    session()->flash('message', 'RPS berhasil disetujui.');
    $this->redirect(route('review.list'), navigate: true);
};

$executeRevision = function () {
    $user = Auth::user();
    $service = app(WorkflowService::class);

    $reviewData = [
        'rps_id' => $this->rps->id,
        'skor_total' => $this->skorTotal,
        'skor_per_komponen' => $this->skorPerKomponen,
        'komentar' => array_filter($this->komentar),
        'catatan' => $this->catatan,
    ];

    $service->requestRevision($this->rps, $user, $reviewData);

    $this->showConfirmRevision = false;
    session()->flash('message', 'Revisi diminta. Dosen akan diberi notifikasi.');
    $this->redirect(route('review.list'), navigate: true);
};

$cancelConfirm = function () {
    $this->showConfirmApprove = false;
    $this->showConfirmRevision = false;
};

?>

<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Review RPS</h2>
                    <div class="text-secondary mt-1">
                        <span class="badge bg-<?= $rps->status->color() ?> me-2"><?= $rps->status->label() ?></span>
                        <?= $rps->mataKuliah->code ?> - <?= $rps->mataKuliah->name ?>
                    </div>
                </div>
                <div class="col-auto ms-auto">
                    <a href="<?= route('review.list') ?>" class="btn btn-ghost-secondary">
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
                                                <dd class="col-sm-8"><?= $rps->mataKuliah->code ?> - <?= $rps->mataKuliah->name ?> (<?= $rps->mataKuliah->sks ?> SKS)</dd>
                                                <dt class="col-sm-4">Program Studi</dt>
                                                <dd class="col-sm-8"><?= $rps->mataKuliah->kurikulum->programStudi->name ?? '-' ?></dd>
                                                <dt class="col-sm-4">Semester</dt>
                                                <dd class="col-sm-8"><?= $rps->semester->name ?? '-' ?></dd>
                                                <dt class="col-sm-4">Dosen</dt>
                                                <dd class="col-sm-8"><?= $rps->user->name ?? '-' ?></dd>
                                                <dt class="col-sm-4">Versi</dt>
                                                <dd class="col-sm-8"><span class="badge bg-primary-lt"><?= $rps->version_label ?></span></dd>
                                            </dl>
                                            <?php if($rps->deskripsi)
                                            <div class="mt-3">
                                                <strong>Deskripsi:</strong>
                                                <p class="text-secondary mt-1"><?= $rps->deskripsi ?></p>
                                            </div>
                                            <?php endif; ?>
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
                                            <?php foreach($rps->cpl as $cpl)
                                            <div class="mb-2"><span class="badge bg-blue-lt me-2"><?= $cpl->code ?></span> <?= $cpl->deskripsi ?></div>
                                            <?php endforeach; ?>
                                            <hr>
                                            <h5 class="mb-2">Capaian Pembelajaran Mata Kuliah (CPMK)</h5>
                                            <?php foreach($rps->cpml as $cpml)
                                            <div class="mb-2 p-2 border rounded">
                                                <strong><?= $cpml->code ?></strong>: <?= $cpml->deskripsi ?>
                                                <?php if($cpml->cpl->isNotEmpty())
                                                <div class="mt-1">
                                                    <small class="text-secondary">Terkait CPL: <?= $cpml->cpl->pluck('code')->join(', ') ?></small>
                                                </div>
                                                <?php endif; ?>
                                                <?php if($cpml->subCpmk->isNotEmpty())
                                                <div class="mt-2">
                                                    <small class="text-secondary">Sub-CPMK:</small>
                                                    <ul class="mb-0 small">
                                                        <?php foreach($cpml->subCpmk as $sub)
                                                        <li><?= $sub->code ?>: <?= $sub->deskripsi ?></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            <?php endforeach; ?>
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
                                                        <?php foreach($rps->materiPertemuan as $materi)
                                                        <tr>
                                                            <td><?= $materi->pertemuan_ke ?></td>
                                                            <td><small><?= $materi->subCpmk->code ?? '-' ?></small></td>
                                                            <td><?= Str::limit($materi->materi, 60) ?></td>
                                                            <td>
                                                                <?php if($materi->metode_pembelajaran)
                                                                    <?php foreach($materi->metode_pembelajaran as $m)
                                                                        <span class="badge bg-azure-lt me-1"><?= $m ?></span>
                                                                    <?php endforeach; ?>
                                                                <?php else: ?>
                                                                    -
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
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
                                                        <?php foreach($rps->assessment as $as)
                                                        <tr>
                                                            <td><?= $as->nama ?></td>
                                                            <td><span class="badge bg-purple-lt"><?= $as->jenis->value ?></span></td>
                                                            <td><?= $as->bobot_persen ?>%</td>
                                                            <td>
                                                                <?php foreach($as->subCpmk as $sub)
                                                                    <span class="badge bg-green-lt me-1"><?= $sub->code ?></span>
                                                                <?php endforeach; ?>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
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

                                <?php foreach($this->komponenLabels as $key => $label)
                                <div class="mb-3">
                                    <label class="form-label"><?= $label ?></label>
                                    <div class="row g-2">
                                        <div class="col-7">
                                            <input type="range" class="form-range" min="1" max="10"
                                                wire:model.live="skorPerKomponen.<?= $key ?>"
                                                value="<?= $skorPerKomponen[$key] ?? 5 ?>">
                                        </div>
                                        <div class="col-5">
                                            <input type="number" class="form-control form-control-sm"
                                                wire:model.live="skorPerKomponen.<?= $key ?>"
                                                min="1" max="10"
                                                value="<?= $skorPerKomponen[$key] ?? '' ?>"
                                                placeholder="1-10">
                                        </div>
                                    </div>
                                    @error('skorPerKomponen.' . $key)
                                        <small class="text-danger"><?= $message ?></small>
                                    @enderror
                                    <textarea class="form-control form-control-sm mt-1"
                                        wire:model="komentar.<?= $key ?>"
                                        rows="2"
                                        placeholder="Komentar <?= $label ?>..."></textarea>
                                </div>
                                <?php endforeach; ?>

                                <div class="card bg-light mb-3">
                                    <div class="card-body text-center">
                                        <div class="h3 mb-0">
                                            Total: <span class="text-<?= $this->skorTotal >= ($this->skorMax * 0.7) ? 'success' : 'danger' ?>">
                                                <?= $this->skorTotal ?> / <?= $this->skorMax ?>
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

    <?php if($showConfirmApprove)
        <div class="modal modal-blur fade show" tabindex="-1" style="display:block" role="dialog">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-status bg-success"></div>
                    <div class="modal-body text-center py-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-success mb-2"><path d="M5 12l5 5l10 -10"/></svg>
                        <h3>Konfirmasi Persetujuan</h3>
                        <p class="text-secondary">Apakah Anda yakin ingin menyetujui RPS ini?</p>
                        <p class="small text-secondary">Total Skor: <?= $this->skorTotal ?> / <?= $this->skorMax ?></p>
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
    <?php endif; ?>

    <?php if($showConfirmRevision)
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
    <?php endif; ?>

    <?php if(session()->has('error'))
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
            <div class="toast show bg-danger text-white" role="alert">
                <div class="toast-header"><strong class="me-auto">Error</strong><button type="button" class="btn-close" data-bs-dismiss="toast"></button></div>
                <div class="toast-body"><?= session('error') ?></div>
            </div>
        </div>
    <?php endif; ?>
</div>

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

return view('livewire.rps.workflow.review-form');
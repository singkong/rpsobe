<?php

use function Livewire\Volt\{state, mount, computed};
use App\Enums\RPSStatus;
use App\Models\RPS;
use App\Models\Dosen;
use App\Services\RPSValidationService;

state('rpsId', null);
state('rps', null);
state('rpsData', []);
state('validationResult', null);
state('validating', false);
state('showSubmitModal', false);

$totalCpmk = fn () => count($this->rpsData['cpml'] ?? []);
$totalSubCpmk = fn () => count($this->rpsData['sub_cpmk_flat'] ?? []);
$totalPertemuan = fn () => count($this->rpsData['pertemuan'] ?? []);
$totalAssessment = fn () => count($this->rpsData['assessments'] ?? []);
$totalBobot = fn () => round($this->rpsData['total_bobot'] ?? 0, 2);
$bobotOk = fn () => abs($this->totalBobot() - 100) < 0.01;
$cplCoverage = fn () => $this->rpsData['cpl_coverage'] ?? '0/0';

$totalReferensi = fn () => count($this->rpsData['all_referensi'] ?? []);

$getDosenPengampu = function () {
    $ids = $this->rpsData['dosen_pengampu'] ?? [];
    if (empty($ids)) return '-';
    return Dosen::whereIn('id', $ids)->pluck('nama')->implode(', ');
};

mount(function ($rpsId) {
    $this->rpsId = $rpsId;

    if ($this->rpsId) {
        $this->rps = RPS::with([
            'mataKuliah.kurikulum.programStudi',
            'semester',
            'cpl',
            'cpml.cpl',
            'cpml.subCpmk',
            'materiPertemuan.subCpmk.cpmk',
            'assessment.subCpmk.cpmk',
        ])->findOrFail($this->rpsId);

        $allSubCpmkFlat = [];
        foreach ($this->rps->cpml as $cpml) {
            foreach ($cpml->subCpmk as $sub) {
                $cpmlCodes = $cpml->cpl->pluck('code')->implode(', ');
                $allSubCpmkFlat[] = [
                    'id' => $sub->id,
                    'code' => $sub->code,
                    'deskripsi' => $sub->deskripsi,
                    'cpmk_code' => $cpml->code,
                    'cpl_codes' => $cpmlCodes,
                ];
            }
        }

        $allReferensi = [];
        foreach ($this->rps->materiPertemuan as $m) {
            if (!empty($m->referensi_ids)) {
                foreach ($m->referensi_ids as $rid) {
                    $allReferensi[$rid] = $rid;
                }
            }
        }
        $allReferensi = array_values($allReferensi);

        $pertemuanData = $this->rps->materiPertemuan
            ->sortBy('pertemuan_ke')
            ->map(function ($m) {
                return [
                    'pertemuan_ke' => $m->pertemuan_ke,
                    'sub_cpmk_code' => $m->subCpmk?->code ?? '-',
                    'materi' => $m->materi,
                    'metode' => $m->metode_pembelajaran ?? [],
                ];
            })
            ->values()
            ->toArray();

        $assessmentData = $this->rps->assessment->map(function ($a) {
            return [
                'id' => $a->id,
                'nama' => $a->nama,
                'jenis' => $a->jenis->value,
                'jenis_label' => $a->jenis->label(),
                'bobot' => $a->bobot_persen,
                'sub_cpmk_codes' => $a->subCpmk->pluck('code')->implode(', '),
            ];
        })->toArray();

        $totalBobot = round($this->rps->assessment->sum('bobot_persen'), 2);

        $cplWithCpmk = collect();
        foreach ($this->rps->cpml as $cpml) {
            foreach ($cpml->cpl as $cpl) {
                $cplWithCpmk->push($cpl->id);
            }
        }
        $cplCovered = $cplWithCpmk->unique()->count();
        $totalCpl = $this->rps->cpl->count();

        $this->rpsData = [
            'id' => $this->rps->id,
            'mata_kuliah_name' => $this->rps->mataKuliah?->name ?? '-',
            'mata_kuliah_code' => $this->rps->mataKuliah?->code ?? '-',
            'sks' => $this->rps->mataKuliah?->sks ?? '-',
            'semester_name' => $this->rps->semester?->name ?? '-',
            'program_studi_name' => $this->rps->mataKuliah?->kurikulum?->programStudi?->name ?? '-',
            'deskripsi' => $this->rps->deskripsi ?? '',
            'dosen_pengampu' => $this->rps->dosen_pengampu_json ?? [],
            'version_label' => $this->rps->version_label,
            'cpl' => $this->rps->cpl->toArray(),
            'cpml' => $this->rps->cpml->map(function ($cpml) {
                return [
                    'id' => $cpml->id,
                    'code' => $cpml->code,
                    'deskripsi' => $cpml->deskripsi,
                    'cpl_codes' => $cpml->cpl->pluck('code')->implode(', '),
                ];
            })->toArray(),
            'sub_cpmk_flat' => $allSubCpmkFlat,
            'pertemuan' => $pertemuanData,
            'assessments' => $assessmentData,
            'total_bobot' => $totalBobot,
            'cpl_coverage' => "{$cplCovered}/{$totalCpl}",
            'all_referensi' => $allReferensi,
        ];
    }
});

$validasi = function () {
    $this->validating = true;
    $this->validationResult = null;

    $validator = app(RPSValidationService::class);
    $this->validationResult = $validator->validateAll($this->rps);
    $this->validating = false;
};

$simpanDraft = function () {
    $this->rps->save();
    session()->flash('message', 'Draft RPS berhasil disimpan.');
};

$confirmSubmit = function () {
    $this->showSubmitModal = true;
};

$cancelSubmit = function () {
    $this->showSubmitModal = false;
};

$ajukanReview = function () {
    $this->rps->update(['status' => RPSStatus::Review]);
    $this->showSubmitModal = false;
    session()->flash('message', 'RPS berhasil diajukan untuk review.');
    $this->dispatch('rps-submitted');
};

return view('livewire.rps.builder.step8-review');
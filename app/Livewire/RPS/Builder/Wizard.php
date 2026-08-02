<?php

use function Livewire\Volt\{state, mount, computed};
use App\Enums\RPSStatus;
use App\Models\RPS;
use App\Models\MataKuliah;
use App\Models\Kurikulum;
use App\Models\Dosen;
use App\Models\Semester;
use App\Models\ProgramStudi;
use App\Models\CPL;
use App\Models\CPMK;
use App\Models\SubCPMK;
use App\Services\RPSService;
use Illuminate\Support\Facades\Auth;

state('rps', null);
state('currentStep', 1);
state('isCreating', true);
state('rpsId', null);
state('completionPercentage', 0);

mount(function ($rpsId = null) {
    $this->rpsId = $rpsId;

    if ($rpsId) {
        $rps = RPS::with([
            'mataKuliah.kurikulum.programStudi',
            'semester',
            'cpl',
            'cpml.cpl',
            'cpml.subCpmk',
            'materiPertemuan',
            'assessment',
        ])->findOrFail($rpsId);

        if (!$rps->isEditable() && $rps->user_id !== Auth::id()) {
            abort(403, 'RPS tidak dapat diedit.');
        }

        $this->rps = $rps;
        $this->isCreating = false;

        if ($rps->status !== RPSStatus::Draft && $rps->status !== RPSStatus::Revision) {
            $this->currentStep = 8;
        }
    } else {
        $this->rps = new RPS([
            'status' => RPSStatus::Draft,
            'version_label' => 'v0.1',
        ]);
        $this->isCreating = true;
    }

    $this->calculateCompletion();
});

$nextStep = function () {
    $errors = $this->validateCurrentStep();

    if (!empty($errors)) {
        session()->flash('step_errors', $errors);
        return;
    }

    if ($this->currentStep < 8) {
        $this->currentStep++;
    }
};

$prevStep = function () {
    if ($this->currentStep > 1) {
        $this->currentStep--;
    }
};

$goToStep = function ($step) {
    if ($step < $this->currentStep || $step === $this->currentStep) {
        $this->currentStep = $step;
    }
};

$validateCurrentStep = function () {
    $service = app(RPSService::class);

    if ($this->rps && $this->rps->exists) {
        return $service->validateStep($this->currentStep, $this->rps);
    }

    return [];
};

$saveDraft = function () {
    if ($this->rps && $this->rps->exists) {
        $this->rps->save();
        session()->flash('message', 'Draft berhasil disimpan.');
    }
};

$calculateCompletion = function () {
    if ($this->rps && $this->rps->exists) {
        $service = app(RPSService::class);
        $progress = $service->getWizardProgress($this->rps);
        $completed = count(array_filter($progress, fn ($v) => $v === 100));
        $this->completionPercentage = (int) round(($completed / 8) * 100);
    } else {
        $this->completionPercentage = 0;
    }
};

$getStepLabel = function ($step) {
    return match ((int) $step) {
        1 => 'Info MK',
        2 => 'Pilih CPL',
        3 => 'CPMK',
        4 => 'Sub-CPMK',
        5 => 'Materi',
        6 => 'Metode',
        7 => 'Assessment',
        8 => 'Review',
        default => '',
    };
};

$getStepStatus = function ($step) {
    if ((int) $step < $this->currentStep) {
        return 'completed';
    }
    if ((int) $step === $this->currentStep) {
        return 'current';
    }
    return 'pending';
};

return view('livewire.rps.builder.wizard');

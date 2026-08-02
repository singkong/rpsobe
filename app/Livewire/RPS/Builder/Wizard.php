<?php

namespace App\Livewire\RPS\Builder;

use Livewire\Component;
use App\Enums\RPSStatus;
use App\Models\RPS;
use App\Services\RPSService;
use Illuminate\Support\Facades\Auth;

class Wizard extends Component
{
    public $rps = null;
    public int $currentStep = 1;
    public bool $isCreating = true;
    public $rpsId = null;
    public int $completionPercentage = 0;

    public function mount($rpsId = null): void
    {
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
    }

    public function nextStep(): void
    {
        $errors = $this->validateCurrentStep();

        if (!empty($errors)) {
            session()->flash('step_errors', $errors);
            return;
        }

        if ($this->currentStep < 8) {
            $this->currentStep++;
        }
    }

    public function prevStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep(int $step): void
    {
        if ($step < $this->currentStep || $step === $this->currentStep) {
            $this->currentStep = $step;
        }
    }

    public function validateCurrentStep(): array
    {
        $service = app(RPSService::class);

        if ($this->rps && $this->rps->exists) {
            return $service->validateStep($this->currentStep, $this->rps);
        }

        return [];
    }

    public function saveDraft(): void
    {
        if ($this->rps && $this->rps->exists) {
            $this->rps->save();
            session()->flash('message', 'Draft berhasil disimpan.');
        }
    }

    public function calculateCompletion(): void
    {
        if ($this->rps && $this->rps->exists) {
            $service = app(RPSService::class);
            $progress = $service->getWizardProgress($this->rps);
            $completed = count(array_filter($progress, fn($v) => $v === 100));
            $this->completionPercentage = (int) round(($completed / 8) * 100);
        } else {
            $this->completionPercentage = 0;
        }
    }

    public function getStepLabel(int $step): string
    {
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
    }

    public function getStepStatus(int $step): string
    {
        if ((int) $step < $this->currentStep) {
            return 'completed';
        }
        if ((int) $step === $this->currentStep) {
            return 'current';
        }
        return 'pending';
    }

    public function render()
    {
        return view('livewire.rps.builder.wizard');
    }
}

<?php

namespace App\Livewire\RPS\Workflow;

use Livewire\Component;
use App\Models\RPS;
use App\Models\User;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Auth;

class ReviewerAssignment extends Component
{
    public $rpsId;
    public $rps;
    public string $selectedReviewerId = '';
    public $availableReviewers = [];
    public $currentReviewer = null;

    public function mount($rpsId): void
    {
        $this->rpsId = $rpsId;
        $this->rps = RPS::with(['mataKuliah.kurikulum.programStudi'])->findOrFail($rpsId);

        $prodi = $this->rps->mataKuliah?->kurikulum?->programStudi;
        if ($prodi) {
            $service = app(WorkflowService::class);
            $this->availableReviewers = $service->getAvailableReviewers($prodi);
        } else {
            $this->availableReviewers = User::role('reviewer')->where('is_active', true)->get();
        }

        $this->currentReviewer = User::role('reviewer')->first();
    }

    public function assign(): void
    {
        if (empty($this->selectedReviewerId)) {
            session()->flash('error', 'Pilih reviewer terlebih dahulu.');
            return;
        }

        $user = Auth::user();
        $reviewer = User::findOrFail($this->selectedReviewerId);
        $service = app(WorkflowService::class);
        $service->assignReviewer($this->rps, $reviewer, $user);

        $this->currentReviewer = $reviewer;
        session()->flash('message', 'Reviewer berhasil ditugaskan.');
    }

    public function render()
    {
        return view('livewire.rps.workflow.reviewer-assignment');
    }
}

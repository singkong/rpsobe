<?php

use function Livewire\Volt\{state, mount};
use App\Models\RPS;
use App\Models\User;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Auth;

state('rpsId');
state('rps');
state('selectedReviewerId', '');
state('availableReviewers', []);
state('currentReviewer', null);

mount(function ($rpsId) {
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
});

$assign = function () {
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
};

return view('livewire.rps.workflow.reviewer-assignment');
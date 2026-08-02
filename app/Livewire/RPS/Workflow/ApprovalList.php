<?php

use function Livewire\Volt\{state, mount};
use App\Enums\RPSStatus;
use App\Models\RPS;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Auth;

state('search', '');
state('filterStatus', '');
state('sortField', 'updated_at');
state('sortDirection', 'desc');
state('perPage', 10);
state('showConfirmPublish', null);
state('showConfirmApprove', null);

mount(function () {
    //
});

$approvalList = function () {
    $user = Auth::user();

    $query = RPS::with(['mataKuliah.kurikulum.programStudi', 'semester', 'user', 'reviews.reviewer'])
        ->whereIn('status', [RPSStatus::Review->value, RPSStatus::Approved->value]);

    if ($user->tenant_id && !$user->hasRole('super-admin')) {
        $query->whereHas('mataKuliah.kurikulum.programStudi.fakultas', function ($q) use ($user) {
            $q->where('tenant_id', $user->tenant_id);
        });
    }

    if ($this->search) {
        $query->where(function ($q) {
            $q->whereHas('mataKuliah', function ($q2) {
                $q2->where('name', 'like', '%' . $this->search . '%')
                   ->orWhere('code', 'like', '%' . $this->search . '%');
            })->orWhereHas('user', function ($q2) {
                $q2->where('name', 'like', '%' . $this->search . '%');
            });
        });
    }

    if ($this->filterStatus) {
        $query->where('status', $this->filterStatus);
    }

    $query->orderBy($this->sortField, $this->sortDirection);

    return $query->paginate($this->perPage);
};

$sortBy = function ($field) {
    if ($this->sortField === $field) {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        $this->sortField = $field;
        $this->sortDirection = 'asc';
    }
};

$approve = function ($rpsId) {
    $user = Auth::user();
    $rps = RPS::findOrFail($rpsId);

    $service = app(WorkflowService::class);
    $service->approve($rps, $user);

    session()->flash('message', 'RPS berhasil disetujui.');
};

$publish = function ($rpsId) {
    $user = Auth::user();
    $rps = RPS::findOrFail($rpsId);

    $service = app(WorkflowService::class);
    $service->publish($rps, $user);

    $this->showConfirmPublish = null;
    session()->flash('message', 'RPS berhasil dipublikasi.');
};

$requestRevision = function ($rpsId) {
    return redirect()->route('rps.review', ['rpsId' => $rpsId]);
};

$confirmPublish = function ($rpsId) {
    $this->showConfirmPublish = $rpsId;
};

$confirmApprove = function ($rpsId) {
    $this->showConfirmApprove = $rpsId;
};

$cancelConfirm = function () {
    $this->showConfirmPublish = null;
    $this->showConfirmApprove = null;
};

$archive = function ($rpsId) {
    $user = Auth::user();
    $rps = RPS::findOrFail($rpsId);

    $service = app(WorkflowService::class);
    $service->archive($rps, $user);

    session()->flash('message', 'RPS berhasil diarsipkan.');
};

return view('livewire.rps.workflow.approval-list');

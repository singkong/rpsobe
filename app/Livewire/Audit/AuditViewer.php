<?php

use function Livewire\Volt\{state, mount, withPagination};
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

withPagination();

state('search', '');
state('filterAction', '');
state('filterModel', '');
state('filterUser', '');
state('filterDateFrom', '');
state('filterDateTo', '');
state('sortField', 'created_at');
state('sortDirection', 'desc');
state('perPage', 15);
state('showDetail', false);
state('selectedAudit', null);

mount(function () {
    //
});

$auditList = function () {
    $query = AuditLog::with('user')->latest();

    if ($this->search) {
        $query->where(function ($q) {
            $q->whereHas('user', function ($q2) {
                $q2->where('name', 'like', '%' . $this->search . '%');
            })
            ->orWhere('action', 'like', '%' . $this->search . '%')
            ->orWhere('model_type', 'like', '%' . $this->search . '%')
            ->orWhere('ip_address', 'like', '%' . $this->search . '%');
        });
    }

    if ($this->filterAction) {
        $query->where('action', $this->filterAction);
    }

    if ($this->filterModel) {
        $query->where('model_type', 'like', '%' . $this->filterModel . '%');
    }

    if ($this->filterDateFrom) {
        $query->whereDate('created_at', '>=', $this->filterDateFrom);
    }

    if ($this->filterDateTo) {
        $query->whereDate('created_at', '<=', $this->filterDateTo);
    }

    if ($this->filterUser) {
        $query->whereHas('user', function ($q) {
            $q->where('name', 'like', '%' . $this->filterUser . '%');
        });
    }

    $query->orderBy($this->sortField, $this->sortDirection);

    return $query->paginate($this->perPage);
};

$actionOptions = function () {
    return [
        '' => 'Semua Aksi',
        'RPSSubmitted' => 'RPS Diajukan',
        'RPSReviewed' => 'RPS Direview',
        'RPSRevisionRequested' => 'Revisi Diminta',
        'RPSApproved' => 'RPS Disetujui',
        'RPSPublished' => 'RPS Dipublikasi',
        'RPSArchived' => 'RPS Diarsipkan',
        'ReviewerAssigned' => 'Reviewer Ditugaskan',
    ];
};

$viewDetail = function ($id) {
    $this->selectedAudit = AuditLog::with('user')->find($id);
    $this->showDetail = true;
};

$closeDetail = function () {
    $this->showDetail = false;
    $this->selectedAudit = null;
};

$sortBy = function ($field) {
    if ($this->sortField === $field) {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        $this->sortField = $field;
        $this->sortDirection = 'asc';
    }
};

$resetFilters = function () {
    $this->search = '';
    $this->filterAction = '';
    $this->filterModel = '';
    $this->filterUser = '';
    $this->filterDateFrom = '';
    $this->filterDateTo = '';
};

return view('livewire.audit.audit-viewer');

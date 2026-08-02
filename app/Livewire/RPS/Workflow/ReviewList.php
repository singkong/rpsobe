<?php

use function Livewire\Volt\{state, mount};
use App\Enums\RPSStatus;
use App\Models\RPS;
use Illuminate\Support\Facades\Auth;

state('search', '');
state('filterStatus', '');
state('sortField', 'updated_at');
state('sortDirection', 'desc');
state('perPage', 10);

mount(function () {
    //
});

$reviewList = function () {
    $user = Auth::user();

    $query = RPS::with(['mataKuliah.kurikulum.programStudi', 'semester', 'user', 'reviews'])
        ->whereIn('status', [RPSStatus::Review->value, RPSStatus::Revision->value]);

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

return view('livewire.rps.workflow.review-list');

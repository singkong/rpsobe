<?php

namespace App\Livewire\RPS\Workflow;

use Livewire\Component;
use Livewire\WithPagination;
use App\Enums\RPSStatus;
use App\Models\RPS;
use Illuminate\Support\Facades\Auth;

class ReviewList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $sortField = 'updated_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    public function mount(): void
    {
        //
    }

    public function reviewList()
    {
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
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        return view('livewire.rps.workflow.review-list');
    }
}

<?php

namespace App\Livewire\RPS\Workflow;

use Livewire\Component;
use Livewire\WithPagination;
use App\Enums\RPSStatus;
use App\Models\RPS;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Auth;

class ApprovalList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $sortField = 'updated_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;
    public $showConfirmPublish = null;
    public $showConfirmApprove = null;

    public function mount(): void
    {
        //
    }

    public function approvalList()
    {
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

    public function approve(int $rpsId): void
    {
        $user = Auth::user();
        $rps = RPS::findOrFail($rpsId);
        $service = app(WorkflowService::class);
        $service->approve($rps, $user);
        session()->flash('message', 'RPS berhasil disetujui.');
    }

    public function publish(int $rpsId): void
    {
        $user = Auth::user();
        $rps = RPS::findOrFail($rpsId);
        $service = app(WorkflowService::class);
        $service->publish($rps, $user);
        $this->showConfirmPublish = null;
        session()->flash('message', 'RPS berhasil dipublikasi.');
    }

    public function requestRevision(int $rpsId)
    {
        return redirect()->route('rps.review', ['rpsId' => $rpsId]);
    }

    public function confirmPublish(int $rpsId): void
    {
        $this->showConfirmPublish = $rpsId;
    }

    public function confirmApprove(int $rpsId): void
    {
        $this->showConfirmApprove = $rpsId;
    }

    public function cancelConfirm(): void
    {
        $this->showConfirmPublish = null;
        $this->showConfirmApprove = null;
    }

    public function archive(int $rpsId): void
    {
        $user = Auth::user();
        $rps = RPS::findOrFail($rpsId);
        $service = app(WorkflowService::class);
        $service->archive($rps, $user);
        session()->flash('message', 'RPS berhasil diarsipkan.');
    }

    public function render()
    {
        return view('livewire.rps.workflow.approval-list');
    }
}

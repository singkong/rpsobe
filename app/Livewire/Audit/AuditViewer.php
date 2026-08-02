<?php

namespace App\Livewire\Audit;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditViewer extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterAction = '';
    public string $filterModel = '';
    public string $filterUser = '';
    public string $filterDateFrom = '';
    public string $filterDateTo = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 15;
    public bool $showDetail = false;
    public $selectedAudit = null;

    public function mount(): void
    {
        //
    }

    public function auditList()
    {
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
    }

    public function actionOptions(): array
    {
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
    }

    public function viewDetail(int $id): void
    {
        $this->selectedAudit = AuditLog::with('user')->find($id);
        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->selectedAudit = null;
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

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterAction = '';
        $this->filterModel = '';
        $this->filterUser = '';
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
    }

    public function render()
    {
        return view('livewire.audit.audit-viewer');
    }
}

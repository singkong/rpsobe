<?php

namespace App\Livewire\RPS\Builder;

use Livewire\Component;
use Livewire\WithPagination;
use App\Enums\RPSStatus;
use App\Models\RPS;
use App\Models\MataKuliah;
use App\Models\Kurikulum;
use App\Models\Semester;
use App\Services\RPSService;
use Illuminate\Support\Facades\Auth;

class RPSIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $filterSemester = '';
    public string $filterMk = '';
    public string $sortField = 'updated_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    public $semesterList = [];
    public $mataKuliahList = [];
    public $showDeleteConfirm = null;

    public function mount(): void
    {
        $user = Auth::user();

        $this->semesterList = Semester::where('is_active', true)->get();

        $this->mataKuliahList = MataKuliah::when($user->tenant_id && !$user->hasRole('super-admin'), function ($q) use ($user) {
            $q->whereHas('kurikulum.programStudi.fakultas', function ($q2) use ($user) {
                $q2->where('tenant_id', $user->tenant_id);
            });
        })->get();
    }

    public function rpsList()
    {
        $user = Auth::user();

        $query = RPS::with(['mataKuliah', 'semester', 'user']);

        if (!$user->hasRole(['super-admin', 'admin-prodi', 'kaprodi'])) {
            $query->where('user_id', $user->id);
        }

        if ($user->tenant_id && !$user->hasRole('super-admin')) {
            $query->orWhereHas('mataKuliah.kurikulum.programStudi.fakultas', function ($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            });
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('deskripsi', 'like', '%' . $this->search . '%')
                  ->orWhereHas('mataKuliah', function ($q2) {
                      $q2->where('name', 'like', '%' . $this->search . '%')
                         ->orWhere('code', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterSemester) {
            $query->where('semester_id', $this->filterSemester);
        }

        if ($this->filterMk) {
            $query->where('mata_kuliah_id', $this->filterMk);
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

    public function getProgress(int $rpsId): int
    {
        $rps = RPS::find($rpsId);
        if (!$rps) return 0;
        $service = app(RPSService::class);
        $progress = $service->getWizardProgress($rps);
        $completed = count(array_filter($progress, fn($v) => $v === 100));
        return (int) round(($completed / 8) * 100);
    }

    public function confirmDelete(int $id): void
    {
        $this->showDeleteConfirm = $id;
    }

    public function delete(): void
    {
        $rps = RPS::findOrFail($this->showDeleteConfirm);
        $rps->delete();
        session()->flash('message', 'RPS berhasil dihapus.');
        $this->showDeleteConfirm = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = null;
    }

    public function duplicate(int $id)
    {
        $original = RPS::with(['cpl', 'cpml.subCpmk', 'materiPertemuan', 'assessment.subCpmk'])->findOrFail($id);

        $newRps = $original->replicate();
        $newRps->status = RPSStatus::Draft;
        $newRps->version_label = 'v0.1';
        $newRps->save();

        foreach ($original->cpl as $cpl) {
            $newRps->cpl()->attach($cpl->id);
        }

        foreach ($original->cpml as $cpml) {
            $newCpml = $cpml->replicate();
            $newCpml->rps_id = $newRps->id;
            $newCpml->save();

            foreach ($cpml->cpl as $cpl) {
                $newCpml->cpl()->attach($cpl->id);
            }

            foreach ($cpml->subCpmk as $sub) {
                $newSub = $sub->replicate();
                $newSub->cpml_id = $newCpml->id;
                $newSub->save();
            }
        }

        foreach ($original->materiPertemuan as $materi) {
            $newMateri = $materi->replicate();
            $newMateri->rps_id = $newRps->id;
            $newMateri->save();
        }

        foreach ($original->assessment as $assessment) {
            $newAssessment = $assessment->replicate();
            $newAssessment->rps_id = $newRps->id;
            $newAssessment->save();

            foreach ($assessment->subCpmk as $sub) {
                $newAssessment->subCpmk()->attach($sub->id);
            }
        }

        session()->flash('message', 'RPS berhasil diduplikasi.');

        return redirect()->route('rps.edit', ['rpsId' => $newRps->id]);
    }

    public function render()
    {
        return view('livewire.rps.builder.rps-index');
    }
}

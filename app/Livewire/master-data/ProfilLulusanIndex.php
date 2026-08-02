<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProfilLulusan;
use App\Models\ProgramStudi;
use App\Models\CPL;
use Illuminate\Support\Facades\Gate;

class ProfilLulusanIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'name';
    public string $sortDirection = 'asc';
    public int $perPage = 10;

    public ?int $editId = null;
    public string $program_studi_id = '';
    public string $name = '';
    public string $deskripsi = '';
    public array $selectedCpls = [];
    public bool $showModal = false;
    public $showDeleteConfirm = null;

    protected function rules(): array
    {
        return [
            'program_studi_id' => ['required', 'exists:program_studi,id'],
            'name' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'selectedCpls' => ['nullable', 'array'],
        ];
    }

    public function mount(): void
    {
        if (!Gate::allows('profil-lulusan.view-any')) {
            abort(403);
        }
    }

    public function prodiOptions()
    {
        return ProgramStudi::orderBy('name')->get();
    }

    public function cplOptions()
    {
        return CPL::orderBy('code')->get();
    }

    public function profilList()
    {
        $query = ProfilLulusan::with(['programStudi', 'cpls']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $this->search . '%');
            });
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

    public function openCreate(): void
    {
        Gate::authorize('profil-lulusan.create');
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        Gate::authorize('profil-lulusan.update');
        $profil = ProfilLulusan::with('cpls')->findOrFail($id);
        $this->editId = $profil->id;
        $this->program_studi_id = $profil->program_studi_id;
        $this->name = $profil->name;
        $this->deskripsi = $profil->deskripsi ?? '';
        $this->selectedCpls = $profil->cpls->pluck('id')->toArray();
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->editId) {
            Gate::authorize('profil-lulusan.update');
        } else {
            Gate::authorize('profil-lulusan.create');
        }

        $validated = $this->validate();

        if ($this->editId) {
            $profil = ProfilLulusan::findOrFail($this->editId);
            $profil->update([
                'program_studi_id' => $validated['program_studi_id'],
                'name' => $validated['name'],
                'deskripsi' => $validated['deskripsi'],
            ]);
        } else {
            $profil = ProfilLulusan::create([
                'program_studi_id' => $validated['program_studi_id'],
                'name' => $validated['name'],
                'deskripsi' => $validated['deskripsi'],
            ]);
        }

        $profil->cpls()->sync($this->selectedCpls ?? []);

        session()->flash('message', $this->editId ? 'Profil Lulusan berhasil diperbarui.' : 'Profil Lulusan berhasil ditambahkan.');

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->showDeleteConfirm = $id;
    }

    public function delete(): void
    {
        Gate::authorize('profil-lulusan.delete');
        $profil = ProfilLulusan::findOrFail($this->showDeleteConfirm);
        $profil->cpls()->detach();
        $profil->delete();
        session()->flash('message', 'Profil Lulusan berhasil dihapus.');
        $this->showDeleteConfirm = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = null;
    }

    public function resetForm(): void
    {
        $this->editId = null;
        $this->program_studi_id = '';
        $this->name = '';
        $this->deskripsi = '';
        $this->selectedCpls = [];
        $this->resetValidation();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.master-data.profil-lulusan-index');
    }
}

<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProgramStudi;
use App\Models\Fakultas;
use App\Enums\Jenjang;
use Illuminate\Support\Facades\Gate;

class ProgramStudiIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'name';
    public string $sortDirection = 'asc';
    public int $perPage = 10;
    public string $filterFakultasId = '';

    public ?int $editId = null;
    public string $fakultas_id = '';
    public string $name = '';
    public string $code = '';
    public string $jenjang = '';
    public string $akreditasi = '';
    public string $kaprodi_name = '';
    public bool $showModal = false;
    public $showDeleteConfirm = null;

    protected function rules(): array
    {
        return [
            'fakultas_id' => ['required', 'exists:fakultas,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50'],
            'jenjang' => ['required', 'string', 'in:D3,D4,S1,S2,S3,Profesi,Spesialis'],
            'akreditasi' => ['nullable', 'string', 'max:50'],
            'kaprodi_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function mount(): void
    {
        
    }

    public function fakultasOptions()
    {
        return Fakultas::orderBy('name')->get();
    }

    public function jenjangOptions()
    {
        return collect(Jenjang::cases())->map(fn($j) => ['value' => $j->value, 'label' => $j->label()]);
    }

    public function prodiList()
    {
        $query = ProgramStudi::with('fakultas');

        if ($this->filterFakultasId) {
            $query->where('fakultas_id', $this->filterFakultasId);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        $query->orderBy($this->sortField === 'fakultas_name' ? 'fakultas_id' : $this->sortField, $this->sortDirection);

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
        Gate::authorize('program-studi.create');
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        Gate::authorize('program-studi.update');
        $prodi = ProgramStudi::findOrFail($id);
        $this->editId = $prodi->id;
        $this->fakultas_id = $prodi->fakultas_id;
        $this->name = $prodi->name;
        $this->code = $prodi->code;
        $this->jenjang = $prodi->jenjang instanceof Jenjang ? $prodi->jenjang->value : $prodi->jenjang;
        $this->akreditasi = $prodi->akreditasi ?? '';
        $this->kaprodi_name = $prodi->kaprodi_name ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->editId) {
            Gate::authorize('program-studi.update');
        } else {
            Gate::authorize('program-studi.create');
        }

        $validated = $this->validate();

        if ($this->editId) {
            $prodi = ProgramStudi::findOrFail($this->editId);
            $prodi->update($validated);
            session()->flash('message', 'Program Studi berhasil diperbarui.');
        } else {
            ProgramStudi::create($validated);
            session()->flash('message', 'Program Studi berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->showDeleteConfirm = $id;
    }

    public function delete(): void
    {
        Gate::authorize('program-studi.delete');
        $prodi = ProgramStudi::findOrFail($this->showDeleteConfirm);
        $prodi->delete();
        session()->flash('message', 'Program Studi berhasil dihapus.');
        $this->showDeleteConfirm = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = null;
    }

    public function resetForm(): void
    {
        $this->editId = null;
        $this->fakultas_id = '';
        $this->name = '';
        $this->code = '';
        $this->jenjang = '';
        $this->akreditasi = '';
        $this->kaprodi_name = '';
        $this->resetValidation();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.master-data.program-studi-index');
    }
}

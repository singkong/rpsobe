<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Kurikulum;
use App\Models\ProgramStudi;
use Illuminate\Support\Facades\Gate;

class KurikulumIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'name';
    public string $sortDirection = 'asc';
    public int $perPage = 10;

    public ?int $editId = null;
    public string $program_studi_id = '';
    public string $name = '';
    public string $tahun_mulai = '';
    public string $tahun_selesai = '';
    public string $total_sks = '';
    public bool $is_active = true;
    public bool $showModal = false;
    public $showDeleteConfirm = null;

    protected function rules(): array
    {
        return [
            'program_studi_id' => ['required', 'exists:program_studi,id'],
            'name' => ['required', 'string', 'max:255'],
            'tahun_mulai' => ['required', 'integer', 'min:2000', 'max:2100'],
            'tahun_selesai' => ['required', 'integer', 'min:2000', 'max:2100', 'gte:tahun_mulai'],
            'total_sks' => ['required', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ];
    }

    public function mount(): void
    {
        
    }

    public function prodiOptions()
    {
        return ProgramStudi::orderBy('name')->get();
    }

    public function kurikulumList()
    {
        $query = Kurikulum::with('programStudi');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
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
        Gate::authorize('kurikulum.create');
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        Gate::authorize('kurikulum.update');
        $kurikulum = Kurikulum::findOrFail($id);
        $this->editId = $kurikulum->id;
        $this->program_studi_id = $kurikulum->program_studi_id;
        $this->name = $kurikulum->name;
        $this->tahun_mulai = $kurikulum->tahun_mulai;
        $this->tahun_selesai = $kurikulum->tahun_selesai;
        $this->total_sks = (string) $kurikulum->total_sks;
        $this->is_active = $kurikulum->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->editId) {
            Gate::authorize('kurikulum.update');
        } else {
            Gate::authorize('kurikulum.create');
        }

        $validated = $this->validate();

        if ($this->editId) {
            $kurikulum = Kurikulum::findOrFail($this->editId);
            $kurikulum->update($validated);
            session()->flash('message', 'Kurikulum berhasil diperbarui.');
        } else {
            Kurikulum::create($validated);
            session()->flash('message', 'Kurikulum berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function toggleActive(int $id): void
    {
        Gate::authorize('kurikulum.update');
        $kurikulum = Kurikulum::findOrFail($id);
        $kurikulum->update(['is_active' => !$kurikulum->is_active]);
        session()->flash('message', 'Status kurikulum berhasil diubah.');
    }

    public function confirmDelete(int $id): void
    {
        $this->showDeleteConfirm = $id;
    }

    public function delete(): void
    {
        Gate::authorize('kurikulum.delete');
        $kurikulum = Kurikulum::findOrFail($this->showDeleteConfirm);
        $kurikulum->delete();
        session()->flash('message', 'Kurikulum berhasil dihapus.');
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
        $this->tahun_mulai = '';
        $this->tahun_selesai = '';
        $this->total_sks = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.master-data.kurikulum-index');
    }
}

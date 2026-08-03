<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MataKuliah;
use App\Models\Kurikulum;
use Illuminate\Support\Facades\Gate;

class MataKuliahIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'name';
    public string $sortDirection = 'asc';
    public int $perPage = 10;

    public ?int $editId = null;
    public string $kurikulum_id = '';
    public string $name = '';
    public string $code = '';
    public string $sks = '';
    public string $semester = '';
    public string $jenis = '';
    public string $deskripsi = '';
    public bool $showModal = false;
    public $showDeleteConfirm = null;
    public array $selectedItems = [];

    protected function rules(): array
    {
        return [
            'kurikulum_id' => ['required', 'exists:kurikulum,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50'],
            'sks' => ['required', 'integer', 'min:1', 'max:20'],
            'semester' => ['required', 'integer', 'min:1', 'max:8'],
            'jenis' => ['required', 'string', 'in:wajib,pilihan'],
            'deskripsi' => ['nullable', 'string'],
        ];
    }

    public function mount(): void
    {
        
    }

    public function kurikulumOptions()
    {
        return Kurikulum::orderBy('name')->get();
    }

    public function jenisOptions()
    {
        return [['value' => 'wajib', 'label' => 'Wajib'], ['value' => 'pilihan', 'label' => 'Pilihan']];
    }

    public function mataKuliahList()
    {
        $query = MataKuliah::with('kurikulum');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
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
        Gate::authorize('mata-kuliah.create');
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        Gate::authorize('mata-kuliah.update');
        $mk = MataKuliah::findOrFail($id);
        $this->editId = $mk->id;
        $this->kurikulum_id = $mk->kurikulum_id;
        $this->name = $mk->name;
        $this->code = $mk->code;
        $this->sks = (string) $mk->sks;
        $this->semester = (string) $mk->semester;
        $this->jenis = $mk->jenis;
        $this->deskripsi = $mk->deskripsi ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->editId) {
            Gate::authorize('mata-kuliah.update');
        } else {
            Gate::authorize('mata-kuliah.create');
        }

        $validated = $this->validate();

        if ($this->editId) {
            $mk = MataKuliah::findOrFail($this->editId);
            $mk->update($validated);
            session()->flash('message', 'Mata Kuliah berhasil diperbarui.');
        } else {
            MataKuliah::create($validated);
            session()->flash('message', 'Mata Kuliah berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id): void
    {
        $this->showDeleteConfirm = $id;
    }

    public function delete(): void
    {
        Gate::authorize('mata-kuliah.delete');

        if ($this->showDeleteConfirm === 'bulk') {
            MataKuliah::whereIn('id', $this->selectedItems)->delete();
            session()->flash('message', 'Mata Kuliah terpilih berhasil dihapus.');
            $this->selectedItems = [];
        } else {
            $mk = MataKuliah::findOrFail($this->showDeleteConfirm);
            $mk->delete();
            session()->flash('message', 'Mata Kuliah berhasil dihapus.');
        }

        $this->showDeleteConfirm = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = null;
    }

    public function toggleSelectAll(): void
    {
        if (count($this->selectedItems) > 0) {
            $this->selectedItems = [];
        } else {
            $this->selectedItems = $this->mataKuliahList()->pluck('id')->toArray();
        }
    }

    public function bulkDelete(): void
    {
        Gate::authorize('mata-kuliah.delete');
        if (count($this->selectedItems) > 0) {
            $this->showDeleteConfirm = 'bulk';
        }
    }

    public function resetForm(): void
    {
        $this->editId = null;
        $this->kurikulum_id = '';
        $this->name = '';
        $this->code = '';
        $this->sks = '';
        $this->semester = '';
        $this->jenis = '';
        $this->deskripsi = '';
        $this->resetValidation();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.master-data.mata-kuliah-index');
    }
}

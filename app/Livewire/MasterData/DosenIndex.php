<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Dosen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DosenIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'name';
    public string $sortDirection = 'asc';
    public int $perPage = 10;

    public ?int $editId = null;
    public string $nidn = '';
    public string $name = '';
    public string $gelar_depan = '';
    public string $gelar_belakang = '';
    public string $jabatan_fungsional = '';
    public string $bidang_keahlian = '';
    public string $email = '';
    public string $phone = '';
    public bool $is_active = true;
    public bool $showModal = false;
    public $showDeleteConfirm = null;

    protected function rules(): array
    {
        return [
            'nidn' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'gelar_depan' => ['nullable', 'string', 'max:50'],
            'gelar_belakang' => ['nullable', 'string', 'max:50'],
            'jabatan_fungsional' => ['nullable', 'string', 'max:255'],
            'bidang_keahlian' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ];
    }

    public function mount(): void
    {
        
    }

    public function dosenList()
    {
        $query = Dosen::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('nidn', 'like', '%' . $this->search . '%');
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
        Gate::authorize('dosen.create');
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        Gate::authorize('dosen.update');
        $dosen = Dosen::findOrFail($id);
        $this->editId = $dosen->id;
        $this->nidn = $dosen->nidn;
        $this->name = $dosen->name;
        $this->gelar_depan = $dosen->gelar_depan ?? '';
        $this->gelar_belakang = $dosen->gelar_belakang ?? '';
        $this->jabatan_fungsional = $dosen->jabatan_fungsional ?? '';
        $this->bidang_keahlian = $dosen->bidang_keahlian ?? '';
        $this->email = $dosen->email ?? '';
        $this->phone = $dosen->phone ?? '';
        $this->is_active = $dosen->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->editId) {
            Gate::authorize('dosen.update');
        } else {
            Gate::authorize('dosen.create');
        }

        $validated = $this->validate();

        if (!$this->editId) {
            $validated['tenant_id'] = Auth::user()->tenant_id;
        }

        if ($this->editId) {
            $dosen = Dosen::findOrFail($this->editId);
            $dosen->update($validated);
            session()->flash('message', 'Dosen berhasil diperbarui.');
        } else {
            Dosen::create($validated);
            session()->flash('message', 'Dosen berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function toggleActive(int $id): void
    {
        Gate::authorize('dosen.update');
        $dosen = Dosen::findOrFail($id);
        $dosen->update(['is_active' => !$dosen->is_active]);
        session()->flash('message', 'Status dosen berhasil diubah.');
    }

    public function confirmDelete(int $id): void
    {
        $this->showDeleteConfirm = $id;
    }

    public function delete(): void
    {
        Gate::authorize('dosen.delete');
        $dosen = Dosen::findOrFail($this->showDeleteConfirm);
        $dosen->delete();
        session()->flash('message', 'Dosen berhasil dihapus.');
        $this->showDeleteConfirm = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = null;
    }

    public function resetForm(): void
    {
        $this->editId = null;
        $this->nidn = '';
        $this->name = '';
        $this->gelar_depan = '';
        $this->gelar_belakang = '';
        $this->jabatan_fungsional = '';
        $this->bidang_keahlian = '';
        $this->email = '';
        $this->phone = '';
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
        return view('livewire.master-data.dosen-index');
    }
}

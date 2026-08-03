<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Fakultas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class FakultasIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'name';
    public string $sortDirection = 'asc';
    public int $perPage = 10;

    public ?int $editId = null;
    public string $name = '';
    public string $code = '';
    public string $dekan = '';
    public string $akreditasi = '';
    public bool $showModal = false;
    public $showDeleteConfirm = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50'],
            'dekan' => ['nullable', 'string', 'max:255'],
            'akreditasi' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function mount(): void
    {
        
    }

    public function fakultasList()
    {
        $query = Fakultas::query();

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
        Gate::authorize('fakultas.create');
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        Gate::authorize('fakultas.update');
        $fakultas = Fakultas::findOrFail($id);
        $this->editId = $fakultas->id;
        $this->name = $fakultas->name;
        $this->code = $fakultas->code;
        $this->dekan = $fakultas->dekan ?? '';
        $this->akreditasi = $fakultas->akreditasi ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->editId) {
            Gate::authorize('fakultas.update');
        } else {
            Gate::authorize('fakultas.create');
        }

        $validated = $this->validate();

        if ($this->editId) {
            $fakultas = Fakultas::findOrFail($this->editId);
            $fakultas->update($validated);
            session()->flash('message', 'Fakultas berhasil diperbarui.');
        } else {
            $validated['tenant_id'] = Auth::user()->tenant_id;
            Fakultas::create($validated);
            session()->flash('message', 'Fakultas berhasil ditambahkan.');
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
        Gate::authorize('fakultas.delete');
        $fakultas = Fakultas::findOrFail($this->showDeleteConfirm);
        $fakultas->delete();
        session()->flash('message', 'Fakultas berhasil dihapus.');
        $this->showDeleteConfirm = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = null;
    }

    public function resetForm(): void
    {
        $this->editId = null;
        $this->name = '';
        $this->code = '';
        $this->dekan = '';
        $this->akreditasi = '';
        $this->resetValidation();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.master-data.fakultas-index');
    }
}

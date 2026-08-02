<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CPL;
use App\Models\ProgramStudi;
use App\Enums\CPKategori;
use Illuminate\Support\Facades\Gate;

class CplIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'code';
    public string $sortDirection = 'asc';
    public int $perPage = 10;
    public string $filterProdiId = '';
    public string $filterKategori = '';

    public ?int $editId = null;
    public string $program_studi_id = '';
    public string $code = '';
    public string $deskripsi = '';
    public string $kategori = '';
    public bool $showModal = false;
    public $showDeleteConfirm = null;

    protected function rules(): array
    {
        return [
            'program_studi_id' => ['required', 'exists:program_studi,id'],
            'code' => ['required', 'string', 'max:50'],
            'deskripsi' => ['required', 'string'],
            'kategori' => ['required', 'string', 'in:S,P,KU,KK'],
        ];
    }

    public function mount(): void
    {
        if (!Gate::allows('cpl.view-any')) {
            abort(403);
        }
    }

    public function prodiOptions()
    {
        return ProgramStudi::orderBy('name')->get();
    }

    public function kategoriOptions()
    {
        return collect(CPKategori::cases())->map(fn($k) => ['value' => $k->value, 'label' => $k->label()]);
    }

    public function cplList()
    {
        $query = CPL::with('programStudi');

        if ($this->filterProdiId) {
            $query->where('program_studi_id', $this->filterProdiId);
        }

        if ($this->filterKategori) {
            $query->where('kategori', $this->filterKategori);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
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
        Gate::authorize('cpl.create');
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        Gate::authorize('cpl.update');
        $cpl = CPL::findOrFail($id);
        $this->editId = $cpl->id;
        $this->program_studi_id = $cpl->program_studi_id;
        $this->code = $cpl->code;
        $this->deskripsi = $cpl->deskripsi;
        $this->kategori = $cpl->kategori instanceof CPKategori ? $cpl->kategori->value : $cpl->kategori;
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->editId) {
            Gate::authorize('cpl.update');
        } else {
            Gate::authorize('cpl.create');
        }

        $validated = $this->validate();

        if ($this->editId) {
            $cpl = CPL::findOrFail($this->editId);
            $cpl->update($validated);
            session()->flash('message', 'CPL berhasil diperbarui.');
        } else {
            CPL::create($validated);
            session()->flash('message', 'CPL berhasil ditambahkan.');
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
        Gate::authorize('cpl.delete');
        $cpl = CPL::with('mataKuliah')->findOrFail($this->showDeleteConfirm);

        if ($cpl->mataKuliah->count() > 0) {
            session()->flash('error', 'CPL tidak dapat dihapus karena masih digunakan di RPS.');
            $this->showDeleteConfirm = null;
            return;
        }

        $cpl->delete();
        session()->flash('message', 'CPL berhasil dihapus.');
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
        $this->code = '';
        $this->deskripsi = '';
        $this->kategori = '';
        $this->resetValidation();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.master-data.cpl-index');
    }
}

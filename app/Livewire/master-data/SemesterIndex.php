<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Semester;
use App\Enums\SemesterTipe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class SemesterIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'tahun_akademik';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    public ?int $editId = null;
    public string $name = '';
    public string $tipe = '';
    public string $tahun_akademik = '';
    public string $tanggal_mulai = '';
    public string $tanggal_selesai = '';
    public bool $is_active = true;
    public bool $showModal = false;
    public $showDeleteConfirm = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'tipe' => ['required', 'string', 'in:ganjil,genap'],
            'tahun_akademik' => ['required', 'string', 'max:20'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_mulai'],
            'is_active' => ['boolean'],
        ];
    }

    public function mount(): void
    {
        if (!Gate::allows('semester.view-any')) {
            abort(403);
        }
    }

    public function tipeOptions()
    {
        return collect(SemesterTipe::cases())->map(fn($t) => ['value' => $t->value, 'label' => $t->label()]);
    }

    public function semesterList()
    {
        $query = Semester::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('tahun_akademik', 'like', '%' . $this->search . '%');
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
        Gate::authorize('semester.create');
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        Gate::authorize('semester.update');
        $semester = Semester::findOrFail($id);
        $this->editId = $semester->id;
        $this->name = $semester->name;
        $this->tipe = $semester->tipe instanceof SemesterTipe ? $semester->tipe->value : $semester->tipe;
        $this->tahun_akademik = $semester->tahun_akademik;
        $this->tanggal_mulai = $semester->tanggal_mulai->format('Y-m-d');
        $this->tanggal_selesai = $semester->tanggal_selesai->format('Y-m-d');
        $this->is_active = $semester->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->editId) {
            Gate::authorize('semester.update');
        } else {
            Gate::authorize('semester.create');
        }

        $validated = $this->validate();

        if (!$this->editId) {
            $validated['tenant_id'] = Auth::user()->tenant_id;
        }

        if ($this->editId) {
            $semester = Semester::findOrFail($this->editId);
            $semester->update($validated);
            session()->flash('message', 'Semester berhasil diperbarui.');
        } else {
            Semester::create($validated);
            session()->flash('message', 'Semester berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function toggleActive(int $id): void
    {
        Gate::authorize('semester.update');
        $semester = Semester::findOrFail($id);
        $semester->update(['is_active' => !$semester->is_active]);
        session()->flash('message', 'Status semester berhasil diubah.');
    }

    public function confirmDelete(int $id): void
    {
        $this->showDeleteConfirm = $id;
    }

    public function delete(): void
    {
        Gate::authorize('semester.delete');
        $semester = Semester::findOrFail($this->showDeleteConfirm);
        $semester->delete();
        session()->flash('message', 'Semester berhasil dihapus.');
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
        $this->tipe = '';
        $this->tahun_akademik = '';
        $this->tanggal_mulai = '';
        $this->tanggal_selesai = '';
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
        return view('livewire.master-data.semester-index');
    }
}

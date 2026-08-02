<?php

use function Livewire\Volt\{state, rules, mount};
use App\Models\Semester;
use App\Enums\SemesterTipe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

state('search', '');
state('sortField', 'tahun_akademik');
state('sortDirection', 'desc');
state('perPage', 10);
state('editId', null);
state('name', '');
state('tipe', '');
state('tahun_akademik', '');
state('tanggal_mulai', '');
state('tanggal_selesai', '');
state('is_active', true);
state('showModal', false);
state('showDeleteConfirm', null);

rules([
    'name' => ['required', 'string', 'max:255'],
    'tipe' => ['required', 'string', 'in:ganjil,genap'],
    'tahun_akademik' => ['required', 'string', 'max:20'],
    'tanggal_mulai' => ['required', 'date'],
    'tanggal_selesai' => ['required', 'date', 'after:tanggal_mulai'],
    'is_active' => ['boolean'],
]);

mount(function () {
    if (!Gate::allows('semester.view-any')) {
        abort(403);
    }
});

$tipeOptions = function () {
    return collect(SemesterTipe::cases())->map(fn($t) => ['value' => $t->value, 'label' => $t->label()]);
};

$semesterList = function () {
    $query = Semester::query();

    if ($this->search) {
        $query->where(function ($q) {
            $q->where('name', 'like', '%' . $this->search . '%')
              ->orWhere('tahun_akademik', 'like', '%' . $this->search . '%');
        });
    }

    $query->orderBy($this->sortField, $this->sortDirection);

    return $query->paginate($this->perPage);
};

$sortBy = function ($field) {
    if ($this->sortField === $field) {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        $this->sortField = $field;
        $this->sortDirection = 'asc';
    }
};

$openCreate = function () {
    Gate::authorize('semester.create');
    $this->resetForm();
    $this->showModal = true;
};

$edit = function ($id) {
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
};

$save = function () {
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
};

$toggleActive = function ($id) {
    Gate::authorize('semester.update');
    $semester = Semester::findOrFail($id);
    $semester->update(['is_active' => !$semester->is_active]);
    session()->flash('message', 'Status semester berhasil diubah.');
};

$confirmDelete = function ($id) {
    $this->showDeleteConfirm = $id;
};

$delete = function () {
    Gate::authorize('semester.delete');
    $semester = Semester::findOrFail($this->showDeleteConfirm);
    $semester->delete();
    session()->flash('message', 'Semester berhasil dihapus.');
    $this->showDeleteConfirm = null;
};

$cancelDelete = function () {
    $this->showDeleteConfirm = null;
};

$resetForm = function () {
    $this->editId = null;
    $this->name = '';
    $this->tipe = '';
    $this->tahun_akademik = '';
    $this->tanggal_mulai = '';
    $this->tanggal_selesai = '';
    $this->is_active = true;
    $this->resetValidation();
};

$closeModal = function () {
    $this->showModal = false;
    $this->resetForm();
};

return view('livewire.master-data.semester-index');

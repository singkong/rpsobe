<?php

use function Livewire\Volt\{state, rules, mount};
use App\Models\Dosen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

state('search', '');
state('sortField', 'name');
state('sortDirection', 'asc');
state('perPage', 10);
state('editId', null);
state('nidn', '');
state('name', '');
state('gelar_depan', '');
state('gelar_belakang', '');
state('jabatan_fungsional', '');
state('bidang_keahlian', '');
state('email', '');
state('phone', '');
state('is_active', true);
state('showModal', false);
state('showDeleteConfirm', null);

rules([
    'nidn' => ['required', 'string', 'max:50'],
    'name' => ['required', 'string', 'max:255'],
    'gelar_depan' => ['nullable', 'string', 'max:50'],
    'gelar_belakang' => ['nullable', 'string', 'max:50'],
    'jabatan_fungsional' => ['nullable', 'string', 'max:255'],
    'bidang_keahlian' => ['nullable', 'string', 'max:255'],
    'email' => ['nullable', 'email', 'max:255'],
    'phone' => ['nullable', 'string', 'max:20'],
    'is_active' => ['boolean'],
]);

mount(function () {
    if (!Gate::allows('dosen.view-any')) {
        abort(403);
    }
});

$dosenList = function () {
    $query = Dosen::query();

    if ($this->search) {
        $query->where(function ($q) {
            $q->where('name', 'like', '%' . $this->search . '%')
              ->orWhere('nidn', 'like', '%' . $this->search . '%');
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
    Gate::authorize('dosen.create');
    $this->resetForm();
    $this->showModal = true;
};

$edit = function ($id) {
    Gate::authorize('dosen.update');
    $dosen = Dosen::findOrFail($id);
    $this->editId = $dosen->id;
    $this->nidn = $dosen->nidn;
    $this->name = $dosen->name;
    $this->gelar_depan = $dosen->gelar_depan;
    $this->gelar_belakang = $dosen->gelar_belakang;
    $this->jabatan_fungsional = $dosen->jabatan_fungsional;
    $this->bidang_keahlian = $dosen->bidang_keahlian;
    $this->email = $dosen->email;
    $this->phone = $dosen->phone;
    $this->is_active = $dosen->is_active;
    $this->showModal = true;
};

$save = function () {
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
};

$toggleActive = function ($id) {
    Gate::authorize('dosen.update');
    $dosen = Dosen::findOrFail($id);
    $dosen->update(['is_active' => !$dosen->is_active]);
    session()->flash('message', 'Status dosen berhasil diubah.');
};

$confirmDelete = function ($id) {
    $this->showDeleteConfirm = $id;
};

$delete = function () {
    Gate::authorize('dosen.delete');
    $dosen = Dosen::findOrFail($this->showDeleteConfirm);
    $dosen->delete();
    session()->flash('message', 'Dosen berhasil dihapus.');
    $this->showDeleteConfirm = null;
};

$cancelDelete = function () {
    $this->showDeleteConfirm = null;
};

$resetForm = function () {
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
};

$closeModal = function () {
    $this->showModal = false;
    $this->resetForm();
};

return view('livewire.master-data.dosen-index');

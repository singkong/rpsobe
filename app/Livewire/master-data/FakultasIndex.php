<?php

use function Livewire\Volt\{state, rules, mount};
use App\Models\Fakultas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\LengthAwarePaginator;

state('search', '');
state('sortField', 'name');
state('sortDirection', 'asc');
state('perPage', 10);
state('editId', null);
state('name', '');
state('code', '');
state('dekan', '');
state('akreditasi', '');
state('showModal', false);
state('showDeleteConfirm', null);

rules([
    'name' => ['required', 'string', 'max:255'],
    'code' => ['required', 'string', 'max:50'],
    'dekan' => ['nullable', 'string', 'max:255'],
    'akreditasi' => ['nullable', 'string', 'max:50'],
]);

mount(function () {
    if (!Gate::allows('fakultas.view-any')) {
        abort(403);
    }
});

$fakultasList = function () {
    $query = Fakultas::query();

    if ($this->search) {
        $query->where(function ($q) {
            $q->where('name', 'like', '%' . $this->search . '%')
              ->orWhere('code', 'like', '%' . $this->search . '%');
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
    Gate::authorize('fakultas.create');
    $this->resetForm();
    $this->showModal = true;
};

$edit = function ($id) {
    Gate::authorize('fakultas.update');
    $fakultas = Fakultas::findOrFail($id);
    $this->editId = $fakultas->id;
    $this->name = $fakultas->name;
    $this->code = $fakultas->code;
    $this->dekan = $fakultas->dekan;
    $this->akreditasi = $fakultas->akreditasi;
    $this->showModal = true;
};

$save = function () {
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
};

$confirmDelete = function ($id) {
    $this->showDeleteConfirm = $id;
};

$delete = function () {
    Gate::authorize('fakultas.delete');

    $fakultas = Fakultas::findOrFail($this->showDeleteConfirm);
    $fakultas->delete();

    session()->flash('message', 'Fakultas berhasil dihapus.');
    $this->showDeleteConfirm = null;
};

$cancelDelete = function () {
    $this->showDeleteConfirm = null;
};

$resetForm = function () {
    $this->editId = null;
    $this->name = '';
    $this->code = '';
    $this->dekan = '';
    $this->akreditasi = '';
    $this->resetValidation();
};

$closeModal = function () {
    $this->showModal = false;
    $this->resetForm();
};

return view('livewire.master-data.fakultas-index');

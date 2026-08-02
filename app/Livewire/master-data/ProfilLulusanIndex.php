<?php

use function Livewire\Volt\{state, rules, mount};
use App\Models\ProfilLulusan;
use App\Models\ProgramStudi;
use App\Models\CPL;
use Illuminate\Support\Facades\Gate;

state('search', '');
state('sortField', 'name');
state('sortDirection', 'asc');
state('perPage', 10);
state('editId', null);
state('program_studi_id', '');
state('name', '');
state('deskripsi', '');
state('selectedCpls', []);
state('showModal', false);
state('showDeleteConfirm', null);

rules([
    'program_studi_id' => ['required', 'exists:program_studi,id'],
    'name' => ['required', 'string', 'max:255'],
    'deskripsi' => ['nullable', 'string'],
    'selectedCpls' => ['nullable', 'array'],
]);

mount(function () {
    if (!Gate::allows('profil-lulusan.view-any')) {
        abort(403);
    }
});

$prodiOptions = function () {
    return ProgramStudi::orderBy('name')->get();
};

$cplOptions = function () {
    return CPL::orderBy('code')->get();
};

$profilList = function () {
    $query = ProfilLulusan::with(['programStudi', 'cpls']);

    if ($this->search) {
        $query->where(function ($q) {
            $q->where('name', 'like', '%' . $this->search . '%')
              ->orWhere('deskripsi', 'like', '%' . $this->search . '%');
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
    Gate::authorize('profil-lulusan.create');
    $this->resetForm();
    $this->showModal = true;
};

$edit = function ($id) {
    Gate::authorize('profil-lulusan.update');
    $profil = ProfilLulusan::with('cpls')->findOrFail($id);
    $this->editId = $profil->id;
    $this->program_studi_id = $profil->program_studi_id;
    $this->name = $profil->name;
    $this->deskripsi = $profil->deskripsi;
    $this->selectedCpls = $profil->cpls->pluck('id')->toArray();
    $this->showModal = true;
};

$save = function () {
    if ($this->editId) {
        Gate::authorize('profil-lulusan.update');
    } else {
        Gate::authorize('profil-lulusan.create');
    }

    $validated = $this->validate();

    if ($this->editId) {
        $profil = ProfilLulusan::findOrFail($this->editId);
        $profil->update([
            'program_studi_id' => $validated['program_studi_id'],
            'name' => $validated['name'],
            'deskripsi' => $validated['deskripsi'],
        ]);
    } else {
        $profil = ProfilLulusan::create([
            'program_studi_id' => $validated['program_studi_id'],
            'name' => $validated['name'],
            'deskripsi' => $validated['deskripsi'],
        ]);
    }

    $profil->cpls()->sync($this->selectedCpls ?? []);

    session()->flash('message', $this->editId ? 'Profil Lulusan berhasil diperbarui.' : 'Profil Lulusan berhasil ditambahkan.');

    $this->showModal = false;
    $this->resetForm();
};

$confirmDelete = function ($id) {
    $this->showDeleteConfirm = $id;
};

$delete = function () {
    Gate::authorize('profil-lulusan.delete');
    $profil = ProfilLulusan::findOrFail($this->showDeleteConfirm);
    $profil->cpls()->detach();
    $profil->delete();
    session()->flash('message', 'Profil Lulusan berhasil dihapus.');
    $this->showDeleteConfirm = null;
};

$cancelDelete = function () {
    $this->showDeleteConfirm = null;
};

$resetForm = function () {
    $this->editId = null;
    $this->program_studi_id = '';
    $this->name = '';
    $this->deskripsi = '';
    $this->selectedCpls = [];
    $this->resetValidation();
};

$closeModal = function () {
    $this->showModal = false;
    $this->resetForm();
};

return view('livewire.master-data.profil-lulusan-index');
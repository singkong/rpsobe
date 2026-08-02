<?php

use function Livewire\Volt\{state, rules, mount};
use App\Models\CPL;
use App\Models\ProgramStudi;
use App\Enums\CPKategori;
use Illuminate\Support\Facades\Gate;

state('search', '');
state('sortField', 'code');
state('sortDirection', 'asc');
state('perPage', 10);
state('filterProdiId', '');
state('filterKategori', '');
state('editId', null);
state('program_studi_id', '');
state('code', '');
state('deskripsi', '');
state('kategori', '');
state('showModal', false);
state('showDeleteConfirm', null);

rules([
    'program_studi_id' => ['required', 'exists:program_studi,id'],
    'code' => ['required', 'string', 'max:50'],
    'deskripsi' => ['required', 'string'],
    'kategori' => ['required', 'string', 'in:S,P,KU,KK'],
]);

mount(function () {
    if (!Gate::allows('cpl.view-any')) {
        abort(403);
    }
});

$prodiOptions = function () {
    return ProgramStudi::orderBy('name')->get();
};

$kategoriOptions = function () {
    return collect(CPKategori::cases())->map(fn($k) => ['value' => $k->value, 'label' => $k->label()]);
};

$cplList = function () {
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
    Gate::authorize('cpl.create');
    $this->resetForm();
    $this->showModal = true;
};

$edit = function ($id) {
    Gate::authorize('cpl.update');
    $cpl = CPL::findOrFail($id);
    $this->editId = $cpl->id;
    $this->program_studi_id = $cpl->program_studi_id;
    $this->code = $cpl->code;
    $this->deskripsi = $cpl->deskripsi;
    $this->kategori = $cpl->kategori instanceof CPKategori ? $cpl->kategori->value : $cpl->kategori;
    $this->showModal = true;
};

$save = function () {
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
};

$confirmDelete = function ($id) {
    $this->showDeleteConfirm = $id;
};

$delete = function () {
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
};

$cancelDelete = function () {
    $this->showDeleteConfirm = null;
};

$resetForm = function () {
    $this->editId = null;
    $this->program_studi_id = '';
    $this->code = '';
    $this->deskripsi = '';
    $this->kategori = '';
    $this->resetValidation();
};

$closeModal = function () {
    $this->showModal = false;
    $this->resetForm();
};

return view('livewire.master-data.cpl-index');

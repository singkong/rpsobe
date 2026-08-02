<?php

use function Livewire\Volt\{state, rules, mount};
use App\Models\ProgramStudi;
use App\Models\Fakultas;
use App\Enums\Jenjang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

state('search', '');
state('sortField', 'name');
state('sortDirection', 'asc');
state('perPage', 10);
state('filterFakultasId', '');
state('editId', null);
state('fakultas_id', '');
state('name', '');
state('code', '');
state('jenjang', '');
state('akreditasi', '');
state('kaprodi_name', '');
state('showModal', false);
state('showDeleteConfirm', null);

rules([
    'fakultas_id' => ['required', 'exists:fakultas,id'],
    'name' => ['required', 'string', 'max:255'],
    'code' => ['required', 'string', 'max:50'],
    'jenjang' => ['required', 'string', 'in:D3,D4,S1,S2,S3,Profesi,Spesialis'],
    'akreditasi' => ['nullable', 'string', 'max:50'],
    'kaprodi_name' => ['nullable', 'string', 'max:255'],
]);

mount(function () {
    if (!Gate::allows('program-studi.view-any')) {
        abort(403);
    }
});

$fakultasOptions = function () {
    return Fakultas::orderBy('name')->get();
};

$jenjangOptions = function () {
    return collect(Jenjang::cases())->map(fn($j) => ['value' => $j->value, 'label' => $j->label()]);
};

$prodiList = function () {
    $query = ProgramStudi::with('fakultas');

    if ($this->filterFakultasId) {
        $query->where('fakultas_id', $this->filterFakultasId);
    }

    if ($this->search) {
        $query->where(function ($q) {
            $q->where('name', 'like', '%' . $this->search . '%')
              ->orWhere('code', 'like', '%' . $this->search . '%');
        });
    }

    $query->orderBy($this->sortField === 'fakultas_name' ? 'fakultas_id' : $this->sortField, $this->sortDirection);

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
    Gate::authorize('program-studi.create');
    $this->resetForm();
    $this->showModal = true;
};

$edit = function ($id) {
    Gate::authorize('program-studi.update');
    $prodi = ProgramStudi::findOrFail($id);
    $this->editId = $prodi->id;
    $this->fakultas_id = $prodi->fakultas_id;
    $this->name = $prodi->name;
    $this->code = $prodi->code;
    $this->jenjang = $prodi->jenjang instanceof Jenjang ? $prodi->jenjang->value : $prodi->jenjang;
    $this->akreditasi = $prodi->akreditasi;
    $this->kaprodi_name = $prodi->kaprodi_name;
    $this->showModal = true;
};

$save = function () {
    if ($this->editId) {
        Gate::authorize('program-studi.update');
    } else {
        Gate::authorize('program-studi.create');
    }

    $validated = $this->validate();

    if ($this->editId) {
        $prodi = ProgramStudi::findOrFail($this->editId);
        $prodi->update($validated);
        session()->flash('message', 'Program Studi berhasil diperbarui.');
    } else {
        ProgramStudi::create($validated);
        session()->flash('message', 'Program Studi berhasil ditambahkan.');
    }

    $this->showModal = false;
    $this->resetForm();
};

$confirmDelete = function ($id) {
    $this->showDeleteConfirm = $id;
};

$delete = function () {
    Gate::authorize('program-studi.delete');

    $prodi = ProgramStudi::findOrFail($this->showDeleteConfirm);
    $prodi->delete();

    session()->flash('message', 'Program Studi berhasil dihapus.');
    $this->showDeleteConfirm = null;
};

$cancelDelete = function () {
    $this->showDeleteConfirm = null;
};

$resetForm = function () {
    $this->editId = null;
    $this->fakultas_id = '';
    $this->name = '';
    $this->code = '';
    $this->jenjang = '';
    $this->akreditasi = '';
    $this->kaprodi_name = '';
    $this->resetValidation();
};

$closeModal = function () {
    $this->showModal = false;
    $this->resetForm();
};

return view('livewire.master-data.program-studi-index');

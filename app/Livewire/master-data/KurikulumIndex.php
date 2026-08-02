<?php

use function Livewire\Volt\{state, rules, mount};
use App\Models\Kurikulum;
use App\Models\ProgramStudi;
use Illuminate\Support\Facades\Gate;

state('search', '');
state('sortField', 'name');
state('sortDirection', 'asc');
state('perPage', 10);
state('editId', null);
state('program_studi_id', '');
state('name', '');
state('tahun_mulai', '');
state('tahun_selesai', '');
state('total_sks', '');
state('is_active', true);
state('showModal', false);
state('showDeleteConfirm', null);

rules([
    'program_studi_id' => ['required', 'exists:program_studi,id'],
    'name' => ['required', 'string', 'max:255'],
    'tahun_mulai' => ['required', 'integer', 'min:2000', 'max:2100'],
    'tahun_selesai' => ['required', 'integer', 'min:2000', 'max:2100', 'gte:tahun_mulai'],
    'total_sks' => ['required', 'integer', 'min:1'],
    'is_active' => ['boolean'],
]);

mount(function () {
    if (!Gate::allows('kurikulum.view-any')) {
        abort(403);
    }
});

$prodiOptions = function () {
    return ProgramStudi::orderBy('name')->get();
};

$kurikulumList = function () {
    $query = Kurikulum::with('programStudi');

    if ($this->search) {
        $query->where(function ($q) {
            $q->where('name', 'like', '%' . $this->search . '%');
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
    Gate::authorize('kurikulum.create');
    $this->resetForm();
    $this->showModal = true;
};

$edit = function ($id) {
    Gate::authorize('kurikulum.update');
    $kurikulum = Kurikulum::findOrFail($id);
    $this->editId = $kurikulum->id;
    $this->program_studi_id = $kurikulum->program_studi_id;
    $this->name = $kurikulum->name;
    $this->tahun_mulai = $kurikulum->tahun_mulai;
    $this->tahun_selesai = $kurikulum->tahun_selesai;
    $this->total_sks = $kurikulum->total_sks;
    $this->is_active = $kurikulum->is_active;
    $this->showModal = true;
};

$save = function () {
    if ($this->editId) {
        Gate::authorize('kurikulum.update');
    } else {
        Gate::authorize('kurikulum.create');
    }

    $validated = $this->validate();

    if ($this->editId) {
        $kurikulum = Kurikulum::findOrFail($this->editId);
        $kurikulum->update($validated);
        session()->flash('message', 'Kurikulum berhasil diperbarui.');
    } else {
        Kurikulum::create($validated);
        session()->flash('message', 'Kurikulum berhasil ditambahkan.');
    }

    $this->showModal = false;
    $this->resetForm();
};

$toggleActive = function ($id) {
    Gate::authorize('kurikulum.update');
    $kurikulum = Kurikulum::findOrFail($id);
    $kurikulum->update(['is_active' => !$kurikulum->is_active]);
    session()->flash('message', 'Status kurikulum berhasil diubah.');
};

$confirmDelete = function ($id) {
    $this->showDeleteConfirm = $id;
};

$delete = function () {
    Gate::authorize('kurikulum.delete');
    $kurikulum = Kurikulum::findOrFail($this->showDeleteConfirm);
    $kurikulum->delete();
    session()->flash('message', 'Kurikulum berhasil dihapus.');
    $this->showDeleteConfirm = null;
};

$cancelDelete = function () {
    $this->showDeleteConfirm = null;
};

$resetForm = function () {
    $this->editId = null;
    $this->program_studi_id = '';
    $this->name = '';
    $this->tahun_mulai = '';
    $this->tahun_selesai = '';
    $this->total_sks = '';
    $this->is_active = true;
    $this->resetValidation();
};

$closeModal = function () {
    $this->showModal = false;
    $this->resetForm();
};

return view('livewire.master-data.kurikulum-index');

<?php

use function Livewire\Volt\{state, rules, mount};
use App\Models\MataKuliah;
use App\Models\Kurikulum;
use Illuminate\Support\Facades\Gate;

state('search', '');
state('sortField', 'name');
state('sortDirection', 'asc');
state('perPage', 10);
state('editId', null);
state('kurikulum_id', '');
state('name', '');
state('code', '');
state('sks', '');
state('semester', '');
state('jenis', '');
state('deskripsi', '');
state('showModal', false);
state('showDeleteConfirm', null);
state('selectedItems', []);

rules([
    'kurikulum_id' => ['required', 'exists:kurikulum,id'],
    'name' => ['required', 'string', 'max:255'],
    'code' => ['required', 'string', 'max:50'],
    'sks' => ['required', 'integer', 'min:1', 'max:20'],
    'semester' => ['required', 'integer', 'min:1', 'max:8'],
    'jenis' => ['required', 'string', 'in:wajib,pilihan'],
    'deskripsi' => ['nullable', 'string'],
]);

mount(function () {
    if (!Gate::allows('mata-kuliah.view-any')) {
        abort(403);
    }
});

$kurikulumOptions = function () {
    return Kurikulum::orderBy('name')->get();
};

$jenisOptions = function () {
    return [['value' => 'wajib', 'label' => 'Wajib'], ['value' => 'pilihan', 'label' => 'Pilihan']];
};

$mataKuliahList = function () {
    $query = MataKuliah::with('kurikulum');

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
    Gate::authorize('mata-kuliah.create');
    $this->resetForm();
    $this->showModal = true;
};

$edit = function ($id) {
    Gate::authorize('mata-kuliah.update');
    $mk = MataKuliah::findOrFail($id);
    $this->editId = $mk->id;
    $this->kurikulum_id = $mk->kurikulum_id;
    $this->name = $mk->name;
    $this->code = $mk->code;
    $this->sks = $mk->sks;
    $this->semester = $mk->semester;
    $this->jenis = $mk->jenis;
    $this->deskripsi = $mk->deskripsi;
    $this->showModal = true;
};

$save = function () {
    if ($this->editId) {
        Gate::authorize('mata-kuliah.update');
    } else {
        Gate::authorize('mata-kuliah.create');
    }

    $validated = $this->validate();

    if ($this->editId) {
        $mk = MataKuliah::findOrFail($this->editId);
        $mk->update($validated);
        session()->flash('message', 'Mata Kuliah berhasil diperbarui.');
    } else {
        MataKuliah::create($validated);
        session()->flash('message', 'Mata Kuliah berhasil ditambahkan.');
    }

    $this->showModal = false;
    $this->resetForm();
};

$confirmDelete = function ($id) {
    $this->showDeleteConfirm = $id;
};

$delete = function () {
    Gate::authorize('mata-kuliah.delete');

    if ($this->showDeleteConfirm === 'bulk') {
        MataKuliah::whereIn('id', $this->selectedItems)->delete();
        session()->flash('message', 'Mata Kuliah terpilih berhasil dihapus.');
        $this->selectedItems = [];
    } else {
        $mk = MataKuliah::findOrFail($this->showDeleteConfirm);
        $mk->delete();
        session()->flash('message', 'Mata Kuliah berhasil dihapus.');
    }

    $this->showDeleteConfirm = null;
};

$cancelDelete = function () {
    $this->showDeleteConfirm = null;
};

$bulkDelete = function () {
    Gate::authorize('mata-kuliah.delete');
    if (count($this->selectedItems) > 0) {
        $this->showDeleteConfirm = 'bulk';
    }
};

$resetForm = function () {
    $this->editId = null;
    $this->kurikulum_id = '';
    $this->name = '';
    $this->code = '';
    $this->sks = '';
    $this->semester = '';
    $this->jenis = '';
    $this->deskripsi = '';
    $this->resetValidation();
};

$closeModal = function () {
    $this->showModal = false;
    $this->resetForm();
};

return view('livewire.master-data.mata-kuliah-index');
<?php

use function Livewire\Volt\{state, rules, mount};
use App\Models\Referensi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

state('search', '');
state('sortField', 'judul');
state('sortDirection', 'asc');
state('perPage', 10);
state('editId', null);
state('judul', '');
state('penulis', '');
state('tahun', '');
state('penerbit', '');
state('format', '');
state('url', '');
state('showModal', false);
state('showDeleteConfirm', null);

rules([
    'judul' => ['required', 'string', 'max:255'],
    'penulis' => ['nullable', 'string', 'max:255'],
    'tahun' => ['nullable', 'string', 'max:10'],
    'penerbit' => ['nullable', 'string', 'max:255'],
    'format' => ['required', 'string', 'in:buku,jurnal,artikel,website,lainnya'],
    'url' => ['nullable', 'url', 'max:255'],
]);

mount(function () {
    if (!Gate::allows('referensi.view-any')) {
        abort(403);
    }
});

$formatOptions = function () {
    return [
        ['value' => 'buku', 'label' => 'Buku'],
        ['value' => 'jurnal', 'label' => 'Jurnal'],
        ['value' => 'artikel', 'label' => 'Artikel'],
        ['value' => 'website', 'label' => 'Website'],
        ['value' => 'lainnya', 'label' => 'Lainnya'],
    ];
};

$referensiList = function () {
    $query = Referensi::query();

    if ($this->search) {
        $query->where(function ($q) {
            $q->where('judul', 'like', '%' . $this->search . '%')
              ->orWhere('penulis', 'like', '%' . $this->search . '%')
              ->orWhere('penerbit', 'like', '%' . $this->search . '%');
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
    Gate::authorize('referensi.create');
    $this->resetForm();
    $this->showModal = true;
};

$edit = function ($id) {
    Gate::authorize('referensi.update');
    $ref = Referensi::findOrFail($id);
    $this->editId = $ref->id;
    $this->judul = $ref->judul;
    $this->penulis = $ref->penulis;
    $this->tahun = $ref->tahun;
    $this->penerbit = $ref->penerbit;
    $this->format = $ref->format;
    $this->url = $ref->url;
    $this->showModal = true;
};

$save = function () {
    if ($this->editId) {
        Gate::authorize('referensi.update');
    } else {
        Gate::authorize('referensi.create');
    }

    $validated = $this->validate();

    if (!$this->editId) {
        $validated['tenant_id'] = Auth::user()->tenant_id;
    }

    if ($this->editId) {
        $ref = Referensi::findOrFail($this->editId);
        $ref->update($validated);
        session()->flash('message', 'Referensi berhasil diperbarui.');
    } else {
        Referensi::create($validated);
        session()->flash('message', 'Referensi berhasil ditambahkan.');
    }

    $this->showModal = false;
    $this->resetForm();
};

$confirmDelete = function ($id) {
    $this->showDeleteConfirm = $id;
};

$delete = function () {
    Gate::authorize('referensi.delete');
    $ref = Referensi::findOrFail($this->showDeleteConfirm);
    $ref->delete();
    session()->flash('message', 'Referensi berhasil dihapus.');
    $this->showDeleteConfirm = null;
};

$cancelDelete = function () {
    $this->showDeleteConfirm = null;
};

$resetForm = function () {
    $this->editId = null;
    $this->judul = '';
    $this->penulis = '';
    $this->tahun = '';
    $this->penerbit = '';
    $this->format = '';
    $this->url = '';
    $this->resetValidation();
};

$closeModal = function () {
    $this->showModal = false;
    $this->resetForm();
};

return view('livewire.master-data.referensi-index');
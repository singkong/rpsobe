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

?>

<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Fakultas</h3>
            <div class="card-actions">
                <?php if(Gate::allows('fakultas.create')): ?>
                    <button wire:click="openCreate" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                        Tambah
                    </button>
                
            </div>
        </div>
        <div class="card-body border-bottom py-3">
            <div class="d-flex">
                <div class="ms-auto text-secondary">
                    <div class="input-icon">
                        <input type="text" wire:model.live="search" class="form-control" placeholder="Cari fakultas...">
                        <span class="input-icon-addon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><circle cx="10" cy="10" r="7"/><path d="M21 21l-6-6"/></svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-striped">
                <thead>
                    <tr>
                        <th wire:click="sortBy('name')" style="cursor:pointer">Nama <?php if($sortField === 'name'): ?><?= $sortDirection === 'asc' ? '&#9650;' : '&#9660;' ?></th>
                        <th wire:click="sortBy('code')" style="cursor:pointer">Kode <?php if($sortField === 'code'): ?><?= $sortDirection === 'asc' ? '&#9650;' : '&#9660;' ?></th>
                        <th>Dekan</th>
                        <th>Akreditasi</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($this->fakultasList() as $item): ?>
                        <tr>
                            <td><?= $item->name ?></td>
                            <td><span class="badge bg-primary-lt"><?= $item->code ?></span></td>
                            <td><?= $item->dekan ?? '-' ?></td>
                            <td><?= $item->akreditasi ?? '-' ?></td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <?php if(Gate::allows('fakultas.update')): ?>
                                        <button wire:click="edit(<?= $item->id ?>)" class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/></svg>
                                        </button>
                                    
                                    <?php if(Gate::allows('fakultas.delete')): ?>
                                        <button wire:click="confirmDelete(<?= $item->id ?>)" class="btn btn-sm btn-icon btn-outline-danger" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg>
                                        </button>
                                    
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-secondary">Menampilkan <?= $this->fakultasList()->firstItem() ?> - <?= $this->fakultasList()->lastItem() ?> dari <?= $this->fakultasList()->total() ?></p>
            <div class="ms-auto"><?= $this->fakultasList()->links() ?></div>
        </div>
    </div>

    <?php if($showModal): ?>
        <div class="modal modal-blur fade show" tabindex="-1" style="display:block" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?= $editId ? 'Edit' : 'Tambah' ?> Fakultas</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit="save">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label required">Nama</label>
                                <input type="text" wire:model="name" class="form-control" placeholder="Nama Fakultas">
                                <div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('name') ?></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">Kode</label>
                                <input type="text" wire:model="code" class="form-control" placeholder="Kode Fakultas">
                                <div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('code') ?></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Dekan</label>
                                <input type="text" wire:model="dekan" class="form-control" placeholder="Nama Dekan">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Akreditasi</label>
                                <input type="text" wire:model="akreditasi" class="form-control" placeholder="Akreditasi">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link link-secondary" wire:click="closeModal">Batal</button>
                            <button type="submit" class="btn btn-primary ms-auto">
                                <span wire:loading.remove wire:target="save"><?= $editId ? 'Simpan' : 'Tambah' ?></span>
                                <span wire:loading wire:target="save">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    

    <?php if($showDeleteConfirm): ?>
        <div class="modal modal-blur fade show" tabindex="-1" style="display:block" role="dialog">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-status bg-danger"></div>
                    <div class="modal-body text-center py-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-danger mb-2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                        <h3>Konfirmasi Hapus</h3>
                        <p class="text-secondary">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <div class="modal-footer">
                        <div class="w-100">
                            <div class="row">
                                <div class="col"><button class="btn btn-ghost-secondary w-100" wire:click="cancelDelete">Batal</button></div>
                                <div class="col"><button class="btn btn-danger w-100" wire:click="delete">Hapus</button></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    

    <?php if(session()->has('message')): ?>
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
            <div class="toast show" role="alert">
                <div class="toast-header">
                    <strong class="me-auto">Berhasil</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body"><?= session('message') ?></div>
            </div>
        </div>
    
</div>


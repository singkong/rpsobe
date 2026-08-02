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

?>

<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Dosen</h3>
            <div class="card-actions">
                <?php if(Gate::allows('dosen.create')): ?>
                    <button wire:click="openCreate" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                        Tambah
                    </button>
                
            </div>
        </div>
        <div class="card-body border-bottom py-3">
            <div class="d-flex">
                <div class="ms-auto text-secondary">
                    <div class="input-icon">
                        <input type="text" wire:model.live="search" class="form-control" placeholder="Cari dosen...">
                        <span class="input-icon-addon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="10" r="7"/><path d="M21 21l-6-6"/></svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-striped">
                <thead>
                    <tr>
                        <th wire:click="sortBy('nidn')" style="cursor:pointer">NIDN</th>
                        <th wire:click="sortBy('name')" style="cursor:pointer">Nama</th>
                        <th>Jabatan Fungsional</th>
                        <th>Bidang Keahlian</th>
                        <th>Status</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($this->dosenList() as $item): ?>
                        <tr>
                            <td><span class="badge bg-primary-lt"><?= $item->nidn ?></span></td>
                            <td><?= $item->gelar_depan ? $item->gelar_depan . ' ' : '' ?><?= $item->name ?><?= $item->gelar_belakang ? ', ' . $item->gelar_belakang : '' ?></td>
                            <td><?= $item->jabatan_fungsional ?? '-' ?></td>
                            <td><?= $item->bidang_keahlian ?? '-' ?></td>
                            <td>
                                <?php if(Gate::allows('dosen.update')): ?>
                                    <button wire:click="toggleActive(<?= $item->id ?>)" class="badge <?= $item->is_active ? 'bg-success' : 'bg-secondary' ?>" style="border:none; cursor:pointer">
                                        <?= $item->is_active ? 'Aktif' : 'Nonaktif' ?>
                                    </button>
                                <?php else: ?>
                                    <span class="badge <?= $item->is_active ? 'bg-success' : 'bg-secondary' ?>"><?= $item->is_active ? 'Aktif' : 'Nonaktif' ?></span>
                                
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <?php if(Gate::allows('dosen.update')): ?>
                                        <button wire:click="edit(<?= $item->id ?>)" class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/></svg>
                                        </button>
                                    
                                    <?php if(Gate::allows('dosen.delete')): ?>
                                        <button wire:click="confirmDelete(<?= $item->id ?>)" class="btn btn-sm btn-icon btn-outline-danger" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg>
                                        </button>
                                    
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-secondary">Menampilkan <?= $this->dosenList()->firstItem() ?> - <?= $this->dosenList()->lastItem() ?> dari <?= $this->dosenList()->total() ?></p>
            <div class="ms-auto"><?= $this->dosenList()->links() ?></div>
        </div>
    </div>

    <?php if($showModal): ?>
        <div class="modal modal-blur fade show" tabindex="-1" style="display:block" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?= $editId ? 'Edit' : 'Tambah' ?> Dosen</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit="save">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">NIDN</label>
                                    <input type="text" wire:model="nidn" class="form-control" placeholder="Nomor NIDN">
                                    <div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('nidn') ?></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Nama</label>
                                    <input type="text" wire:model="name" class="form-control" placeholder="Nama Dosen">
                                    <div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('name') ?></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Gelar Depan</label>
                                    <input type="text" wire:model="gelar_depan" class="form-control" placeholder="Dr.">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Gelar Belakang</label>
                                    <input type="text" wire:model="gelar_belakang" class="form-control" placeholder="M.Kom">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jabatan Fungsional</label>
                                <input type="text" wire:model="jabatan_fungsional" class="form-control" placeholder="Lektor Kepala">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Bidang Keahlian</label>
                                <input type="text" wire:model="bidang_keahlian" class="form-control" placeholder="Bidang Keahlian">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" wire:model="email" class="form-control" placeholder="email@example.com">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Telepon</label>
                                    <input type="text" wire:model="phone" class="form-control" placeholder="Nomor Telepon">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" wire:model="is_active">
                                    <span class="form-check-label">Dosen Aktif</span>
                                </label>
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
                        <p class="text-secondary">Apakah Anda yakin ingin menghapus data dosen ini?</p>
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
                <div class="toast-header"><strong class="me-auto">Berhasil</strong><button type="button" class="btn-close" data-bs-dismiss="toast"></button></div>
                <div class="toast-body"><?= session('message') ?></div>
            </div>
        </div>
    
</div>


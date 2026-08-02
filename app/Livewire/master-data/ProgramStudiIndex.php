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

?>

<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Program Studi</h3>
            <div class="card-actions">
                <?php if(Gate::allows('program-studi.create')): ?>
                    <button wire:click="openCreate" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                        Tambah
                    </button>
                
            </div>
        </div>
        <div class="card-body border-bottom py-3">
            <div class="d-flex gap-2">
                <select wire:model.live="filterFakultasId" class="form-select w-auto">
                    <option value="">Semua Fakultas</option>
                    <?php foreach($this->fakultasOptions() as $f): ?>
                        <option value="<?= $f->id ?>"><?= $f->name ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="ms-auto text-secondary">
                    <div class="input-icon">
                        <input type="text" wire:model.live="search" class="form-control" placeholder="Cari prodi...">
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
                        <th wire:click="sortBy('name')" style="cursor:pointer">Nama</th>
                        <th wire:click="sortBy('code')" style="cursor:pointer">Kode</th>
                        <th>Fakultas</th>
                        <th>Jenjang</th>
                        <th>Akreditasi</th>
                        <th>Kaprodi</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($this->prodiList() as $item): ?>
                        <tr>
                            <td><?= $item->name ?></td>
                            <td><span class="badge bg-primary-lt"><?= $item->code ?></span></td>
                            <td><?= $item->fakultas?->name ?? '-' ?></td>
                            <td><span class="badge bg-azure-lt"><?= $item->jenjang?->label() ?? $item->jenjang ?></span></td>
                            <td><?= $item->akreditasi ?? '-' ?></td>
                            <td><?= $item->kaprodi_name ?? '-' ?></td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <?php if(Gate::allows('program-studi.update')): ?>
                                        <button wire:click="edit(<?= $item->id ?>)" class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/></svg>
                                        </button>
                                    
                                    <?php if(Gate::allows('program-studi.delete')): ?>
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
            <p class="m-0 text-secondary">Menampilkan <?= $this->prodiList()->firstItem() ?> - <?= $this->prodiList()->lastItem() ?> dari <?= $this->prodiList()->total() ?></p>
            <div class="ms-auto"><?= $this->prodiList()->links() ?></div>
        </div>
    </div>

    <?php if($showModal): ?>
        <div class="modal modal-blur fade show" tabindex="-1" style="display:block" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?= $editId ? 'Edit' : 'Tambah' ?> Program Studi</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit="save">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label required">Fakultas</label>
                                <select wire:model="fakultas_id" class="form-select is-invalid">
                                    <option value="">Pilih Fakultas</option>
                                    <?php foreach($this->fakultasOptions() as $f): ?>
                                        <option value="<?= $f->id ?>"><?= $f->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('fakultas_id') ?></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">Nama</label>
                                <input type="text" wire:model="name" class="form-control" placeholder="Nama Program Studi">
                                <div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('name') ?></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">Kode</label>
                                <input type="text" wire:model="code" class="form-control" placeholder="Kode Prodi">
                                <div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('code') ?></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">Jenjang</label>
                                <select wire:model="jenjang" class="form-select is-invalid">
                                    <option value="">Pilih Jenjang</option>
                                    <?php foreach($this->jenjangOptions() as $j): ?>
                                        <option value="<?= $j['value'] ?>"><?= $j['label'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('jenjang') ?></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Akreditasi</label>
                                <input type="text" wire:model="akreditasi" class="form-control" placeholder="Akreditasi">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kaprodi</label>
                                <input type="text" wire:model="kaprodi_name" class="form-control" placeholder="Nama Kaprodi">
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
                        <p class="text-secondary">Apakah Anda yakin ingin menghapus data ini?</p>
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


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

?>

<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Capaian Pembelajaran Lulusan (CPL)</h3>
            <div class="card-actions">
                <?php if(Gate::allows('cpl.create')): ?>
                    <button wire:click="openCreate" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                        Tambah
                    </button>
                
            </div>
        </div>
        <div class="card-body border-bottom py-3">
            <div class="d-flex gap-2">
                <select wire:model.live="filterProdiId" class="form-select w-auto">
                    <option value="">Semua Prodi</option>
                    <?php foreach($this->prodiOptions() as $p): ?>
                        <option value="<?= $p->id ?>"><?= $p->name ?></option>
                    <?php endforeach; ?>
                </select>
                <select wire:model.live="filterKategori" class="form-select w-auto">
                    <option value="">Semua Kategori</option>
                    <?php foreach($this->kategoriOptions() as $k): ?>
                        <option value="<?= $k['value'] ?>"><?= $k['label'] ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="ms-auto text-secondary">
                    <div class="input-icon">
                        <input type="text" wire:model.live="search" class="form-control" placeholder="Cari CPL...">
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
                        <th wire:click="sortBy('code')" style="cursor:pointer">Kode</th>
                        <th>Deskripsi</th>
                        <th wire:click="sortBy('kategori')" style="cursor:pointer">Kategori</th>
                        <th>Prodi</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($this->cplList() as $item): ?>
                        <tr>
                            <td><span class="badge bg-primary-lt"><?= $item->code ?></span></td>
                            <td><?= \Illuminate\Support\Str::limit($item->deskripsi, 80) ?></td>
                            <td>
                                <?php
                                    $kategoriColors = ['S' => 'bg-green-lt', 'P' => 'bg-blue-lt', 'KU' => 'bg-yellow-lt', 'KK' => 'bg-purple-lt'];
                                    $kategori = $item->kategori instanceof \App\Enums\CPKategori ? $item->kategori->value : $item->kategori;
                                    $kategoriLabel = $item->kategori instanceof \App\Enums\CPKategori ? $item->kategori->label() : $item->kategori;
                                ?>
                                <span class="badge <?= $kategoriColors[$kategori] ?? 'bg-secondary-lt' ?>"><?= $kategoriLabel ?></span>
                            </td>
                            <td><?= $item->programStudi?->name ?? '-' ?></td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <?php if(Gate::allows('cpl.update')): ?>
                                        <button wire:click="edit(<?= $item->id ?>)" class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/></svg>
                                        </button>
                                    
                                    <?php if(Gate::allows('cpl.delete')): ?>
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
            <p class="m-0 text-secondary">Menampilkan <?= $this->cplList()->firstItem() ?> - <?= $this->cplList()->lastItem() ?> dari <?= $this->cplList()->total() ?></p>
            <div class="ms-auto"><?= $this->cplList()->links() ?></div>
        </div>
    </div>

    <?php if($showModal): ?>
        <div class="modal modal-blur fade show" tabindex="-1" style="display:block" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?= $editId ? 'Edit' : 'Tambah' ?> CPL</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit="save">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label required">Program Studi</label>
                                <select wire:model="program_studi_id" class="form-select : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('program_studi_id') ?></div> is-invalid ">
                                    <option value="">Pilih Program Studi</option>
                                    <?php foreach($this->prodiOptions() as $p): ?>
                                        <option value="<?= $p->id ?>"><?= $p->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                                : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('program_studi_id') ?></div>  
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Kode</label>
                                    <input type="text" wire:model="code" class="form-control : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('code') ?></div> is-invalid " placeholder="CPL-S1">
                                    : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('code') ?></div>  
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Kategori</label>
                                    <select wire:model="kategori" class="form-select : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('kategori') ?></div> is-invalid ">
                                        <option value="">Pilih Kategori</option>
                                        <?php foreach($this->kategoriOptions() as $k): ?>
                                            <option value="<?= $k['value'] ?>"><?= $k['label'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('kategori') ?></div>  
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">Deskripsi</label>
                                <textarea wire:model="deskripsi" class="form-control : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('deskripsi') ?></div> is-invalid " rows="3" placeholder="Deskripsi CPL"></textarea>
                                : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('deskripsi') ?></div>  
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
                        <p class="text-secondary">Apakah Anda yakin ingin menghapus CPL ini? CPL yang digunakan di RPS tidak dapat dihapus.</p>
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
    

    <?php if(session()->has('error')): ?>
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
            <div class="toast show bg-danger text-white" role="alert">
                <div class="toast-header bg-danger text-white"><strong class="me-auto">Error</strong><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button></div>
                <div class="toast-body"><?= session('error') ?></div>
            </div>
        </div>
    
</div>





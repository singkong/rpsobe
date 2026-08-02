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

?>

<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Mata Kuliah</h3>
            <div class="card-actions">
                <?php if(Gate::allows('mata-kuliah.create')): ?>
                    <button wire:click="openCreate" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                        Tambah
                    </button>
                
            </div>
        </div>
        <div class="card-body border-bottom py-3">
            <div class="d-flex">
                <div class="text-secondary">
                    <?php if(Gate::allows('mata-kuliah.delete')): ?>
                        <button wire:click="bulkDelete" class="btn btn-outline-danger btn-sm" <?php if(count($selectedItems) === 0): ?>disabled >
                            Hapus Terpilih (<?= count($selectedItems) ?>)
                        </button>
                    
                </div>
                <div class="ms-auto text-secondary">
                    <div class="input-icon">
                        <input type="text" wire:model.live="search" class="form-control" placeholder="Cari mata kuliah...">
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
                        <th class="w-1"><input type="checkbox" class="form-check-input" wire:model.live="selectedItems" wire:click="$toggleSelectAll"></th>
                        <th wire:click="sortBy('code')" style="cursor:pointer">Kode</th>
                        <th wire:click="sortBy('name')" style="cursor:pointer">Nama</th>
                        <th>SKS</th>
                        <th>Semester</th>
                        <th>Jenis</th>
                        <th>Kurikulum</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($this->mataKuliahList() as $item): ?>
                        <tr>
                            <td><input type="checkbox" class="form-check-input" wire:model.live="selectedItems" value="<?= $item->id ?>"></td>
                            <td><span class="badge bg-primary-lt"><?= $item->code ?></span></td>
                            <td><?= $item->name ?></td>
                            <td><?= $item->sks ?></td>
                            <td><?= $item->semester ?></td>
                            <td><span class="badge <?= $item->jenis === 'wajib' ? 'bg-azure-lt' : 'bg-orange-lt' ?>"><?= $item->jenis === 'wajib' ? 'Wajib' : 'Pilihan' ?></span></td>
                            <td><?= $item->kurikulum?->name ?? '-' ?></td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <?php if(Gate::allows('mata-kuliah.update')): ?>
                                        <button wire:click="edit(<?= $item->id ?>)" class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/></svg>
                                        </button>
                                    
                                    <?php if(Gate::allows('mata-kuliah.delete')): ?>
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
            <p class="m-0 text-secondary">Menampilkan <?= $this->mataKuliahList()->firstItem() ?> - <?= $this->mataKuliahList()->lastItem() ?> dari <?= $this->mataKuliahList()->total() ?></p>
            <div class="ms-auto"><?= $this->mataKuliahList()->links() ?></div>
        </div>
    </div>

    <?php if($showModal): ?>
        <div class="modal modal-blur fade show" tabindex="-1" style="display:block" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?= $editId ? 'Edit' : 'Tambah' ?> Mata Kuliah</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit="save">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label required">Kurikulum</label>
                                <select wire:model="kurikulum_id" class="form-select : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('kurikulum_id') ?></div> is-invalid ">
                                    <option value="">Pilih Kurikulum</option>
                                    <?php foreach($this->kurikulumOptions() as $k): ?>
                                        <option value="<?= $k->id ?>"><?= $k->name ?> (<?= $k->programStudi?->name ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                                : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('kurikulum_id') ?></div>  
                            </div>
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label required">Nama Mata Kuliah</label>
                                    <input type="text" wire:model="name" class="form-control : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('name') ?></div> is-invalid " placeholder="Nama Mata Kuliah">
                                    : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('name') ?></div>  
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label required">Kode</label>
                                    <input type="text" wire:model="code" class="form-control : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('code') ?></div> is-invalid " placeholder="Kode MK">
                                    : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('code') ?></div>  
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label required">SKS</label>
                                    <input type="number" wire:model="sks" class="form-control : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('sks') ?></div> is-invalid " placeholder="3">
                                    : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('sks') ?></div>  
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label required">Semester</label>
                                    <select wire:model="semester" class="form-select : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('semester') ?></div> is-invalid ">
                                        <option value="">Pilih Semester</option>
                                        <?php for($i = 1; $i <= 8; $i++): ?>
                                            <option value="<?= $i ?>">Semester <?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('semester') ?></div>  
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label required">Jenis</label>
                                    <select wire:model="jenis" class="form-select : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('jenis') ?></div> is-invalid ">
                                        <option value="">Pilih Jenis</option>
                                        <?php foreach($this->jenisOptions() as $j): ?>
                                            <option value="<?= $j['value'] ?>"><?= $j['label'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('jenis') ?></div>  
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea wire:model="deskripsi" class="form-control" rows="3" placeholder="Deskripsi mata kuliah"></textarea>
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
                        <p class="text-secondary">Apakah Anda yakin ingin menghapus <?= $showDeleteConfirm === 'bulk' ? count($selectedItems) . ' mata kuliah' : 'mata kuliah ini' ?>?</p>
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





<?php

use function Livewire\Volt\{state, rules, mount};
use App\Models\Semester;
use App\Enums\SemesterTipe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

state('search', '');
state('sortField', 'tahun_akademik');
state('sortDirection', 'desc');
state('perPage', 10);
state('editId', null);
state('name', '');
state('tipe', '');
state('tahun_akademik', '');
state('tanggal_mulai', '');
state('tanggal_selesai', '');
state('is_active', true);
state('showModal', false);
state('showDeleteConfirm', null);

rules([
    'name' => ['required', 'string', 'max:255'],
    'tipe' => ['required', 'string', 'in:ganjil,genap'],
    'tahun_akademik' => ['required', 'string', 'max:20'],
    'tanggal_mulai' => ['required', 'date'],
    'tanggal_selesai' => ['required', 'date', 'after:tanggal_mulai'],
    'is_active' => ['boolean'],
]);

mount(function () {
    if (!Gate::allows('semester.view-any')) {
        abort(403);
    }
});

$tipeOptions = function () {
    return collect(SemesterTipe::cases())->map(fn($t) => ['value' => $t->value, 'label' => $t->label()]);
};

$semesterList = function () {
    $query = Semester::query();

    if ($this->search) {
        $query->where(function ($q) {
            $q->where('name', 'like', '%' . $this->search . '%')
              ->orWhere('tahun_akademik', 'like', '%' . $this->search . '%');
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
    Gate::authorize('semester.create');
    $this->resetForm();
    $this->showModal = true;
};

$edit = function ($id) {
    Gate::authorize('semester.update');
    $semester = Semester::findOrFail($id);
    $this->editId = $semester->id;
    $this->name = $semester->name;
    $this->tipe = $semester->tipe instanceof SemesterTipe ? $semester->tipe->value : $semester->tipe;
    $this->tahun_akademik = $semester->tahun_akademik;
    $this->tanggal_mulai = $semester->tanggal_mulai->format('Y-m-d');
    $this->tanggal_selesai = $semester->tanggal_selesai->format('Y-m-d');
    $this->is_active = $semester->is_active;
    $this->showModal = true;
};

$save = function () {
    if ($this->editId) {
        Gate::authorize('semester.update');
    } else {
        Gate::authorize('semester.create');
    }

    $validated = $this->validate();

    if (!$this->editId) {
        $validated['tenant_id'] = Auth::user()->tenant_id;
    }

    if ($this->editId) {
        $semester = Semester::findOrFail($this->editId);
        $semester->update($validated);
        session()->flash('message', 'Semester berhasil diperbarui.');
    } else {
        Semester::create($validated);
        session()->flash('message', 'Semester berhasil ditambahkan.');
    }

    $this->showModal = false;
    $this->resetForm();
};

$toggleActive = function ($id) {
    Gate::authorize('semester.update');
    $semester = Semester::findOrFail($id);
    $semester->update(['is_active' => !$semester->is_active]);
    session()->flash('message', 'Status semester berhasil diubah.');
};

$confirmDelete = function ($id) {
    $this->showDeleteConfirm = $id;
};

$delete = function () {
    Gate::authorize('semester.delete');
    $semester = Semester::findOrFail($this->showDeleteConfirm);
    $semester->delete();
    session()->flash('message', 'Semester berhasil dihapus.');
    $this->showDeleteConfirm = null;
};

$cancelDelete = function () {
    $this->showDeleteConfirm = null;
};

$resetForm = function () {
    $this->editId = null;
    $this->name = '';
    $this->tipe = '';
    $this->tahun_akademik = '';
    $this->tanggal_mulai = '';
    $this->tanggal_selesai = '';
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
            <h3 class="card-title">Data Semester</h3>
            <div class="card-actions">
                <?php if(Gate::allows('semester.create')): ?>
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
                        <input type="text" wire:model.live="search" class="form-control" placeholder="Cari semester...">
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
                        <th>Tipe</th>
                        <th wire:click="sortBy('tahun_akademik')" style="cursor:pointer">Tahun Akademik</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($this->semesterList(): ?> as $item)
                        <tr>
                            <td><?= $item->name ?></td>
                            <td><span class="badge <?= $item->tipe === 'ganjil' ? 'bg-azure-lt' : 'bg-green-lt' ?>"><?= $item->tipe instanceof \App\Enums\SemesterTipe ? $item->tipe->label() : ($item->tipe === 'ganjil' ? 'Ganjil' : 'Genap') ?></span></td>
                            <td><?= $item->tahun_akademik ?></td>
                            <td><?= $item->tanggal_mulai?->format('d/m/Y') ?> - <?= $item->tanggal_selesai?->format('d/m/Y') ?></td>
                            <td>
                                <?php if(Gate::allows('semester.update')): ?>
                                    <button wire:click="toggleActive(<?= $item->id ?>)" class="badge <?= $item->is_active ? 'bg-success' : 'bg-secondary' ?>" style="border:none; cursor:pointer">
                                        <?= $item->is_active ? 'Aktif' : 'Nonaktif' ?>
                                    </button>
                                <?php else: ?>
                                    <span class="badge <?= $item->is_active ? 'bg-success' : 'bg-secondary' ?>"><?= $item->is_active ? 'Aktif' : 'Nonaktif' ?></span>
                                
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <?php if(Gate::allows('semester.update')): ?>
                                        <button wire:click="edit(<?= $item->id ?>)" class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/></svg>
                                        </button>
                                    
                                    <?php if(Gate::allows('semester.delete')): ?>
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
            <p class="m-0 text-secondary">Menampilkan <?= $this->semesterList()->firstItem() ?> - <?= $this->semesterList()->lastItem() ?> dari <?= $this->semesterList()->total() ?></p>
            <div class="ms-auto"><?= $this->semesterList()->links() ?></div>
        </div>
    </div>

    <?php if($showModal): ?>
        <div class="modal modal-blur fade show" tabindex="-1" style="display:block" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?= $editId ? 'Edit' : 'Tambah' ?> Semester</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit="save">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label required">Nama Semester</label>
                                <input type="text" wire:model="name" class="form-control : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('name') ?></div> is-invalid " placeholder="Semester Ganjil 2024/2025">
                                : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('name') ?></div>  
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">Tipe</label>
                                <select wire:model="tipe" class="form-select : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('tipe') ?></div> is-invalid ">
                                    <option value="">Pilih Tipe</option>
                                    <?php foreach($this->tipeOptions(): ?> as $t)
                                        <option value="<?= $t['value'] ?>"><?= $t['label'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('tipe') ?></div>  
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">Tahun Akademik</label>
                                <input type="text" wire:model="tahun_akademik" class="form-control : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('tahun_akademik') ?></div> is-invalid " placeholder="2024/2025">
                                : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('tahun_akademik') ?></div>  
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Tanggal Mulai</label>
                                    <input type="date" wire:model="tanggal_mulai" class="form-control : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('tanggal_mulai') ?></div> is-invalid ">
                                    : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('tanggal_mulai') ?></div>  
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Tanggal Selesai</label>
                                    <input type="date" wire:model="tanggal_selesai" class="form-control : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('tanggal_selesai') ?></div> is-invalid ">
                                    : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('tanggal_selesai') ?></div>  
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" wire:model="is_active">
                                    <span class="form-check-label">Semester Aktif</span>
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
                        <p class="text-secondary">Apakah Anda yakin ingin menghapus semester ini?</p>
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
    

    <?php if(session(): ?>->has('message'))
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
            <div class="toast show" role="alert">
                <div class="toast-header"><strong class="me-auto">Berhasil</strong><button type="button" class="btn-close" data-bs-dismiss="toast"></button></div>
                <div class="toast-body"><?= session('message') ?></div>
            </div>
        </div>
    
</div>

?>

<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Semester</h3>
            <div class="card-actions">
                <?php if(Gate::allows('semester.create')): ?>
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
                        <input type="text" wire:model.live="search" class="form-control" placeholder="Cari semester...">
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
                        <th>Tipe</th>
                        <th wire:click="sortBy('tahun_akademik')" style="cursor:pointer">Tahun Akademik</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($this->semesterList() as $item): ?>
                        <tr>
                            <td><?= $item->name ?></td>
                            <td><span class="badge <?= $item->tipe === 'ganjil' ? 'bg-azure-lt' : 'bg-green-lt' ?>"><?= $item->tipe instanceof \App\Enums\SemesterTipe ? $item->tipe->label() : ($item->tipe === 'ganjil' ? 'Ganjil' : 'Genap') ?></span></td>
                            <td><?= $item->tahun_akademik ?></td>
                            <td><?= $item->tanggal_mulai?->format('d/m/Y') ?> - <?= $item->tanggal_selesai?->format('d/m/Y') ?></td>
                            <td>
                                <?php if(Gate::allows('semester.update')): ?>
                                    <button wire:click="toggleActive(<?= $item->id ?>)" class="badge <?= $item->is_active ? 'bg-success' : 'bg-secondary' ?>" style="border:none; cursor:pointer">
                                        <?= $item->is_active ? 'Aktif' : 'Nonaktif' ?>
                                    </button>
                                <?php else: ?>
                                    <span class="badge <?= $item->is_active ? 'bg-success' : 'bg-secondary' ?>"><?= $item->is_active ? 'Aktif' : 'Nonaktif' ?></span>
                                
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <?php if(Gate::allows('semester.update')): ?>
                                        <button wire:click="edit(<?= $item->id ?>)" class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/></svg>
                                        </button>
                                    
                                    <?php if(Gate::allows('semester.delete')): ?>
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
            <p class="m-0 text-secondary">Menampilkan <?= $this->semesterList()->firstItem() ?> - <?= $this->semesterList()->lastItem() ?> dari <?= $this->semesterList()->total() ?></p>
            <div class="ms-auto"><?= $this->semesterList()->links() ?></div>
        </div>
    </div>

    <?php if($showModal): ?>
        <div class="modal modal-blur fade show" tabindex="-1" style="display:block" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?= $editId ? 'Edit' : 'Tambah' ?> Semester</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit="save">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label required">Nama Semester</label>
                                <input type="text" wire:model="name" class="form-control : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('name') ?></div> is-invalid " placeholder="Semester Ganjil 2024/2025">
                                : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('name') ?></div>  
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">Tipe</label>
                                <select wire:model="tipe" class="form-select : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('tipe') ?></div> is-invalid ">
                                    <option value="">Pilih Tipe</option>
                                    <?php foreach($this->tipeOptions() as $t): ?>
                                        <option value="<?= $t['value'] ?>"><?= $t['label'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('tipe') ?></div>  
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">Tahun Akademik</label>
                                <input type="text" wire:model="tahun_akademik" class="form-control : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('tahun_akademik') ?></div> is-invalid " placeholder="2024/2025">
                                : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('tahun_akademik') ?></div>  
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Tanggal Mulai</label>
                                    <input type="date" wire:model="tanggal_mulai" class="form-control : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('tanggal_mulai') ?></div> is-invalid ">
                                    : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('tanggal_mulai') ?></div>  
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Tanggal Selesai</label>
                                    <input type="date" wire:model="tanggal_selesai" class="form-control : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('tanggal_selesai') ?></div> is-invalid ">
                                    : ?><div class="invalid-feedback"><?= (isset(`$errors) ? `$errors->first('tanggal_selesai') ?></div>  
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" wire:model="is_active">
                                    <span class="form-check-label">Semester Aktif</span>
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
                        <p class="text-secondary">Apakah Anda yakin ingin menghapus semester ini?</p>
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





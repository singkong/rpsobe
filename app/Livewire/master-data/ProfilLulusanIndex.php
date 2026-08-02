<?php

use function Livewire\Volt\{state, rules, mount};
use App\Models\ProfilLulusan;
use App\Models\ProgramStudi;
use App\Models\CPL;
use Illuminate\Support\Facades\Gate;

state('search', '');
state('sortField', 'name');
state('sortDirection', 'asc');
state('perPage', 10);
state('editId', null);
state('program_studi_id', '');
state('name', '');
state('deskripsi', '');
state('selectedCpls', []);
state('showModal', false);
state('showDeleteConfirm', null);

rules([
    'program_studi_id' => ['required', 'exists:program_studi,id'],
    'name' => ['required', 'string', 'max:255'],
    'deskripsi' => ['nullable', 'string'],
    'selectedCpls' => ['nullable', 'array'],
]);

mount(function () {
    if (!Gate::allows('profil-lulusan.view-any')) {
        abort(403);
    }
});

$prodiOptions = function () {
    return ProgramStudi::orderBy('name')->get();
};

$cplOptions = function () {
    return CPL::orderBy('code')->get();
};

$profilList = function () {
    $query = ProfilLulusan::with(['programStudi', 'cpls']);

    if ($this->search) {
        $query->where(function ($q) {
            $q->where('name', 'like', '%' . $this->search . '%')
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
    Gate::authorize('profil-lulusan.create');
    $this->resetForm();
    $this->showModal = true;
};

$edit = function ($id) {
    Gate::authorize('profil-lulusan.update');
    $profil = ProfilLulusan::with('cpls')->findOrFail($id);
    $this->editId = $profil->id;
    $this->program_studi_id = $profil->program_studi_id;
    $this->name = $profil->name;
    $this->deskripsi = $profil->deskripsi;
    $this->selectedCpls = $profil->cpls->pluck('id')->toArray();
    $this->showModal = true;
};

$save = function () {
    if ($this->editId) {
        Gate::authorize('profil-lulusan.update');
    } else {
        Gate::authorize('profil-lulusan.create');
    }

    $validated = $this->validate();

    if ($this->editId) {
        $profil = ProfilLulusan::findOrFail($this->editId);
        $profil->update([
            'program_studi_id' => $validated['program_studi_id'],
            'name' => $validated['name'],
            'deskripsi' => $validated['deskripsi'],
        ]);
    } else {
        $profil = ProfilLulusan::create([
            'program_studi_id' => $validated['program_studi_id'],
            'name' => $validated['name'],
            'deskripsi' => $validated['deskripsi'],
        ]);
    }

    $profil->cpls()->sync($this->selectedCpls ?? []);

    session()->flash('message', $this->editId ? 'Profil Lulusan berhasil diperbarui.' : 'Profil Lulusan berhasil ditambahkan.');

    $this->showModal = false;
    $this->resetForm();
};

$confirmDelete = function ($id) {
    $this->showDeleteConfirm = $id;
};

$delete = function () {
    Gate::authorize('profil-lulusan.delete');
    $profil = ProfilLulusan::findOrFail($this->showDeleteConfirm);
    $profil->cpls()->detach();
    $profil->delete();
    session()->flash('message', 'Profil Lulusan berhasil dihapus.');
    $this->showDeleteConfirm = null;
};

$cancelDelete = function () {
    $this->showDeleteConfirm = null;
};

$resetForm = function () {
    $this->editId = null;
    $this->program_studi_id = '';
    $this->name = '';
    $this->deskripsi = '';
    $this->selectedCpls = [];
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
            <h3 class="card-title">Data Profil Lulusan</h3>
            <div class="card-actions">
                @can('profil-lulusan.create')
                    <button wire:click="openCreate" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                        Tambah
                    </button>
                @endcan
            </div>
        </div>
        <div class="card-body border-bottom py-3">
            <div class="d-flex">
                <div class="ms-auto text-secondary">
                    <div class="input-icon">
                        <input type="text" wire:model.live="search" class="form-control" placeholder="Cari profil lulusan...">
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
                        <th>Deskripsi</th>
                        <th>Prodi</th>
                        <th>CPL</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->profilList() as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($item->deskripsi, 60) }}</td>
                            <td>{{ $item->programStudi?->name ?? '-' }}</td>
                            <td><span class="badge bg-blue-lt">{{ $item->cpls->count() }} CPL</span></td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    @can('profil-lulusan.update')
                                        <button wire:click="edit({{ $item->id }})" class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/></svg>
                                        </button>
                                    @endcan
                                    @can('profil-lulusan.delete')
                                        <button wire:click="confirmDelete({{ $item->id }})" class="btn btn-sm btn-icon btn-outline-danger" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-secondary">Menampilkan {{ $this->profilList()->firstItem() }} - {{ $this->profilList()->lastItem() }} dari {{ $this->profilList()->total() }}</p>
            <div class="ms-auto">{{ $this->profilList()->links() }}</div>
        </div>
    </div>

    @if($showModal)
        <div class="modal modal-blur fade show" tabindex="-1" style="display:block" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $editId ? 'Edit' : 'Tambah' }} Profil Lulusan</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit="save">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label required">Program Studi</label>
                                <select wire:model="program_studi_id" class="form-select @error('program_studi_id') is-invalid @enderror">
                                    <option value="">Pilih Program Studi</option>
                                    @foreach($this->prodiOptions() as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                                @error('program_studi_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">Nama Profil</label>
                                <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama Profil Lulusan">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea wire:model="deskripsi" class="form-control" rows="3" placeholder="Deskripsi profil lulusan"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kaitkan dengan CPL</label>
                                <div class="row">
                                    @foreach($this->cplOptions() as $cpl)
                                        <div class="col-md-6">
                                            <label class="form-check">
                                                <input type="checkbox" wire:model="selectedCpls" value="{{ $cpl->id }}" class="form-check-input">
                                                <span class="form-check-label"><strong>{{ $cpl->code }}</strong> - {{ \Illuminate\Support\Str::limit($cpl->deskripsi, 50) }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-link link-secondary" wire:click="closeModal">Batal</button>
                            <button type="submit" class="btn btn-primary ms-auto">
                                <span wire:loading.remove wire:target="save">{{ $editId ? 'Simpan' : 'Tambah' }}</span>
                                <span wire:loading wire:target="save">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    @if($showDeleteConfirm)
        <div class="modal modal-blur fade show" tabindex="-1" style="display:block" role="dialog">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-status bg-danger"></div>
                    <div class="modal-body text-center py-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-danger mb-2"><path stroke="none" d="M0 0h24v24H0z"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                        <h3>Konfirmasi Hapus</h3>
                        <p class="text-secondary">Apakah Anda yakin ingin menghapus profil lulusan ini?</p>
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
    @endif

    @if(session()->has('message'))
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
            <div class="toast show" role="alert">
                <div class="toast-header"><strong class="me-auto">Berhasil</strong><button type="button" class="btn-close" data-bs-dismiss="toast"></button></div>
                <div class="toast-body">{{ session('message') }}</div>
            </div>
        </div>
    @endif
</div>


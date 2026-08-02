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

?>

<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Referensi</h3>
            <div class="card-actions">
                @can('referensi.create')
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
                        <input type="text" wire:model.live="search" class="form-control" placeholder="Cari referensi...">
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
                        <th wire:click="sortBy('judul')" style="cursor:pointer">Judul</th>
                        <th>Penulis</th>
                        <th>Tahun</th>
                        <th>Penerbit</th>
                        <th>Format</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->referensiList() as $item)
                        <tr>
                            <td>{{ \Illuminate\Support\Str::limit($item->judul, 50) }}</td>
                            <td>{{ $item->penulis ?? '-' }}</td>
                            <td>{{ $item->tahun ?? '-' }}</td>
                            <td>{{ $item->penerbit ?? '-' }}</td>
                            <td>
                                <span class="badge {{ match($item->format) { 'buku' => 'bg-primary-lt', 'jurnal' => 'bg-azure-lt', 'artikel' => 'bg-green-lt', 'website' => 'bg-yellow-lt', default => 'bg-secondary-lt' } }}">
                                    {{ match($item->format) { 'buku' => 'Buku', 'jurnal' => 'Jurnal', 'artikel' => 'Artikel', 'website' => 'Website', default => 'Lainnya' } }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    @can('referensi.update')
                                        <button wire:click="edit({{ $item->id }})" class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/><path d="M16 5l3 3"/></svg>
                                        </button>
                                    @endcan
                                    @can('referensi.delete')
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
            <p class="m-0 text-secondary">Menampilkan {{ $this->referensiList()->firstItem() }} - {{ $this->referensiList()->lastItem() }} dari {{ $this->referensiList()->total() }}</p>
            <div class="ms-auto">{{ $this->referensiList()->links() }}</div>
        </div>
    </div>

    @if($showModal)
        <div class="modal modal-blur fade show" tabindex="-1" style="display:block" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $editId ? 'Edit' : 'Tambah' }} Referensi</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit="save">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label required">Judul</label>
                                <input type="text" wire:model="judul" class="form-control @error('judul') is-invalid @enderror" placeholder="Judul Referensi">
                                @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Penulis</label>
                                <input type="text" wire:model="penulis" class="form-control" placeholder="Nama Penulis">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tahun</label>
                                    <input type="text" wire:model="tahun" class="form-control" placeholder="2024">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">Format</label>
                                    <select wire:model="format" class="form-select @error('format') is-invalid @enderror">
                                        <option value="">Pilih Format</option>
                                        @foreach($this->formatOptions() as $f)
                                            <option value="{{ $f['value'] }}">{{ $f['label'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('format') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Penerbit</label>
                                <input type="text" wire:model="penerbit" class="form-control" placeholder="Nama Penerbit">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">URL</label>
                                <input type="url" wire:model="url" class="form-control" placeholder="https://...">
                                @error('url') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                        <p class="text-secondary">Apakah Anda yakin ingin menghapus referensi ini?</p>
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


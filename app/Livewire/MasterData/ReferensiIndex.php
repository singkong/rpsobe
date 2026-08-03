<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Referensi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ReferensiIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'judul';
    public string $sortDirection = 'asc';
    public int $perPage = 10;

    public ?int $editId = null;
    public string $judul = '';
    public string $penulis = '';
    public string $tahun = '';
    public string $penerbit = '';
    public string $format = '';
    public string $url = '';
    public bool $showModal = false;
    public $showDeleteConfirm = null;

    protected function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'penulis' => ['nullable', 'string', 'max:255'],
            'tahun' => ['nullable', 'string', 'max:10'],
            'penerbit' => ['nullable', 'string', 'max:255'],
            'format' => ['required', 'string', 'in:buku,jurnal,artikel,website,lainnya'],
            'url' => ['nullable', 'url', 'max:255'],
        ];
    }

    public function mount(): void
    {
        
    }

    public function formatOptions()
    {
        return [
            ['value' => 'buku', 'label' => 'Buku'],
            ['value' => 'jurnal', 'label' => 'Jurnal'],
            ['value' => 'artikel', 'label' => 'Artikel'],
            ['value' => 'website', 'label' => 'Website'],
            ['value' => 'lainnya', 'label' => 'Lainnya'],
        ];
    }

    public function referensiList()
    {
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
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function openCreate(): void
    {
        Gate::authorize('referensi.create');
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        Gate::authorize('referensi.update');
        $ref = Referensi::findOrFail($id);
        $this->editId = $ref->id;
        $this->judul = $ref->judul;
        $this->penulis = $ref->penulis ?? '';
        $this->tahun = $ref->tahun ?? '';
        $this->penerbit = $ref->penerbit ?? '';
        $this->format = $ref->format;
        $this->url = $ref->url ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
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
    }

    public function confirmDelete(int $id): void
    {
        $this->showDeleteConfirm = $id;
    }

    public function delete(): void
    {
        Gate::authorize('referensi.delete');
        $ref = Referensi::findOrFail($this->showDeleteConfirm);
        $ref->delete();
        session()->flash('message', 'Referensi berhasil dihapus.');
        $this->showDeleteConfirm = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = null;
    }

    public function resetForm(): void
    {
        $this->editId = null;
        $this->judul = '';
        $this->penulis = '';
        $this->tahun = '';
        $this->penerbit = '';
        $this->format = '';
        $this->url = '';
        $this->resetValidation();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.master-data.referensi-index');
    }
}

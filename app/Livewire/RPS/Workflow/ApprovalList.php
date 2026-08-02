<?php

use function Livewire\Volt\{state, mount};
use App\Enums\RPSStatus;
use App\Models\RPS;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Auth;

state('search', '');
state('filterStatus', '');
state('sortField', 'updated_at');
state('sortDirection', 'desc');
state('perPage', 10);
state('showConfirmPublish', null);
state('showConfirmApprove', null);

mount(function () {
    //
});

$approvalList = function () {
    $user = Auth::user();

    $query = RPS::with(['mataKuliah.kurikulum.programStudi', 'semester', 'user', 'reviews.reviewer'])
        ->whereIn('status', [RPSStatus::Review->value, RPSStatus::Approved->value]);

    if ($user->tenant_id && !$user->hasRole('super-admin')) {
        $query->whereHas('mataKuliah.kurikulum.programStudi.fakultas', function ($q) use ($user) {
            $q->where('tenant_id', $user->tenant_id);
        });
    }

    if ($this->search) {
        $query->where(function ($q) {
            $q->whereHas('mataKuliah', function ($q2) {
                $q2->where('name', 'like', '%' . $this->search . '%')
                   ->orWhere('code', 'like', '%' . $this->search . '%');
            })->orWhereHas('user', function ($q2) {
                $q2->where('name', 'like', '%' . $this->search . '%');
            });
        });
    }

    if ($this->filterStatus) {
        $query->where('status', $this->filterStatus);
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

$approve = function ($rpsId) {
    $user = Auth::user();
    $rps = RPS::findOrFail($rpsId);

    $service = app(WorkflowService::class);
    $service->approve($rps, $user);

    session()->flash('message', 'RPS berhasil disetujui.');
};

$publish = function ($rpsId) {
    $user = Auth::user();
    $rps = RPS::findOrFail($rpsId);

    $service = app(WorkflowService::class);
    $service->publish($rps, $user);

    $this->showConfirmPublish = null;
    session()->flash('message', 'RPS berhasil dipublikasi.');
};

$requestRevision = function ($rpsId) {
    return redirect()->route('rps.review', ['rpsId' => $rpsId]);
};

$confirmPublish = function ($rpsId) {
    $this->showConfirmPublish = $rpsId;
};

$confirmApprove = function ($rpsId) {
    $this->showConfirmApprove = $rpsId;
};

$cancelConfirm = function () {
    $this->showConfirmPublish = null;
    $this->showConfirmApprove = null;
};

$archive = function ($rpsId) {
    $user = Auth::user();
    $rps = RPS::findOrFail($rpsId);

    $service = app(WorkflowService::class);
    $service->archive($rps, $user);

    session()->flash('message', 'RPS berhasil diarsipkan.');
};

?>

<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Daftar Persetujuan RPS</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">RPS Menunggu Persetujuan / Publikasi</h3>
                </div>
                <div class="card-body border-bottom py-3">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="input-icon">
                                <input type="text" wire:model.live="search" class="form-control" placeholder="Cari mata kuliah atau dosen...">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="10" r="7"/><path d="M21 21l-6-6"/></svg>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select wire:model.live="filterStatus" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="review">Dalam Review</option>
                                <option value="approved">Disetujui</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-striped">
                        <thead>
                            <tr>
                                <th wire:click="sortBy('mata_kuliah_id')" style="cursor:pointer">Mata Kuliah</th>
                                <th>Dosen</th>
                                <th wire:click="sortBy('status')" style="cursor:pointer">Status</th>
                                <th>Reviewer</th>
                                <th wire:click="sortBy('updated_at')" style="cursor:pointer">Diperbarui</th>
                                <th class="w-1">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $list = $this->approvalList(); @endphp
                            @forelse($list as $rps)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <strong>{{ $rps->mataKuliah->code ?? '-' }}</strong>
                                            <small class="text-secondary">{{ $rps->mataKuliah->name ?? '-' }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $rps->user->name ?? '-' }}</td>
                                    <td><span class="badge bg-{{ $rps->status->color() }}">{{ $rps->status->label() }}</span></td>
                                    <td>
                                        @if($rps->reviews->isNotEmpty())
                                            @foreach($rps->reviews->take(2) as $review)
                                                <span class="badge bg-cyan-lt me-1">{{ $review->reviewer->name ?? 'Unknown' }}</span>
                                            @endforeach
                                        @else
                                            <span class="badge bg-secondary-lt">-</span>
                                        @endif
                                    </td>
                                    <td><small>{{ $rps->updated_at->format('d M Y H:i') }}</small></td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            @if($rps->status === \App\Enums\RPSStatus::Review)
                                                <button wire:click="approve({{ $rps->id }})" class="btn btn-sm btn-success">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12l5 5l10 -10"/></svg>
                                                    Setujui
                                                </button>
                                                <a href="{{ route('rps.review', ['rpsId' => $rps->id]) }}" class="btn btn-sm btn-warning">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h-9"/><path d="M16.793 3.293a1 1 0 0 1 1.414 0l2.5 2.5a1 1 0 0 1 0 1.414l-9 9h-3v-3z"/></svg>
                                                    Minta Revisi
                                                </a>
                                            @elseif($rps->status === \App\Enums\RPSStatus::Approved)
                                                <button wire:click="confirmPublish({{ $rps->id }})" class="btn btn-sm btn-blue">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 6v6l3 3"/></svg>
                                                    Publikasikan
                                                </button>
                                                <a href="{{ route('rps.review', ['rpsId' => $rps->id]) }}" class="btn btn-sm btn-warning">
                                                    Minta Revisi
                                                </a>
                                            @endif
                                            <a href="{{ route('rps.history', ['rpsId' => $rps->id]) }}" class="btn btn-sm btn-ghost-secondary" title="Riwayat">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9 -9a9 9 0 0 0 -9 9"/><path d="M12 7v5l3 3"/></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="empty py-4">
                                            <div class="empty-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M10 3h4a1 1 0 0 1 1 1v3h-6v-3a1 1 0 0 1 1 -1z"/><path d="M5 6h14a2 2 0 0 1 2 2v11a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2z"/></svg>
                                            </div>
                                            <p class="empty-title">Tidak ada RPS menunggu persetujuan</p>
                                            <p class="empty-subtitle text-secondary">RPS yang perlu disetujui atau dipublikasi akan muncul di sini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex align-items-center">
                    <p class="m-0 text-secondary">Menampilkan {{ $list->firstItem() }} - {{ $list->lastItem() }} dari {{ $list->total() }}</p>
                    <div class="ms-auto">{{ $list->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    @if($showConfirmPublish)
        <div class="modal modal-blur fade show" tabindex="-1" style="display:block" role="dialog">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-status bg-blue"></div>
                    <div class="modal-body text-center py-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-blue mb-2"><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 6v6l3 3"/></svg>
                        <h3>Konfirmasi Publikasi</h3>
                        <p class="text-secondary">Apakah Anda yakin ingin mempublikasi RPS ini?</p>
                    </div>
                    <div class="modal-footer">
                        <div class="w-100">
                            <div class="row">
                                <div class="col"><button class="btn btn-ghost-secondary w-100" wire:click="cancelConfirm">Batal</button></div>
                                <div class="col"><button class="btn btn-blue w-100" wire:click="publish({{ $showConfirmPublish }})">Ya, Publikasi</button></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    @if($showConfirmApprove)
        <div class="modal modal-blur fade show" tabindex="-1" style="display:block" role="dialog">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-status bg-success"></div>
                    <div class="modal-body text-center py-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-success mb-2"><path d="M5 12l5 5l10 -10"/></svg>
                        <h3>Konfirmasi Persetujuan</h3>
                        <p class="text-secondary">Apakah Anda yakin ingin menyetujui RPS ini?</p>
                    </div>
                    <div class="modal-footer">
                        <div class="w-100">
                            <div class="row">
                                <div class="col"><button class="btn btn-ghost-secondary w-100" wire:click="cancelConfirm">Batal</button></div>
                                <div class="col"><button class="btn btn-success w-100" wire:click="approve({{ $showConfirmApprove }})">Ya, Setujui</button></div>
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


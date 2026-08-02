<?php

use function Livewire\Volt\{state, mount};
use App\Models\RPS;
use App\Models\User;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Auth;

state('rpsId');
state('rps');
state('selectedReviewerId', '');
state('availableReviewers', []);
state('currentReviewer', null);

mount(function ($rpsId) {
    $this->rpsId = $rpsId;
    $this->rps = RPS::with(['mataKuliah.kurikulum.programStudi'])->findOrFail($rpsId);

    $prodi = $this->rps->mataKuliah?->kurikulum?->programStudi;
    if ($prodi) {
        $service = app(WorkflowService::class);
        $this->availableReviewers = $service->getAvailableReviewers($prodi);
    } else {
        $this->availableReviewers = User::role('reviewer')->where('is_active', true)->get();
    }

    $this->currentReviewer = User::role('reviewer')->first();
});

$assign = function () {
    if (empty($this->selectedReviewerId)) {
        session()->flash('error', 'Pilih reviewer terlebih dahulu.');
        return;
    }

    $user = Auth::user();
    $reviewer = User::findOrFail($this->selectedReviewerId);
    $service = app(WorkflowService::class);

    $service->assignReviewer($this->rps, $reviewer, $user);

    $this->currentReviewer = $reviewer;
    session()->flash('message', 'Reviewer berhasil ditugaskan.');
};

?>

<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Penugasan Reviewer</h2>
                    <div class="text-secondary mt-1">
                        {{ $rps->mataKuliah->code }} - {{ $rps->mataKuliah->name }}
                        <span class="badge bg-{{ $rps->status->color() }} ms-2">{{ $rps->status->label() }}</span>
                    </div>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('approval.list') }}" class="btn btn-ghost-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12l14 0"/><path d="M5 12l4 4"/><path d="M5 12l4 -4"/></svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Reviewer Saat Ini</h3>
                        </div>
                        <div class="card-body">
                            @if($currentReviewer)
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-md bg-primary-lt me-3">{{ strtoupper(substr($currentReviewer->name, 0, 2)) }}</span>
                                    <div>
                                        <div class="font-weight-medium">{{ $currentReviewer->name }}</div>
                                        <div class="text-secondary">{{ $currentReviewer->email }}</div>
                                        <div>
                                            @foreach($currentReviewer->getRoleNames() as $role)
                                                <span class="badge bg-blue-lt me-1">{{ $role }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="empty">
                                    <p class="empty-title">Belum ada reviewer ditugaskan</p>
                                    <p class="empty-subtitle text-secondary">Pilih reviewer dari daftar di bawah ini.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Assign Reviewer</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Pilih Reviewer</label>
                                <select wire:model.live="selectedReviewerId" class="form-select">
                                    <option value="">-- Pilih Reviewer --</option>
                                    @foreach($availableReviewers as $reviewer)
                                        <option value="{{ $reviewer->id }}">
                                            {{ $reviewer->name }} ({{ $reviewer->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button wire:click="assign" class="btn btn-primary w-100" {{ empty($selectedReviewerId) ? 'disabled' : '' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                                Assign Reviewer
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Informasi RPS</h3>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-5">Mata Kuliah</dt>
                                <dd class="col-sm-7">{{ $rps->mataKuliah->code }} - {{ $rps->mataKuliah->name }}</dd>
                                <dt class="col-sm-5">SKS</dt>
                                <dd class="col-sm-7">{{ $rps->mataKuliah->sks }}</dd>
                                <dt class="col-sm-5">Program Studi</dt>
                                <dd class="col-sm-7">{{ $rps->mataKuliah->kurikulum->programStudi->name ?? '-' }}</dd>
                                <dt class="col-sm-5">Dosen</dt>
                                <dd class="col-sm-7">{{ $rps->user->name ?? '-' }}</dd>
                                <dt class="col-sm-5">Status</dt>
                                <dd class="col-sm-7"><span class="badge bg-{{ $rps->status->color() }}">{{ $rps->status->label() }}</span></dd>
                                <dt class="col-sm-5">Versi</dt>
                                <dd class="col-sm-7"><span class="badge bg-primary-lt">{{ $rps->version_label }}</span></dd>
                            </dl>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Daftar Reviewer Tersedia</h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($availableReviewers as $reviewer)
                                        <tr class="{{ $selectedReviewerId == $reviewer->id ? 'bg-primary-lt' : '' }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-sm bg-primary-lt me-2">{{ strtoupper(substr($reviewer->name, 0, 2)) }}</span>
                                                    {{ $reviewer->name }}
                                                </div>
                                            </td>
                                            <td><small>{{ $reviewer->email }}</small></td>
                                            <td>
                                                @foreach($reviewer->getRoleNames() as $role)
                                                    <span class="badge bg-blue-lt">{{ $role }}</span>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3">
                                                <div class="empty py-3">
                                                    <p class="empty-title mb-0">Tidak ada reviewer tersedia</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session()->has('message'))
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
            <div class="toast show" role="alert">
                <div class="toast-header"><strong class="me-auto">Berhasil</strong><button type="button" class="btn-close" data-bs-dismiss="toast"></button></div>
                <div class="toast-body">{{ session('message') }}</div>
            </div>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
            <div class="toast show bg-danger text-white" role="alert">
                <div class="toast-header"><strong class="me-auto">Error</strong><button type="button" class="btn-close" data-bs-dismiss="toast"></button></div>
                <div class="toast-body">{{ session('error') }}</div>
            </div>
        </div>
    @endif
</div>


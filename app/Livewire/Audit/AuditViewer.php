<?php

use function Livewire\Volt\{state, mount, withPagination};
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

withPagination();

state('search', '');
state('filterAction', '');
state('filterModel', '');
state('filterUser', '');
state('filterDateFrom', '');
state('filterDateTo', '');
state('sortField', 'created_at');
state('sortDirection', 'desc');
state('perPage', 15);
state('showDetail', false);
state('selectedAudit', null);

mount(function () {
    //
});

$auditList = function () {
    $query = AuditLog::with('user')->latest();

    if ($this->search) {
        $query->where(function ($q) {
            $q->whereHas('user', function ($q2) {
                $q2->where('name', 'like', '%' . $this->search . '%');
            })
            ->orWhere('action', 'like', '%' . $this->search . '%')
            ->orWhere('model_type', 'like', '%' . $this->search . '%')
            ->orWhere('ip_address', 'like', '%' . $this->search . '%');
        });
    }

    if ($this->filterAction) {
        $query->where('action', $this->filterAction);
    }

    if ($this->filterModel) {
        $query->where('model_type', 'like', '%' . $this->filterModel . '%');
    }

    if ($this->filterDateFrom) {
        $query->whereDate('created_at', '>=', $this->filterDateFrom);
    }

    if ($this->filterDateTo) {
        $query->whereDate('created_at', '<=', $this->filterDateTo);
    }

    if ($this->filterUser) {
        $query->whereHas('user', function ($q) {
            $q->where('name', 'like', '%' . $this->filterUser . '%');
        });
    }

    $query->orderBy($this->sortField, $this->sortDirection);

    return $query->paginate($this->perPage);
};

$actionOptions = function () {
    return [
        '' => 'Semua Aksi',
        'RPSSubmitted' => 'RPS Diajukan',
        'RPSReviewed' => 'RPS Direview',
        'RPSRevisionRequested' => 'Revisi Diminta',
        'RPSApproved' => 'RPS Disetujui',
        'RPSPublished' => 'RPS Dipublikasi',
        'RPSArchived' => 'RPS Diarsipkan',
        'ReviewerAssigned' => 'Reviewer Ditugaskan',
    ];
};

$viewDetail = function ($id) {
    $this->selectedAudit = AuditLog::with('user')->find($id);
    $this->showDetail = true;
};

$closeDetail = function () {
    $this->showDetail = false;
    $this->selectedAudit = null;
};

$sortBy = function ($field) {
    if ($this->sortField === $field) {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        $this->sortField = $field;
        $this->sortDirection = 'asc';
    }
};

$resetFilters = function () {
    $this->search = '';
    $this->filterAction = '';
    $this->filterModel = '';
    $this->filterUser = '';
    $this->filterDateFrom = '';
    $this->filterDateTo = '';
};

?>

<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Audit Log</h2>
                    <div class="text-secondary">
                        <span>Catatan aktivitas sistem</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-body border-bottom py-3">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="input-icon">
                                <input type="text" wire:model.live="search" class="form-control" placeholder="Cari user, aksi, atau IP...">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="10" r="7"/><path d="M21 21l-6-6"/></svg>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select wire:model.live="filterAction" class="form-select">
                                <?php foreach($this->actionOptions() as $value => $label)
                                    <option value="<?= $value ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" wire:model.live="filterDateFrom" class="form-control" placeholder="Dari Tanggal">
                        </div>
                        <div class="col-md-2">
                            <input type="date" wire:model.live="filterDateTo" class="form-control" placeholder="Sampai Tanggal">
                        </div>
                        <div class="col-md-2">
                            <button wire:click="resetFilters" class="btn btn-ghost-secondary w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9 -9a9 9 0 0 0 -9 9"/><path d="M3 21v-4h4"/></svg>
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
                <?php $list = $this->auditList(); ?>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-striped">
                        <thead>
                            <tr>
                                <th wire:click="sortBy('created_at')" style="cursor:pointer">
                                    Waktu
                                    <?php if($sortField === 'created_at')
                                        <small><?= $sortDirection === 'asc' ? '&#9650;' : '&#9660;' ?></small>
                                    <?php endif; ?>
                                </th>
                                <th>User</th>
                                <th>Aksi</th>
                                <th>Model</th>
                                <th>Deskripsi</th>
                                <th>IP Address</th>
                                <th class="w-1">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($list as $audit)
                                <tr>
                                    <td>
                                        <small class="text-nowrap"><?= $audit->created_at->format('d M Y H:i:s') ?></small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-xs me-2" style="background-image: url(<?= $audit->user ? ($audit->user->avatar ? asset($audit->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($audit->user->name) . '&background=206bc4&color=fff&size=32') : '' ?>)"></span>
                                            <span><?= $audit->user->name ?? 'System' ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ match($audit->action) {
                                            'RPSSubmitted' => 'blue',
                                            'RPSReviewed' => 'green',
                                            'RPSRevisionRequested' => 'orange',
                                            'RPSApproved' => 'success',
                                            'RPSPublished' => 'primary',
                                            'RPSArchived' => 'secondary',
                                            'ReviewerAssigned' => 'cyan',
                                            default => 'secondary',
                                        } }-lt">
                                            {{ match($audit->action) {
                                                'RPSSubmitted' => 'RPS Diajukan',
                                                'RPSReviewed' => 'RPS Direview',
                                                'RPSRevisionRequested' => 'Revisi Diminta',
                                                'RPSApproved' => 'RPS Disetujui',
                                                'RPSPublished' => 'RPS Dipublikasi',
                                                'RPSArchived' => 'RPS Diarsipkan',
                                                'ReviewerAssigned' => 'Reviewer Ditugaskan',
                                                default => $audit->action,
                                            } }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <small class="fw-bold"><?= class_basename($audit->model_type) ?></small>
                                            <small class="text-secondary">ID: <?= $audit->model_id ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-secondary">
                                            <?php if($audit->new_values)
                                                <?php if(isset($audit->new_values['status']))
                                                    Status: <?= $audit->new_values['status'] ?>
                                                <?php else: ?>
                                                    <?= \Illuminate\Support\Str::limit(json_encode($audit->new_values), 60) ?>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <small class="text-secondary"><?= $audit->ip_address ?? '-' ?></small>
                                    </td>
                                    <td>
                                        <button wire:click="viewDetail(<?= $audit->id ?>)" class="btn btn-sm btn-ghost-secondary" title="Detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?><?php if(empty($items)): ?>
                                <tr>
                                    <td colspan="7">
                                        <div class="empty py-4">
                                            <div class="empty-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M10 3h4a1 1 0 0 1 1 1v3h-6v-3a1 1 0 0 1 1 -1z"/><path d="M5 6h14a2 2 0 0 1 2 2v11a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2z"/></svg>
                                            </div>
                                            <p class="empty-title">Tidak ada audit log</p>
                                            <p class="empty-subtitle text-secondary">Catatan aktivitas sistem akan muncul di sini.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex align-items-center">
                    <p class="m-0 text-secondary">Menampilkan <?= $list->firstItem() ?> - <?= $list->lastItem() ?> dari <?= $list->total() ?></p>
                    <div class="ms-auto"><?= $list->links() ?></div>
                </div>
            </div>
        </div>
    </div>

    <?php if($showDetail && $selectedAudit)
        <div class="modal modal-blur fade show" tabindex="-1" style="display:block" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Audit Log #<?= $selectedAudit->id ?></h5>
                        <button type="button" class="btn-close" wire:click="closeDetail" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <strong>Waktu:</strong>
                            <span><?= $selectedAudit->created_at->format('d M Y, H:i:s') ?></span>
                        </div>
                        <div class="mb-3">
                            <strong>User:</strong>
                            <span><?= $selectedAudit->user->name ?? 'System' ?> (<?= $selectedAudit->user->email ?? '-' ?>)</span>
                        </div>
                        <div class="mb-3">
                            <strong>Aksi:</strong>
                            <span class="badge bg-blue-lt"><?= $selectedAudit->action ?></span>
                        </div>
                        <div class="mb-3">
                            <strong>Model:</strong>
                            <span><?= $selectedAudit->model_type ?> #<?= $selectedAudit->model_id ?></span>
                        </div>
                        <div class="mb-3">
                            <strong>IP Address:</strong>
                            <span><?= $selectedAudit->ip_address ?? '-' ?></span>
                        </div>
                        <div class="mb-3">
                            <strong>User Agent:</strong>
                            <small class="text-secondary d-block"><?= $selectedAudit->user_agent ?? '-' ?></small>
                        </div>

                        <?php if($selectedAudit->old_values || $selectedAudit->new_values)
                            <hr>
                            <h6>Perubahan Data</h6>
                            <div class="row g-3">
                                <?php if($selectedAudit->old_values)
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header bg-red-lt">
                                                <strong>Data Sebelumnya</strong>
                                            </div>
                                            <div class="card-body">
                                                <pre class="m-0" style="font-size: 12px;"><?= json_encode($selectedAudit->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?></pre>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if($selectedAudit->new_values)
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header bg-green-lt">
                                                <strong>Data Baru</strong>
                                            </div>
                                            <div class="card-body">
                                                <pre class="m-0" style="font-size: 12px;"><?= json_encode($selectedAudit->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?></pre>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if($selectedAudit->changes)
                            <hr>
                            <h6>Field yang Berubah</h6>
                            <div class="card">
                                <div class="card-body">
                                    <pre class="m-0" style="font-size: 12px;"><?= json_encode($selectedAudit->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?></pre>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-ghost-secondary" wire:click="closeDetail">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    <?php endif; ?>
</div>


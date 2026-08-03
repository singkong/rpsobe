<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Daftar RPS untuk Direview</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">RPS Menunggu Review</h3>
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
                                <option value="revision">Revisi</option>
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
                                <th wire:click="sortBy('updated_at')" style="cursor:pointer">Diajukan</th>
                                <th>Reviewer</th>
                                <th class="w-1">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $list = $this->reviewList(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rps): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <strong><?php echo e($rps->mataKuliah->code ?? '-'); ?></strong>
                                            <small class="text-secondary"><?php echo e($rps->mataKuliah->name ?? '-'); ?></small>
                                        </div>
                                    </td>
                                    <td><?php echo e($rps->user->name ?? '-'); ?></td>
                                    <td><span class="badge bg-<?php echo e($rps->status->color()); ?>"><?php echo e($rps->status->label()); ?></span></td>
                                    <td><small><?php echo e($rps->updated_at->format('d M Y')); ?></small></td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rps->reviews->isNotEmpty()): ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $rps->reviews->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="badge bg-cyan-lt me-1"><?php echo e($review->reviewer->name ?? 'Unknown'); ?></span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-lt">Belum</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <a href="<?php echo e(route('rps.review', ['rpsId' => $rps->id])); ?>" class="btn btn-sm btn-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 3h4a1 1 0 0 1 1 1v3h-6v-3a1 1 0 0 1 1 -1z"/><path d="M5 6h14a2 2 0 0 1 2 2v11a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2z"/></svg>
                                                Review
                                            </a>
                                            <a href="<?php echo e(route('rps.history', ['rpsId' => $rps->id])); ?>" class="btn btn-sm btn-ghost-secondary" title="Riwayat">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9 -9a9 9 0 0 0 -9 9"/><path d="M12 7v5l3 3"/></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6">
                                        <div class="empty py-4">
                                            <div class="empty-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M10 3h4a1 1 0 0 1 1 1v3h-6v-3a1 1 0 0 1 1 -1z"/><path d="M5 6h14a2 2 0 0 1 2 2v11a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2z"/></svg>
                                            </div>
                                            <p class="empty-title">Tidak ada RPS untuk direview</p>
                                            <p class="empty-subtitle text-secondary">RPS yang perlu direview akan muncul di sini.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex align-items-center">
                    <p class="m-0 text-secondary">Menampilkan <?php echo e($list->firstItem()); ?> - <?php echo e($list->lastItem()); ?> dari <?php echo e($list->total()); ?></p>
                    <div class="ms-auto"><?php echo e($list->links()); ?></div>
                </div>
            </div>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('message')): ?>
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
            <div class="toast show" role="alert">
                <div class="toast-header"><strong class="me-auto">Berhasil</strong><button type="button" class="btn-close" data-bs-dismiss="toast"></button></div>
                <div class="toast-body"><?php echo e(session('message')); ?></div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH E:\laragon\www\rps-obe\resources\views/livewire/rps/workflow/review-list.blade.php ENDPATH**/ ?>
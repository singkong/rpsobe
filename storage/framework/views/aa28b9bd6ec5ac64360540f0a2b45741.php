<div>
    <?php
        $stats = $this->stats();
    ?>
    <!-- Stats Cards -->
    <div class="row row-deck row-cards mb-4">
        <div class="col-sm-6 col-lg-2">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h1 m-0"><?php echo e($stats['total'] ?? 0); ?></div>
                    <div class="text-secondary">Total RPS</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h1 m-0 text-yellow"><?php echo e($stats['draft'] ?? 0); ?></div>
                    <div class="text-secondary">Draft</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h1 m-0 text-blue"><?php echo e($stats['review'] ?? 0); ?></div>
                    <div class="text-secondary">Review</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h1 m-0 text-green"><?php echo e($stats['approved'] ?? 0); ?></div>
                    <div class="text-secondary">Disetujui</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h1 m-0 text-teal"><?php echo e($stats['published'] ?? 0); ?></div>
                    <div class="text-secondary">Published</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h1 m-0 text-orange"><?php echo e($stats['revision'] ?? 0); ?></div>
                    <div class="text-secondary">Revisi</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent RPS -->
    <div class="card">
        <div class="card-header d-flex">
            <h3 class="card-title">RPS Terbaru</h3>
            <div class="ms-auto">
                <a href="<?php echo e(route('rps.index')); ?>" class="btn btn-sm btn-primary">Lihat Semua</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Mata Kuliah</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th>Diperbarui</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $stats['recentRps'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rps): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <strong><?php echo e($rps->mataKuliah->name ?? '-'); ?></strong>
                                <div class="text-secondary small"><?php echo e($rps->mataKuliah->code ?? ''); ?></div>
                            </td>
                            <td><?php echo e($rps->semester->name ?? '-'); ?></td>
                            <td><span class="badge <?php echo e($this->getStatusBadge($rps->status)); ?>"><?php echo e($rps->status->value); ?></span></td>
                            <td class="text-secondary"><?php echo e($rps->updated_at->diffForHumans()); ?></td>
                            <td class="text-end">
                                <a href="<?php echo e(route('rps.edit', $rps->id)); ?>" class="btn btn-sm btn-ghost-primary">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-secondary">Belum ada RPS. <a href="<?php echo e(route('rps.create')); ?>">Buat RPS baru</a></td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php /**PATH E:\laragon\www\rps-obe\resources\views/livewire/dashboard/dosen-dashboard.blade.php ENDPATH**/ ?>
<div>
    <?php $stats = $this->stats(); ?>
    <!-- Stats Cards -->
    <div class="row row-deck row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h1 m-0"><?php echo e($stats['total'] ?? 0); ?></div>
                    <div class="text-secondary">Total RPS</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h1 m-0 text-green"><?php echo e($stats['published'] ?? 0); ?></div>
                    <div class="text-secondary">Published</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h1 m-0 text-blue"><?php echo e($stats['review'] ?? 0); ?></div>
                    <div class="text-secondary">Menunggu Review</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="h1 m-0"><?php echo e($stats['completionRate'] ?? 0); ?>%</div>
                    <div class="text-secondary">Tingkat Penyelesaian</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Menunggu Review -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex">
                    <h3 class="card-title">Menunggu Review</h3>
                    <span class="badge bg-blue ms-2"><?php echo e(count($stats['rpsMenungguReview'] ?? [])); ?></span>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead><tr><th>MK</th><th>Dosen</th><th></th></tr></thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $stats['rpsMenungguReview'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rps): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><strong><?php echo e($rps->mataKuliah->name ?? '-'); ?></strong></td>
                                    <td><?php echo e($rps->user->name ?? '-'); ?></td>
                                    <td class="text-end"><a href="<?php echo e(route('rps.review', $rps->id)); ?>" class="btn btn-sm btn-primary">Review</a></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="3" class="text-center py-3 text-secondary">Tidak ada RPS menunggu review</td></tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Progress Card -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Status RPS</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2"><span>Draft</span> <strong class="text-yellow"><?php echo e($stats['draft'] ?? 0); ?></strong></div>
                    <div class="progress mb-3" style="height:6px"><div class="progress-bar bg-yellow" style="width:<?php echo e(($stats['total'] > 0) ? ($stats['draft'] / $stats['total'] * 100) : 0); ?>%"></div></div>
                    <div class="d-flex justify-content-between mb-2"><span>Review</span> <strong class="text-blue"><?php echo e($stats['review'] ?? 0); ?></strong></div>
                    <div class="progress mb-3" style="height:6px"><div class="progress-bar bg-blue" style="width:<?php echo e(($stats['total'] > 0) ? ($stats['review'] / $stats['total'] * 100) : 0); ?>%"></div></div>
                    <div class="d-flex justify-content-between mb-2"><span>Approved</span> <strong class="text-green"><?php echo e($stats['approved'] ?? 0); ?></strong></div>
                    <div class="progress mb-3" style="height:6px"><div class="progress-bar bg-green" style="width:<?php echo e(($stats['total'] > 0) ? ($stats['approved'] / $stats['total'] * 100) : 0); ?>%"></div></div>
                    <div class="d-flex justify-content-between mb-2"><span>Published</span> <strong class="text-teal"><?php echo e($stats['published'] ?? 0); ?></strong></div>
                    <div class="progress" style="height:6px"><div class="progress-bar bg-teal" style="width:<?php echo e(($stats['total'] > 0) ? ($stats['published'] / $stats['total'] * 100) : 0); ?>%"></div></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH E:\laragon\www\rps-obe\resources\views/livewire/dashboard/kaprodi-dashboard.blade.php ENDPATH**/ ?>
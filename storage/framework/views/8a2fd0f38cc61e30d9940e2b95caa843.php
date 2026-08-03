<div>
    <div class="row row-deck row-cards mb-4">
        <div class="col-sm-6 col-lg-4">
            <div class="card"><div class="card-body text-center"><div class="h1 m-0"><?php echo e($this->stats()['totalProdi'] ?? 0); ?></div><div class="text-secondary">Program Studi</div></div></div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card"><div class="card-body text-center"><div class="h1 m-0"><?php echo e($this->stats()['totalRps'] ?? 0); ?></div><div class="text-secondary">Total RPS</div></div></div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card"><div class="card-body text-center"><div class="h1 m-0"><?php echo e($this->stats()['completionRate'] ?? 0); ?>%</div><div class="text-secondary">Penyelesaian</div></div></div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3 class="card-title">Per Program Studi</h3></div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead><tr><th>Prodi</th><th>Total RPS</th><th>Published</th><th>Penyelesaian</th></tr></thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->stats()['prodiStats'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ps): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($ps['name']); ?></strong> <small class="text-secondary"><?php echo e($ps['code']); ?></small></td>
                            <td><?php echo e($ps['totalRps']); ?></td>
                            <td><?php echo e($ps['published']); ?></td>
                            <td><div class="progress" style="height:6px"><div class="progress-bar bg-teal" style="width:<?php echo e($ps['completionRate']); ?>%"></div></div><small><?php echo e($ps['completionRate']); ?>%</small></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="text-center py-3">Belum ada data</td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php /**PATH E:\laragon\www\rps-obe\resources\views/livewire/dashboard/fakultas-dashboard.blade.php ENDPATH**/ ?>
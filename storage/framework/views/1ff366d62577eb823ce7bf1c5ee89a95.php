<div>
    <?php $stats = $this->stats(); ?>
    <div class="row row-deck row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card"><div class="card-body text-center"><div class="h1 m-0"><?php echo e($stats['totalTenants'] ?? 0); ?></div><div class="text-secondary">Total Universitas</div></div></div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card"><div class="card-body text-center"><div class="h1 m-0 text-green"><?php echo e($stats['activeTenants'] ?? 0); ?></div><div class="text-secondary">Aktif</div></div></div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card"><div class="card-body text-center"><div class="h1 m-0"><?php echo e($stats['totalUsers'] ?? 0); ?></div><div class="text-secondary">Total Pengguna</div></div></div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card"><div class="card-body text-center"><div class="h1 m-0 text-teal"><?php echo e($stats['totalPublished'] ?? 0); ?></div><div class="text-secondary">RPS Published</div></div></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Tenant Terbaru</h3></div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead><tr><th>Nama</th><th>Fakultas</th><th>Prodi</th><th>Users</th><th>RPS</th><th>Status</th></tr></thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $stats['recentTenants'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tenant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($tenant['name']); ?></strong></td>
                            <td><?php echo e($tenant['fakultas_count']); ?></td>
                            <td><?php echo e($tenant['prodi_count']); ?></td>
                            <td><?php echo e($tenant['users_count']); ?></td>
                            <td><?php echo e($tenant['rps_count']); ?></td>
                            <td><span class="badge <?php echo e($tenant['is_active'] ? 'bg-green' : 'bg-red'); ?>-lt"><?php echo e($tenant['is_active'] ? 'Aktif' : 'Nonaktif'); ?></span></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="text-center py-3 text-secondary">Belum ada tenant</td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php /**PATH E:\laragon\www\rps-obe\resources\views/livewire/dashboard/admin-dashboard.blade.php ENDPATH**/ ?>
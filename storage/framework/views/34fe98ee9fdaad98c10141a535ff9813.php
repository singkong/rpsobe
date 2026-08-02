<div>
    <div class="card">
        <div class="card-body border-bottom py-3">
            <div class="row g-2">
                <div class="col-auto">
                    <button wire:click="markAllAsRead" class="btn btn-primary">Tandai Semua Dibaca</button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Pesan</th>
                        <th>Tipe</th>
                        <th>Waktu</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><strong><?php echo e($notification->title); ?></strong></td>
                            <td><?php echo e($notification->message); ?></td>
                            <td>
                                <?php
                                    $bg = match($notification->type) {
                                        'rps_submitted' => 'blue', 'rps_reviewed' => 'green',
                                        'rps_revision_requested' => 'orange', 'rps_approved' => 'success',
                                        'rps_published' => 'primary', 'reviewer_assigned' => 'cyan',
                                        'deadline_reminder' => 'red', default => 'secondary',
                                    };
                                ?>
                                <span class="badge bg-<?php echo e($bg); ?>-lt"><?php echo e(str_replace('_', ' ', ucfirst($notification->type))); ?></span>
                            </td>
                            <td class="text-muted"><?php echo e($notification->created_at->diffForHumans()); ?></td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notification->read_at): ?>
                                    <span class="badge bg-success-lt">Dibaca</span>
                                <?php else: ?>
                                    <span class="badge bg-blue-lt">Baru</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-secondary">Tidak ada notifikasi</td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php /**PATH E:\laragon\www\rps-obe\resources\views/livewire/notification/notification-list.blade.php ENDPATH**/ ?>
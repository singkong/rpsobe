<div>
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Dashboard Kaprodi</h2>
                <div class="text-secondary">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Kaprodi</li>
                    </ol>
                </div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->prodi()): ?>
                <div class="col-auto">
                    <span class="badge bg-azure-lt fs-5"><?php echo e($this->prodi()->name); ?> (<?php echo e($this->prodi()->code); ?>)</span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$this->stats()): ?>
        <div class="empty">
            <div class="empty-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" class="icon text-muted">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 8v4l2 2"/><path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5"/>
                </svg>
            </div>
            <p class="empty-title">Prodi belum dikonfigurasi</p>
            <p class="empty-subtitle text-secondary">Hubungi administrator untuk mengaitkan akun Anda dengan program studi</p>
        </div>
    <?php else: ?>
        <div class="row mb-3">
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0"><?php echo e($this->stats()['total']); ?></div>
                        <div class="text-secondary">Total RPS</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0"><?php echo e($this->stats()['published']); ?></div>
                        <div class="text-secondary">Completed</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0"><?php echo e($this->stats()['review']); ?></div>
                        <div class="text-secondary">In Review</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0"><?php echo e($this->stats()['approved']); ?></div>
                        <div class="text-secondary">Approved</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-2">
                <div class="card">
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0"><?php echo e($this->stats()['draft']); ?></div>
                        <div class="text-secondary">Draft</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-2">
                <div class="card">
                    <div class="card-status-top bg-green"></div>
                    <div class="card-body p-3 text-center">
                        <div class="h1 m-0"><?php echo e($this->stats()['completionRate']); ?>%</div>
                        <div class="text-secondary">Completion Rate</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <strong>Progress Semester Ini</strong>
                    <span class="ms-auto"><?php echo e($this->stats()['published']); ?> / <?php echo e($this->stats()['totalMataKuliah']); ?> RPS Completed</span>
                </div>
                <div class="progress">
                    <div class="progress-bar bg-green" style="width: <?php echo e(min($this->stats()['completionRate'], 100)); ?>%" role="progressbar" aria-valuenow="<?php echo e($this->stats()['completionRate']); ?>" aria-valuemin="0" aria-valuemax="100">
                        <span><?php echo e($this->stats()['completionRate']); ?>%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h3 class="card-title">Distribusi Status RPS</h3>
                    </div>
                    <div class="card-body">
                        <?php
                            $maxVal = max(array_values($this->statusCounts()) ?: [1]);
                            $colors = [
                                'draft' => '#f59f00',
                                'review' => '#206bc4',
                                'revision' => '#d63939',
                                'approved' => '#2fb344',
                                'published' => '#0ca678',
                                'archived' => '#616876',
                            ];
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->statusCounts(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $label = \App\Enums\RPSStatus::from($status)->label();
                                $color = $colors[$status] ?? '#616876';
                                $pct = ($this->stats())['total'] > 0 ? round(($count / ($this->stats())['total']) * 100, 1) : 0;
                            ?>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span><?php echo e($label); ?></span>
                                    <span><?php echo e($count); ?> (<?php echo e($pct); ?>%)</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" style="width: <?php echo e($pct); ?>%; background-color: <?php echo e($color); ?>;" role="progressbar"></div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h3 class="card-title">Progress per Dosen</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Dosen</th>
                                        <th>RPS</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->dosenProgress(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold"><?php echo e($dp['name']); ?></div>
                                                <div class="text-secondary small"><?php echo e($dp['email']); ?></div>
                                            </td>
                                            <td class="text-secondary"><?php echo e($dp['completed']); ?>/<?php echo e($dp['total']); ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress progress-xs flex-grow-1 me-2" style="max-width: 100px;">
                                                        <div class="progress-bar <?php echo e($dp['percentage'] >= 100 ? 'bg-green' : ($dp['percentage'] >= 50 ? 'bg-azure' : 'bg-yellow')); ?>" style="width: <?php echo e($dp['percentage']); ?>%" role="progressbar"></div>
                                                    </div>
                                                    <span class="small text-secondary"><?php echo e($dp['percentage']); ?>%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($this->dosenProgress())): ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-secondary py-4">Belum ada data dosen</td>
                                        </tr>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">RPS Menunggu Review</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Mata Kuliah</th>
                                        <th>Dosen</th>
                                        <th>Diajukan</th>
                                        <th class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->rpsMenungguReview(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rps): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold"><?php echo e($rps->mataKuliah?->name ?? '-'); ?></div>
                                                <div class="text-secondary small"><?php echo e($rps->mataKuliah?->code ?? ''); ?></div>
                                            </td>
                                            <td><?php echo e($rps->user?->name ?? '-'); ?></td>
                                            <td class="text-secondary small"><?php echo e($rps->updated_at->diffForHumans()); ?></td>
                                            <td>
                                                <a href="<?php echo e(route('rps.review', ['rpsId' => $rps->id])); ?>" class="btn btn-sm btn-outline-blue">Review</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->rpsMenungguReview()->isEmpty()): ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-secondary py-4">Tidak ada RPS menunggu review</td>
                                        </tr>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">RPS Menunggu Approval</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Mata Kuliah</th>
                                        <th>Dosen</th>
                                        <th>Diajukan</th>
                                        <th class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->rpsMenungguApproval(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rps): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold"><?php echo e($rps->mataKuliah?->name ?? '-'); ?></div>
                                                <div class="text-secondary small"><?php echo e($rps->mataKuliah?->code ?? ''); ?></div>
                                            </td>
                                            <td><?php echo e($rps->user?->name ?? '-'); ?></td>
                                            <td class="text-secondary small"><?php echo e($rps->updated_at->diffForHumans()); ?></td>
                                            <td>
                                                <a href="<?php echo e(route('rps.review', ['rpsId' => $rps->id])); ?>" class="btn btn-sm btn-outline-green">Approve</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->rpsMenungguApproval()->isEmpty()): ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-secondary py-4">Tidak ada RPS menunggu approval</td>
                                        </tr>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Aksi Cepat</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="<?php echo e(route('rps.create')); ?>" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                                Buat RPS
                            </a>
                            <a href="<?php echo e(route('review.list')); ?>" class="btn btn-outline-blue">
                                Assign Reviewer
                            </a>
                            <a href="<?php echo e(route('approval.list')); ?>" class="btn btn-outline-green">
                                Batch Approval
                            </a>
                            <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-outline-azure">
                                Lihat Laporan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH E:\laragon\www\rps-obe\resources\views/livewire/dashboard/kaprodi-dashboard.blade.php ENDPATH**/ ?>
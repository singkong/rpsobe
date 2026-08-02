<?php

use function Livewire\Volt\{mount};
use Illuminate\Support\Facades\Auth;

$redirectRoute = 'dashboard.dosen';

mount(function () use (&$redirectRoute) {
    $user = Auth::user();
    $redirectRoute = match (true) {
        $user->hasRole('super-admin') => 'dashboard.admin',
        $user->hasRole('admin-univ') => 'dashboard.universitas',
        $user->hasRole('admin-fakultas') => 'dashboard.fakultas',
        $user->hasRole('kaprodi') => 'dashboard.kaprodi',
        default => 'dashboard.dosen',
    };
});

$userName = Auth::user()->name;
$userRole = Auth::user()->roles->first()->name ?? 'Pengguna';
$userInitial = substr($userName, 0, 1);
$tenantName = Auth::user()->tenant?->name;
?>

<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Dashboard</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <span class="avatar avatar-xl me-3"><?= $userInitial ?></span>
                        <div>
                            <h2 class="mb-0">Selamat Datang, <?= $userName ?></h2>
                            <p class="text-secondary mb-0">
                                <?= $userRole ?>
                                <?php if ($tenantName): ?> - <?= $tenantName ?><?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <p>Sistem RPS OBE membantu Anda menyusun, mereview, dan mengelola RPS berbasis OBE.</p>
                    <a href="<?= route($redirectRoute) ?>" class="btn btn-primary">Ke Dashboard Saya</a>
                </div>
            </div>
        </div>
    </div>
</div>

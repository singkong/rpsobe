<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\Auth\EmailVerificationController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Volt::route('/login', 'auth.login')->name('login');
    Volt::route('/register', 'auth.register')->name('register');
    Volt::route('/forgot-password', 'auth.forgot-password')->name('password.request');
    Volt::route('/reset-password/{token}', 'auth.reset-password')->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

Route::middleware(['auth', 'verified', 'user.active', 'tenant.active', 'tenant'])->group(function () {
    Volt::route('/dashboard', 'dashboard.index')->name('dashboard');
    Volt::route('/dashboard/dosen', 'dashboard.dosen-dashboard')->name('dashboard.dosen');
    Volt::route('/dashboard/kaprodi', 'dashboard.kaprodi-dashboard')->name('dashboard.kaprodi');
    Volt::route('/dashboard/fakultas', 'dashboard.fakultas-dashboard')->name('dashboard.fakultas');
    Volt::route('/dashboard/universitas', 'dashboard.universitas-dashboard')->name('dashboard.universitas');
    Volt::route('/dashboard/admin', 'dashboard.admin-dashboard')->name('dashboard.admin');

    Route::middleware(['can:manage-master-data'])->group(function () {
        Volt::route('/admin/master-data', 'master-data.dashboard')->name('master-data.dashboard');
        Volt::route('/admin/master-data/fakultas', 'master-data.fakultas-index')->name('master-data.fakultas');
        Volt::route('/admin/master-data/prodi', 'master-data.program-studi-index')->name('master-data.program-studi');
        Volt::route('/admin/master-data/kurikulum', 'master-data.kurikulum-index')->name('master-data.kurikulum');
        Volt::route('/admin/master-data/mata-kuliah', 'master-data.mata-kuliah-index')->name('master-data.mata-kuliah');
        Volt::route('/admin/master-data/dosen', 'master-data.dosen-index')->name('master-data.dosen');
        Volt::route('/admin/master-data/cpl', 'master-data.cpl-index')->name('master-data.cpl');
        Volt::route('/admin/master-data/profil-lulusan', 'master-data.profil-lulusan-index')->name('master-data.profil-lulusan');
        Volt::route('/admin/master-data/semester', 'master-data.semester-index')->name('master-data.semester');
        Volt::route('/admin/master-data/referensi', 'master-data.referensi-index')->name('master-data.referensi');
    });

    Route::middleware(['can:manage-rps'])->group(function () {
        Volt::route('/rps', 'rps.builder.rps-index')->name('rps.index');
        Volt::route('/rps/create', 'rps.builder.wizard')->name('rps.create');
        Volt::route('/rps/{rpsId}/edit', 'rps.builder.wizard')->name('rps.edit');
    });

    Route::middleware(['can:review-rps'])->group(function () {
        Volt::route('/rps/{rpsId}/review', 'rps.workflow.review-form')->name('rps.review');
        Volt::route('/rps/{rpsId}/history', 'rps.workflow.workflow-history')->name('rps.history');
        Volt::route('/review', 'rps.workflow.review-list')->name('review.list');
    });

    Route::middleware(['can:approve-rps'])->group(function () {
        Volt::route('/approval', 'rps.workflow.approval-list')->name('approval.list');
        Volt::route('/rps/{rpsId}/assign-reviewer', 'rps.workflow.reviewer-assignment')->name('rps.assign-reviewer');
    });

    // Reports
    Volt::route('/reports', 'reporting.report-index')->name('reports.index');
    Route::get('/reports/export/excel', [\App\Http\Controllers\ReportExportController::class, 'exportExcel'])
        ->name('reports.export-excel');
    Route::get('/reports/export/pdf', [\App\Http\Controllers\ReportExportController::class, 'exportPdf'])
        ->name('reports.export-pdf');

    // Export routes
    Route::get('/rps/{rpsId}/export/{format}', [\App\Http\Controllers\ExportController::class, 'download'])
        ->name('rps.export.download');
    Route::get('/rps/export/download-batch/{path}', [\App\Http\Controllers\ExportController::class, 'downloadBatch'])
        ->name('rps.export.download-batch');
    Route::post('/rps/batch-export', [\App\Http\Controllers\ExportController::class, 'batchExport'])
        ->name('rps.batch-export');

    // Notifications
    Volt::route('/notifications', 'notification.notification-list')->name('notifications.index');

    // Audit Log
    Route::middleware(['can:manage-master-data'])->group(function () {
        Volt::route('/audit', 'audit.audit-viewer')->name('audit.index');
    });

    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

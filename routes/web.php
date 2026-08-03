<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ReportExportController;

Route::get('/', fn () => redirect()->route('dashboard'));

// --- Guest Routes ---
Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login-page')->name('login');
    Route::view('/register', 'auth.register-page')->name('register');
    Route::view('/forgot-password', 'auth.forgot-password-page')->name('password.request');
    Route::view('/reset-password/{token}', 'auth.reset-password-page')->name('password.reset');
});

// --- Email Verification ---
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])->middleware('throttle:6,1')->name('verification.send');
});

// --- Authenticated Routes ---
Route::middleware(['auth', 'verified', 'user.active', 'tenant.active', 'tenant'])->group(function () {
    Route::view('/dashboard', 'dashboard.index')->name('dashboard');

    // Dashboard by role (accessible by respective roles)
    Route::view('/dashboard/dosen', 'dashboard.dosen')->name('dashboard.dosen');
    Route::view('/dashboard/kaprodi', 'dashboard.kaprodi')->name('dashboard.kaprodi');
    Route::view('/dashboard/fakultas', 'dashboard.fakultas')->name('dashboard.fakultas');
    Route::view('/dashboard/universitas', 'dashboard.universitas')->name('dashboard.universitas');
    Route::view('/dashboard/admin', 'dashboard.admin')->name('dashboard.admin');

    // --- Super Admin Only ---
    Route::middleware(['role:super-admin'])->group(function () {
        Route::view('/admin/tenants', 'tenants.index')->name('tenants.index');
    });

    // --- Admin Roles (User Management) ---
    Route::middleware(['role:super-admin|admin-univ|admin-fakultas|admin-prodi|kaprodi'])->group(function () {
        Route::view('/admin/users', 'users.index')->name('users.index');
    });

    // --- Template Management ---
    Route::middleware(['role:super-admin|admin-univ|kaprodi'])->group(function () {
        Route::view('/admin/templates', 'templates.index')->name('templates.index');
    });

    // --- Master Data ---
    Route::middleware(['role:super-admin|admin-univ|admin-fakultas|admin-prodi|kaprodi'])->group(function () {
        Route::view('/admin/master-data/fakultas', 'master-data.fakultas')->name('master-data.fakultas');
        Route::view('/admin/master-data/prodi', 'master-data.prodi')->name('master-data.program-studi');
        Route::view('/admin/master-data/kurikulum', 'master-data.kurikulum')->name('master-data.kurikulum');
        Route::view('/admin/master-data/mata-kuliah', 'master-data.mata-kuliah')->name('master-data.mata-kuliah');
        Route::view('/admin/master-data/dosen', 'master-data.dosen')->name('master-data.dosen');
        Route::view('/admin/master-data/cpl', 'master-data.cpl')->name('master-data.cpl');
        Route::view('/admin/master-data/profil-lulusan', 'master-data.profil-lulusan')->name('master-data.profil-lulusan');
        Route::view('/admin/master-data/semester', 'master-data.semester')->name('master-data.semester');
        Route::view('/admin/master-data/referensi', 'master-data.referensi')->name('master-data.referensi');
    });

    // --- RPS ---
    Route::middleware(['role:super-admin|admin-univ|admin-fakultas|admin-prodi|kaprodi|dosen'])->group(function () {
        Route::view('/rps', 'rps.index')->name('rps.index');
        Route::view('/rps/create', 'rps.create')->name('rps.create');
        Route::view('/rps/{rpsId}/edit', 'rps.edit')->name('rps.edit');
    });

    // --- Review ---
    Route::middleware(['role:super-admin|reviewer|kaprodi'])->group(function () {
        Route::view('/rps/{rpsId}/review', 'rps.review')->name('rps.review');
        Route::view('/rps/{rpsId}/history', 'rps.history')->name('rps.history');
        Route::view('/review', 'review.list')->name('review.list');
    });

    // --- Approval ---
    Route::middleware(['role:super-admin|kaprodi'])->group(function () {
        Route::view('/approval', 'approval.list')->name('approval.list');
        Route::view('/rps/{rpsId}/assign-reviewer', 'rps.assign-reviewer')->name('rps.assign-reviewer');
    });

    // --- Reports (all authenticated) ---
    Route::view('/reports', 'reports.index')->name('reports.index');
    Route::get('/reports/export/excel', [ReportExportController::class, 'exportExcel'])->name('reports.export-excel');
    Route::get('/reports/export/pdf', [ReportExportController::class, 'exportPdf'])->name('reports.export-pdf');

    // --- Export ---
    Route::get('/rps/{rpsId}/export/{format}', [ExportController::class, 'download'])->name('rps.export.download');
    Route::post('/rps/batch-export', [ExportController::class, 'batchExport'])->name('rps.batch-export');

    // --- Notifications (all authenticated) ---
    Route::view('/notifications', 'notifications.index')->name('notifications.index');

    // --- Audit ---
    Route::middleware(['role:super-admin|admin-univ|lpm'])->group(function () {
        Route::view('/audit', 'audit.index')->name('audit.index');
    });

    // --- Logout ---
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

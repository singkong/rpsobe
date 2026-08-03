<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            'super-admin' => 'Super Admin',
            'admin-univ' => 'Admin Universitas',
            'admin-fakultas' => 'Admin Fakultas',
            'admin-prodi' => 'Admin Program Studi',
            'kaprodi' => 'Ketua Program Studi',
            'reviewer' => 'Reviewer',
            'dosen' => 'Dosen',
            'lpm' => 'LPM',
            'mahasiswa' => 'Mahasiswa',
        ];

        foreach ($roles as $slug => $name) {
            Role::firstOrCreate(['name' => $slug, 'guard_name' => 'web']);
        }

        $permissions = [
            // Gate-level permissions (used in route middleware)
            'manage-master-data', 'manage-rps', 'review-rps', 'approve-rps',
            // User management
            'user.view-any', 'user.view', 'user.create', 'user.update',
            // Master data
            'fakultas.view-any', 'fakultas.create', 'fakultas.update', 'fakultas.delete',
            'prodi.view-any', 'prodi.create', 'prodi.update', 'prodi.delete',
            'mata-kuliah.view-any', 'mata-kuliah.create', 'mata-kuliah.update', 'mata-kuliah.delete',
            'cpl.view-any', 'cpl.create', 'cpl.update', 'cpl.delete',
            // RPS
            'rps.create', 'rps.view-any', 'rps.view', 'rps.update', 'rps.delete',
            'rps.submit-review', 'rps.export',
            // Dashboard & Reports
            'dashboard.view', 'report.view',
            // Export
            'export.pdf', 'export.word', 'export.excel',
            // Audit
            'audit.view',
            // Notification
            'notification.view',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Super Admin: ALL permissions
        Role::findByName('super-admin', 'web')->syncPermissions(Permission::all());

        // Admin Universitas
        Role::findByName('admin-univ', 'web')->syncPermissions([
            'manage-master-data', 'manage-rps', 'review-rps', 'approve-rps',
            'user.view-any', 'user.view', 'user.create', 'user.update',
            'fakultas.view-any', 'fakultas.create', 'fakultas.update', 'fakultas.delete',
            'prodi.view-any', 'prodi.create', 'prodi.update', 'prodi.delete',
            'mata-kuliah.view-any', 'cpl.view-any',
            'rps.view-any', 'dashboard.view', 'report.view',
            'export.pdf', 'export.word', 'export.excel',
            'audit.view', 'notification.view',
        ]);

        // Admin Fakultas
        Role::findByName('admin-fakultas', 'web')->syncPermissions([
            'manage-master-data', 'manage-rps',
            'fakultas.view-any', 'fakultas.update',
            'prodi.view-any', 'prodi.create', 'prodi.update',
            'mata-kuliah.view-any', 'cpl.view-any',
            'rps.view-any', 'dashboard.view', 'report.view',
            'notification.view',
        ]);

        // Admin Prodi
        Role::findByName('admin-prodi', 'web')->syncPermissions([
            'manage-master-data', 'manage-rps',
            'mata-kuliah.view-any', 'mata-kuliah.create', 'mata-kuliah.update',
            'cpl.view-any', 'cpl.create', 'cpl.update',
            'prodi.view-any', 'prodi.update',
            'rps.view-any', 'dashboard.view', 'report.view',
            'notification.view',
        ]);

        // Kaprodi
        Role::findByName('kaprodi', 'web')->syncPermissions([
            'manage-master-data', 'manage-rps', 'review-rps', 'approve-rps',
            'mata-kuliah.view-any', 'mata-kuliah.create', 'mata-kuliah.update',
            'cpl.view-any', 'cpl.create', 'cpl.update', 'cpl.delete',
            'rps.create', 'rps.view-any', 'rps.update', 'rps.delete',
            'rps.submit-review', 'rps.export',
            'dashboard.view', 'report.view',
            'export.pdf', 'export.word',
            'notification.view',
        ]);

        // Reviewer
        Role::findByName('reviewer', 'web')->syncPermissions([
            'review-rps',
            'rps.view-any', 'rps.view',
            'dashboard.view',
            'notification.view',
        ]);

        // Dosen
        Role::findByName('dosen', 'web')->syncPermissions([
            'manage-rps',
            'rps.create', 'rps.view', 'rps.view-any', 'rps.update',
            'rps.submit-review', 'rps.export',
            'mata-kuliah.view-any', 'cpl.view-any',
            'dashboard.view',
            'export.pdf', 'export.word',
            'notification.view',
        ]);

        // LPM
        Role::findByName('lpm', 'web')->syncPermissions([
            'dashboard.view', 'report.view',
            'audit.view', 'notification.view',
            'rps.view-any',
            'export.pdf', 'export.excel',
        ]);
    }
}

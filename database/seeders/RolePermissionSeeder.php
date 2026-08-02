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
            ['name' => 'user.view-any', 'label' => 'Melihat daftar pengguna'],
            ['name' => 'user.view', 'label' => 'Melihat detail pengguna'],
            ['name' => 'user.create', 'label' => 'Membuat pengguna'],
            ['name' => 'user.update', 'label' => 'Mengubah pengguna'],
            ['name' => 'user.delete', 'label' => 'Menghapus pengguna'],

            ['name' => 'tenant.view-any', 'label' => 'Melihat daftar tenant'],
            ['name' => 'tenant.view', 'label' => 'Melihat detail tenant'],
            ['name' => 'tenant.create', 'label' => 'Membuat tenant'],
            ['name' => 'tenant.update', 'label' => 'Mengubah tenant'],
            ['name' => 'tenant.delete', 'label' => 'Menghapus tenant'],

            ['name' => 'fakultas.view-any', 'label' => 'Melihat daftar fakultas'],
            ['name' => 'fakultas.view', 'label' => 'Melihat detail fakultas'],
            ['name' => 'fakultas.create', 'label' => 'Membuat fakultas'],
            ['name' => 'fakultas.update', 'label' => 'Mengubah fakultas'],
            ['name' => 'fakultas.delete', 'label' => 'Menghapus fakultas'],

            ['name' => 'prodi.view-any', 'label' => 'Melihat daftar prodi'],
            ['name' => 'prodi.view', 'label' => 'Melihat detail prodi'],
            ['name' => 'prodi.create', 'label' => 'Membuat prodi'],
            ['name' => 'prodi.update', 'label' => 'Mengubah prodi'],
            ['name' => 'prodi.delete', 'label' => 'Menghapus prodi'],

            ['name' => 'mata-kuliah.view-any', 'label' => 'Melihat daftar mata kuliah'],
            ['name' => 'mata-kuliah.view', 'label' => 'Melihat detail mata kuliah'],
            ['name' => 'mata-kuliah.create', 'label' => 'Membuat mata kuliah'],
            ['name' => 'mata-kuliah.update', 'label' => 'Mengubah mata kuliah'],
            ['name' => 'mata-kuliah.delete', 'label' => 'Menghapus mata kuliah'],

            ['name' => 'cpl.view-any', 'label' => 'Melihat daftar CPL'],
            ['name' => 'cpl.view', 'label' => 'Melihat detail CPL'],
            ['name' => 'cpl.create', 'label' => 'Membuat CPL'],
            ['name' => 'cpl.update', 'label' => 'Mengubah CPL'],
            ['name' => 'cpl.delete', 'label' => 'Menghapus CPL'],

            ['name' => 'cpmk.view-any', 'label' => 'Melihat daftar CPMK'],
            ['name' => 'cpmk.view', 'label' => 'Melihat detail CPMK'],
            ['name' => 'cpmk.create', 'label' => 'Membuat CPMK'],
            ['name' => 'cpmk.update', 'label' => 'Mengubah CPMK'],
            ['name' => 'cpmk.delete', 'label' => 'Menghapus CPMK'],

            ['name' => 'rps.create', 'label' => 'Membuat RPS'],
            ['name' => 'rps.view-any', 'label' => 'Melihat daftar RPS'],
            ['name' => 'rps.view', 'label' => 'Melihat detail RPS'],
            ['name' => 'rps.update', 'label' => 'Mengubah RPS'],
            ['name' => 'rps.delete', 'label' => 'Menghapus RPS'],
            ['name' => 'rps.submit-review', 'label' => 'Mengajukan RPS untuk review'],
            ['name' => 'rps.approve', 'label' => 'Menyetujui RPS'],
            ['name' => 'rps.reject', 'label' => 'Menolak RPS'],
            ['name' => 'rps.revise', 'label' => 'Merevisi RPS'],

            ['name' => 'workflow.view', 'label' => 'Melihat alur kerja'],
            ['name' => 'workflow.manage', 'label' => 'Mengelola alur kerja'],

            ['name' => 'ai.generate', 'label' => 'Menggunakan AI untuk generate'],
            ['name' => 'ai.validate', 'label' => 'Menggunakan AI untuk validasi'],

            ['name' => 'dashboard.view', 'label' => 'Melihat dashboard'],

            ['name' => 'report.view', 'label' => 'Melihat laporan'],
            ['name' => 'report.generate', 'label' => 'Membuat laporan'],

            ['name' => 'export.pdf', 'label' => 'Ekspor PDF'],
            ['name' => 'export.word', 'label' => 'Ekspor Word'],
            ['name' => 'export.excel', 'label' => 'Ekspor Excel'],

            ['name' => 'audit.view', 'label' => 'Melihat log audit'],

            ['name' => 'template.view-any', 'label' => 'Melihat daftar template'],
            ['name' => 'template.view', 'label' => 'Melihat detail template'],
            ['name' => 'template.create', 'label' => 'Membuat template'],
            ['name' => 'template.update', 'label' => 'Mengubah template'],
            ['name' => 'template.delete', 'label' => 'Menghapus template'],

            ['name' => 'notification.send', 'label' => 'Mengirim notifikasi'],
            ['name' => 'notification.view', 'label' => 'Melihat notifikasi'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission['name'],
                'guard_name' => 'web',
            ]);
        }

        $superAdmin = Role::findByName('super-admin', 'web');
        $superAdmin->syncPermissions(Permission::all());

        $adminUniv = Role::findByName('admin-univ', 'web');
        $adminUniv->syncPermissions([
            'user.view-any', 'user.view', 'user.create', 'user.update',
            'fakultas.view-any', 'fakultas.view', 'fakultas.create', 'fakultas.update', 'fakultas.delete',
            'prodi.view-any', 'prodi.view', 'prodi.create', 'prodi.update', 'prodi.delete',
            'dashboard.view',
            'report.view', 'report.generate',
            'export.pdf', 'export.word', 'export.excel',
            'audit.view',
            'notification.send', 'notification.view',
        ]);

        $kaprodi = Role::findByName('kaprodi', 'web');
        $kaprodi->syncPermissions([
            'rps.create', 'rps.view-any', 'rps.view', 'rps.update', 'rps.delete',
            'rps.submit-review', 'rps.approve', 'rps.reject', 'rps.revise',
            'cpl.view-any', 'cpl.view', 'cpl.create', 'cpl.update',
            'cpmk.view-any', 'cpmk.view', 'cpmk.create', 'cpmk.update',
            'mata-kuliah.view-any', 'mata-kuliah.view',
            'ai.generate', 'ai.validate',
            'dashboard.view',
            'report.view', 'report.generate',
            'export.pdf', 'export.word',
            'template.view-any', 'template.view', 'template.create', 'template.update',
            'notification.view',
        ]);

        $dosen = Role::findByName('dosen', 'web');
        $dosen->syncPermissions([
            'rps.create', 'rps.view', 'rps.view-any', 'rps.update',
            'rps.submit-review', 'rps.revise',
            'cpl.view-any', 'cpl.view',
            'cpmk.view-any', 'cpmk.view',
            'mata-kuliah.view-any', 'mata-kuliah.view',
            'ai.generate', 'ai.validate',
            'dashboard.view',
            'export.pdf', 'export.word',
            'template.view-any', 'template.view',
            'notification.view',
        ]);

        $reviewer = Role::findByName('reviewer', 'web');
        $reviewer->syncPermissions([
            'rps.view-any', 'rps.view',
            'rps.approve', 'rps.reject', 'rps.revise',
            'ai.validate',
            'dashboard.view',
            'notification.view',
        ]);
    }
}

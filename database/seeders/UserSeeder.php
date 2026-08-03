<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::firstOrFail();

        $users = [
            ['name' => 'Super Admin',      'email' => 'superadmin@rpsobe.id', 'role' => 'super-admin', 'tenant_id' => null],
            ['name' => 'Admin Universitas', 'email' => 'admin-univ@rpsobe.id', 'role' => 'admin-univ', 'tenant_id' => $tenant->id],
            ['name' => 'Admin Fakultas',    'email' => 'admin-fakultas@rpsobe.id', 'role' => 'admin-fakultas', 'tenant_id' => $tenant->id],
            ['name' => 'Admin Prodi',       'email' => 'admin-prodi@rpsobe.id', 'role' => 'admin-prodi', 'tenant_id' => $tenant->id],
            ['name' => 'Dr. Kaprodi',       'email' => 'kaprodi@rpsobe.id', 'role' => 'kaprodi', 'tenant_id' => $tenant->id],
            ['name' => 'Eka Reviewer',      'email' => 'reviewer@rpsobe.id', 'role' => 'reviewer', 'tenant_id' => $tenant->id],
            ['name' => 'Dosen Rina',        'email' => 'dosen@rpsobe.id', 'role' => 'dosen', 'tenant_id' => $tenant->id],
            ['name' => 'Dosen Budi',        'email' => 'dosen2@rpsobe.id', 'role' => 'dosen', 'tenant_id' => $tenant->id],
            ['name' => 'LPM Sari',          'email' => 'lpm@rpsobe.id', 'role' => 'lpm', 'tenant_id' => $tenant->id],
            ['name' => 'Mahasiswa Test',    'email' => 'mhs@rpsobe.id', 'role' => 'mahasiswa', 'tenant_id' => $tenant->id],
        ];

        foreach ($users as $u) {
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => 'password',
                    'tenant_id' => $u['tenant_id'],
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
            $user->syncRoles([$u['role']]);
        }
    }
}

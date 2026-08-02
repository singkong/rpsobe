<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
use App\Enums\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where('code', 'UI')->first();

        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@rpsobe.id',
                'password' => 'password',
                'role' => Role::SuperAdmin,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Admin Universitas',
                'email' => 'admin@univ.ac.id',
                'password' => 'password',
                'role' => Role::AdminUniv,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ketua Program Studi',
                'email' => 'kaprodi@univ.ac.id',
                'password' => 'password',
                'role' => Role::Kaprodi,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Dosen',
                'email' => 'dosen@univ.ac.id',
                'password' => 'password',
                'role' => Role::Dosen,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Reviewer',
                'email' => 'reviewer@univ.ac.id',
                'password' => 'password',
                'role' => Role::Reviewer,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $data['password'],
                    'tenant_id' => $tenant ? $tenant->id : null,
                    'is_active' => true,
                    'email_verified_at' => $data['email_verified_at'],
                ]
            );

            if (!$user->hasRole($data['role']->value)) {
                $user->assignRole($data['role']->value);
            }
        }
    }
}

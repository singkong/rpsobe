<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        Tenant::firstOrCreate(
            ['code' => 'UI'],
            [
                'name' => 'Universitas Indonesia',
                'code' => 'UI',
                'akronim' => 'UI',
                'alamat' => 'Jl. Margonda Raya, Pondok Cina, Kecamatan Beji, Kota Depok, Jawa Barat',
                'website' => 'https://www.ui.ac.id',
                'phone' => '021-7867222',
                'email' => 'humas@ui.ac.id',
                'akreditasi' => 'Unggul',
                'is_active' => true,
                'subscription_package' => 'enterprise',
                'subscription_expires_at' => now()->addYear(),
            ]
        );
    }
}

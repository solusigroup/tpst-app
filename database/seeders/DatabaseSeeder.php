<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default superuser
        User::firstOrCreate(
            ['email' => 'su@superuser.com'],
            [
                'name' => 'Wawan',
                'username' => 'wawan',
                'email' => 'su@superuser.com',
                'password' => Hash::make('Ku4tSek@Li'),
                'role' => 'superuser',
                'tenant_id' => null,
            ]
        );

        // Create default test tenant and users
        $tenant = Tenant::firstOrCreate(
            ['name' => 'PT Sampah Jaya'],
            ['domain' => 'sampahjaya.test']
        );

        User::firstOrCreate(
            ['email' => 'admin@tpst.test'],
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'email' => 'admin@tpst.test',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'tenant_id' => $tenant->id,
            ]
        );
    }
}

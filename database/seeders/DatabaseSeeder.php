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
                'is_super_admin' => true,
                'tenant_id' => null,
            ]
        );

        // Create default test tenant and users
        $tenant = Tenant::firstOrCreate(
            ['name' => 'PT Tatabumi Adilimbah'],
            [
                'domain' => 'sampahjaya.test',
                'address' => 'Jl. Raya Tambakboyo No. 123, Lamongan, Jawa Timur',
                'email' => 'info@tatabumi.id',
                'bank_name' => 'Bank Jatim',
                'bank_account_number' => '0123456789',
                'bank_account_name' => 'PT Tatabumi Adilimbah',
                'director_name' => 'Budi Sucahyo',
                'manager_name' => 'Sucahyo',
                'finance_name' => 'Ana'
            ]
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

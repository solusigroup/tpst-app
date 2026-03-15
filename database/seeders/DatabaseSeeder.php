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
        // Jalankan seeder Role dan Permission terlebih dahulu
        $this->call(RolePermissionSeeder::class);

        // Create default superuser
        $su = User::firstOrCreate(
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
        // Penting: Assign Role Spatie agar fitur authorization tidak kena 403
        $su->assignRole('super_admin');

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

        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin User',
                'email' => 'admin@tpst.test',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'tenant_id' => $tenant->id,
            ]
        );
        // Assign Role Spatie 'manajemen' ke admin default
        $admin->assignRole('manajemen');
    }
}

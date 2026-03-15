<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions List
        $permissions = [
            // Operasional
            'view_ritase', 'create_ritase', 'update_ritase', 'delete_ritase',
            'view_klien', 'create_klien', 'update_klien', 'delete_klien',
            'view_armada', 'create_armada', 'update_armada', 'delete_armada',
            'view_hasil_pilahan', 'create_hasil_pilahan', 'update_hasil_pilahan', 'delete_hasil_pilahan',
            'view_penjualan', 'create_penjualan', 'update_penjualan', 'delete_penjualan',
            
            // Keuangan
            'view_coa', 'create_coa', 'update_coa', 'delete_coa',
            'view_jurnal', 'create_jurnal', 'update_jurnal', 'delete_jurnal',
            'view_jurnal_kas', 'create_jurnal_kas', 'update_jurnal_kas', 'delete_jurnal_kas',
            'view_invoice', 'create_invoice', 'update_invoice', 'delete_invoice',
            
            // Laporan
            'view_laporan_keuangan',
            'view_laporan_operasional',
            
            // Administrasi
            'view_users', 'create_users', 'update_users', 'delete_users',
            'view_company_settings', 'update_company_settings',
            'view_activity_log',
            
            // Central System (Superadmin only)
            'view_tenants', 'create_tenants', 'update_tenants', 'delete_tenants',
            'view_central_users',
        ];

        // Create the permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 1. Super Admin Role
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // 2. Manajemen Role (Semua operasional & keuangan, BUKAN admin users/settings)
        $manajemen = Role::firstOrCreate(['name' => 'manajemen']);
        $manajemen->givePermissionTo([
            'view_ritase', 'create_ritase', 'update_ritase', 'delete_ritase',
            'view_klien', 'create_klien', 'update_klien', 'delete_klien',
            'view_armada', 'create_armada', 'update_armada', 'delete_armada',
            'view_hasil_pilahan', 'create_hasil_pilahan', 'update_hasil_pilahan', 'delete_hasil_pilahan',
            'view_penjualan', 'create_penjualan', 'update_penjualan', 'delete_penjualan',
            'view_coa', 'create_coa', 'update_coa', 'delete_coa',
            'view_jurnal', 'create_jurnal', 'update_jurnal', 'delete_jurnal',
            'view_jurnal_kas', 'create_jurnal_kas', 'update_jurnal_kas', 'delete_jurnal_kas',
            'view_invoice', 'create_invoice', 'update_invoice', 'delete_invoice',
            'view_laporan_keuangan', 'view_laporan_operasional',
            // no users, company_settings, etc
        ]);

        // 3. Keuangan Role
        $keuangan = Role::firstOrCreate(['name' => 'keuangan']);
        $keuangan->givePermissionTo([
            'view_jurnal', 'create_jurnal', 'update_jurnal', 'delete_jurnal',
            'view_jurnal_kas', 'create_jurnal_kas', 'update_jurnal_kas', 'delete_jurnal_kas',
            'view_penjualan', 'create_penjualan', 'update_penjualan', 'delete_penjualan',
            'view_invoice', 'create_invoice', 'update_invoice', 'delete_invoice',
            'view_laporan_keuangan', 'view_laporan_operasional',
        ]);

        // 4. Operator / Petugas Role
        $operator = Role::firstOrCreate(['name' => 'operator']);
        $operator->givePermissionTo([
            'view_ritase', 'create_ritase', 'update_ritase', 'delete_ritase',
            'view_penjualan', 'create_penjualan', 'update_penjualan', 'delete_penjualan',
            'view_hasil_pilahan', 'create_hasil_pilahan', 'update_hasil_pilahan', 'delete_hasil_pilahan',
        ]);

        // 5. Ritase Only Role
        $ritaseOnly = Role::firstOrCreate(['name' => 'ritase_only']);
        $ritaseOnly->givePermissionTo([
            'view_ritase',
        ]);
        
        $this->command->info('Roles and Permissions created successfully.');
    }
}

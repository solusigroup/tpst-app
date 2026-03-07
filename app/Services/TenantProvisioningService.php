<?php

namespace App\Services;

use App\Models\Coa;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TenantProvisioningService
{
    /**
     * Provision a new tenant with admin user and default data.
     */
    public function provision(array $tenantData, array $adminData = []): Tenant
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($tenantData, $adminData) {
            // 1. Create tenant
            $tenant = Tenant::create([
                'name' => $tenantData['name'],
                'domain' => $tenantData['domain'],
            ]);

            // 2. Create admin user for the tenant (if admin data provided)
            if (!empty($adminData)) {
                User::withoutGlobalScopes()->create([
                    'tenant_id' => $tenant->id,
                    'name' => $adminData['name'],
                    'username' => $adminData['username'] ?? strtolower(str_replace(' ', '', $adminData['name'])),
                    'email' => $adminData['email'],
                    'password' => Hash::make($adminData['password']),
                    'role' => 'admin',
                    'is_super_admin' => false,
                ]);
            }

            // 3. Seed default Chart of Accounts
            $this->seedDefaultCoa($tenant);

            return $tenant;
        });
    }

    /**
     * Seed default Chart of Accounts for a tenant.
     */
    protected function seedDefaultCoa(Tenant $tenant): void
    {
        $defaultCoa = [
            // Assets
            ['kode_akun' => '1-1000', 'nama_akun' => 'Kas', 'tipe' => 'Asset'],
            ['kode_akun' => '1-1100', 'nama_akun' => 'Bank', 'tipe' => 'Asset'],
            ['kode_akun' => '1-1200', 'nama_akun' => 'Piutang Usaha', 'tipe' => 'Asset'],
            ['kode_akun' => '1-1300', 'nama_akun' => 'Persediaan', 'tipe' => 'Asset'],
            ['kode_akun' => '1-2000', 'nama_akun' => 'Aset Tetap', 'tipe' => 'Asset'],
            ['kode_akun' => '1-2100', 'nama_akun' => 'Akumulasi Penyusutan', 'tipe' => 'Asset'],

            // Liabilities
            ['kode_akun' => '2-1000', 'nama_akun' => 'Hutang Usaha', 'tipe' => 'Liability'],
            ['kode_akun' => '2-1100', 'nama_akun' => 'Hutang Pajak', 'tipe' => 'Liability'],
            ['kode_akun' => '2-2000', 'nama_akun' => 'Hutang Jangka Panjang', 'tipe' => 'Liability'],

            // Equity
            ['kode_akun' => '3-1000', 'nama_akun' => 'Modal', 'tipe' => 'Equity'],
            ['kode_akun' => '3-2000', 'nama_akun' => 'Laba Ditahan', 'tipe' => 'Equity'],

            // Revenue
            ['kode_akun' => '4-1000', 'nama_akun' => 'Pendapatan Tipping Fee', 'tipe' => 'Revenue'],
            ['kode_akun' => '4-1100', 'nama_akun' => 'Pendapatan Penjualan RDF', 'tipe' => 'Revenue'],
            ['kode_akun' => '4-1200', 'nama_akun' => 'Pendapatan Penjualan Plastik', 'tipe' => 'Revenue'],
            ['kode_akun' => '4-1300', 'nama_akun' => 'Pendapatan Penjualan Kompos', 'tipe' => 'Revenue'],
            ['kode_akun' => '4-9000', 'nama_akun' => 'Pendapatan Lain-lain', 'tipe' => 'Revenue'],

            // Expenses
            ['kode_akun' => '5-1000', 'nama_akun' => 'Beban Gaji', 'tipe' => 'Expense'],
            ['kode_akun' => '5-1100', 'nama_akun' => 'Beban Operasional', 'tipe' => 'Expense'],
            ['kode_akun' => '5-1200', 'nama_akun' => 'Beban BBM', 'tipe' => 'Expense'],
            ['kode_akun' => '5-1300', 'nama_akun' => 'Beban Pemeliharaan', 'tipe' => 'Expense'],
            ['kode_akun' => '5-1400', 'nama_akun' => 'Beban Penyusutan', 'tipe' => 'Expense'],
            ['kode_akun' => '5-9000', 'nama_akun' => 'Beban Lain-lain', 'tipe' => 'Expense'],
        ];

        foreach ($defaultCoa as $coa) {
            Coa::withoutGlobalScopes()->create(array_merge($coa, [
                'tenant_id' => $tenant->id,
            ]));
        }
    }
}

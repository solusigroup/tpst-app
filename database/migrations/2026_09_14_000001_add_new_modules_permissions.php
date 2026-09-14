<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // New permissions to register
        $newPermissions = [
            // TPST Performance (KPI)
            'view_kpi_dashboard',
            'view_kpi_daily_input',
            'create_kpi_daily_input',
            'view_kpi_evaluasi',
            'approve_kpi_evaluasi',

            // Keuangan & Akuntansi tambahan
            'view_rekonsiliasi_bank',
            'view_tracing',

            // Operasional tambahan
            'view_ritase_dlh',

            // Asisten AI
            'view_ai_assistant',
        ];

        foreach ($newPermissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        // Assign all permissions to Super Admin if exists
        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo(Permission::all());
        }

        // Assign relevant permissions to Manajemen role if exists
        $manajemen = Role::where('name', 'manajemen')->first();
        if ($manajemen) {
            $manajemen->givePermissionTo([
                'view_kpi_dashboard',
                'view_kpi_daily_input',
                'create_kpi_daily_input',
                'view_kpi_evaluasi',
                'approve_kpi_evaluasi',
                'view_rekonsiliasi_bank',
                'view_tracing',
                'view_ritase_dlh',
                'view_ai_assistant',
            ]);
        }

        // Assign relevant permissions to Keuangan role if exists
        $keuangan = Role::where('name', 'keuangan')->first();
        if ($keuangan) {
            $keuangan->givePermissionTo([
                'view_rekonsiliasi_bank',
                'view_tracing',
                'view_ritase_dlh',
            ]);
        }

        // Assign relevant permissions to Operator role if exists
        $operator = Role::where('name', 'operator')->first();
        if ($operator) {
            $operator->givePermissionTo([
                'view_kpi_daily_input',
                'create_kpi_daily_input',
            ]);
        }

        // Reset cache again
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'view_kpi_dashboard',
            'view_kpi_daily_input',
            'create_kpi_daily_input',
            'view_kpi_evaluasi',
            'approve_kpi_evaluasi',
            'view_rekonsiliasi_bank',
            'view_tracing',
            'view_ritase_dlh',
            'view_ai_assistant',
        ];

        foreach ($permissions as $permissionName) {
            Permission::where('name', $permissionName)->delete();
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};

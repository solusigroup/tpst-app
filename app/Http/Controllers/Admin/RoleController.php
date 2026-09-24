<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    public function index()
    {
        Gate::authorize('view_users');

        // Get all roles except super_admin (which cannot be modified/deleted normally)
        $roles = Role::where('name', '!=', 'super_admin')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        Gate::authorize('view_users');
        abort_if(!auth()->user()->hasRole('super_admin'), 403, 'Akses ditolak. Hanya Super Admin yang diizinkan mengelola role.');

        $structuredModules = self::getStructuredPermissions();
        $rolePermissions = [];

        return view('admin.roles.form', compact('structuredModules', 'rolePermissions'));
    }

    public function store(Request $request)
    {
        Gate::authorize('view_users');
        abort_if(!auth()->user()->hasRole('super_admin'), 403, 'Akses ditolak. Hanya Super Admin yang diizinkan mengelola role.');

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role = Role::create(['name' => $validated['name']]);
        
        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        // Flush Spatie's permission cache so changes take effect immediately
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil dibuat.');
    }

    public function edit(Role $role)
    {
        Gate::authorize('view_users');
        abort_if(!auth()->user()->hasRole('super_admin'), 403, 'Akses ditolak. Hanya Super Admin yang diizinkan mengelola role.');

        if ($role->name === 'super_admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Role Super Admin tidak dapat diubah.');
        }

        $structuredModules = self::getStructuredPermissions();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.form', compact('role', 'structuredModules', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        Gate::authorize('view_users');
        abort_if(!auth()->user()->hasRole('super_admin'), 403, 'Akses ditolak. Hanya Super Admin yang diizinkan mengelola role.');

        if ($role->name === 'super_admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Role Super Admin tidak dapat diubah.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role->update(['name' => $validated['name']]);

        // Sync strictly what is submitted
        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        } else {
            // If empty array, it removes all permissions
            $role->syncPermissions([]);
        }

        // Flush Spatie's permission cache so changes take effect immediately
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        Gate::authorize('view_users');
        abort_if(!auth()->user()->hasRole('super_admin'), 403, 'Akses ditolak. Hanya Super Admin yang diizinkan mengelola role.');

        if ($role->name === 'super_admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Role Super Admin tidak dapat dihapus.');
        }

        // Prevent deleting a role that is currently in use by any user
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')->with('error', 'Role tidak dapat dihapus karena masih digunakan oleh beberapa pengguna.');
        }

        $role->delete();

        // Flush Spatie's permission cache so changes take effect immediately
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')->with('success', 'Role berhasil dihapus.');
    }

    /**
     * Memastikan semua permission sistem telah terdaftar pada database.
     */
    public static function ensureAllPermissionsExist(): void
    {
        $defs = self::getModuleDefinitions();
        $allPermNames = [];
        foreach ($defs as $mod) {
            foreach ($mod['permissions'] as $permName => $label) {
                $allPermNames[] = $permName;
            }
        }

        $existing = Permission::pluck('name')->toArray();
        $missing = array_diff($allPermNames, $existing);

        if (!empty($missing)) {
            $records = array_map(function ($name) {
                return [
                    'name' => $name,
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $missing);

            Permission::insert($records);
            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            $superAdmin = Role::where('name', 'super_admin')->first();
            if ($superAdmin) {
                $superAdmin->givePermissionTo(Permission::all());
            }
        }
    }

    /**
     * Mengelompokkan seluruh permission sistem ke dalam modul-modul terstruktur.
     */
    public static function getStructuredPermissions(): array
    {
        self::ensureAllPermissionsExist();

        $allPermissions = Permission::all()->keyBy('name');
        $moduleDefs = self::getModuleDefinitions();
        $structured = [];
        $assignedPermNames = [];

        foreach ($moduleDefs as $key => $def) {
            $items = [];
            foreach ($def['permissions'] as $permName => $label) {
                if (isset($allPermissions[$permName])) {
                    $items[] = [
                        'id' => $allPermissions[$permName]->id,
                        'name' => $permName,
                        'label' => $label,
                    ];
                    $assignedPermNames[] = $permName;
                }
            }

            if (!empty($items)) {
                $structured[$key] = [
                    'key' => $key,
                    'name' => $def['name'],
                    'icon' => $def['icon'] ?? 'cil-folder',
                    'description' => $def['description'] ?? '',
                    'permissions' => $items,
                ];
            }
        }

        // Ambil permission yang belum terkelompokkan secara spesifik
        $unmapped = $allPermissions->except($assignedPermNames);
        if ($unmapped->isNotEmpty()) {
            $otherItems = [];
            foreach ($unmapped as $perm) {
                $otherItems[] = [
                    'id' => $perm->id,
                    'name' => $perm->name,
                    'label' => ucwords(str_replace('_', ' ', $perm->name)),
                ];
            }
            $structured['other'] = [
                'key' => 'other',
                'name' => 'Izin Lainnya',
                'icon' => 'cil-options',
                'description' => 'Izin sistem tambahan atau kustom',
                'permissions' => $otherItems,
            ];
        }

        return $structured;
    }

    /**
     * Definisi lengkap 9 Modul Utama TPST App beserta permissions dan penamaan Indonesia.
     */
    public static function getModuleDefinitions(): array
    {
        return [
            'kpi' => [
                'name' => 'TPST Performance (KPI)',
                'icon' => 'cil-chart-line',
                'description' => 'Sistem KPI operasional, input checklist & logbook harian, dan evaluasi supervisi PT PBS',
                'permissions' => [
                    'view_kpi_dashboard'    => 'Melihat 5 Dashboard KPI (Site Manager, QC, Mesin, Revenue, Pemilah)',
                    'view_kpi_daily_input'  => 'Melihat Halaman Input Harian KPI',
                    'create_kpi_daily_input'=> 'Mengisi Checklist Kebersihan, Logbook Mesin & Komplain KPI',
                    'view_kpi_evaluasi'     => 'Melihat Daftar Evaluasi & Dokumen Pengesahan KPI',
                    'approve_kpi_evaluasi'  => 'Menyetujui (Approve) Evaluasi KPI & Pengesahan Supervisi PBS',
                    'view_holiday'          => 'Melihat Daftar Hari Libur (Holiday)',
                    'create_holiday'        => 'Menambah Hari Libur Baru',
                    'update_holiday'        => 'Mengubah Data Hari Libur',
                    'delete_holiday'        => 'Menghapus Hari Libur',
                ]
            ],
            'operasional_ritase' => [
                'name' => 'Operasional - Ritase & Armada',
                'icon' => 'cil-truck',
                'description' => 'Pencatatan truk sampah masuk (ritase), verifikasi DLH, armada, dan klien pengirim',
                'permissions' => [
                    'view_ritase'       => 'Melihat Data Ritase Sampah Masuk',
                    'create_ritase'     => 'Menambah Data Ritase Masuk',
                    'update_ritase'     => 'Mengubah / Mengoreksi Data Ritase',
                    'delete_ritase'     => 'Menghapus Data Ritase',
                    'view_ritase_dlh'   => 'Melihat Ritase DLH (Disetujui & Dibayar)',
                    'view_armada'       => 'Melihat Data Armada Truk',
                    'create_armada'     => 'Menambah Armada Truk Baru',
                    'update_armada'     => 'Mengubah Data Armada',
                    'delete_armada'     => 'Menghapus Data Armada',
                    'view_klien'        => 'Melihat Data Klien (DLH & Swasta)',
                    'create_klien'      => 'Menambah Data Klien Baru',
                    'update_klien'      => 'Mengubah Data Klien',
                    'delete_klien'      => 'Menghapus Data Klien',
                ]
            ],
            'operasional_produksi' => [
                'name' => 'Operasional - Pilahan & Residu',
                'icon' => 'cil-recycle',
                'description' => 'Hasil pilahan bernilai ekonomis, penjualan komoditas/RDF, dan pengangkutan residu ke TPA',
                'permissions' => [
                    'view_hasil_pilahan'        => 'Melihat Data Hasil Pilahan',
                    'create_hasil_pilahan'      => 'Menambah Pencatatan Hasil Pilahan',
                    'update_hasil_pilahan'      => 'Mengubah Data Hasil Pilahan',
                    'delete_hasil_pilahan'      => 'Menghapus Data Hasil Pilahan',
                    'view_penjualan'            => 'Melihat Penjualan Komoditas / Hasil Pilahan',
                    'create_penjualan'          => 'Membuat Transaksi Penjualan Komoditas',
                    'update_penjualan'          => 'Mengubah Transaksi Penjualan',
                    'delete_penjualan'          => 'Menghapus Transaksi Penjualan',
                    'view_pengangkutan_residu'  => 'Melihat Pengangkutan Residu',
                    'create_pengangkutan_residu'=> 'Mencatat Pengangkutan Residu ke TPA',
                    'update_pengangkutan_residu'=> 'Mengubah Data Residu',
                    'delete_pengangkutan_residu'=> 'Menghapus Data Residu',
                ]
            ],
            'operasional_mesin' => [
                'name' => 'Operasional - Mesin & Logbook',
                'icon' => 'cil-memory',
                'description' => 'Master data mesin operasional TPST dan logbook aktivitas/kendala mesin',
                'permissions' => [
                    'view_machine'          => 'Melihat Master Data Mesin',
                    'create_machine'        => 'Menambah Mesin Baru',
                    'update_machine'        => 'Mengubah Data Mesin',
                    'delete_machine'        => 'Menghapus Data Mesin',
                    'view_machine_log'      => 'Melihat Logbook Aktivitas Mesin',
                    'create_machine_log'    => 'Mencatat Logbook Aktivitas Mesin',
                    'update_machine_log'    => 'Mengubah Logbook Mesin',
                    'delete_machine_log'    => 'Menghapus Logbook Mesin',
                ]
            ],
            'keuangan_akuntansi' => [
                'name' => 'Keuangan - Akuntansi & Kas',
                'icon' => 'cil-money',
                'description' => 'Bagan akun (COA), jurnal umum akuntansi, jurnal kas, dan rekonsiliasi mutasi bank',
                'permissions' => [
                    'view_coa'              => 'Melihat Bagan Akun (Chart of Accounts)',
                    'create_coa'            => 'Menambah Akun COA Baru',
                    'update_coa'            => 'Mengubah Data Akun COA',
                    'delete_coa'            => 'Menghapus Akun COA',
                    'view_jurnal'           => 'Melihat Jurnal Umum Akuntansi',
                    'create_jurnal'         => 'Membuat Transaksi Jurnal Umum',
                    'update_jurnal'         => 'Mengubah / Posting / Unpost Jurnal',
                    'delete_jurnal'         => 'Menghapus / Purge Jurnal Umum',
                    'view_jurnal_kas'       => 'Melihat Jurnal Kas (Masuk & Keluar)',
                    'create_jurnal_kas'     => 'Membuat Kas Masuk / Keluar / Transfer',
                    'update_jurnal_kas'     => 'Mengubah Data Jurnal Kas',
                    'delete_jurnal_kas'     => 'Menghapus Jurnal Kas',
                    'view_rekonsiliasi_bank'=> 'Melihat & Memproses Rekonsiliasi Bank',
                ]
            ],
            'keuangan_invoice' => [
                'name' => 'Keuangan - Invoice & Buku Pembantu',
                'icon' => 'cil-description',
                'description' => 'Penagihan invoice tipping fee, data vendor rekanan, dan buku pembantu piutang/utang',
                'permissions' => [
                    'view_invoice'          => 'Melihat Data Invoice Penagihan',
                    'create_invoice'        => 'Membuat Tagihan / Generate DLH Bulanan',
                    'update_invoice'        => 'Mengubah / Sinkronisasi / Reminder WA Invoice',
                    'delete_invoice'        => 'Menghapus / Membatalkan Tagihan Invoice',
                    'view_vendor'           => 'Melihat Data Vendor Rekanan',
                    'create_vendor'         => 'Menambah Vendor Baru',
                    'update_vendor'         => 'Mengubah Data Vendor',
                    'delete_vendor'         => 'Menghapus Data Vendor',
                    'view_buku_pembantu'    => 'Melihat Buku Pembantu Piutang & Utang',
                    'view_tracing'          => 'Melihat Portal Tracing Transaksi & Audit Trail',
                ]
            ],
            'laporan_analitik' => [
                'name' => 'Laporan & Analitik',
                'icon' => 'cil-chart',
                'description' => 'Laporan keuangan standar akuntansi, laporan operasional terperinci, dan grafik analitik komparatif',
                'permissions' => [
                    'view_laporan_keuangan'         => 'Laporan Keuangan (Laba Rugi, Posisi Keuangan, Arus Kas, Buku Besar, dll)',
                    'view_laporan_operasional'      => 'Akses Utama Semua Laporan Operasional',
                    'view_laporan_ritase'           => 'Laporan Ritase Harian & Rerata Bulanan',
                    'view_laporan_rekap_ritase'     => 'Laporan Rekapitulasi Ritase',
                    'view_laporan_rekap_ritase_2'   => 'Laporan Rekapitulasi Ritase II',
                    'view_laporan_penjualan_op'     => 'Laporan Penjualan (Per Klien & Offtaker per Invoice)',
                    'view_laporan_hasil_pilahan'    => 'Laporan Hasil Pilahan & Kartu Stok Item',
                    'view_laporan_residu'           => 'Laporan Pengangkutan Residu & Rerata Bulanan',
                    'view_laporan_kehadiran'        => 'Laporan Rekapitulasi Kehadiran',
                    'view_laporan_upah'             => 'Laporan Rekapitulasi Gaji & Upah',
                    'view_statistik_komparatif'     => 'Statistik Komparatif & Grafik Tren Analitik',
                ]
            ],
            'hrd' => [
                'name' => 'Sumber Daya Manusia (HRD)',
                'icon' => 'cil-people',
                'description' => 'Master data karyawan, absensi kehadiran, output pemilah sampah, dan perhitungan upah borongan/harian/bulanan',
                'permissions' => [
                    'view_employee'         => 'Melihat Data Master Karyawan',
                    'create_employee'       => 'Menambah Data Karyawan Baru',
                    'update_employee'       => 'Mengubah Data Karyawan',
                    'delete_employee'       => 'Menghapus Data Karyawan',
                    'view_attendance'       => 'Melihat Rekap Kehadiran / Absensi',
                    'create_attendance'     => 'Mencatat Kehadiran / Quick Check-In',
                    'update_attendance'     => 'Mengubah Rekaman Kehadiran',
                    'delete_attendance'     => 'Menghapus Catatan Kehadiran',
                    'view_employee_output'  => 'Melihat Output Hasil Timbangan Pemilah',
                    'create_employee_output'=> 'Mencatat Output Timbangan Pemilah',
                    'update_employee_output'=> 'Mengubah Catatan Output Pemilah',
                    'delete_employee_output'=> 'Menghapus Catatan Output Pemilah',
                    'view_waste_category'   => 'Melihat Kategori Sampah Pilahan',
                    'create_waste_category' => 'Menambah Kategori Sampah Baru',
                    'update_waste_category' => 'Mengubah Kategori Sampah',
                    'delete_waste_category' => 'Menghapus Kategori Sampah',
                    'view_wage_rate'        => 'Melihat Master Tarif Upah',
                    'create_wage_rate'      => 'Menambah Tarif Upah Baru',
                    'update_wage_rate'      => 'Mengubah Nilai Tarif Upah',
                    'delete_wage_rate'      => 'Menghapus Tarif Upah',
                    'view_wage_calculation' => 'Melihat Rekap Perhitungan Upah',
                    'create_wage_calculation'=> 'Menghitung (Calculate) Gaji & Upah Karyawan',
                    'update_wage_calculation'=> 'Approve / Bayar / Recalculate Upah',
                    'delete_wage_calculation'=> 'Menghapus Perhitungan Upah',
                ]
            ],
            'administrasi_sistem' => [
                'name' => 'Administrasi & Sistem',
                'icon' => 'cil-settings',
                'description' => 'Manajemen pengguna, pengaturan profil perusahaan, audit log, asisten AI cerdas, dan multi-tenant central',
                'permissions' => [
                    'view_users'            => 'Melihat & Mengelola Pengguna (Users & Roles)',
                    'create_users'          => 'Menambah Pengguna Baru',
                    'update_users'          => 'Mengubah Data Pengguna / Reset Password',
                    'delete_users'          => 'Menghapus / Menonaktifkan Pengguna',
                    'view_company_settings' => 'Melihat Profil Perusahaan & Kop Surat',
                    'update_company_settings'=> 'Mengubah Pengaturan Perusahaan & Kop',
                    'view_activity_log'     => 'Melihat Log Rekam Jejak Aktivitas Sistem',
                    'view_ai_assistant'     => 'Menggunakan Widget Asisten AI (Chatbot TPST)',
                    'view_tenants'          => 'Melihat Manajemen Tenant (Central Panel)',
                    'create_tenants'        => 'Menambah Tenant Baru',
                    'update_tenants'        => 'Mengubah Data Tenant',
                    'delete_tenants'        => 'Menghapus Tenant',
                    'view_central_users'    => 'Melihat Semua Pengguna Lintas Tenant',
                ]
            ],
        ];
    }
}

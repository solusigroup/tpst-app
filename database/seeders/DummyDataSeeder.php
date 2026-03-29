<?php

namespace Database\Seeders;

use App\Models\Armada;
use App\Models\Attendance;
use App\Models\BukuPembantu;
use App\Models\Coa;
use App\Models\EmployeeOutput;
use App\Models\HasilPilahan;
use App\Models\Invoice;
use App\Models\JurnalHeader;
use App\Models\JurnalKas;
use App\Models\Klien;
use App\Models\Penjualan;
use App\Models\ProduksiHarian;
use App\Models\Ritase;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Vendor;
use App\Models\WageCalculation;
use App\Models\WageRate;
use App\Models\WasteCategory;
use App\Scopes\TenantScope;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Dummy data ini dibuat untuk memperlihatkan relasi dan koneksi antar modul:
     *
     * ALUR RELASI UTAMA:
     * ┌─────────┐
     * │ Tenant  │──┐
     * └─────────┘  │
     *              ├── Users (admin, operator, keuangan, hrd, karyawan)
     *              ├── Klien ──── Armada
     *              │     │           │
     *              │     └───┐       │
     *              │         ▼       ▼
     *              │      Ritase ◄───┘
     *              │         │
     *              │         ▼
     *              ├── Invoice ────► JurnalHeader ────► JurnalDetail ────► COA
     *              │     ▲                                   │
     *              │     │                                   ▼
     *              │  Penjualan                        BukuPembantu
     *              │                                        ▲
     *              ├── Vendor ──────────────────────────────┘
     *              │
     *              ├── JurnalKas ────► JurnalHeader ────► JurnalDetail
     *              │
     *              ├── ProduksiHarian
     *              ├── HasilPilahan
     *              │
     *              └── HRD Module:
     *                   ├── WasteCategory ──── WageRate
     *                   ├── Attendance (User)
     *                   ├── EmployeeOutput (User + WasteCategory)
     *                   └── WageCalculation (User)
     */
    public function run(): void
    {
        $this->command->info('🔄 Memulai seeding dummy data...');

        // ─── 1. TENANT ───────────────────────────────────────────
        $tenant = Tenant::where('name', 'PT Tatabumi Adilimbah')
            ->orWhere('domain', 'sampahjaya.test')
            ->first();

        if (!$tenant) {
            $tenant = Tenant::create([
                'name'                => 'PT Tatabumi Adilimbah',
                'domain'              => 'sampahjaya.test',
                'address'             => 'Jl. Raya Tambakboyo No. 123, Lamongan, Jawa Timur',
                'email'               => 'info@tatabumi.id',
                'bank_name'           => 'Bank Jatim',
                'bank_account_number' => '0123456789',
                'bank_account_name'   => 'PT Tatabumi Adilimbah',
                'director_name'       => 'Budi Sucahyo',
                'manager_name'        => 'Sucahyo',
                'finance_name'        => 'Ana',
            ]);
        }
        $tenantId = $tenant->id;
        $this->command->info("✅ Tenant: {$tenant->name} (ID: {$tenantId})");

        // ─── 2. USERS (berbagai role) ────────────────────────────
        $users = [
            [
                'name' => 'Pak Budi (Operator)',
                'username' => 'operator1',
                'email' => 'operator1@tpst.test',
                'password' => Hash::make('password123'),
                'role' => 'operator',
                'tenant_id' => $tenantId,
                'position' => 'Operator Jembatan Timbang',
                'salary_type' => 'bulanan',
                'monthly_salary' => 3500000,
            ],
            [
                'name' => 'Bu Ana (Keuangan)',
                'username' => 'keuangan1',
                'email' => 'keuangan1@tpst.test',
                'password' => Hash::make('password123'),
                'role' => 'keuangan',
                'tenant_id' => $tenantId,
                'position' => 'Staff Keuangan',
                'salary_type' => 'bulanan',
                'monthly_salary' => 4000000,
            ],
            [
                'name' => 'Pak Joko (HRD)',
                'username' => 'hrd1',
                'email' => 'hrd1@tpst.test',
                'password' => Hash::make('password123'),
                'role' => 'hrd',
                'tenant_id' => $tenantId,
                'position' => 'Staff HRD',
                'salary_type' => 'bulanan',
                'monthly_salary' => 3800000,
            ],
            [
                'name' => 'Siti Aminah (Pemilah)',
                'username' => 'karyawan1',
                'email' => 'karyawan1@tpst.test',
                'password' => Hash::make('password123'),
                'role' => 'karyawan',
                'tenant_id' => $tenantId,
                'position' => 'Pemilah Sampah',
                'salary_type' => 'borongan',
                'monthly_salary' => null,
            ],
            [
                'name' => 'Ahmad Fauzi (Pemilah)',
                'username' => 'karyawan2',
                'email' => 'karyawan2@tpst.test',
                'password' => Hash::make('password123'),
                'role' => 'karyawan',
                'tenant_id' => $tenantId,
                'position' => 'Pemilah Sampah',
                'salary_type' => 'borongan',
                'monthly_salary' => null,
            ],
            [
                'name' => 'Rudi Hartono (Pemilah)',
                'username' => 'karyawan3',
                'email' => 'karyawan3@tpst.test',
                'password' => Hash::make('password123'),
                'role' => 'karyawan',
                'tenant_id' => $tenantId,
                'position' => 'Pemilah Sampah',
                'salary_type' => 'borongan',
                'monthly_salary' => null,
            ],
        ];

        $createdUsers = [];
        foreach ($users as $userData) {
            $user = User::withoutGlobalScope(TenantScope::class)
                ->firstOrCreate(
                    ['username' => $userData['username']],
                    $userData
                );
            // Assign Spatie role
            $roleMap = [
                'operator' => 'operator',
                'keuangan' => 'keuangan',
                'hrd' => 'hrd',
                'karyawan' => 'karyawan',
            ];
            if (isset($roleMap[$userData['role']]) && !$user->hasRole($roleMap[$userData['role']])) {
                $user->assignRole($roleMap[$userData['role']]);
            }
            $createdUsers[$userData['username']] = $user;
        }
        $this->command->info("✅ Users: " . count($createdUsers) . " users dibuat");

        // ─── 3. KLIEN (Pelanggan) ────────────────────────────────
        $klienData = [
            ['nama_klien' => 'DLH Kab. Lamongan',    'jenis' => 'DLH',      'kontak' => '0322-321456', 'alamat' => 'Jl. Lamongrejo No. 1'],
            ['nama_klien' => 'PT Maju Bersama',       'jenis' => 'Swasta',   'kontak' => '0322-654321', 'alamat' => 'Jl. Industri No. 55'],
            ['nama_klien' => 'Hotel Grand Lamongan',   'jenis' => 'Swasta',   'kontak' => '0322-111222', 'alamat' => 'Jl. Veteran No. 12'],
            ['nama_klien' => 'RS Muhammadiyah',        'jenis' => 'Swasta',   'kontak' => '0322-333444', 'alamat' => 'Jl. Jaksa Agung No. 8'],
            ['nama_klien' => 'Pasar Besar Lamongan',   'jenis' => 'Offtaker', 'kontak' => '0322-555666', 'alamat' => 'Jl. Pasar No. 1'],
        ];

        $kliens = [];
        foreach ($klienData as $kd) {
            $klien = Klien::withoutGlobalScope(TenantScope::class)
                ->firstOrCreate(
                    ['tenant_id' => $tenantId, 'nama_klien' => $kd['nama_klien']],
                    array_merge($kd, ['tenant_id' => $tenantId])
                );
            $kliens[] = $klien;
        }
        $this->command->info("✅ Klien: " . count($kliens) . " klien dibuat");

        // ─── 4. ARMADA (Kendaraan per Klien) ─────────────────────
        $armadaData = [
            // DLH
            ['klien_idx' => 0, 'plat_nomor' => 'S 1234 AB', 'kapasitas_maksimal' => 8.00],
            ['klien_idx' => 0, 'plat_nomor' => 'S 5678 CD', 'kapasitas_maksimal' => 10.00],
            ['klien_idx' => 0, 'plat_nomor' => 'S 9012 EF', 'kapasitas_maksimal' => 6.00],
            // PT Maju Bersama
            ['klien_idx' => 1, 'plat_nomor' => 'L 2468 GH', 'kapasitas_maksimal' => 12.00],
            ['klien_idx' => 1, 'plat_nomor' => 'L 1357 IJ', 'kapasitas_maksimal' => 8.00],
            // Hotel Grand
            ['klien_idx' => 2, 'plat_nomor' => 'S 7777 KL', 'kapasitas_maksimal' => 4.00],
            // RS
            ['klien_idx' => 3, 'plat_nomor' => 'S 8888 MN', 'kapasitas_maksimal' => 6.00],
            // Pasar Besar
            ['klien_idx' => 4, 'plat_nomor' => 'S 3333 OP', 'kapasitas_maksimal' => 10.00],
            ['klien_idx' => 4, 'plat_nomor' => 'S 4444 QR', 'kapasitas_maksimal' => 8.00],
        ];

        $armadas = [];
        foreach ($armadaData as $ad) {
            $armada = Armada::withoutGlobalScope(TenantScope::class)
                ->firstOrCreate(
                    ['tenant_id' => $tenantId, 'plat_nomor' => $ad['plat_nomor']],
                    [
                        'tenant_id' => $tenantId,
                        'klien_id' => $kliens[$ad['klien_idx']]->id,
                        'plat_nomor' => $ad['plat_nomor'],
                        'kapasitas_maksimal' => $ad['kapasitas_maksimal'],
                    ]
                );
            $armadas[] = $armada;
        }
        $this->command->info("✅ Armada: " . count($armadas) . " kendaraan dibuat");

        // ─── 5. VENDOR (Pemasok) ─────────────────────────────────
        $vendorData = [
            ['nama_vendor' => 'CV Spare Part Jaya',    'kontak' => '081234567890', 'alamat' => 'Jl. Bengkel No. 5, Lamongan'],
            ['nama_vendor' => 'UD Solar Makmur',       'kontak' => '081345678901', 'alamat' => 'Jl. SPBU No. 10, Lamongan'],
            ['nama_vendor' => 'Toko Alat Safety Indo',  'kontak' => '081456789012', 'alamat' => 'Jl. Industri No. 22, Surabaya'],
        ];

        $vendors = [];
        foreach ($vendorData as $vd) {
            $vendor = Vendor::withoutGlobalScope(TenantScope::class)
                ->firstOrCreate(
                    ['tenant_id' => $tenantId, 'nama_vendor' => $vd['nama_vendor']],
                    array_merge($vd, ['tenant_id' => $tenantId])
                );
            $vendors[] = $vendor;
        }
        $this->command->info("✅ Vendor: " . count($vendors) . " vendor dibuat");

        // ─── 6. COA (pastikan sudah ada, jika belum panggil seeder) ──
        $coaCount = Coa::withoutGlobalScope(TenantScope::class)->where('tenant_id', $tenantId)->count();
        if ($coaCount === 0) {
            $this->call(CoaSeeder::class);
        }
        $this->command->info("✅ COA: {$coaCount} akun tersedia");

        // ─── 7. WASTE CATEGORIES & WAGE RATES ────────────────────
        $wcCount = WasteCategory::where('tenant_id', $tenantId)->count();
        if ($wcCount === 0) {
            $this->call(WasteCategorySeeder::class);
            $this->call(WageRateSeeder::class);
        }
        $wasteCategories = WasteCategory::where('tenant_id', $tenantId)->get();
        $this->command->info("✅ Waste Categories: {$wasteCategories->count()} kategori");

        // ─── 8. RITASE (Pengangkutan Sampah) ─────────────────────
        // 30 hari terakhir, beberapa ritase per hari
        $ritaseRecords = [];
        $startDate = Carbon::now()->subDays(30);

        $jenisSampah = ['Organik', 'Anorganik', 'B3', 'Campuran'];
        $biayaPerTon = [150000, 200000, 350000, 175000];

        for ($day = 0; $day < 30; $day++) {
            $date = $startDate->copy()->addDays($day);
            if ($date->isWeekend() && rand(0, 1) === 0) continue; // kadang skip weekend

            $tripsPerDay = rand(3, 7);
            for ($t = 0; $t < $tripsPerDay; $t++) {
                $armadaIdx = rand(0, count($armadas) - 1);
                $armada = $armadas[$armadaIdx];
                $klien = $kliens[array_search($armada->klien_id, array_column(array_map(fn($k) => ['id' => $k->id], $kliens), 'id'))] ?? $kliens[0];

                $jIdx = rand(0, 3);
                $beratBruto = round(rand(3000, 12000) / 1000, 2); // 3-12 ton
                $beratTarra = round(rand(1500, 3000) / 1000, 2);  // 1.5-3 ton
                $biayaTipping = round(($beratBruto - $beratTarra) * $biayaPerTon[$jIdx] / 1000, 2);

                $waktuMasuk = $date->copy()->setTime(rand(6, 16), rand(0, 59));
                $waktuKeluar = $waktuMasuk->copy()->addMinutes(rand(15, 90));

                try {
                    $ritase = Ritase::withoutGlobalScope(TenantScope::class)->create([
                        'tenant_id'     => $tenantId,
                        'armada_id'     => $armada->id,
                        'klien_id'      => $klien->id,
                        'waktu_masuk'   => $waktuMasuk,
                        'waktu_keluar'  => $waktuKeluar,
                        'berat_bruto'   => $beratBruto,
                        'berat_tarra'   => $beratTarra,
                        'jenis_sampah'  => $jenisSampah[$jIdx],
                        'biaya_tipping' => $biayaTipping,
                        'status'        => $day < 25 ? 'selesai' : 'proses',
                        'tiket'         => 'TKT-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                        'is_approved'   => $day < 20,
                        'approved_at'   => $day < 20 ? $date->copy()->addHours(rand(1, 4)) : null,
                    ]);
                    $ritaseRecords[] = $ritase;
                } catch (\Exception $e) {
                    // Skip duplicates
                }
            }
        }
        $this->command->info("✅ Ritase: " . count($ritaseRecords) . " records dibuat");

        // ─── 9. INVOICE (Tagihan dari Ritase) ────────────────────
        // Buat invoice per klien per bulan untuk ritase yang sudah approved
        $invoices = [];
        $now = Carbon::now();
        $months = [
            ['bulan' => $now->copy()->subMonth()->month, 'tahun' => $now->copy()->subMonth()->year],
            ['bulan' => $now->month, 'tahun' => $now->year],
        ];

        foreach ($kliens as $klienIdx => $klien) {
            foreach ($months as $period) {
                // Get approved ritase for this klien & period
                $ritaseForInvoice = collect($ritaseRecords)
                    ->filter(fn($r) =>
                        $r->klien_id === $klien->id &&
                        $r->is_approved &&
                        Carbon::parse($r->waktu_masuk)->month === $period['bulan'] &&
                        Carbon::parse($r->waktu_masuk)->year === $period['tahun']
                    );

                if ($ritaseForInvoice->isEmpty()) continue;

                $totalTagihan = $ritaseForInvoice->sum('biaya_tipping');
                $isPastMonth = $period['bulan'] < $now->month || $period['tahun'] < $now->year;

                try {
                    $invoice = Invoice::withoutGlobalScope(TenantScope::class)->create([
                        'tenant_id'           => $tenantId,
                        'klien_id'            => $klien->id,
                        'tanggal_invoice'     => Carbon::create($period['tahun'], $period['bulan'], 25),
                        'tanggal_jatuh_tempo' => Carbon::create($period['tahun'], $period['bulan'], 25)->addDays(30),
                        'periode_bulan'       => $period['bulan'],
                        'periode_tahun'       => $period['tahun'],
                        'total_tagihan'       => $totalTagihan,
                        'status'              => $isPastMonth ? (rand(0, 1) ? 'Paid' : 'Sent') : 'Draft',
                        'keterangan'          => "Tagihan jasa pengelolaan sampah periode " . Carbon::create($period['tahun'], $period['bulan'], 1)->format('F Y'),
                        'deskripsi_layanan'   => 'Jasa Pengangkutan & Pengolahan Sampah',
                    ]);

                    // Link ritase to invoice
                    $ritaseIds = $ritaseForInvoice->pluck('id')->toArray();
                    Ritase::withoutGlobalScope(TenantScope::class)
                        ->whereIn('id', $ritaseIds)
                        ->update([
                            'invoice_id'     => $invoice->id,
                            'status_invoice' => $invoice->status,
                        ]);

                    $invoices[] = $invoice;
                } catch (\Exception $e) {
                    // Skip
                }
            }
        }
        $this->command->info("✅ Invoice: " . count($invoices) . " invoice dibuat (→ JurnalHeader otomatis via Observer)");

        // ─── 10. PENJUALAN (Penjualan Material Daur Ulang) ───────
        $produkDaurUlang = [
            ['jenis' => 'Plastik PET',   'harga' => 3500],
            ['jenis' => 'Plastik HDPE',  'harga' => 4000],
            ['jenis' => 'Kertas HVS',    'harga' => 2000],
            ['jenis' => 'Kardus',        'harga' => 1800],
            ['jenis' => 'Besi/Logam',    'harga' => 5000],
            ['jenis' => 'Aluminium',     'harga' => 12000],
            ['jenis' => 'Kaca Botol',    'harga' => 1500],
            ['jenis' => 'Kompos',        'harga' => 800],
        ];

        $penjualanRecords = [];
        // Pembeli material = klien swasta (idx 1,2,3)
        $pembeliKliens = [$kliens[1], $kliens[2], $kliens[3]];

        for ($day = 0; $day < 30; $day++) {
            $date = $startDate->copy()->addDays($day);
            if ($date->isWeekend()) continue;

            $salesPerDay = rand(1, 3);
            for ($s = 0; $s < $salesPerDay; $s++) {
                $produk = $produkDaurUlang[rand(0, count($produkDaurUlang) - 1)];
                $berat = round(rand(50, 500) / 10, 2); // 5-50 kg
                $pembeli = $pembeliKliens[rand(0, 2)];

                try {
                    $penjualan = Penjualan::withoutGlobalScope(TenantScope::class)->create([
                        'tenant_id'    => $tenantId,
                        'klien_id'     => $pembeli->id,
                        'tanggal'      => $date,
                        'jenis_produk' => $produk['jenis'],
                        'berat_kg'     => $berat,
                        'harga_satuan' => $produk['harga'],
                        'jumlah_bayar' => ($day < 20) ? round($berat * $produk['harga'], 2) : 0,
                    ]);
                    $penjualanRecords[] = $penjualan;
                } catch (\Exception $e) {
                    // Skip
                }
            }
        }
        $this->command->info("✅ Penjualan: " . count($penjualanRecords) . " transaksi dibuat");

        // ─── 11. PRODUKSI HARIAN ─────────────────────────────────
        $produksiRecords = [];
        for ($day = 0; $day < 30; $day++) {
            $date = $startDate->copy()->addDays($day);
            if ($date->isWeekend()) continue;

            $totalInput = round(rand(8000, 25000) / 100, 2); // 80-250 ton

            try {
                $produksi = ProduksiHarian::withoutGlobalScope(TenantScope::class)->create([
                    'tenant_id'         => $tenantId,
                    'tanggal'           => $date,
                    'total_input_sampah' => $totalInput,
                    'hasil_rdf'         => round($totalInput * rand(15, 25) / 100, 2),
                    'hasil_plastik'     => round($totalInput * rand(5, 15) / 100, 2),
                    'hasil_kompos'      => round($totalInput * rand(10, 20) / 100, 2),
                    'residu_tpa'        => round($totalInput * rand(30, 50) / 100, 2),
                ]);
                $produksiRecords[] = $produksi;
            } catch (\Exception $e) {
                // Skip
            }
        }
        $this->command->info("✅ Produksi Harian: " . count($produksiRecords) . " records dibuat");

        // ─── 12. HASIL PILAHAN ───────────────────────────────────
        $kategoriPilahan = ['Limbah Organik', 'Limbah Anorganik', 'Limbah B3'];
        $jenisPilahan = [
            'Limbah Organik'    => ['Sisa Makanan', 'Daun/Ranting', 'Sayur Busuk'],
            'Limbah Anorganik'  => ['Plastik', 'Kertas', 'Logam', 'Kaca'],
            'Limbah B3'         => ['Baterai', 'Lampu Neon', 'Oli Bekas'],
        ];
        $officers = ['Siti Aminah', 'Ahmad Fauzi', 'Rudi Hartono'];

        $hasilPilahanRecords = [];
        for ($day = 0; $day < 30; $day++) {
            $date = $startDate->copy()->addDays($day);
            if ($date->isWeekend()) continue;

            foreach ($kategoriPilahan as $kategori) {
                $jenisList = $jenisPilahan[$kategori];
                $jenis = $jenisList[rand(0, count($jenisList) - 1)];

                try {
                    $hp = HasilPilahan::withoutGlobalScope(TenantScope::class)->create([
                        'tenant_id'  => $tenantId,
                        'tanggal'    => $date,
                        'kategori'   => $kategori,
                        'jenis'      => $jenis,
                        'tonase'     => round(rand(100, 5000) / 100, 2),
                        'officer'    => $officers[rand(0, 2)],
                        'keterangan' => 'Hasil pilahan harian',
                    ]);
                    $hasilPilahanRecords[] = $hp;
                } catch (\Exception $e) {
                    // Skip
                }
            }
        }
        $this->command->info("✅ Hasil Pilahan: " . count($hasilPilahanRecords) . " records dibuat");

        // ─── 13. JURNAL KAS (Penerimaan & Pengeluaran) ───────────
        $coaKas = Coa::withoutGlobalScope(TenantScope::class)
            ->where('tenant_id', $tenantId)
            ->where('kode_akun', '1101')
            ->first();
        $coaBank = Coa::withoutGlobalScope(TenantScope::class)
            ->where('tenant_id', $tenantId)
            ->where('kode_akun', '1102')
            ->first();
        $coaBBM = Coa::withoutGlobalScope(TenantScope::class)
            ->where('tenant_id', $tenantId)
            ->where('kode_akun', '6104')
            ->first();
        $coaPerbaikan = Coa::withoutGlobalScope(TenantScope::class)
            ->where('tenant_id', $tenantId)
            ->where('kode_akun', '6105')
            ->first();
        $coaAPD = Coa::withoutGlobalScope(TenantScope::class)
            ->where('tenant_id', $tenantId)
            ->where('kode_akun', '6107')
            ->first();
        $coaGaji = Coa::withoutGlobalScope(TenantScope::class)
            ->where('tenant_id', $tenantId)
            ->where('kode_akun', '6101')
            ->first();
        $coaPendLain = Coa::withoutGlobalScope(TenantScope::class)
            ->where('tenant_id', $tenantId)
            ->where('kode_akun', '7101')
            ->first();

        $jurnalKasRecords = [];
        if ($coaKas && $coaBank) {
            // Pengeluaran operasional
            $pengeluaran = [
                ['deskripsi' => 'Pembelian BBM Solar 200L', 'nominal' => 2400000, 'coa_lawan' => $coaBBM, 'vendor_idx' => 1],
                ['deskripsi' => 'Pembelian BBM Solar 150L', 'nominal' => 1800000, 'coa_lawan' => $coaBBM, 'vendor_idx' => 1],
                ['deskripsi' => 'Perbaikan mesin conveyor', 'nominal' => 3500000, 'coa_lawan' => $coaPerbaikan, 'vendor_idx' => 0],
                ['deskripsi' => 'Pembelian sparepart belt', 'nominal' => 1200000, 'coa_lawan' => $coaPerbaikan, 'vendor_idx' => 0],
                ['deskripsi' => 'Pembelian APD karyawan (sarung tangan, masker)', 'nominal' => 850000, 'coa_lawan' => $coaAPD, 'vendor_idx' => 2],
                ['deskripsi' => 'Pembelian sepatu safety', 'nominal' => 1500000, 'coa_lawan' => $coaAPD, 'vendor_idx' => 2],
                ['deskripsi' => 'Pembayaran gaji staff bulan lalu', 'nominal' => 15000000, 'coa_lawan' => $coaGaji, 'vendor_idx' => null],
            ];

            foreach ($pengeluaran as $idx => $p) {
                $coaLawan = $p['coa_lawan'];
                if (!$coaLawan) continue;

                try {
                    $jk = JurnalKas::withoutGlobalScope(TenantScope::class)->create([
                        'tenant_id'        => $tenantId,
                        'tipe'             => 'Pengeluaran',
                        'tanggal'          => $startDate->copy()->addDays($idx * 4 + rand(0, 3)),
                        'coa_kas_id'       => $coaKas->id,
                        'coa_lawan_id'     => $coaLawan->id,
                        'nominal'          => $p['nominal'],
                        'deskripsi'        => $p['deskripsi'],
                        'status'           => 'approved',
                        'contactable_type' => $p['vendor_idx'] !== null ? Vendor::class : null,
                        'contactable_id'   => $p['vendor_idx'] !== null ? $vendors[$p['vendor_idx']]->id : null,
                    ]);
                    $jurnalKasRecords[] = $jk;
                } catch (\Exception $e) {
                    // Skip
                }
            }

            // Penerimaan
            if ($coaPendLain) {
                $penerimaan = [
                    ['deskripsi' => 'Penerimaan pembayaran dari DLH', 'nominal' => 25000000, 'klien_idx' => 0],
                    ['deskripsi' => 'Penerimaan penjualan material plastik', 'nominal' => 5500000, 'klien_idx' => 1],
                    ['deskripsi' => 'Penerimaan retribusi Hotel Grand', 'nominal' => 3200000, 'klien_idx' => 2],
                ];

                foreach ($penerimaan as $idx => $p) {
                    try {
                        $jk = JurnalKas::withoutGlobalScope(TenantScope::class)->create([
                            'tenant_id'        => $tenantId,
                            'tipe'             => 'Penerimaan',
                            'tanggal'          => $startDate->copy()->addDays($idx * 7 + 5),
                            'coa_kas_id'       => $coaBank->id,
                            'coa_lawan_id'     => $coaPendLain->id,
                            'nominal'          => $p['nominal'],
                            'deskripsi'        => $p['deskripsi'],
                            'status'           => 'approved',
                            'contactable_type' => Klien::class,
                            'contactable_id'   => $kliens[$p['klien_idx']]->id,
                        ]);
                        $jurnalKasRecords[] = $jk;
                    } catch (\Exception $e) {
                        // Skip
                    }
                }
            }
        }
        $this->command->info("✅ Jurnal Kas: " . count($jurnalKasRecords) . " transaksi (→ JurnalHeader+Detail otomatis)");

        // ─── 14. ATTENDANCE (Absensi) ────────────────────────────
        $employeeUsers = [
            $createdUsers['karyawan1'] ?? null,
            $createdUsers['karyawan2'] ?? null,
            $createdUsers['karyawan3'] ?? null,
            $createdUsers['operator1'] ?? null,
        ];
        $employeeUsers = array_filter($employeeUsers);

        $attendanceRecords = [];
        foreach ($employeeUsers as $empUser) {
            for ($day = 0; $day < 30; $day++) {
                $date = $startDate->copy()->addDays($day);
                if ($date->isWeekend()) continue;

                $statusOptions = ['present', 'present', 'present', 'present', 'present', 'late', 'absent', 'sick'];
                $status = $statusOptions[rand(0, count($statusOptions) - 1)];

                try {
                    $att = Attendance::create([
                        'tenant_id'       => $tenantId,
                        'user_id'         => $empUser->id,
                        'attendance_date' => $date,
                        'check_in'        => $status !== 'absent' ? ($status === 'late' ? '08:' . rand(15, 45) : '07:' . str_pad(rand(0, 30), 2, '0', STR_PAD_LEFT)) : null,
                        'check_out'       => $status !== 'absent' ? '16:' . str_pad(rand(0, 30), 2, '0', STR_PAD_LEFT) : null,
                        'status'          => $status,
                        'notes'           => $status === 'sick' ? 'Sakit (surat dokter)' : ($status === 'absent' ? 'Izin keperluan keluarga' : null),
                    ]);
                    $attendanceRecords[] = $att;
                } catch (\Exception $e) {
                    // Skip duplicate
                }
            }
        }
        $this->command->info("✅ Attendance: " . count($attendanceRecords) . " records dibuat");

        // ─── 15. EMPLOYEE OUTPUT (Hasil Kerja Borongan) ──────────
        $boronganUsers = [
            $createdUsers['karyawan1'] ?? null,
            $createdUsers['karyawan2'] ?? null,
            $createdUsers['karyawan3'] ?? null,
        ];
        $boronganUsers = array_filter($boronganUsers);

        $outputRecords = [];
        foreach ($boronganUsers as $empUser) {
            for ($day = 0; $day < 30; $day++) {
                $date = $startDate->copy()->addDays($day);
                if ($date->isWeekend()) continue;

                // Each worker sorts 1-3 categories per day
                $categoriesPerDay = rand(1, 3);
                $usedCategories = [];

                for ($c = 0; $c < $categoriesPerDay; $c++) {
                    $cat = $wasteCategories->random();
                    if (in_array($cat->id, $usedCategories)) continue;
                    $usedCategories[] = $cat->id;

                    try {
                        $eo = EmployeeOutput::create([
                            'tenant_id'         => $tenantId,
                            'user_id'           => $empUser->id,
                            'waste_category_id' => $cat->id,
                            'output_date'       => $date,
                            'quantity'          => round(rand(10, 80), 2),
                            'unit'              => 'kg',
                            'notes'             => null,
                        ]);
                        $outputRecords[] = $eo;
                    } catch (\Exception $e) {
                        // Skip
                    }
                }
            }
        }
        $this->command->info("✅ Employee Output: " . count($outputRecords) . " records dibuat");

        // ─── 16. WAGE CALCULATION (Perhitungan Upah) ─────────────
        $wageRecords = [];
        foreach ($boronganUsers as $empUser) {
            // Weekly calculations for the past 4 weeks
            for ($week = 0; $week < 4; $week++) {
                $weekStart = Carbon::now()->subWeeks($week)->startOfWeek();
                $weekEnd = $weekStart->copy()->addDays(6);

                // Sum outputs for this week
                $weekOutputs = collect($outputRecords)->filter(fn($o) =>
                    $o->user_id === $empUser->id &&
                    Carbon::parse($o->output_date)->between($weekStart, $weekEnd)
                );

                $totalQty = $weekOutputs->sum('quantity');
                $totalWage = $weekOutputs->sum(fn($o) => $o->getWageForThisOutput());

                if ($totalQty > 0) {
                    try {
                        $wc = WageCalculation::firstOrCreate(
                            [
                                'tenant_id'  => $tenantId,
                                'user_id'    => $empUser->id,
                                'week_start' => $weekStart,
                            ],
                            [
                                'week_end'       => $weekEnd,
                                'total_quantity' => $totalQty,
                                'total_wage'     => $totalWage,
                                'status'         => $week > 1 ? 'paid' : 'pending',
                                'paid_date'      => $week > 1 ? $weekEnd->copy()->addDays(3) : null,
                                'notes'          => $week > 1 ? 'Sudah dibayar via transfer' : null,
                            ]
                        );
                        $wageRecords[] = $wc;
                    } catch (\Exception $e) {
                        // Skip
                    }
                }
            }
        }
        $this->command->info("✅ Wage Calculation: " . count($wageRecords) . " records dibuat");

        // ─── SUMMARY ─────────────────────────────────────────────
        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('  ✅ DUMMY DATA SEEDING SELESAI!');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->newLine();
        $this->command->info('RELASI ANTAR MODUL:');
        $this->command->info('───────────────────────────────────────────────────────');
        $this->command->info('  Tenant → User, Klien, Armada, Vendor, COA');
        $this->command->info('  Klien → Armada → Ritase');
        $this->command->info('  Klien → Ritase → Invoice → JurnalHeader → JurnalDetail');
        $this->command->info('  Klien → Penjualan');
        $this->command->info('  Invoice → BukuPembantu (via JurnalDetail Observer)');
        $this->command->info('  Vendor → BukuPembantu (via JurnalKas)');
        $this->command->info('  JurnalKas → JurnalHeader → JurnalDetail → COA');
        $this->command->info('  User → Attendance');
        $this->command->info('  User → EmployeeOutput → WasteCategory → WageRate');
        $this->command->info('  User → WageCalculation');
        $this->command->info('  Tenant → ProduksiHarian, HasilPilahan');
        $this->command->info('───────────────────────────────────────────────────────');
    }
}

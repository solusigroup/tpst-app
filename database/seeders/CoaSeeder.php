<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coas = [
            ['kode_akun' => '1101', 'nama_akun' => 'Kas Kecil Operasional TPST', 'tipe' => 'Asset', 'klasifikasi' => 'Aset Lancar'],
            ['kode_akun' => '1102', 'nama_akun' => 'Bank (Rekening Bersama KSO)', 'tipe' => 'Asset', 'klasifikasi' => 'Aset Lancar'],
            ['kode_akun' => '1103', 'nama_akun' => 'Piutang Usaha - DLH Lamongan', 'tipe' => 'Asset', 'klasifikasi' => 'Aset Lancar'],
            ['kode_akun' => '1104', 'nama_akun' => 'Piutang Usaha - Swasta/Komersial', 'tipe' => 'Asset', 'klasifikasi' => 'Aset Lancar'],
            ['kode_akun' => '1105', 'nama_akun' => 'Piutang Transito - PT TABUM', 'tipe' => 'Asset', 'klasifikasi' => 'Aset Lancar'],
            ['kode_akun' => '1106', 'nama_akun' => 'Uang Muka & Biaya Dibayar Dimuka', 'tipe' => 'Asset', 'klasifikasi' => 'Aset Lancar'],
            ['kode_akun' => '1201', 'nama_akun' => 'Mesin & Peralatan TPST', 'tipe' => 'Asset', 'klasifikasi' => 'Aset Tidak Lancar'],
            ['kode_akun' => '1202', 'nama_akun' => 'Kendaraan Operasional', 'tipe' => 'Asset', 'klasifikasi' => 'Aset Tidak Lancar'],
            ['kode_akun' => '1203', 'nama_akun' => 'Akumulasi Penyusutan Aset Tetap', 'tipe' => 'Asset', 'klasifikasi' => 'Aset Tidak Lancar'],
            ['kode_akun' => '1204', 'nama_akun' => 'Goodwill / Hak Kelola KSO', 'tipe' => 'Asset', 'klasifikasi' => 'Aset Tidak Lancar'],
            ['kode_akun' => '1205', 'nama_akun' => 'Akumulasi Amortisasi Goodwill', 'tipe' => 'Asset', 'klasifikasi' => 'Aset Tidak Lancar'],
            ['kode_akun' => '2101', 'nama_akun' => 'Utang Usaha', 'tipe' => 'Liability', 'klasifikasi' => 'Liabilitas Jangka Pendek'],
            ['kode_akun' => '2102', 'nama_akun' => 'Hutang Gaji & Upah Borongan', 'tipe' => 'Liability', 'klasifikasi' => 'Liabilitas Jangka Pendek'],
            ['kode_akun' => '2103', 'nama_akun' => 'Hutang Pajak', 'tipe' => 'Liability', 'klasifikasi' => 'Liabilitas Jangka Pendek'],
            ['kode_akun' => '2104', 'nama_akun' => 'Hutang BPJS', 'tipe' => 'Liability', 'klasifikasi' => 'Liabilitas Jangka Pendek'],
            ['kode_akun' => '2105', 'nama_akun' => 'Hutang Pengembalian ROI KSO', 'tipe' => 'Liability', 'klasifikasi' => 'Liabilitas Jangka Pendek'],
            ['kode_akun' => '2106', 'nama_akun' => 'Kewajiban Dana Cadangan (Sinking Fund)', 'tipe' => 'Liability', 'klasifikasi' => 'Liabilitas Jangka Panjang'],
            ['kode_akun' => '2107', 'nama_akun' => 'Pencadangan THR Karyawan', 'tipe' => 'Liability', 'klasifikasi' => 'Liabilitas Jangka Pendek'],
            ['kode_akun' => '3101', 'nama_akun' => 'Modal Disetor - PT PBS', 'tipe' => 'Equity', 'klasifikasi' => 'Ekuitas'],
            ['kode_akun' => '3102', 'nama_akun' => 'Modal Disetor - PT TABUM', 'tipe' => 'Equity', 'klasifikasi' => 'Ekuitas'],
            ['kode_akun' => '3201', 'nama_akun' => 'Penarikan ROI / Prive - PT PBS', 'tipe' => 'Equity', 'klasifikasi' => 'Ekuitas'],
            ['kode_akun' => '3202', 'nama_akun' => 'Penarikan ROI / Prive - PT TABUM', 'tipe' => 'Equity', 'klasifikasi' => 'Ekuitas'],
            ['kode_akun' => '3301', 'nama_akun' => 'Laba Ditahan / Retained Earnings', 'tipe' => 'Equity', 'klasifikasi' => 'Ekuitas'],
            ['kode_akun' => '3302', 'nama_akun' => 'Dividen Dibagikan', 'tipe' => 'Equity', 'klasifikasi' => 'Ekuitas'],
            ['kode_akun' => '3303', 'nama_akun' => 'Laba/(Rugi) Tahun Berjalan', 'tipe' => 'Equity', 'klasifikasi' => 'Ekuitas'],
            ['kode_akun' => '4101', 'nama_akun' => 'Pendapatan Jasa Pengelolaan', 'tipe' => 'Revenue', 'klasifikasi' => 'Pendapatan Operasional'],
            ['kode_akun' => '4102', 'nama_akun' => 'Pendapatan Penjualan Material Daur Ulang', 'tipe' => 'Revenue', 'klasifikasi' => 'Pendapatan Operasional'],
            ['kode_akun' => '4103', 'nama_akun' => 'Pendapatan Retribusi Swasta/Komersial', 'tipe' => 'Revenue', 'klasifikasi' => 'Pendapatan Operasional'],
            ['kode_akun' => '5101', 'nama_akun' => 'Beban Upah Borongan Pemilah', 'tipe' => 'Expense', 'klasifikasi' => 'Harga Pokok Penjualan'],
            ['kode_akun' => '5102', 'nama_akun' => 'Beban Bahan Penolong', 'tipe' => 'Expense', 'klasifikasi' => 'Harga Pokok Penjualan'],
            ['kode_akun' => '6101', 'nama_akun' => 'Beban Gaji Manajemen & Operasional', 'tipe' => 'Expense', 'klasifikasi' => 'Beban Operasional'],
            ['kode_akun' => '6102', 'nama_akun' => 'Beban BPJS', 'tipe' => 'Expense', 'klasifikasi' => 'Beban Operasional'],
            ['kode_akun' => '6103', 'nama_akun' => 'Beban Pajak', 'tipe' => 'Expense', 'klasifikasi' => 'Beban Operasional'],
            ['kode_akun' => '6104', 'nama_akun' => 'Beban BBM & Operasional', 'tipe' => 'Expense', 'klasifikasi' => 'Beban Operasional'],
            ['kode_akun' => '6105', 'nama_akun' => 'Beban Perbaikan & Pemeliharaan', 'tipe' => 'Expense', 'klasifikasi' => 'Beban Operasional'],
            ['kode_akun' => '6106', 'nama_akun' => 'Beban Internet', 'tipe' => 'Expense', 'klasifikasi' => 'Beban Operasional'],
            ['kode_akun' => '6107', 'nama_akun' => 'Beban Perlengkapan K3 & APD', 'tipe' => 'Expense', 'klasifikasi' => 'Beban Operasional'],
            ['kode_akun' => '6108', 'nama_akun' => 'Beban Rumah Tangga & Kantor', 'tipe' => 'Expense', 'klasifikasi' => 'Beban Operasional'],
            ['kode_akun' => '6109', 'nama_akun' => 'Beban Penyusutan Aset Tetap', 'tipe' => 'Expense', 'klasifikasi' => 'Beban Operasional'],
            ['kode_akun' => '6110', 'nama_akun' => 'Beban Amortisasi Goodwill', 'tipe' => 'Expense', 'klasifikasi' => 'Beban Operasional'],
            ['kode_akun' => '7101', 'nama_akun' => 'Pendapatan Lain di Luar Usaha', 'tipe' => 'Revenue', 'klasifikasi' => 'Pendapatan Non-Operasional'],
            ['kode_akun' => '7102', 'nama_akun' => 'Pendapatan Bunga Bank', 'tipe' => 'Revenue', 'klasifikasi' => 'Pendapatan Non-Operasional'],
            ['kode_akun' => '8101', 'nama_akun' => 'Beban Lain di Luar Usaha', 'tipe' => 'Expense', 'klasifikasi' => 'Beban Non-Operasional'],
            ['kode_akun' => '8102', 'nama_akun' => 'Beban Administrasi Bank', 'tipe' => 'Expense', 'klasifikasi' => 'Beban Non-Operasional'],
        ];

        foreach ($coas as $coa) {
            \App\Models\Coa::withoutGlobalScopes()->updateOrCreate(
                ['tenant_id' => 1, 'kode_akun' => $coa['kode_akun']],
                [
                    'nama_akun' => $coa['nama_akun'],
                    'tipe' => $coa['tipe'],
                    'klasifikasi' => $coa['klasifikasi'],
                ]
            );
        }
    }
}

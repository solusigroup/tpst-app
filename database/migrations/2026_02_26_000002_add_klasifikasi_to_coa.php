<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coa', function (Blueprint $table) {
            $table->string('klasifikasi')->nullable()->after('tipe');
        });

        // Auto-classify existing records
        DB::table('coa')->where('kode_akun', 'like', '11%')->update(['klasifikasi' => 'Aset Lancar']);
        DB::table('coa')->where('kode_akun', 'like', '12%')->update(['klasifikasi' => 'Aset Lancar']);
        DB::table('coa')->where('kode_akun', 'like', '13%')->update(['klasifikasi' => 'Aset Tidak Lancar']);
        DB::table('coa')->where('kode_akun', 'like', '14%')->update(['klasifikasi' => 'Aset Tidak Lancar']);
        DB::table('coa')->where('kode_akun', 'like', '15%')->update(['klasifikasi' => 'Aset Tidak Lancar']);
        DB::table('coa')->where('kode_akun', 'like', '21%')->update(['klasifikasi' => 'Liabilitas Jangka Pendek']);
        DB::table('coa')->where('kode_akun', 'like', '22%')->update(['klasifikasi' => 'Liabilitas Jangka Panjang']);
        DB::table('coa')->where('tipe', 'Equity')->whereNull('klasifikasi')->update(['klasifikasi' => 'Ekuitas']);
        DB::table('coa')->where('tipe', 'Revenue')->whereNull('klasifikasi')->update(['klasifikasi' => 'Pendapatan']);
        DB::table('coa')->where('tipe', 'Expense')->whereNull('klasifikasi')->update(['klasifikasi' => 'Beban']);
    }

    public function down(): void
    {
        Schema::table('coa', function (Blueprint $table) {
            $table->dropColumn('klasifikasi');
        });
    }
};

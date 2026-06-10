<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('coa', function (Blueprint $table) {
            $table->string('kategori_buku_pembantu')->nullable()->after('klasifikasi');
        });

        // Safe dynamic initialization based on code and name patterns
        DB::table('coa')
            ->where(function ($query) {
                $query->whereIn('kode_akun', ['1103', '1113'])
                      ->where('nama_akun', 'like', '%Piutang%');
            })
            ->orWhere('nama_akun', 'like', '%Piutang%DLH%')
            ->update(['kategori_buku_pembantu' => 'piutang_dlh']);

        DB::table('coa')
            ->where(function ($query) {
                $query->whereIn('kode_akun', ['1104', '1114'])
                      ->where('nama_akun', 'like', '%Piutang%');
            })
            ->orWhere('nama_akun', 'like', '%Piutang%Swasta%')
            ->update(['kategori_buku_pembantu' => 'piutang_swasta']);

        DB::table('coa')
            ->where(function ($query) {
                $query->whereIn('kode_akun', ['1105', '1115'])
                      ->where('nama_akun', 'like', '%Piutang%');
            })
            ->orWhere('nama_akun', 'like', '%Piutang%TABUM%')
            ->orWhere('nama_akun', 'like', '%Piutang%Offtaker%')
            ->update(['kategori_buku_pembantu' => 'piutang_offtaker']);
            
        DB::table('coa')
            ->where('kode_akun', 'like', '21%')
            ->where(function ($query) {
                $query->where('nama_akun', 'like', '%Utang Usaha%')
                      ->orWhere('nama_akun', 'like', '%Hutang Usaha%');
            })
            ->update(['kategori_buku_pembantu' => 'utang_usaha']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coa', function (Blueprint $table) {
            $table->dropColumn('kategori_buku_pembantu');
        });
    }
};

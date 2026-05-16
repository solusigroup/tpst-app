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
        Schema::table('penjualan', function (Blueprint $table) {
            $table->foreignId('waste_category_id')->nullable()->after('klien_id')->constrained('waste_categories')->nullOnDelete();
        });

        // Try to populate waste_category_id based on existing jenis_produk (string)
        $penjualans = DB::table('penjualan')->get();
        foreach ($penjualans as $penjualan) {
            if ($penjualan->jenis_produk) {
                $category = DB::table('waste_categories')
                    ->where('tenant_id', $penjualan->tenant_id)
                    ->where('name', $penjualan->jenis_produk)
                    ->first();
                
                if ($category) {
                    DB::table('penjualan')
                        ->where('id', $penjualan->id)
                        ->update(['waste_category_id' => $category->id]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropForeign(['waste_category_id']);
            $table->dropColumn('waste_category_id');
        });
    }
};

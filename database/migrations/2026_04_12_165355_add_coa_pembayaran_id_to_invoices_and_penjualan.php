<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('coa_pembayaran_id')->nullable()->constrained('coa')->onDelete('set null');
        });

        Schema::table('penjualan', function (Blueprint $table) {
            $table->foreignId('coa_pembayaran_id')->nullable()->constrained('coa')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['coa_pembayaran_id']);
            $table->dropColumn('coa_pembayaran_id');
        });

        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropForeign(['coa_pembayaran_id']);
            $table->dropColumn('coa_pembayaran_id');
        });
    }
};

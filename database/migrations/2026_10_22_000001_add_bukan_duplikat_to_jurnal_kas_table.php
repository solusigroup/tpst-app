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
        Schema::table('jurnal_kas', function (Blueprint $table) {
            $table->boolean('is_bukan_duplikat')->default(false)->after('bukti_transaksi');
            $table->string('catatan_duplikat')->nullable()->after('is_bukan_duplikat');
            $table->foreignId('verified_duplikat_by')->nullable()->after('catatan_duplikat')->constrained('users')->nullOnDelete();
            $table->timestamp('verified_duplikat_at')->nullable()->after('verified_duplikat_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal_kas', function (Blueprint $table) {
            $table->dropForeign(['verified_duplikat_by']);
            $table->dropColumn([
                'is_bukan_duplikat',
                'catatan_duplikat',
                'verified_duplikat_by',
                'verified_duplikat_at',
            ]);
        });
    }
};

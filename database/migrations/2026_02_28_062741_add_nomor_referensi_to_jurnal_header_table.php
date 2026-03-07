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
        Schema::table('jurnal_header', function (Blueprint $table) {
            if (!Schema::hasColumn('jurnal_header', 'nomor_referensi')) {
                $table->string('nomor_referensi')->nullable()->after('tanggal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal_header', function (Blueprint $table) {
            $table->dropColumn('nomor_referensi');
        });
    }
};

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
            $table->string('status')->default('draft')->after('deskripsi'); // draft, posted, unposted
            $table->string('bukti_transaksi')->nullable()->after('status');
        });

        Schema::table('jurnal_kas', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('deskripsi'); // draft, posted, unposted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal_header', function (Blueprint $table) {
            $table->dropColumn(['status', 'bukti_transaksi']);
        });

        Schema::table('jurnal_kas', function (Blueprint $table) {
            $table->dropColumn(['status']);
        });
    }
};

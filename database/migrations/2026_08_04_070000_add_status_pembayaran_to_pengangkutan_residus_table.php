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
        Schema::table('pengangkutan_residus', function (Blueprint $table) {
            $table->string('status_pembayaran')->default('Belum')->after('biaya_retribusi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengangkutan_residus', function (Blueprint $table) {
            $table->dropColumn('status_pembayaran');
        });
    }
};

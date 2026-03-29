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
        // 1. Add soft deletes to master tables
        Schema::table('users', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('klien', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('vendors', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('coa', function (Blueprint $table) { $table->softDeletes(); });
        Schema::table('armada', function (Blueprint $table) { $table->softDeletes(); });

        // 2. Update foreign keys to restrictOnDelete instead of cascade
        
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['klien_id']);
            $table->foreign('klien_id')->references('id')->on('klien')->restrictOnDelete();
        });

        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropForeign(['klien_id']);
            $table->foreign('klien_id')->references('id')->on('klien')->restrictOnDelete();
        });

        Schema::table('ritase', function (Blueprint $table) {
            $table->dropForeign(['klien_id']);
            $table->foreign('klien_id')->references('id')->on('klien')->restrictOnDelete();
            
            $table->dropForeign(['armada_id']);
            $table->foreign('armada_id')->references('id')->on('armada')->restrictOnDelete();
        });

        Schema::table('jurnal_detail', function (Blueprint $table) {
            $table->dropForeign(['coa_id']);
            $table->foreign('coa_id')->references('id')->on('coa')->restrictOnDelete();
        });

        Schema::table('jurnal_kas', function (Blueprint $table) {
            $table->dropForeign(['coa_kas_id']);
            $table->foreign('coa_kas_id')->references('id')->on('coa')->restrictOnDelete();
            
            $table->dropForeign(['coa_lawan_id']);
            $table->foreign('coa_lawan_id')->references('id')->on('coa')->restrictOnDelete();
        });
        
        Schema::table('wage_calculations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });
        
        Schema::table('employee_outputs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });
        
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restoring to cascade
    }
};

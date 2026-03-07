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
        Schema::create('coa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('kode_akun'); // e.g., 1100, 2100, etc.
            $table->unique(['tenant_id', 'kode_akun']);
            $table->string('nama_akun');
            $table->enum('tipe', ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense']);
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('kode_akun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coa');
    }
};

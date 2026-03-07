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
        Schema::create('jurnal_kas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->enum('tipe', ['Penerimaan', 'Pengeluaran']);
            $table->date('tanggal');
            $table->foreignId('coa_kas_id')->constrained('coa')->cascadeOnDelete(); // Akun Kas/Bank
            $table->foreignId('coa_lawan_id')->constrained('coa')->cascadeOnDelete(); // Akun Pendapatan/Biaya
            $table->decimal('nominal', 15, 2)->default(0);
            $table->text('deskripsi')->nullable();
            $table->string('bukti_transaksi')->nullable(); // For Image uploads
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnal_kas');
    }
};

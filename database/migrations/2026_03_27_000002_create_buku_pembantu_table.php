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
        Schema::create('buku_pembantu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('jurnal_header_id')->nullable()->constrained('jurnal_header')->nullOnDelete();
            $table->morphs('contactable');
            $table->enum('tipe', ['piutang', 'utang']);
            $table->date('tanggal');
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->decimal('jumlah', 15, 2);
            $table->string('keterangan')->nullable();
            $table->string('bukti_transaksi')->nullable();
            $table->enum('status', ['pending', 'lunas'])->default('pending');
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('tanggal');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku_pembantu');
    }
};

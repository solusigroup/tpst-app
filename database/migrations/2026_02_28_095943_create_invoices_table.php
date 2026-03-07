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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('klien_id')->constrained('klien')->cascadeOnDelete();
            $table->string('nomor_invoice')->unique();
            $table->date('tanggal_invoice');
            $table->date('tanggal_jatuh_tempo');
            $table->string('periode_bulan');
            $table->string('periode_tahun');
            $table->decimal('total_tagihan', 15, 2);
            $table->enum('status', ['Draft', 'Sent', 'Paid', 'Canceled'])->default('Draft');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

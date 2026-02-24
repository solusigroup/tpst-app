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
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('klien_id')->constrained('klien')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('jenis_produk'); // e.g., RDF, Plastik, Kompos
            $table->decimal('berat_kg', 12, 2);
            $table->decimal('harga_satuan', 12, 2); // per kg
            $table->decimal('total_harga', 12, 2); // berat_kg * harga_satuan
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('klien_id');
            $table->index('tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};

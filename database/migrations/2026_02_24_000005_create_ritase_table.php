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
        Schema::create('ritase', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('armada_id')->constrained('armada')->cascadeOnDelete();
            $table->foreignId('klien_id')->constrained('klien')->cascadeOnDelete();
            $table->string('nomor_tiket')->unique();
            $table->dateTime('waktu_masuk');
            $table->dateTime('waktu_keluar')->nullable();
            $table->decimal('berat_bruto', 12, 2)->nullable(); // in kg
            $table->decimal('berat_tarra', 12, 2)->nullable(); // in kg
            $table->decimal('berat_netto', 12, 2)->nullable(); // in kg
            $table->string('jenis_sampah')->nullable();
            $table->decimal('biaya_tipping', 12, 2)->default(0);
            $table->enum('status', ['masuk', 'timbang', 'keluar', 'selesai'])->default('masuk');
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('armada_id');
            $table->index('klien_id');
            $table->index('waktu_masuk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ritase');
    }
};

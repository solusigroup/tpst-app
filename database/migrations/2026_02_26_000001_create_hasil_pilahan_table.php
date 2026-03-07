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
        Schema::create('hasil_pilahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('kategori'); // e.g. Organik, Anorganik, B3
            $table->string('jenis');    // e.g. Plastik, Kertas, Logam, Kompos, dll.
            $table->decimal('tonase', 12, 2)->default(0); // in kg
            $table->string('officer');  // Petugas yang mencatat
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('tanggal');
            $table->index('kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_pilahan');
    }
};

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
        Schema::create('produksi_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->date('tanggal');
            $table->decimal('total_input_sampah', 12, 2)->default(0); // in kg
            $table->decimal('hasil_rdf', 12, 2)->default(0); // in kg
            $table->decimal('hasil_plastik', 12, 2)->default(0); // in kg
            $table->decimal('hasil_kompos', 12, 2)->default(0); // in kg
            $table->decimal('residu_tpa', 12, 2)->default(0); // in kg
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('tanggal');
            $table->unique(['tenant_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produksi_harian');
    }
};

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
        Schema::create('pengangkutan_residus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('armada_id')->constrained('armada')->onDelete('cascade');
            $table->string('nomor_tiket')->unique();
            $table->date('tanggal');
            $table->time('waktu_keluar')->nullable();
            $table->time('waktu_masuk')->nullable();
            $table->decimal('berat_bruto', 15, 2)->default(0);
            $table->decimal('berat_tarra', 15, 2)->default(0);
            $table->decimal('berat_netto', 15, 2)->default(0);
            $table->decimal('biaya_retribusi', 15, 2)->default(30000);
            $table->string('tujuan')->default('TPA Tambakrigadung');
            $table->text('keterangan')->nullable();
            $table->foreignId('jurnal_header_id')->nullable()->constrained('jurnal_header')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengangkutan_residus');
    }
};

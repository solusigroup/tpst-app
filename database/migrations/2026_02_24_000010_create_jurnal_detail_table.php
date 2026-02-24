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
        Schema::create('jurnal_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurnal_header_id')->constrained('jurnal_header')->cascadeOnDelete();
            $table->foreignId('coa_id')->constrained('coa')->cascadeOnDelete();
            $table->decimal('debit', 14, 2)->default(0);
            $table->decimal('kredit', 14, 2)->default(0);
            $table->timestamps();

            $table->index('jurnal_header_id');
            $table->index('coa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnal_detail');
    }
};

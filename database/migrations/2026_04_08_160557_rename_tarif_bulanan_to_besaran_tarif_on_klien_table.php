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
        Schema::table('klien', function (Blueprint $table) {
            $table->renameColumn('tarif_bulanan', 'besaran_tarif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('klien', function (Blueprint $table) {
            $table->renameColumn('besaran_tarif', 'tarif_bulanan');
        });
    }
};

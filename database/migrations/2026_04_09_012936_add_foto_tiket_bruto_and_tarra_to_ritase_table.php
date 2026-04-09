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
        Schema::table('ritase', function (Blueprint $table) {
            $table->string('foto_tiket_bruto')->nullable()->after('foto_tiket');
            $table->string('foto_tiket_tarra')->nullable()->after('foto_tiket_bruto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ritase', function (Blueprint $table) {
            $table->dropColumn(['foto_tiket_bruto', 'foto_tiket_tarra']);
        });
    }
};

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
            $table->string('tiket')->nullable()->after('nomor_tiket');
            $table->string('foto_tiket')->nullable()->after('tiket');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ritase', function (Blueprint $table) {
            $table->dropColumn(['tiket', 'foto_tiket']);
        });
    }
};

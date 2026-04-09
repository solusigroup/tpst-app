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
        Schema::table('armada', function (Blueprint $table) {
            $table->decimal('berat_kosong', 10, 2)->nullable()->after('kapasitas_maksimal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('armada', function (Blueprint $table) {
            $table->dropColumn('berat_kosong');
        });
    }
};

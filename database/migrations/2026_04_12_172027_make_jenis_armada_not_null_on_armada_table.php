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
        // 1. Update existing records
        DB::table('armada')->whereNull('jenis_armada')->update(['jenis_armada' => 'Lainnya']);

        // 2. Make non-nullable
        Schema::table('armada', function (Blueprint $table) {
            $table->string('jenis_armada')->default('Lainnya')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('armada', function (Blueprint $table) {
            $table->string('jenis_armada')->nullable()->change();
        });
    }
};

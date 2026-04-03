<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('klien', function (Blueprint $table) {
            $table->decimal('tarif_bulanan', 15, 2)->nullable()->after('jenis');
        });
        
        // Use DB statement for MySQL enum change as Laravel's change() on enum is sometimes problematic
        DB::statement("ALTER TABLE klien MODIFY COLUMN jenis ENUM('DLH', 'Swasta', 'Offtaker', 'Internal') NOT NULL DEFAULT 'Swasta'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('klien', function (Blueprint $table) {
            $table->dropColumn('tarif_bulanan');
        });

        DB::table('klien')->where('jenis', 'Internal')->update(['jenis' => 'Swasta']);
        DB::statement("ALTER TABLE klien MODIFY COLUMN jenis ENUM('DLH', 'Swasta', 'Offtaker') NOT NULL DEFAULT 'Swasta'");
    }
};

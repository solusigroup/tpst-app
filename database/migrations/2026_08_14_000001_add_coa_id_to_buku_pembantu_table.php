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
        Schema::table('buku_pembantu', function (Blueprint $table) {
            $table->foreignId('coa_id')->nullable()->after('jurnal_header_id')->constrained('coa')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buku_pembantu', function (Blueprint $table) {
            $table->dropForeign(['coa_id']);
            $table->dropColumn(['coa_id']);
        });
    }
};

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
            $table->decimal('terbayar', 15, 2)->default(0)->after('jumlah');
            $table->foreignId('settled_by_jurnal_header_id')->nullable()->after('jurnal_header_id')->constrained('jurnal_header')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buku_pembantu', function (Blueprint $table) {
            $table->dropForeign(['settled_by_jurnal_header_id']);
            $table->dropColumn(['terbayar', 'settled_by_jurnal_header_id']);
        });
    }
};

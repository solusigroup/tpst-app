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
        Schema::table('jurnal_detail', function (Blueprint $table) {
            $table->nullableMorphs('contactable');
        });

        Schema::table('jurnal_kas', function (Blueprint $table) {
            $table->nullableMorphs('contactable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal_detail', function (Blueprint $table) {
            $table->dropMorphs('contactable');
        });

        Schema::table('jurnal_kas', function (Blueprint $table) {
            $table->dropMorphs('contactable');
        });
    }
};

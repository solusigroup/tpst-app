<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hasil_pilahan', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('tenant_id')->constrained('users')->nullOnDelete();
            $table->foreignId('waste_category_id')->nullable()->after('user_id')->constrained('waste_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('hasil_pilahan', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['waste_category_id']);
            $table->dropColumn(['user_id', 'waste_category_id']);
        });
    }
};

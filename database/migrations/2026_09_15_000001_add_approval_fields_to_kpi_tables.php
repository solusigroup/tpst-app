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
        Schema::table('kpi_daily_checklists', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false)->after('catatan');
            $table->foreignId('approved_by_id')->nullable()->after('is_approved')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by_id');
        });

        Schema::table('kpi_machine_activity_logs', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false)->after('operator_id');
            $table->foreignId('approved_by_id')->nullable()->after('is_approved')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by_id');
        });

        Schema::table('kpi_stakeholder_complaints', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false)->after('handled_by_id');
            $table->foreignId('approved_by_id')->nullable()->after('is_approved')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpi_daily_checklists', function (Blueprint $table) {
            $table->dropForeign(['approved_by_id']);
            $table->dropColumn(['is_approved', 'approved_by_id', 'approved_at']);
        });

        Schema::table('kpi_machine_activity_logs', function (Blueprint $table) {
            $table->dropForeign(['approved_by_id']);
            $table->dropColumn(['is_approved', 'approved_by_id', 'approved_at']);
        });

        Schema::table('kpi_stakeholder_complaints', function (Blueprint $table) {
            $table->dropForeign(['approved_by_id']);
            $table->dropColumn(['is_approved', 'approved_by_id', 'approved_at']);
        });
    }
};

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
        Schema::table('users', function (Blueprint $table) {
            $table->date('joined_at')->nullable()->after('gender');
            $table->date('ended_at')->nullable()->after('joined_at');
            $table->string('bpjs_status')->default('Tidak Aktif')->after('ended_at');
            $table->string('bpjs_number')->nullable()->after('bpjs_status');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->decimal('overtime_pay', 14, 2)->default(0)->after('status');
        });

        Schema::table('wage_calculations', function (Blueprint $table) {
            $table->decimal('overtime_pay', 14, 2)->default(0)->after('total_wage');
            $table->foreignId('approved_by_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by_id');
        });
    }

    public function down(): void
    {
        Schema::table('wage_calculations', function (Blueprint $table) {
            $table->dropForeign(['approved_by_id']);
            $table->dropColumn(['overtime_pay', 'approved_by_id', 'approved_at']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('overtime_pay');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['joined_at', 'ended_at', 'bpjs_status', 'bpjs_number']);
        });
    }
};

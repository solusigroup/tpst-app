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
            $table->string('gender')->nullable()->after('ktp_number'); // Laki-laki, Perempuan
            $table->decimal('daily_wage', 15, 2)->default(0)->after('monthly_salary');
            $table->string('payment_frequency')->default('Mingguan')->after('daily_wage'); // Mingguan, Dua Mingguan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['gender', 'daily_wage', 'payment_frequency']);
        });
    }
};

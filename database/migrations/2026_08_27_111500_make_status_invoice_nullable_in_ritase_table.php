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
        Schema::table('ritase', function (Blueprint $table) {
            $table->string('status_invoice', 20)->nullable()->default(null)->change();
        });

        Schema::table('penjualan', function (Blueprint $table) {
            $table->string('status_invoice', 20)->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ritase', function (Blueprint $table) {
            $table->enum('status_invoice', ['Draft', 'Sent', 'Paid', 'Canceled'])->default('Draft')->change();
        });

        Schema::table('penjualan', function (Blueprint $table) {
            $table->enum('status_invoice', ['Draft', 'Sent', 'Paid', 'Canceled'])->default('Draft')->change();
        });
    }
};

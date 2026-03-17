<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wage_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('week_start');
            $table->date('week_end');
            $table->decimal('total_quantity', 12, 2);
            $table->decimal('total_wage', 14, 2);
            $table->string('status')->default('pending'); // pending, approved, paid
            $table->date('paid_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['tenant_id', 'user_id', 'week_start']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'week_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wage_calculations');
    }
};

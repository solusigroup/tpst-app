<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_outputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('waste_category_id')->constrained()->cascadeOnDelete();
            $table->date('output_date');
            $table->decimal('quantity', 12, 2); // jumlah yang dipilah
            $table->string('unit')->default('kg');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['tenant_id', 'user_id', 'output_date']);
            $table->index(['tenant_id', 'output_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_outputs');
    }
};

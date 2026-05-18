<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_duty', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->foreignId('duty_id')
                ->constrained('duties')
                ->cascadeOnDelete();

            $table->boolean('is_primary')->default(false);
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['employee_id', 'duty_id']);
            $table->index('employee_id');
            $table->index('duty_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_duty');
    }
};
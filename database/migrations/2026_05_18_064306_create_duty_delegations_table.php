<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('duty_delegations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('duty_id')
                ->constrained('duties')
                ->cascadeOnDelete();

            $table->foreignId('owner_employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->foreignId('delegate_employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->date('start_date');
            $table->date('end_date')->nullable();

            $table->boolean('is_active')->default(true);

            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index([
                'duty_id',
                'owner_employee_id',
                'delegate_employee_id',
                'start_date',
                'end_date',
                'is_active',
            ], 'duty_delegations_main_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duty_delegations');
    }
};
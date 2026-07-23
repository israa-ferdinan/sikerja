<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('development_plans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('unit_id')
                ->constrained()
                ->cascadeOnDelete();

            // Integrasi R12/R13 sengaja disiapkan nullable,
            // tapi implementasi flow-nya kita HOLD dulu.
            $table->foreignId('evaluation_record_id')
                ->nullable()
                ->constrained('evaluation_records')
                ->nullOnDelete();

            $table->foreignId('control_follow_up_id')
                ->nullable()
                ->constrained('control_follow_ups')
                ->nullOnDelete();

            $table->string('title');
            $table->string('category')->default('Aplikasi');
            $table->string('priority')->default('Sedang');

            $table->text('description');
            $table->text('objective')->nullable();

            $table->foreignId('pic_employee_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->date('target_start_date')->nullable();
            $table->date('target_end_date')->nullable();
            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();

            $table->string('status')->default('Usulan');
            $table->unsignedTinyInteger('progress_percentage')->default(0);

            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['unit_id', 'status']);
            $table->index(['category', 'priority']);
            $table->index('pic_employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('development_plans');
    }
};
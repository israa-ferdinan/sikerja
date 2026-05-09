<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('emloyee_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->foreignId('unit_id')
                ->nullable()
                ->constrained('units')
                ->nullOnDelete();

            $table->foreignId('duty_id')
                ->nullable()
                ->constrained('duties')
                ->nullOnDelete();

            $table->foreignId('server_id')
                ->nullable()
                ->constrained('servers')
                ->nullOnDelete();

            $table->foreignId('application_id')
                ->nullable()
                ->constrained('applications')
                ->nullOnDelete();

            $table->date('report_date');

            $table->string('title');
            $table->text('description');

            $table->string('status')->default('draft');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('report_date');
            $table->index('status');
            $table->index(['user_id', 'report_date']);
            $table->index(['unit_id', 'report_date']);
            $table->index(['employee_id', 'report_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
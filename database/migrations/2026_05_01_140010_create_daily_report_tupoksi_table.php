<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_report_tupoksi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('daily_report_id')
                ->constrained('daily_reports')
                ->cascadeOnDelete();

            $table->foreignId('job_duty_id')
                ->constrained('job_duties')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['daily_report_id', 'job_duty_id'], 'daily_report_tupoksi_unique');
            $table->index('daily_report_id');
            $table->index('job_duty_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_report_tupoksi');
    }
};
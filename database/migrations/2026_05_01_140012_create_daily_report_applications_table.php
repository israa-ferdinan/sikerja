<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_report_applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('daily_report_id')
                ->constrained('daily_reports')
                ->cascadeOnDelete();

            $table->foreignId('application_id')
                ->constrained('applications')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['daily_report_id', 'application_id'], 'daily_report_applications_unique');
            $table->index('daily_report_id');
            $table->index('application_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_report_applications');
    }
};
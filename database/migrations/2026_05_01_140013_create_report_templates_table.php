<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_templates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('unit_id')
                ->nullable()
                ->constrained('units')
                ->nullOnDelete();

            $table->foreignId('job_duty_id')
                ->nullable()
                ->constrained('job_duties')
                ->nullOnDelete();

            $table->string('title');
            $table->longText('description_template')->nullable();
            $table->longText('result_template')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('unit_id');
            $table->index('job_duty_id');
            $table->index('title');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_templates');
    }
};
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
        Schema::create('unit_targets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('unit_id')
                ->constrained('units')
                ->cascadeOnDelete();

            $table->foreignId('duty_classification_id')
                ->nullable()
                ->constrained('duty_classifications')
                ->nullOnDelete();

            $table->string('target_name');
            $table->text('target_description')->nullable();

            $table->year('target_year');
            $table->string('period_type', 20); // annual / quarterly
            $table->unsignedTinyInteger('quarter')->nullable(); // 1,2,3,4 untuk quarterly

            $table->string('object_type', 50)->nullable(); // none / server / application / manual
            $table->foreignId('server_id')
                ->nullable()
                ->constrained('servers')
                ->nullOnDelete();

            $table->foreignId('application_id')
                ->nullable()
                ->constrained('applications')
                ->nullOnDelete();

            $table->string('object_name')->nullable();

            $table->unsignedInteger('target_quantity')->default(0);
            $table->string('target_unit', 50)->default('kali');

            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['unit_id', 'target_year']);
            $table->index(['unit_id', 'target_year', 'period_type']);
            $table->index(['unit_id', 'target_year', 'period_type', 'quarter']);
            $table->index('duty_classification_id');
            $table->index('object_type');
            $table->index('server_id');
            $table->index('application_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_targets');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_target_progress_updates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('unit_target_id')
                ->constrained('unit_targets')
                ->cascadeOnDelete();

            $table->foreignId('unit_id')
                ->constrained('units')
                ->cascadeOnDelete();

            $table->string('achievement_method', 30);

            $table->unsignedTinyInteger('progress_value')
                ->default(0);

            $table->string('status', 30)
                ->nullable();

            $table->text('note')
                ->nullable();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['unit_target_id', 'created_at']);
            $table->index(['unit_id', 'created_at']);
            $table->index('achievement_method');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_target_progress_updates');
    }
};
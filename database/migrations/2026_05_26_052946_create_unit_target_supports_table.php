<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_target_supports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('unit_target_id')
                ->constrained('unit_targets')
                ->cascadeOnDelete();

            $table->foreignId('unit_id')
                ->constrained('units')
                ->cascadeOnDelete();

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('support_type', 30)->default('note');
            $table->string('title');
            $table->text('description')->nullable();

            $table->string('file_path')->nullable();
            $table->string('file_original_name')->nullable();
            $table->string('file_mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            $table->string('url')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['unit_target_id', 'support_type']);
            $table->index(['unit_id', 'is_active']);
            $table->index('uploaded_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_target_supports');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('development_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('development_plan_id')
                ->nullable()
                ->constrained('development_plans')
                ->cascadeOnDelete();

            $table->foreignId('unit_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('document_type')->default('Dokumen Pendukung');
            $table->string('title');
            $table->text('description')->nullable();

            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            $table->string('visibility')->default('Unit');

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['unit_id', 'visibility']);
            $table->index(['development_plan_id', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('development_documents');
    }
};
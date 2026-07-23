<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('evaluation_record_id')
                ->constrained('evaluation_records')
                ->cascadeOnDelete();

            $table->foreignId('unit_id')
                ->constrained('units')
                ->restrictOnDelete();

            $table->string('title');
            $table->string('document_type', 50)->default('lainnya');
            $table->text('description')->nullable();

            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['unit_id']);
            $table->index(['document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_documents');
    }
};
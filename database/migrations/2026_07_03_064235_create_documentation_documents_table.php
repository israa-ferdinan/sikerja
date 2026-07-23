<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentation_documents', function (Blueprint $table) {
            $table->id();

            $table->string('section', 50)->default('penetapan');
            $table->string('category', 100);

            $table->string('title');
            $table->string('document_number')->nullable();
            $table->text('description')->nullable();

            $table->date('document_date')->nullable();
            $table->date('effective_date')->nullable();

            $table->string('revision', 50)->nullable();
            $table->string('status', 30)->default('draft');

            $table->string('file_path')->nullable();
            $table->string('original_filename')->nullable();
            $table->string('file_mime')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('published_at')->nullable();
            $table->timestamp('archived_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['section', 'category']);
            $table->index(['section', 'status']);
            $table->index(['category', 'status']);
            $table->index('uploaded_by');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentation_documents');
    }
};
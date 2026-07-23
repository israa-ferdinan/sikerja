<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operational_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('unit_id')
                ->nullable()
                ->constrained('units')
                ->nullOnDelete();

            $table->string('category', 100);
            $table->string('title');
            $table->string('document_number')->nullable();

            $table->unsignedTinyInteger('period_month')->nullable();
            $table->unsignedSmallInteger('period_year')->nullable();
            $table->date('document_date')->nullable();

            $table->text('description')->nullable();

            $table->string('visibility', 50)->default('unit');
            $table->string('status', 50)->default('draft');

            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_original_name');
            $table->string('file_mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            $table->foreignId('uploaded_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('published_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('published_at')->nullable();
            $table->timestamp('archived_at')->nullable();

            $table->timestamps();

            $table->index(['unit_id', 'category'], 'op_docs_unit_category_idx');
            $table->index(['category', 'status'], 'op_docs_category_status_idx');
            $table->index(['period_year', 'period_month'], 'op_docs_period_idx');
            $table->index('visibility', 'op_docs_visibility_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operational_documents');
    }
};
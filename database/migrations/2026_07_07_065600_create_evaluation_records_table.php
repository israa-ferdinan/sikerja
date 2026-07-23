<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('unit_id')
                ->constrained('units')
                ->restrictOnDelete();

            $table->string('title');
            $table->string('evaluation_type', 50)->default('other');
            $table->date('evaluation_date')->nullable();

            $table->string('source')->nullable();
            $table->text('findings')->nullable();
            $table->text('recommendation')->nullable();

            $table->string('status', 30)->default('draft');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('published_at')->nullable();
            $table->timestamp('archived_at')->nullable();

            $table->timestamps();

            $table->index(['unit_id', 'status']);
            $table->index(['evaluation_type', 'evaluation_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_records');
    }
};
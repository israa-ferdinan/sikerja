<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operational_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('unit_id')
                ->nullable()
                ->constrained('units')
                ->nullOnDelete();

            $table->string('category', 100);
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('year')->nullable();
            $table->unsignedInteger('quantity')->nullable();
            $table->string('identifier')->nullable();
            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['unit_id', 'category']);
            $table->index(['category', 'is_active']);
            $table->index('location');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operational_items');
    }
};
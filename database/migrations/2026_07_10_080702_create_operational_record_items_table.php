<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operational_record_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('operational_record_id')
                ->constrained('operational_records')
                ->cascadeOnDelete();

            $table->foreignId('operational_item_id')
                ->nullable()
                ->constrained('operational_items')
                ->nullOnDelete();

            $table->string('item_name');
            $table->string('item_location')->nullable();
            $table->string('item_identifier')->nullable();

            $table->string('condition_status', 50)->default('normal');
            $table->json('component_status')->nullable();

            $table->text('description')->nullable();
            $table->text('action_taken')->nullable();

            $table->timestamps();

            $table->index(['operational_record_id', 'condition_status'], 'ori_record_condition_idx');
            $table->index('operational_item_id', 'ori_item_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operational_record_items');
    }
};
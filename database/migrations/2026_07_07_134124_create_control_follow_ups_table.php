<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('control_follow_ups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('evaluation_record_id')
                ->nullable()
                ->constrained('evaluation_records')
                ->nullOnDelete();

            $table->foreignId('unit_id')
                ->constrained('units')
                ->restrictOnDelete();

            $table->string('title');
            $table->text('description');
            $table->text('recommendation')->nullable();

            $table->foreignId('pic_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('due_date')->nullable();

            $table->string('status')->default('open');

            $table->text('progress_note')->nullable();
            $table->text('completed_note')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->text('cancelled_note')->nullable();

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['unit_id', 'status']);
            $table->index('evaluation_record_id');
            $table->index('pic_user_id');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('control_follow_ups');
    }
};
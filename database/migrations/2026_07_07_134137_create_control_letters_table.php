<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('control_letters', function (Blueprint $table) {
            $table->id();

            $table->foreignId('control_follow_up_id')
                ->nullable()
                ->constrained('control_follow_ups')
                ->nullOnDelete();

            $table->foreignId('unit_id')
                ->constrained('units')
                ->restrictOnDelete();

            $table->string('letter_type');

            $table->string('letter_number')->nullable();
            $table->date('letter_date')->nullable();

            $table->string('subject');
            $table->string('sender')->nullable();
            $table->string('recipient')->nullable();
            $table->text('summary')->nullable();

            $table->string('visibility')->default('unit');

            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            $table->foreignId('uploaded_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();

            $table->index(['unit_id', 'letter_type']);
            $table->index('control_follow_up_id');
            $table->index('visibility');
            $table->index('letter_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('control_letters');
    }
};
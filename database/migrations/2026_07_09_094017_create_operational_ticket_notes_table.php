<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('operational_ticket_notes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('operational_ticket_id')
                ->constrained('operational_tickets')
                ->cascadeOnDelete();

            $table->foreignId('created_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('visibility', 20)->default('internal'); // internal, public
            $table->text('note');

            $table->timestamps();

            $table->index(['operational_ticket_id', 'visibility']);
            $table->index('created_by_user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operational_ticket_notes');
    }
};
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
        Schema::create('operational_tickets', function (Blueprint $table) {
            $table->id();

            // Tracking
            $table->string('ticket_code', 40)->unique();
            $table->string('public_token', 80)->nullable()->unique();

            // Source
            $table->string('source', 20)->default('internal'); // internal, public

            // Requester - dibuat sederhana supaya form tidak berat
            $table->string('requester_name');
            $table->string('requester_contact', 50)->nullable();
            $table->string('requester_unit')->nullable();

            // Ticket content
            $table->string('category', 100)->nullable();
            $table->string('title');
            $table->text('description')->nullable();

            // Management
            $table->string('priority', 20)->default('normal'); // low, normal, high
            $table->string('status', 30)->default('baru'); // baru, diproses, menunggu_pemohon, selesai, dibatalkan

            // Scope & ownership
            $table->foreignId('unit_id')
                ->nullable()
                ->constrained('units')
                ->nullOnDelete();

            $table->foreignId('assigned_to_employee_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->foreignId('created_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('closed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('closed_at')->nullable();
            $table->timestamp('last_public_viewed_at')->nullable();

            $table->timestamps();

            $table->index(['source', 'status']);
            $table->index(['unit_id', 'status']);
            $table->index(['assigned_to_employee_id', 'status']);
            $table->index('created_by_user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operational_tickets');
    }
};
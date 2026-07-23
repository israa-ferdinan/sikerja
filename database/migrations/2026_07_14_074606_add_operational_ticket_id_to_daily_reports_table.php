<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->foreignId('operational_ticket_id')
                ->nullable()
                ->after('id')
                ->constrained('operational_tickets')
                ->nullOnDelete();

            $table->unique(
                'operational_ticket_id',
                'daily_reports_operational_ticket_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropUnique(
                'daily_reports_operational_ticket_unique'
            );

            $table->dropConstrainedForeignId(
                'operational_ticket_id'
            );
        });
    }
};
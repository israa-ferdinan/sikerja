<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->boolean('is_delegated')
                ->default(false)
                ->after('duty_id');

            $table->foreignId('delegation_id')
                ->nullable()
                ->after('is_delegated')
                ->constrained('duty_delegations')
                ->nullOnDelete();

            $table->foreignId('duty_owner_employee_id')
                ->nullable()
                ->after('delegation_id')
                ->constrained('employees')
                ->nullOnDelete();

            $table->foreignId('reported_by_employee_id')
                ->nullable()
                ->after('duty_owner_employee_id')
                ->constrained('employees')
                ->nullOnDelete();

            $table->index([
                'is_delegated',
                'delegation_id',
                'duty_owner_employee_id',
                'reported_by_employee_id',
            ], 'daily_reports_delegation_index');
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropIndex('daily_reports_delegation_index');

            $table->dropConstrainedForeignId('reported_by_employee_id');
            $table->dropConstrainedForeignId('duty_owner_employee_id');
            $table->dropConstrainedForeignId('delegation_id');

            $table->dropColumn('is_delegated');
        });
    }
};
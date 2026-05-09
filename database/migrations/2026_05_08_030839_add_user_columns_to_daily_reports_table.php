<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('daily_reports', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('daily_reports', 'employee_id')) {
                $table->foreignId('employee_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('employees')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('daily_reports', 'unit_id')) {
                $table->foreignId('unit_id')
                    ->nullable()
                    ->after('employee_id')
                    ->constrained('units')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            if (Schema::hasColumn('daily_reports', 'unit_id')) {
                $table->dropConstrainedForeignId('unit_id');
            }

            if (Schema::hasColumn('daily_reports', 'employee_id')) {
                $table->dropConstrainedForeignId('employee_id');
            }

            if (Schema::hasColumn('daily_reports', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};
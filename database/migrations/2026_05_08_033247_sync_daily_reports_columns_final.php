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
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }

            if (!Schema::hasColumn('daily_reports', 'employee_id')) {
                $table->unsignedBigInteger('employee_id')->nullable()->after('user_id');
            }

            if (!Schema::hasColumn('daily_reports', 'unit_id')) {
                $table->unsignedBigInteger('unit_id')->nullable()->after('employee_id');
            }

            if (!Schema::hasColumn('daily_reports', 'duty_id')) {
                $table->unsignedBigInteger('duty_id')->nullable()->after('unit_id');
            }

            if (!Schema::hasColumn('daily_reports', 'server_id')) {
                $table->unsignedBigInteger('server_id')->nullable()->after('duty_id');
            }

            if (!Schema::hasColumn('daily_reports', 'application_id')) {
                $table->unsignedBigInteger('application_id')->nullable()->after('server_id');
            }

            if (!Schema::hasColumn('daily_reports', 'report_date')) {
                $table->date('report_date')->nullable()->after('application_id');
            }

            if (!Schema::hasColumn('daily_reports', 'title')) {
                $table->string('title')->nullable()->after('report_date');
            }

            if (!Schema::hasColumn('daily_reports', 'description')) {
                $table->text('description')->nullable()->after('title');
            }

            if (!Schema::hasColumn('daily_reports', 'notes')) {
                $table->text('notes')->nullable()->after('description');
            }

            if (!Schema::hasColumn('daily_reports', 'status')) {
                $table->string('status')->default('submitted')->after('notes');
            }
        });
    }

    public function down(): void
    {
        // Sengaja dikosongkan agar tidak menghapus kolom penting saat rollback.
    }
};
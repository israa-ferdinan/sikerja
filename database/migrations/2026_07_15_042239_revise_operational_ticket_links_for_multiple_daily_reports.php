<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            /*
             * Sebelumnya satu tiket hanya boleh mempunyai satu laporan.
             */
            $table->dropUnique(
                'daily_reports_operational_ticket_id_unique'
            );

            /*
             * Sekarang satu tiket boleh mempunyai beberapa laporan,
             * tetapi satu PIC hanya boleh mempunyai satu laporan
             * untuk tiket yang sama pada tanggal yang sama.
             */
            $table->unique(
                [
                    'operational_ticket_id',
                    'employee_id',
                    'report_date',
                ],
                'daily_reports_ticket_employee_date_unique'
            );
        });

        Schema::table('duty_delegations', function (Blueprint $table) {
            /*
             * Sebelumnya satu tiket hanya boleh mempunyai satu delegasi
             * otomatis. Ini tidak cukup jika tiket berpindah PIC.
             */
            $table->dropUnique(
                'duty_delegations_operational_ticket_id_unique'
            );

            /*
             * Satu tiket boleh mempunyai beberapa delegasi untuk PIC
             * yang berbeda, tetapi kombinasi tiket, tupoksi, dan PIC
             * tidak boleh dibuat berulang.
             */
            $table->unique(
                [
                    'operational_ticket_id',
                    'duty_id',
                    'delegate_employee_id',
                ],
                'duty_delegations_ticket_duty_delegate_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('duty_delegations', function (Blueprint $table) {
            $table->dropUnique(
                'duty_delegations_ticket_duty_delegate_unique'
            );

            $table->unique(
                'operational_ticket_id',
                'duty_delegations_operational_ticket_id_unique'
            );
        });

        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropUnique(
                'daily_reports_ticket_employee_date_unique'
            );

            $table->unique(
                'operational_ticket_id',
                'daily_reports_operational_ticket_id_unique'
            );
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Foreign key operational_ticket_id saat ini menggunakan
         * unique index sebagai index pendukung.
         *
         * Buat index biasa terlebih dahulu agar unique lama
         * dapat dilepas tanpa merusak foreign key.
         */
        DB::statement('
            ALTER TABLE daily_reports
            ADD INDEX daily_reports_operational_ticket_id_index
            (operational_ticket_id)
        ');

        DB::statement('
            ALTER TABLE daily_reports
            DROP INDEX daily_reports_operational_ticket_unique
        ');

        DB::statement('
            ALTER TABLE daily_reports
            ADD UNIQUE INDEX daily_reports_ticket_employee_date_unique
            (
                operational_ticket_id,
                employee_id,
                report_date
            )
        ');
    }

    public function down(): void
    {
        DB::statement('
            ALTER TABLE daily_reports
            DROP INDEX daily_reports_ticket_employee_date_unique
        ');

        DB::statement('
            ALTER TABLE daily_reports
            ADD UNIQUE INDEX daily_reports_operational_ticket_unique
            (operational_ticket_id)
        ');

        DB::statement('
            ALTER TABLE daily_reports
            DROP INDEX daily_reports_operational_ticket_id_index
        ');
    }
};
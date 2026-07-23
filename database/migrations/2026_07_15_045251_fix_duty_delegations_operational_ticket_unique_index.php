<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Buat index biasa lebih dulu karena operational_ticket_id
         * dipakai foreign key.
         */
        DB::statement('
            ALTER TABLE duty_delegations
            ADD INDEX duty_delegations_operational_ticket_id_index
            (operational_ticket_id)
        ');

        /*
         * Hapus unique lama yang membatasi satu tiket satu delegasi.
         */
        DB::statement('
            ALTER TABLE duty_delegations
            DROP INDEX duty_delegations_operational_ticket_unique
        ');

        /*
         * Satu tiket boleh punya beberapa delegasi,
         * tetapi kombinasi tiket + tupoksi + PIC tidak boleh ganda.
         */
        DB::statement('
            ALTER TABLE duty_delegations
            ADD UNIQUE INDEX duty_delegations_ticket_duty_delegate_unique
            (
                operational_ticket_id,
                duty_id,
                delegate_employee_id
            )
        ');
    }

    public function down(): void
    {
        DB::statement('
            ALTER TABLE duty_delegations
            DROP INDEX duty_delegations_ticket_duty_delegate_unique
        ');

        DB::statement('
            ALTER TABLE duty_delegations
            ADD UNIQUE INDEX duty_delegations_operational_ticket_unique
            (operational_ticket_id)
        ');

        DB::statement('
            ALTER TABLE duty_delegations
            DROP INDEX duty_delegations_operational_ticket_id_index
        ');
    }
};
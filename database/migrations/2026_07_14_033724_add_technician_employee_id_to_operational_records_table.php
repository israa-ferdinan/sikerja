<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operational_records', function (Blueprint $table) {
            $table->foreignId('technician_employee_id')
                ->nullable()
                ->after('updated_by_user_id')
                ->constrained('employees')
                ->nullOnDelete();

            $table->index('technician_employee_id', 'operational_records_technician_idx');
        });
    }

    public function down(): void
    {
        Schema::table('operational_records', function (Blueprint $table) {
            $table->dropForeign(['technician_employee_id']);
            $table->dropIndex('operational_records_technician_idx');
            $table->dropColumn('technician_employee_id');
        });
    }
};
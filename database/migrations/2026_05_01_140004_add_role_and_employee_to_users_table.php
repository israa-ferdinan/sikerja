<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')
                ->nullable()
                ->after('id')
                ->constrained('roles')
                ->nullOnDelete();

            $table->foreignId('employee_id')
                ->nullable()
                ->after('role_id')
                ->constrained('employees')
                ->nullOnDelete();

            $table->string('username')
                ->nullable()
                ->unique()
                ->after('email');

            $table->boolean('is_active')
                ->default(true)
                ->after('password');

            $table->index('role_id');
            $table->index('employee_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['employee_id']);

            $table->dropIndex(['role_id']);
            $table->dropIndex(['employee_id']);
            $table->dropIndex(['is_active']);

            $table->dropColumn([
                'role_id',
                'employee_id',
                'username',
                'is_active',
            ]);
        });
    }
};
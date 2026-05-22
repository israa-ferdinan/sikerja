<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('duties', function (Blueprint $table) {
            $table->foreignId('duty_classification_id')
                ->nullable()
                ->after('unit_id')
                ->constrained('duty_classifications')
                ->nullOnDelete();

            $table->string('object_type')
                ->default('none')
                ->after('duty_classification_id');

            $table->foreignId('server_id')
                ->nullable()
                ->after('object_type')
                ->constrained('servers')
                ->nullOnDelete();

            $table->foreignId('application_id')
                ->nullable()
                ->after('server_id')
                ->constrained('applications')
                ->nullOnDelete();

            $table->string('object_name')
                ->nullable()
                ->after('application_id');

            $table->index('object_type');
            $table->index('object_name');
        });
    }

    public function down(): void
    {
        Schema::table('duties', function (Blueprint $table) {
            $table->dropForeign(['duty_classification_id']);
            $table->dropForeign(['server_id']);
            $table->dropForeign(['application_id']);

            $table->dropIndex(['object_type']);
            $table->dropIndex(['object_name']);

            $table->dropColumn([
                'duty_classification_id',
                'object_type',
                'server_id',
                'application_id',
                'object_name',
            ]);
        });
    }
};
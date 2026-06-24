<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unit_targets', function (Blueprint $table) {
            $table->string('achievement_method', 30)
                ->default('auto_report')
                ->after('target_unit');

            $table->unsignedTinyInteger('manual_progress')
                ->default(0)
                ->after('achievement_method');

            $table->string('manual_status', 30)
                ->default('not_started')
                ->after('manual_progress');

            $table->text('manual_progress_note')
                ->nullable()
                ->after('manual_status');

            $table->foreignId('manual_progress_updated_by')
                ->nullable()
                ->after('manual_progress_note')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('manual_progress_updated_at')
                ->nullable()
                ->after('manual_progress_updated_by');

            $table->index('achievement_method');
            $table->index('manual_status');
        });
    }

    public function down(): void
    {
        Schema::table('unit_targets', function (Blueprint $table) {
            $table->dropIndex(['achievement_method']);
            $table->dropIndex(['manual_status']);

            $table->dropForeign(['manual_progress_updated_by']);

            $table->dropColumn([
                'achievement_method',
                'manual_progress',
                'manual_status',
                'manual_progress_note',
                'manual_progress_updated_by',
                'manual_progress_updated_at',
            ]);
        });
    }
};
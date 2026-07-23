<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluation_records', function (Blueprint $table) {
            $table->string('zoom_link')->nullable()->after('source');
            $table->string('google_drive_link')->nullable()->after('zoom_link');
        });
    }

    public function down(): void
    {
        Schema::table('evaluation_records', function (Blueprint $table) {
            $table->dropColumn([
                'zoom_link',
                'google_drive_link',
            ]);
        });
    }
};
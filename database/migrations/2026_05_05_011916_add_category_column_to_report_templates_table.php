<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('report_templates', 'category')) {
            Schema::table('report_templates', function (Blueprint $table) {
                $table->string('category')->nullable()->after('title')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('report_templates', 'category')) {
            Schema::table('report_templates', function (Blueprint $table) {
                $table->dropIndex(['category']);
                $table->dropColumn('category');
            });
        }
    }
};
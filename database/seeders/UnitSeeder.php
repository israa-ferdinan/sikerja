<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        Unit::updateOrCreate(
            ['code' => 'TI'],
            [
                'name' => 'Unit Teknologi Informasi',
                'description' => 'Unit yang menangani layanan teknologi informasi, aplikasi, server, jaringan, dan dukungan sistem.',
                'is_active' => true,
            ]
        );

        Unit::updateOrCreate(
            ['code' => 'AKD'],
            [
                'name' => 'Unit Akademik',
                'description' => 'Unit yang menangani layanan akademik.',
                'is_active' => true,
            ]
        );

        Unit::updateOrCreate(
            ['code' => 'UMUM'],
            [
                'name' => 'Unit Umum',
                'description' => 'Unit umum dan administrasi.',
                'is_active' => true,
            ]
        );
    }
}
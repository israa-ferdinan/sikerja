<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            [
                'name' => 'Pranata Komputer',
                'code' => 'PRKOM',
                'description' => 'Jabatan fungsional bidang teknologi informasi dan sistem komputer.',
            ],
            [
                'name' => 'Pranata Humas',
                'code' => 'PRHUMAS',
                'description' => 'Jabatan fungsional bidang kehumasan, publikasi, dan komunikasi publik.',
            ],
            [
                'name' => 'Pengelola Data',
                'code' => 'PGLDATA',
                'description' => 'Jabatan pelaksana yang menangani pengelolaan dan validasi data.',
            ],
            [
                'name' => 'Analis Kepegawaian',
                'code' => 'ANPEG',
                'description' => 'Jabatan yang menangani analisis dan administrasi kepegawaian.',
            ],
            [
                'name' => 'Pengelola Sistem Informasi',
                'code' => 'PSI',
                'description' => 'Jabatan yang menangani operasional dan pengelolaan sistem informasi.',
            ],
            [
                'name' => 'Teknisi Jaringan',
                'code' => 'TEKJAR',
                'description' => 'Jabatan yang menangani instalasi, pemeliharaan, dan monitoring jaringan.',
            ],
            [
                'name' => 'Operator Komputer',
                'code' => 'OPKOM',
                'description' => 'Jabatan yang menangani operasional perangkat komputer dan aplikasi pendukung.',
            ],
        ];

        foreach ($positions as $position) {
            Position::updateOrCreate(
                ['code' => $position['code']],
                [
                    'name' => $position['name'],
                    'description' => $position['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
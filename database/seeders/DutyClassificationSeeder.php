<?php

namespace Database\Seeders;

use App\Models\DutyClassification;
use Illuminate\Database\Seeder;

class DutyClassificationSeeder extends Seeder
{
    public function run(): void
    {
        $classifications = [
            [
                'name' => 'Server / Infrastruktur',
                'description' => 'Pekerjaan yang berkaitan dengan server, hosting, storage, virtualisasi, backup, dan infrastruktur sistem.',
            ],
            [
                'name' => 'Database',
                'description' => 'Pekerjaan yang berkaitan dengan backup, restore, monitoring, optimasi, dan pemeliharaan database.',
            ],
            [
                'name' => 'Aplikasi',
                'description' => 'Pekerjaan yang berkaitan dengan pengelolaan, monitoring, pengembangan, dan pemeliharaan aplikasi.',
            ],
            [
                'name' => 'Jaringan',
                'description' => 'Pekerjaan yang berkaitan dengan internet, LAN, router, switch, firewall, dan konektivitas jaringan.',
            ],
            [
                'name' => 'Perangkat / Fasilitas',
                'description' => 'Pekerjaan yang berkaitan dengan perangkat kerja, CCTV, printer, scanner, komputer, dan fasilitas pendukung.',
            ],
            [
                'name' => 'Administrasi',
                'description' => 'Pekerjaan administratif seperti pendataan, pengarsipan, surat-menyurat, rekap, dan pengelolaan dokumen.',
            ],
            [
                'name' => 'Dokumentasi',
                'description' => 'Pekerjaan dokumentasi kegiatan, foto, evidence, laporan, dan bahan pendukung administrasi.',
            ],
            [
                'name' => 'Koordinasi',
                'description' => 'Pekerjaan koordinasi internal, rapat, tindak lanjut, komunikasi lintas unit, dan sinkronisasi pekerjaan.',
            ],
            [
                'name' => 'Layanan Pengguna',
                'description' => 'Pekerjaan bantuan pengguna, troubleshooting, permintaan layanan, pendampingan, dan dukungan teknis.',
            ],
            [
                'name' => 'Lainnya',
                'description' => 'Klasifikasi umum untuk tupoksi yang belum masuk ke kategori khusus.',
            ],
        ];

        foreach ($classifications as $classification) {
            DutyClassification::updateOrCreate(
                ['name' => $classification['name']],
                [
                    'description' => $classification['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
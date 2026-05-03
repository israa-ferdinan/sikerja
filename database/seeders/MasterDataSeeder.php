<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\JobDuty;
use App\Models\ReportTemplate;
use App\Models\Server;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $unitTi = Unit::where('code', 'TI')->first();

        if (! $unitTi) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Tupoksi
        |--------------------------------------------------------------------------
        */

        $monitoringAplikasi = JobDuty::updateOrCreate(
            [
                'unit_id' => $unitTi->id,
                'name' => 'Monitoring Aplikasi',
            ],
            [
                'description' => 'Melakukan pemantauan aplikasi agar layanan tetap berjalan dengan baik.',
                'is_active' => true,
            ]
        );

        $pemeliharaanServer = JobDuty::updateOrCreate(
            [
                'unit_id' => $unitTi->id,
                'name' => 'Pemeliharaan Server',
            ],
            [
                'description' => 'Melakukan pengecekan dan pemeliharaan server secara berkala.',
                'is_active' => true,
            ]
        );

        $backupData = JobDuty::updateOrCreate(
            [
                'unit_id' => $unitTi->id,
                'name' => 'Backup Data',
            ],
            [
                'description' => 'Melakukan backup database dan file aplikasi untuk menjaga ketersediaan data.',
                'is_active' => true,
            ]
        );

        $dukunganPengguna = JobDuty::updateOrCreate(
            [
                'unit_id' => $unitTi->id,
                'name' => 'Dukungan Pengguna',
            ],
            [
                'description' => 'Memberikan bantuan teknis kepada pengguna layanan aplikasi.',
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Server
        |--------------------------------------------------------------------------
        */

        Server::updateOrCreate(
            [
                'unit_id' => $unitTi->id,
                'name' => 'Server Aplikasi Utama',
            ],
            [
                'hostname' => 'app-server-01',
                'ip_address' => '192.168.1.10',
                'server_type' => 'vm',
                'location' => 'Ruang Server',
                'description' => 'Server utama untuk menjalankan aplikasi internal.',
                'is_active' => true,
            ]
        );

        Server::updateOrCreate(
            [
                'unit_id' => $unitTi->id,
                'name' => 'Server Database',
            ],
            [
                'hostname' => 'db-server-01',
                'ip_address' => '192.168.1.11',
                'server_type' => 'vm',
                'location' => 'Ruang Server',
                'description' => 'Server database untuk aplikasi internal.',
                'is_active' => true,
            ]
        );

        Server::updateOrCreate(
            [
                'unit_id' => $unitTi->id,
                'name' => 'Server Backup',
            ],
            [
                'hostname' => 'backup-server-01',
                'ip_address' => '192.168.1.12',
                'server_type' => 'physical',
                'location' => 'Ruang Server',
                'description' => 'Server penyimpanan backup.',
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Aplikasi
        |--------------------------------------------------------------------------
        */

        Application::updateOrCreate(
            [
                'unit_id' => $unitTi->id,
                'name' => 'SIAKAD',
            ],
            [
                'url' => 'https://siakad.example.local',
                'description' => 'Sistem Informasi Akademik.',
                'is_active' => true,
            ]
        );

        Application::updateOrCreate(
            [
                'unit_id' => $unitTi->id,
                'name' => 'LMS',
            ],
            [
                'url' => 'https://lms.example.local',
                'description' => 'Learning Management System.',
                'is_active' => true,
            ]
        );

        Application::updateOrCreate(
            [
                'unit_id' => $unitTi->id,
                'name' => 'Website Utama',
            ],
            [
                'url' => 'https://website.example.local',
                'description' => 'Website utama instansi.',
                'is_active' => true,
            ]
        );

        Application::updateOrCreate(
            [
                'unit_id' => $unitTi->id,
                'name' => 'OJS Penelitian',
            ],
            [
                'url' => 'https://ojs-penelitian.example.local',
                'description' => 'Aplikasi jurnal penelitian.',
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Template Laporan
        |--------------------------------------------------------------------------
        */

        ReportTemplate::updateOrCreate(
            [
                'unit_id' => $unitTi->id,
                'job_duty_id' => $monitoringAplikasi->id,
                'title' => 'Monitoring Aplikasi',
            ],
            [
                'description_template' => 'Melakukan monitoring aplikasi {application_name}, meliputi pengecekan akses aplikasi, fungsi utama, respon halaman, dan ketersediaan layanan.',
                'result_template' => 'Aplikasi {application_name} dalam kondisi normal dan dapat diakses oleh pengguna.',
                'is_active' => true,
            ]
        );

        ReportTemplate::updateOrCreate(
            [
                'unit_id' => $unitTi->id,
                'job_duty_id' => $pemeliharaanServer->id,
                'title' => 'Pemeliharaan Server',
            ],
            [
                'description_template' => 'Melakukan pengecekan server {server_name}, meliputi penggunaan CPU, RAM, storage, service aktif, konektivitas jaringan, dan log sistem.',
                'result_template' => 'Server {server_name} dalam kondisi normal dan layanan berjalan dengan baik.',
                'is_active' => true,
            ]
        );

        ReportTemplate::updateOrCreate(
            [
                'unit_id' => $unitTi->id,
                'job_duty_id' => $backupData->id,
                'title' => 'Backup Data',
            ],
            [
                'description_template' => 'Melakukan backup data pada server/aplikasi terkait, meliputi database, file aplikasi, dan dokumen pendukung.',
                'result_template' => 'Backup data berhasil dilakukan dan file backup tersimpan pada media penyimpanan yang telah ditentukan.',
                'is_active' => true,
            ]
        );

        ReportTemplate::updateOrCreate(
            [
                'unit_id' => $unitTi->id,
                'job_duty_id' => $dukunganPengguna->id,
                'title' => 'Dukungan Pengguna',
            ],
            [
                'description_template' => 'Memberikan dukungan teknis kepada pengguna terkait kendala penggunaan aplikasi atau layanan teknologi informasi.',
                'result_template' => 'Kendala pengguna telah ditindaklanjuti dan pengguna mendapatkan arahan/solusi sesuai kebutuhan.',
                'is_active' => true,
            ]
        );
    }
}
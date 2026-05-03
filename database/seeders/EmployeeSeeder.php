<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $unitTi = Unit::where('code', 'TI')->first();

        Employee::updateOrCreate(
            ['email' => 'admin@sikerja.local'],
            [
                'unit_id' => $unitTi?->id,
                'name' => 'Administrator Sistem',
                'nip' => '000000000000000001',
                'position' => 'Administrator',
                'phone' => null,
                'is_active' => true,
            ]
        );

        Employee::updateOrCreate(
            ['email' => 'kanit.ti@sikerja.local'],
            [
                'unit_id' => $unitTi?->id,
                'name' => 'Kepala Unit Teknologi Informasi',
                'nip' => '000000000000000002',
                'position' => 'Kepala Unit',
                'phone' => null,
                'is_active' => true,
            ]
        );

        Employee::updateOrCreate(
            ['email' => 'pegawai.ti@sikerja.local'],
            [
                'unit_id' => $unitTi?->id,
                'name' => 'Pegawai Teknologi Informasi',
                'nip' => '000000000000000003',
                'position' => 'Staff IT',
                'phone' => null,
                'is_active' => true,
            ]
        );
    }
}
<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $kanitRole = Role::where('name', 'kanit')->first();
        $pegawaiRole = Role::where('name', 'pegawai')->first();

        $adminEmployee = Employee::where('email', 'admin@sikerja.local')->first();
        $kanitEmployee = Employee::where('email', 'kanit.ti@sikerja.local')->first();
        $pegawaiEmployee = Employee::where('email', 'pegawai.ti@sikerja.local')->first();

        User::updateOrCreate(
            ['email' => 'admin@sikerja.local'],
            [
                'role_id' => $adminRole?->id,
                'employee_id' => $adminEmployee?->id,
                'name' => 'Administrator Sistem',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'kanit.ti@sikerja.local'],
            [
                'role_id' => $kanitRole?->id,
                'employee_id' => $kanitEmployee?->id,
                'name' => 'Kepala Unit TI',
                'username' => 'kanitti',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'pegawai.ti@sikerja.local'],
            [
                'role_id' => $pegawaiRole?->id,
                'employee_id' => $pegawaiEmployee?->id,
                'name' => 'Pegawai TI',
                'username' => 'pegawaiti',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
    }
}
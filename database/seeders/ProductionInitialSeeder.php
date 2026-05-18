<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProductionInitialSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        DB::table('roles')->updateOrInsert(
            ['name' => 'admin'],
            [
                'label' => 'Administrator',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('roles')->updateOrInsert(
            ['name' => 'kanit'],
            [
                'label' => 'Kepala Unit',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('roles')->updateOrInsert(
            ['name' => 'pegawai'],
            [
                'label' => 'Pegawai',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Positions Default
        |--------------------------------------------------------------------------
        */

        $positions = [
            'Pranata Komputer',
            'Pranata Humas',
            'Pengelola Data',
            'Pengadministrasi Umum',
            'Analis Sistem Informasi',
            'Teknisi Jaringan',
            'Operator Komputer',
            'Kepala Unit',
        ];

        foreach ($positions as $position) {
            DB::table('positions')->updateOrInsert(
                ['name' => $position],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Admin User Awal
        |--------------------------------------------------------------------------
        */

        $adminRoleId = DB::table('roles')
            ->where('name', 'admin')
            ->value('id');

        DB::table('users')->updateOrInsert(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@localhost.test',
                'password' => Hash::make('Admin12345!'),
                'role_id' => $adminRoleId,
                'employee_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
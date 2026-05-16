<?php

namespace App\Models;

class Pegawai extends Employee
{
    /**
     * Model sementara untuk kompatibilitas kode lama.
     *
     * Tabel tetap memakai employees karena struktur database utama
     * project ini adalah employees, bukan pegawais.
     */
    protected $table = 'employees';
}
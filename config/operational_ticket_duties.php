<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mapping kategori Tiket Operasional ke keyword tupoksi
    |--------------------------------------------------------------------------
    |
    | Keyword akan dicocokkan dengan:
    | - nama tupoksi;
    | - nama klasifikasi tupoksi;
    | - judul tiket;
    | - deskripsi tiket.
    |
    */

    'categories' => [

        'gangguan_aplikasi' => [
            'aplikasi',
            'sistem informasi',
            'software',
            'troubleshooting aplikasi',
            'pemeliharaan aplikasi',
            'pengelolaan aplikasi',
            'layanan aplikasi',
        ],

        'gangguan_jaringan' => [
            'jaringan',
            'internet',
            'wifi',
            'wireless',
            'access point',
            'router',
            'switch',
            'lan',
            'koneksi',
            'infrastruktur jaringan',
        ],

        'gangguan_perangkat' => [
            'perangkat',
            'komputer',
            'laptop',
            'printer',
            'scanner',
            'hardware',
            'perbaikan perangkat',
            'pemeliharaan perangkat',
            'troubleshooting perangkat',
        ],

        'permintaan_akses' => [
            'akses',
            'akun',
            'user',
            'hak akses',
            'password',
            'reset password',
            'otorisasi',
            'manajemen pengguna',
        ],

        'permintaan_data' => [
            'data',
            'database',
            'laporan data',
            'pengolahan data',
            'rekap data',
            'penyediaan data',
            'backup data',
        ],

        'penggunaan_ruangan_lab' => [
            'laboratorium',
            'lab',
            'ruangan',
            'rpk',
            'komputer',
            'perangkat laboratorium',
            'pengelolaan laboratorium',
            'pemeliharaan laboratorium',
        ],

        'dukungan_rapat_zoom' => [
            'zoom',
            'rapat',
            'meeting',
            'video conference',
            'konferensi video',
            'dukungan kegiatan',
            'multimedia',
            'layanan rapat',
        ],

        /*
         * Kategori lainnya tidak mempunyai mapping tetap.
         * Resolver hanya memakai judul dan deskripsi tiket.
         */
        'lainnya' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Nilai minimum kecocokan
    |--------------------------------------------------------------------------
    */

    'minimum_score' => 10,

];
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pendaftaran Akun
    |--------------------------------------------------------------------------
    |
    | Mengatur apakah halaman /register terbuka untuk umum. Set
    | REGISTRASI_DIBUKA=false di .env untuk menonaktifkannya kapan saja —
    | tautan "Daftar" di halaman login ikut hilang dan rutenya membalas 403.
    |
    */

    'registrasi_dibuka' => (bool) env('REGISTRASI_DIBUKA', true),

];

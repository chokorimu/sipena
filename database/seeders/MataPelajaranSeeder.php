<?php

namespace Database\Seeders;

use App\Models\MataPelajaran;
use Illuminate\Database\Seeder;

class MataPelajaranSeeder extends Seeder
{
    /**
     * Seed daftar mata pelajaran. Aman dijalankan berulang (idempotent).
     */
    public function run(): void
    {
        $mapel = [
            'Matematika',
            'IPA',
            'IPS',
            'Bahasa Indonesia',
            'Bahasa Inggris',
            'PAI',
            'PPKn',
            'Penjasorkes',
        ];

        foreach ($mapel as $nama) {
            MataPelajaran::firstOrCreate(['nama_mapel' => $nama]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\MataPelajaran;
use Illuminate\Database\Seeder;

class MataPelajaranSeeder extends Seeder
{
    /**
     * Seed the application's database.
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
            MataPelajaran::create(['nama_mapel' => $nama]);
        }
    }
}

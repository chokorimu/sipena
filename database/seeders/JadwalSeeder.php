<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Database\Seeder;

class JadwalSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $kelas = Kelas::all();
        $mapel = MataPelajaran::all();
        $guru = Guru::all();

        $hari = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];

        foreach ($kelas as $k) {
            foreach ($mapel->take(4) as $index => $m) {
                Jadwal::create([
                    'kelas_id' => $k->id,
                    'mapel_id' => $m->id,
                    'guru_id' => $guru->random()->id,
                    'hari' => $hari[$index % count($hari)],
                    'jam_mulai' => sprintf('%02d:00:00', 7 + $index),
                    'jam_selesai' => sprintf('%02d:30:00', 7 + $index),
                ]);
            }
        }
    }
}

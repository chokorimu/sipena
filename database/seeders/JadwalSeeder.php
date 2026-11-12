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
     * Seed jadwal contoh (4 mapel pertama per kelas).
     *
     * Aman dijalankan berulang: kombinasi kelas + mapel + hari + jam dicek
     * dulu, jadi jadwal yang sudah ada tidak digandakan.
     */
    public function run(): void
    {
        $kelas = Kelas::all();
        $mapel = MataPelajaran::all();
        $guru = Guru::all();

        if ($kelas->isEmpty() || $mapel->isEmpty() || $guru->isEmpty()) {
            return;
        }

        $hari = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];

        foreach ($kelas as $k) {
            foreach ($mapel->take(4) as $index => $m) {
                $hariIni = $hari[$index % count($hari)];
                $jamMulai = sprintf('%02d:00:00', 7 + $index);
                $jamSelesai = sprintf('%02d:30:00', 7 + $index);

                Jadwal::firstOrCreate(
                    [
                        'kelas_id' => $k->id,
                        'mapel_id' => $m->id,
                        'hari' => $hariIni,
                        'jam_mulai' => $jamMulai,
                    ],
                    [
                        'guru_id' => $guru->random()->id,
                        'jam_selesai' => $jamSelesai,
                    ],
                );
            }
        }
    }
}

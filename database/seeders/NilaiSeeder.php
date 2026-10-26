<?php

namespace Database\Seeders;

use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class NilaiSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $tahun = TahunAjaran::where('tahun_ajaran', '2024/2025')
            ->where('semester', 'ganjil')
            ->firstOrFail();

        $mapel = MataPelajaran::all();

        Siswa::all()->each(function (Siswa $siswa) use ($mapel, $tahun): void {
            $mapel->each(function (MataPelajaran $m) use ($siswa, $tahun): void {
                Nilai::create([
                    'siswa_id' => $siswa->id,
                    'mapel_id' => $m->id,
                    'tahun_id' => $tahun->id,
                    'formatif' => random_int(65, 95),
                    'sumatif' => random_int(65, 95),
                ]);
            });
        });
    }
}

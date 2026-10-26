<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class KelasSiswaSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $tahun = TahunAjaran::where('tahun_ajaran', '2024/2025')
            ->where('semester', 'ganjil')
            ->firstOrFail();

        $kelasIds = Kelas::pluck('id');

        Siswa::all()->each(function (Siswa $siswa) use ($kelasIds, $tahun): void {
            $siswa->kelas()->attach($kelasIds->random(), ['tahun_id' => $tahun->id]);
        });
    }
}

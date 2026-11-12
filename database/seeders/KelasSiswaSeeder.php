<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class KelasSiswaSeeder extends Seeder
{
    /**
     * Tempatkan siswa ke kelas acak pada tahun ajaran 2024/2025 ganjil.
     *
     * Aman dijalankan berulang: siswa yang sudah punya kelas di tahun itu
     * dilewati, karena kolom (siswa_id, tahun_id) bersifat unik.
     */
    public function run(): void
    {
        $tahun = TahunAjaran::where('tahun_ajaran', '2024/2025')
            ->where('semester', 'ganjil')
            ->firstOrFail();

        $kelasIds = Kelas::pluck('id');

        if ($kelasIds->isEmpty()) {
            return;
        }

        Siswa::all()->each(function (Siswa $siswa) use ($kelasIds, $tahun): void {
            $sudahPunyaKelas = $siswa->kelas()
                ->wherePivot('tahun_id', $tahun->id)
                ->exists();

            if ($sudahPunyaKelas) {
                return;
            }

            $siswa->kelas()->attach($kelasIds->random(), ['tahun_id' => $tahun->id]);
        });
    }
}

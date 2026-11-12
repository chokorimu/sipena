<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Urutan penting karena saling bergantung:
     * - KelasSeeder butuh guru (wali_kelas_id)
     * - KelasSiswaSeeder butuh kelas, siswa, dan tahun ajaran
     * - NilaiSeeder / EvaluasiSeeder butuh siswa, mapel, dan tahun ajaran
     * - JadwalSeeder butuh kelas, mapel, dan guru
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            GuruSeeder::class,
            MataPelajaranSeeder::class,
            TahunAjaranSeeder::class,
            KelasSeeder::class,
            SiswaSeeder::class,
            KelasSiswaSeeder::class,
            NilaiSeeder::class,
            EvaluasiSeeder::class,
            JadwalSeeder::class,
        ]);
    }
}

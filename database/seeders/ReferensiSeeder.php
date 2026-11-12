<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ReferensiSeeder extends Seeder
{
    /**
     * Seed data referensi dasar saja: mata pelajaran, tahun ajaran, dan kelas.
     *
     * Berguna untuk menyiapkan aplikasi tanpa data contoh (guru, siswa, nilai).
     * Ketiga seeder di dalamnya idempotent, jadi aman dijalankan berulang.
     *
     * Pemakaian: php artisan db:seed --class=ReferensiSeeder
     */
    public function run(): void
    {
        $this->call([
            MataPelajaranSeeder::class,
            TahunAjaranSeeder::class,
            KelasSeeder::class,
        ]);
    }
}

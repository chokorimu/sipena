<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            GuruSeeder::class,
            KelasSeeder::class,
            MataPelajaranSeeder::class,
            TahunAjaranSeeder::class,
            SiswaSeeder::class,
            KelasSiswaSeeder::class,
            NilaiSeeder::class,
            JadwalSeeder::class,
        ]);
    }
}

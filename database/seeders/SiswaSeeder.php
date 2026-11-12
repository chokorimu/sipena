<?php

namespace Database\Seeders;

use App\Models\Siswa;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    /**
     * Seed data siswa contoh. Dilewati kalau tabel siswa sudah terisi, supaya
     * aman dijalankan berulang (idempotent).
     */
    public function run(): void
    {
        if (Siswa::query()->exists()) {
            return;
        }

        Siswa::factory(40)->create();
    }
}

<?php

namespace Database\Seeders;

use App\Models\Guru;
use Illuminate\Database\Seeder;

class GuruSeeder extends Seeder
{
    /**
     * Seed data guru contoh. Dilewati kalau tabel guru sudah terisi, supaya
     * aman dijalankan berulang (idempotent).
     */
    public function run(): void
    {
        if (Guru::query()->exists()) {
            return;
        }

        Guru::factory(10)->create();
    }
}

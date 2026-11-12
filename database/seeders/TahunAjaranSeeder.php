<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Seed tahun ajaran. Aman dijalankan berulang (idempotent) — kombinasi
     * tahun_ajaran + semester sudah unik di database.
     */
    public function run(): void
    {
        $tahun = [
            ['tahun_ajaran' => '2023/2024', 'semester' => 'genap'],
            ['tahun_ajaran' => '2024/2025', 'semester' => 'ganjil'],
            ['tahun_ajaran' => '2024/2025', 'semester' => 'genap'],
            ['tahun_ajaran' => '2025/2026', 'semester' => 'ganjil'],
            ['tahun_ajaran' => '2025/2026', 'semester' => 'genap'],
            ['tahun_ajaran' => '2026/2027', 'semester' => 'ganjil'],
            ['tahun_ajaran' => '2026/2027', 'semester' => 'genap'],
        ];

        foreach ($tahun as $data) {
            TahunAjaran::firstOrCreate($data);
        }
    }
}

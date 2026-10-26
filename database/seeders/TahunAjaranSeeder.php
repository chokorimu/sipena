<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $tahun = [
            ['tahun_ajaran' => '2023/2024', 'semester' => 'genap'],
            ['tahun_ajaran' => '2024/2025', 'semester' => 'ganjil'],
            ['tahun_ajaran' => '2024/2025', 'semester' => 'genap'],
        ];

        foreach ($tahun as $data) {
            TahunAjaran::create($data);
        }
    }
}

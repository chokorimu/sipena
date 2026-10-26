<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $guru = Guru::all();

        $kelas = [
            ['nama_kelas' => 'VII-A', 'tingkat' => 'VII'],
            ['nama_kelas' => 'VII-B', 'tingkat' => 'VII'],
            ['nama_kelas' => 'VIII-A', 'tingkat' => 'VIII'],
            ['nama_kelas' => 'VIII-B', 'tingkat' => 'VIII'],
            ['nama_kelas' => 'VIII-C', 'tingkat' => 'VIII'],
            ['nama_kelas' => 'IX-A', 'tingkat' => 'IX'],
            ['nama_kelas' => 'IX-B', 'tingkat' => 'IX'],
        ];

        foreach ($kelas as $data) {
            Kelas::create([
                ...$data,
                'wali_kelas_id' => $guru->random()->id,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Seed daftar kelas. Aman dijalankan berulang (idempotent).
     *
     * Wali kelas hanya diisi saat baris baru dibuat, jadi menjalankan ulang
     * seeder ini tidak akan menimpa wali kelas yang sudah diatur manual.
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
            Kelas::firstOrCreate(
                ['nama_kelas' => $data['nama_kelas']],
                [
                    'tingkat' => $data['tingkat'],
                    // Wali kelas opsional: seeder ini harus tetap jalan walau
                    // tabel guru belum di-seed.
                    'wali_kelas_id' => $guru->isNotEmpty() ? $guru->random()->id : null,
                ],
            );
        }
    }
}

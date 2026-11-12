<?php

namespace Database\Seeders;

use App\Models\Evaluasi;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class EvaluasiSeeder extends Seeder
{
    /**
     * Seed evaluasi naratif untuk siswa di tahun ajaran 2024/2025 ganjil.
     * Sengaja tidak semua siswa — sebagian dikosongkan untuk demo form.
     *
     * Aman dijalankan berulang: catatan yang sudah ada (termasuk hasil editan
     * guru) tidak ditimpa, karena kolom (siswa_id, tahun_id) bersifat unik.
     */
    public function run(): void
    {
        $tahun = TahunAjaran::where('tahun_ajaran', '2024/2025')
            ->where('semester', 'ganjil')
            ->firstOrFail();

        Siswa::query()->orderBy('id')->get()->each(function (Siswa $siswa, int $index) use ($tahun): void {
            // Sisakan ~40% siswa tanpa evaluasi untuk demo form. Pola berbasis
            // index (bukan acak) supaya hasilnya sama setiap kali dijalankan.
            if ($index % 5 >= 3) {
                return;
            }

            Evaluasi::firstOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'tahun_id' => $tahun->id,
                ],
                [
                    'catatan' => fake()->randomElement([
                        'Ananda menunjukkan perkembangan yang baik dalam memahami pelajaran. Terus tingkatkan kebiasaan belajar di rumah.',
                        'Ananda perlu lebih berani bertanya apabila menemukan kesulitan. Perbanyak latihan soal untuk memperkuat pemahaman.',
                        'Ananda aktif dalam kegiatan kelas dan memiliki sikap sopan kepada guru serta teman. Pertahankan prestasi ini.',
                        'Perkembangan ananda cukup memuaskan. Diharapkan lebih konsisten dalam mengumpulkan tugas tepat waktu.',
                    ]),
                ],
            );
        });
    }
}

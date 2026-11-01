<?php

namespace Database\Factories;

use App\Models\Evaluasi;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Evaluasi>
 */
class EvaluasiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'siswa_id' => Siswa::factory(),
            'tahun_id' => TahunAjaran::factory(),
            'catatan' => fake()->randomElement([
                'Ananda menunjukkan perkembangan yang baik dalam memahami pelajaran. Terus tingkatkan kebiasaan belajar di rumah.',
                'Ananda perlu lebih berani bertanya apabila menemukan kesulitan. Perbanyak latihan soal untuk memperkuat pemahaman.',
                'Ananda aktif dalam kegiatan kelas dan memiliki sikap sopan kepada guru serta teman. Pertahankan prestasi ini.',
                'Perkembangan ananda cukup memuaskan. Diharapkan lebih konsisten dalam mengumpulkan tugas tepat waktu.',
            ]),
        ];
    }
}

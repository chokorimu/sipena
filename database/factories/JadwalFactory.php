<?php

namespace Database\Factories;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Jadwal>
 */
class JadwalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kelas_id' => Kelas::factory(),
            'mapel_id' => MataPelajaran::factory(),
            'guru_id' => Guru::factory(),
            'hari' => fake()->randomElement(['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu']),
            'jam_mulai' => sprintf('%02d:00:00', fake()->numberBetween(7, 13)),
            'jam_selesai' => sprintf('%02d:30:00', fake()->numberBetween(7, 14)),
        ];
    }
}

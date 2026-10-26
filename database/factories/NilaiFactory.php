<?php

namespace Database\Factories;

use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Nilai>
 */
class NilaiFactory extends Factory
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
            'mapel_id' => MataPelajaran::factory(),
            'tahun_id' => TahunAjaran::factory(),
            'formatif' => fake()->numberBetween(65, 95),
            'sumatif' => fake()->numberBetween(65, 95),
        ];
    }
}

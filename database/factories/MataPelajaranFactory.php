<?php

namespace Database\Factories;

use App\Models\MataPelajaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MataPelajaran>
 */
class MataPelajaranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_mapel' => fake()->randomElement([
                'Matematika', 'IPA', 'IPS', 'Bahasa Indonesia', 'Bahasa Inggris',
                'PAI', 'PPKn', 'Penjasorkes',
            ]),
        ];
    }
}

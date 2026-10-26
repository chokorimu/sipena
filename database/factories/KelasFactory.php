<?php

namespace Database\Factories;

use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kelas>
 */
class KelasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_kelas' => fake()->randomElement(['VII-A', 'VII-B', 'VIII-A', 'VIII-B', 'VIII-C', 'IX-A', 'IX-B']),
            'tingkat' => fake()->randomElement(['VII', 'VIII', 'IX']),
            'wali_kelas_id' => null,
        ];
    }
}

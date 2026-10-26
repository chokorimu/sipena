<?php

namespace Database\Factories;

use App\Models\Guru;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Guru>
 */
class GuruFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nip' => fake()->unique()->numerify('##################'),
            'nama_guru' => fake('id_ID')->name(),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'alamat' => fake('id_ID')->address(),
            'no_hp' => fake()->numerify('08##########'),
        ];
    }
}

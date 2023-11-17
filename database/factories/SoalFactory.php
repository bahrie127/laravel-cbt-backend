<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Soal>
 */
class SoalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pertanyaan' => $this->faker->text(),
            'kategori' => $this->faker->randomElement(['Numeric', 'Verbal', 'Logika']),
            'jawaban_a' => $this->faker->word(),
            'jawaban_b' => $this->faker->word(),
            'jawaban_c' => $this->faker->word(),
            'jawaban_d' => $this->faker->word(),
            'kunci' => $this->faker->randomElement(['a', 'b', 'c', 'd']),

        ];
    }
}

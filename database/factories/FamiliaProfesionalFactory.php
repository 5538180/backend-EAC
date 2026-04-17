<?php

namespace Database\Factories;

use App\Models\FamiliaProfesional;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FamiliaProfesional>
 */
class FamiliaProfesionalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->words(3, true),
            'codigo' => strtoupper(fake()->unique()->word()),
            'descripcion' => fake()->optional()->paragraph(),
        ];
    }
}

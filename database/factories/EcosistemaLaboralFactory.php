<?php

namespace Database\Factories;

use App\Models\EcosistemaLaboral;
use App\Models\Modulo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EcosistemaLaboral>
 */
class EcosistemaLaboralFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
   
    {
        return [
            'nombre' => $this->faker->word(),
            'modulo_id'=> Modulo::factory(),
            'codigo' => fake()->unique()->word(),
            'descripcion' => $this->faker->paragraph(),
            'activo' => fake()->boolean(),
        ];
    }
}

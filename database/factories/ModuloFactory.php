<?php

namespace Database\Factories;


use App\Models\CicloFormativo;
use App\Models\Modulo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Modulo>
 */
class ModuloFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
   
    {
        return [
            'ciclo_formativo_id'=>CicloFormativo::factory(),
              'nombre' => $this->faker->word(),
            'codigo' => fake()->unique()->word(),
            'horas_totales'=> fake()->numberBetween(1,600),
            'descripcion' => $this->faker->paragraph(),
            
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Modulo;
use App\Models\ResultadoAprendizaje;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResultadoAprendizaje>
 */
class ResultadoAprendizajeFactory extends Factory
{
    protected $model = ResultadoAprendizaje::class;

    public function definition(): array
    {
        return [
            'modulo_id' => Modulo::factory(),
            'codigo' => 'RA' . $this->faker->unique()->numberBetween(1, 99),
            'descripcion' => $this->faker->sentence(),
        ];
    }
}

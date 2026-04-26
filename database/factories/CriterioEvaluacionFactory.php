<?php

namespace Database\Factories;

use App\Models\CriterioEvaluacion;
use App\Models\ResultadoAprendizaje;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CriterioEvaluacion>
 */
class CriterioEvaluacionFactory extends Factory
{
    protected $model = CriterioEvaluacion::class;

    public function definition(): array
    {
        return [
            'resultado_aprendizaje_id' => ResultadoAprendizaje::factory(),
            'codigo' => 'CE' . $this->faker->unique()->bothify('#?'),
            'descripcion' => $this->faker->sentence(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\EcosistemaLaboral;
use App\Models\HuellaTalento;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HuellaTalento>
 */
class HuellaTalentoFactory extends Factory
{
    protected $model = HuellaTalento::class;

    public function definition(): array
    {
        $estudiante = User::factory()->create();
        $ecosistema = EcosistemaLaboral::factory()->create(['activo' => true]);
        $ngsiLdId = "urn:ngsi-ld:PerfilHabilitacion:estudiante-{$estudiante->id}-ecosistema-{$ecosistema->id}";

        return [
            'estudiante_id' => $estudiante->id,
            'ecosistema_laboral_id' => $ecosistema->id,
            'payload' => [
                'ngsi_ld_id' => $ngsiLdId,
                'calificacion' => 0.0,
                'situaciones_conquistadas' => [],
                'desglose_curricular' => [],
                'version' => '1.0',
                'generada_en' => now()->toIso8601String(),
            ],
            'ngsi_ld_id' => $ngsiLdId,
            'generada_en' => now(),
        ];
    }
}

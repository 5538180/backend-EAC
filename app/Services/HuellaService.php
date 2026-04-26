<?php

namespace App\Services;

use App\Models\HuellaTalento;
use App\Models\PerfilHabilitacion;
use Illuminate\Support\Carbon;

class HuellaService
{
    public function __construct(
        private readonly CalificacionService $calificacionService,
    ) {}

    public function generar(PerfilHabilitacion $perfil): HuellaTalento
    {
        $perfil->loadMissing([
            'estudiante:id,name',
            'ecosistemaLaboral.modulo.cicloFormativo.familiaProfesional',
            'ecosistemaLaboral.modulo.resultadosAprendizaje.criteriosEvaluacion',
            'situacionesConquistadas.criteriosEvaluacion',
            'situacionesConquistadas.prerequisitos:id,codigo',
        ]);

        $generadaEn = now();
        $desglose = $this->calificacionService->desglose($perfil);
        $ngsiLdId = sprintf(
            'urn:ngsi-ld:PerfilHabilitacion:estudiante-%d-ecosistema-%d',
            $perfil->estudiante_id,
            $perfil->ecosistema_laboral_id
        );

        $payload = [
            'ngsi_ld_id' => $ngsiLdId,
            '@context' => 'https://vfds.example.org/ngsi-ld/eac-context.jsonld',
            'modulo' => $this->moduloPayload($perfil),
            'ecosistema' => [
                'id' => $perfil->ecosistema_laboral_id,
                'codigo' => $perfil->ecosistemaLaboral?->codigo,
                'nombre' => $perfil->ecosistemaLaboral?->nombre,
            ],
            'calificacion' => $desglose['calificacion_total'],
            'situaciones_conquistadas' => $perfil->situacionesConquistadas
                ->map(fn ($sc) => [
                    'codigo' => $sc->codigo,
                    'titulo' => $sc->titulo,
                    'gradiente_autonomia' => $sc->pivot->gradiente_autonomia,
                    'puntuacion_conquista' => (float) $sc->pivot->puntuacion_conquista,
                    'puntuacion_efectiva' => round(
                        (float) $sc->pivot->puntuacion_conquista
                            * (CalificacionService::FACTORES[$sc->pivot->gradiente_autonomia] ?? 1.0),
                        2
                    ),
                    'intentos' => (int) $sc->pivot->intentos,
                    'fecha_conquista' => $this->fechaIso($sc->pivot->fecha_conquista),
                ])
                ->values()
                ->all(),
            'desglose_curricular' => $desglose['desglose_ra'],
            'generada_en' => $generadaEn->toIso8601String(),
            'version' => '1.0',
        ];

        return HuellaTalento::create([
            'estudiante_id' => $perfil->estudiante_id,
            'ecosistema_laboral_id' => $perfil->ecosistema_laboral_id,
            'payload' => $payload,
            'ngsi_ld_id' => $ngsiLdId,
            'generada_en' => $generadaEn,
        ]);
    }

    public function ultimaOGenerar(PerfilHabilitacion $perfil): HuellaTalento
    {
        return HuellaTalento::where('estudiante_id', $perfil->estudiante_id)
            ->where('ecosistema_laboral_id', $perfil->ecosistema_laboral_id)
            ->latest('generada_en')
            ->first()
            ?? $this->generar($perfil);
    }

    private function moduloPayload(PerfilHabilitacion $perfil): array
    {
        $modulo = $perfil->ecosistemaLaboral?->modulo;
        $ciclo = $modulo?->cicloFormativo;

        return [
            'codigo' => $modulo?->codigo,
            'nombre' => $modulo?->nombre,
            'ciclo' => $ciclo?->nombre,
            'familia_profesional' => $ciclo?->familiaProfesional?->nombre,
        ];
    }

    private function fechaIso(mixed $fecha): ?string
    {
        if ($fecha instanceof Carbon) {
            return $fecha->toIso8601String();
        }

        return $fecha ? Carbon::parse($fecha)->toIso8601String() : null;
    }
}

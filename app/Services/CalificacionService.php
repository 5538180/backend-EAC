<?php

namespace App\Services;

use App\Models\PerfilHabilitacion;
use App\Models\ResultadoAprendizaje;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CalificacionService
{
    public const FACTORES = [
        'asistido' => 0.60,
        'guiado' => 0.75,
        'supervisado' => 0.90,
        'autonomo' => 1.00,
    ];

    public function calcularYPersistir(PerfilHabilitacion $perfil): float
    {
        $calificacion = $this->calcular($perfil);

        $perfil->update(['calificacion_actual' => $calificacion]);

        return $calificacion;
    }

    public function calcular(PerfilHabilitacion $perfil): float
    {
        return $this->desglose($perfil)['calificacion_total'];
    }

    public function desglose(PerfilHabilitacion $perfil): array
    {
        $perfil->loadMissing([
            'situacionesConquistadas.criteriosEvaluacion',
            'ecosistemaLaboral.modulo.resultadosAprendizaje.criteriosEvaluacion.situacionesCompetencia',
        ]);

        $modulo = $perfil->ecosistemaLaboral?->modulo;

        if (!$modulo) {
            return [
                'calificacion_total' => 0.0,
                'desglose_ra' => [],
            ];
        }

        $conquistasIndexadas = $this->indexarConquistas($perfil->situacionesConquistadas);
        $desglose = [];
        $sumaPonderadaRas = 0.0;
        $sumaPesosRas = 0.0;

        foreach ($modulo->resultadosAprendizaje as $ra) {
            $pesoRa = $this->pesoNormalizado($ra);
            $desgloseCes = $this->calcularDesgloseCes($ra, $conquistasIndexadas);
            $puntuacionRa = $desgloseCes['puntuacion_ra'];

            $sumaPonderadaRas += $puntuacionRa * $pesoRa;
            $sumaPesosRas += $pesoRa;

            $desglose[] = [
                'ra' => $ra->codigo,
                'descripcion' => $ra->descripcion,
                'peso' => $this->pesoMostrado($ra),
                'puntuacion' => round($puntuacionRa, 2),
                'criterios' => $desgloseCes['criterios'],
            ];
        }

        if ($sumaPesosRas <= 0) {
            return [
                'calificacion_total' => 0.0,
                'desglose_ra' => $desglose,
            ];
        }

        $calificacion = ($sumaPonderadaRas / $sumaPesosRas) / 10;

        return [
            'calificacion_total' => round(min(10.0, max(0.0, $calificacion)), 2),
            'desglose_ra' => $desglose,
        ];
    }

    private function indexarConquistas(Collection $situacionesConquistadas): array
    {
        $indice = [];

        foreach ($situacionesConquistadas as $sc) {
            $gradiente = $sc->pivot->gradiente_autonomia;
            $factor = self::FACTORES[$gradiente] ?? 1.0;
            $puntuacionEfectiva = (float) $sc->pivot->puntuacion_conquista * $factor;

            foreach ($sc->criteriosEvaluacion as $ce) {
                $pesoEnSc = (float) ($ce->pivot->peso_en_sc ?? 0);

                $indice[$ce->id][] = [
                    'puntuacion_efectiva' => $puntuacionEfectiva,
                    'peso_en_sc' => $pesoEnSc > 0 ? $pesoEnSc : 1.0,
                ];
            }
        }

        return $indice;
    }

    private function calcularDesgloseCes(ResultadoAprendizaje $ra, array $conquistasIndexadas): array
    {
        $sumaPonderadaCes = 0.0;
        $sumaPesosCes = 0.0;
        $criterios = [];

        foreach ($ra->criteriosEvaluacion as $ce) {
            $pesoCe = $this->pesoNormalizado($ce);
            $puntuacionCe = $this->calcularPuntuacionCe($ce->id, $conquistasIndexadas);

            $sumaPonderadaCes += $puntuacionCe * $pesoCe;
            $sumaPesosCes += $pesoCe;

            $criterios[] = [
                'ce' => $ce->codigo,
                'descripcion' => $ce->descripcion,
                'peso' => $this->pesoMostrado($ce),
                'puntuacion' => round($puntuacionCe, 2),
                'cubierto' => isset($conquistasIndexadas[$ce->id]),
            ];
        }

        return [
            'puntuacion_ra' => $sumaPesosCes > 0 ? $sumaPonderadaCes / $sumaPesosCes : 0.0,
            'criterios' => $criterios,
        ];
    }

    private function calcularPuntuacionCe(int $ceId, array $conquistasIndexadas): float
    {
        if (!isset($conquistasIndexadas[$ceId])) {
            return 0.0;
        }

        $sumaPonderada = 0.0;
        $sumaPesos = 0.0;

        foreach ($conquistasIndexadas[$ceId] as $entrada) {
            $sumaPonderada += $entrada['puntuacion_efectiva'] * $entrada['peso_en_sc'];
            $sumaPesos += $entrada['peso_en_sc'];
        }

        return $sumaPesos > 0 ? $sumaPonderada / $sumaPesos : 0.0;
    }

    private function pesoNormalizado(Model $model): float
    {
        $peso = $this->pesoMostrado($model);

        return $peso / 100;
    }

    private function pesoMostrado(Model $model): float
    {
        $peso = (float) ($model->getAttribute('peso_porcentaje') ?? 0);

        return $peso > 0 ? $peso : 1.0;
    }
}

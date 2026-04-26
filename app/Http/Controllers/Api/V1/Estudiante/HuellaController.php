<?php

namespace App\Http\Controllers\Api\V1\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\EcosistemaLaboral;
use App\Models\HuellaTalento;
use App\Models\PerfilHabilitacion;
use App\Services\HuellaService;
use Illuminate\Http\JsonResponse;

class HuellaController extends Controller
{
    public function __construct(
        private readonly HuellaService $huellaService,
    ) {}

    public function show(EcosistemaLaboral $ecosistema): JsonResponse
    {
        $perfil = $this->perfilOFail($ecosistema);
        $huella = $this->huellaService->ultimaOGenerar($perfil);

        return $this->huellaResponse($huella);
    }

    public function store(EcosistemaLaboral $ecosistema): JsonResponse
    {
        $perfil = $this->perfilOFail($ecosistema);
        $huella = $this->huellaService->generar($perfil);

        return $this->huellaResponse($huella, 201);
    }

    public function index(EcosistemaLaboral $ecosistema): JsonResponse
    {
        $perfil = $this->perfilOFail($ecosistema);

        $huellas = HuellaTalento::where('estudiante_id', $perfil->estudiante_id)
            ->where('ecosistema_laboral_id', $ecosistema->id)
            ->orderByDesc('generada_en')
            ->get(['id', 'generada_en', 'ngsi_ld_id']);

        return response()->json([
            'data' => $huellas->map(fn (HuellaTalento $huella) => [
                'id' => $huella->id,
                'generada_en' => $huella->generada_en->toIso8601String(),
                'ngsi_ld_id' => $huella->ngsi_ld_id,
                'links' => [
                    'self' => route('api.v1.estudiante.huella.show', $ecosistema),
                ],
            ])->values(),
            'meta' => [
                'version' => '1.0',
                'timestamp' => now()->toIso8601String(),
                'total' => $huellas->count(),
            ],
        ]);
    }

    private function perfilOFail(EcosistemaLaboral $ecosistema): PerfilHabilitacion
    {
        $perfil = PerfilHabilitacion::where('estudiante_id', auth()->id())
            ->where('ecosistema_laboral_id', $ecosistema->id)
            ->first();

        if (!$perfil) {
            abort(response()->json([
                'type' => 'https://backend-eac.test/errors/perfil-no-encontrado',
                'title' => 'Perfil no encontrado',
                'status' => 404,
                'detail' => 'No tienes perfil en este ecosistema. Matriculate primero.',
            ], 404));
        }

        return $perfil;
    }

    private function huellaResponse(HuellaTalento $huella, int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => $huella->payload,
            'meta' => [
                'huella_id' => $huella->id,
                'generada_en' => $huella->generada_en->toIso8601String(),
                'ngsi_ld_id' => $huella->ngsi_ld_id,
                'version' => '1.0',
                'timestamp' => now()->toIso8601String(),
            ],
        ], $status);
    }
}

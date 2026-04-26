<?php

namespace App\Http\Controllers\Api\V1\Docente;

use App\Http\Controllers\Controller;
use App\Models\EcosistemaLaboral;
use App\Models\PerfilHabilitacion;
use App\Services\CalificacionService;
use Illuminate\Http\JsonResponse;

class CalificacionController extends Controller
{
    public function __construct(
        private readonly CalificacionService $calificacionService,
    ) {}

    public function __invoke(EcosistemaLaboral $ecosistema, int $estudianteId): JsonResponse
    {
        $this->autorizarDocente($ecosistema);

        $perfil = PerfilHabilitacion::where('estudiante_id', $estudianteId)
            ->where('ecosistema_laboral_id', $ecosistema->id)
            ->first();

        if (!$perfil) {
            abort(response()->json([
                'type' => 'https://backend-eac.test/errors/perfil-no-encontrado',
                'title' => 'Perfil no encontrado',
                'status' => 404,
                'detail' => 'El estudiante no tiene perfil en este ecosistema.',
            ], 404));
        }

        $desglose = $this->calificacionService->desglose($perfil);

        return response()->json([
            'data' => [
                'estudiante_id' => $estudianteId,
                'ecosistema_id' => $ecosistema->id,
                'calificacion_total' => $desglose['calificacion_total'],
                'desglose_ra' => $desglose['desglose_ra'],
            ],
            'meta' => [
                'version' => '1.0',
                'timestamp' => now()->toIso8601String(),
            ],
        ]);
    }

    private function autorizarDocente(EcosistemaLaboral $ecosistema): void
    {
        $esDocente = auth()->user()
            ->userRoles()
            ->wherePivot('ecosistema_laboral_id', $ecosistema->id)
            ->where('name', 'docente')
            ->exists();

        abort_unless($esDocente, 403, 'No tienes rol de docente en este ecosistema.');
    }
}

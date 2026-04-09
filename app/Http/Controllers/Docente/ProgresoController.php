<?php

namespace App\Http\Controllers\Docente;

use App\Models\EcosistemaLaboral;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgresoController
{
    /**
     * Handle the incoming request.
     */
   public function __invoke(EcosistemaLaboral $ecosistema): View
    {
        $esDocente = auth()->user()
            ->userRoles()
            ->where('ecosistema_laboral_id', $ecosistema->id)
            ->whereHas('role', fn($q) => $q->where('name', 'docente'))
            ->exists();

        abort_unless($esDocente, 403);

        $ecosistema->load(['situacionesCompetencia', 'modulo']);

        $perfiles = $ecosistema->perfilesHabilitacion()
            ->with(['estudiante', 'situacionesConquistadas'])
            ->get();

        return view('docente.progreso.show', compact('ecosistema', 'perfiles'));
    }
}

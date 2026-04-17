<?php

namespace App\Http\Controllers\Estudiante;

use App\Models\PerfilHabilitacion;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PerfilController
{
    /**
     * Handle the incoming request.
     */
        public function __invoke(PerfilHabilitacion $perfil): View
    {
        abort_unless($perfil->estudiante_id === auth()->id(), 403);

        $perfil->load([
            'ecosistemaLaboral.modulo',
            'ecosistemaLaboral.situacionesCompetencia.prerequisitos',
            'situacionesConquistadas',
        ]);

        return view('estudiante.perfil.show', compact('perfil'));
    }
}

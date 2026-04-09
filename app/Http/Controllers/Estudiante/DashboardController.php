<?php

namespace App\Http\Controllers\Estudiante;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController
{
    /**
     * Handle the incoming request.
     */
        public function __invoke(): View
    {
        $perfiles = auth()->user()
            ->perfilesHabilitacion()
            ->with([
                'ecosistemaLaboral.modulo',
                'ecosistemaLaboral.situacionesCompetencia',
                'situacionesConquistadas',
            ])
            ->get();

        return view('estudiante.dashboard', compact('perfiles'));
    }
}

<?php

namespace App\Http\Controllers\Publico;

use App\Models\Modulo;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortadaController
{
    /**
     * Handle the incoming request.
     */
       public function __invoke(): View
    {
        $modulos = Modulo::with([
                'cicloFormativo.familiaProfesional',
                'ecosistemasLaborales' => fn($q) => $q->where('activo', true),
            ])
            ->whereHas('ecosistemasLaborales', fn($q) => $q->where('activo', true))
            ->take(6)->get();

        return view('publico.portada', compact('modulos'));
    }
}

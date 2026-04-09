<?php

namespace App\Http\Controllers\Publico;

use App\Models\Modulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class EcosistemaController
{
    /**
     * Handle the incoming request.
     */
       public function __invoke(): \Illuminate\View\View
    {
        $modulos = Modulo::with([
                'cicloFomativo.familiaProfesional',
                'ecosistemasLaborales' => fn($q) => $q->where('activo', true),
            ])
            ->whereHas('ecosistemasLaborales', fn($q) => $q->where('activo', true))
            ->take(6)->get();

        return view('publico.portada', compact('modulos'));
    }
}

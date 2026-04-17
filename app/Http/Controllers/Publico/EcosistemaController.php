<?php

namespace App\Http\Controllers\Publico;

use App\Models\EcosistemaLaboral;
use App\Models\Modulo;
use Illuminate\Http\Request;
use Illuminate\View\View;


class EcosistemaController
{
    /**
     * Handle the incoming request.
     */
     // app/Http/Controllers/Publico/EcosistemaController.php

    public function __invoke(EcosistemaLaboral $ecosistema):View
    {
        $ecosistema->load([
            'modulo.cicloFormativo.familiaProfesional',
            'situacionesCompetencia.prerequisitos',
        ]);

        return view('publico.ecosistemas.show', compact('ecosistema'));
    }
}

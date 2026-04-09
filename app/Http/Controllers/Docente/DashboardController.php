<?php

namespace App\Http\Controllers\Docente;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController
{
    /**
     * Handle the incoming request.
     */
        public function __invoke(): View
    {
        $docenteRoleId = Role::where('name', 'docente')->value('id');

        $ecosistemas = auth()->user()
            ->userRoles()
            ->where('role_id', $docenteRoleId)
            ->with([
                'ecosistemaLaboral.modulo',
                'ecosistemaLaboral.situacionesCompetencia',
                'ecosistemaLaboral.perfilesHabilitacion',
            ])
            ->get()
            ->pluck('ecosistemaLaboral')
            ->filter();

        return view('docente.dashboard', compact('ecosistemas'));
    }
}

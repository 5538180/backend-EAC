<?php

namespace App\Http\Controllers\Docente;

use App\Models\EcosistemaLaboral;
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

        $ecosistemaIds = auth()->user()
            ->userRoles()
            ->where('role_id', $docenteRoleId)
            ->pluck('user_roles.ecosistema_laboral_id')
            ->filter();

        $ecosistemas = EcosistemaLaboral::query()
            ->with([
                'modulo',
                'situacionesCompetencia',
                'perfilesHabilitacion',
            ])
            ->whereIn('id', $ecosistemaIds)
            ->get();

        return view('docente.dashboard', compact('ecosistemas'));
    }
}

<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModuloController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request) : View
    {
        return view("estudiante.modulos.index");
        // - 1 Comprueba mi id de ususario y mis roles 
        // - 2 Lanza una peticion a la BBDD preguntando en los modulos que estoy adscrito
        // - 2 Devuelve a  la vista modulos.index los modulos asociados
    }
}

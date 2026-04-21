<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\FamiliaProfesional;
use App\Models\Modulo;
use App\Models\PerfilHabilitacion;
use App\Services\GrafoService;
use App\Services\RecomendacionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ModuloController extends Controller
{
/* - Parte nueva en 4.4.3 */
 public function __construct(
        private readonly GrafoService         $grafoService,
        private readonly RecomendacionService $recomendacionService,
    ) {}

    /**
    * Handle the incoming request.
    */
    public function index(Request $request) : View
    {
        $familias = FamiliaProfesional::orderBy('nombre')->get();

        $modulos = Modulo::with([
            'cicloFormativo.familiaProfesional',
            'ecosistemasLaborales' => fn($q) => $q->where('activo', true),
        ])
        ->whereHas('ecosistemasLaborales', fn($q) => $q->where('activo', true))
        ->whereHas('matriculas', fn($q) => $q->where('estudiante_id', auth()->id()))
        ->when($request->filled('familia'), fn($q) =>
            $q->whereHas('cicloFormativo',
                fn($q2) => $q2->where('familia_profesional_id', $request->familia))
        )
        ->orderBy('codigo')
        ->paginate(15);

        return view('publico.modulos.index', compact('modulos', 'familias'));
    }

    public function show(Modulo $modulo): View
    {
        abort_unless(
            auth()->user()->matriculas()->where('modulo_id', $modulo->id)->exists(),
            403, 'No estás matriculado en este módulo.'
        );

        $ecosistema = $modulo->ecosistemasLaborales()
            ->where('activo', true)
            ->firstOrFail();

        $perfil = PerfilHabilitacion::where('estudiante_id', auth()->id())
            ->where('ecosistema_laboral_id', $ecosistema->id)
            ->with('situacionesConquistadas')
            ->first();

        $codigosConquistados = $perfil?->codigosConquistados() ?? [];

        $clasificacion = $this->grafoService->clasificar($ecosistema, $codigosConquistados);
        $recomendacion = $this->recomendacionService->recomendar($ecosistema, $codigosConquistados);

        return view('estudiante.modulo', compact(
            'modulo', 'ecosistema', 'perfil',
            'clasificacion', 'recomendacion', 'codigosConquistados'
        ));
    }




     /* - Parte en 2.11 */
    /**
     * Handle the incoming request.
     */
   /*  public function __invoke(Request $request)
    { */

// ! crear array bidimensional/ objeto <modulos> con los datos necesarios para el index; a mitad

/*             $usuarioID = auth()->id();
        $usuarioNombre = auth()->user()->name();

        $tablaMatriculasUsuario = Matricula::where('estudiante_id', $usuarioID)->get();

         ? control posible de no estar matriculado en nada
        if (count($tablaMatriculasUsuario) <= 0) {
            return 'No hay modulos adscritos al usuario' . $usuarioNombre;
        }

        $modulos = [];

        foreach ($tablaMatriculasUsuario as $fila) {
            $moduloId = $fila->modulo_id;

            $modulo = Modulo::with([
                'cicloFormativo.familiaProfesional',
                'ecosistemasLaborales'
            ])->where('modulo_id', $moduloId)->first(); // mejor first() si esperas 1

            if ($modulo) {
                $modulos[] = $modulo; 
            }
        } */
    
    
   
   
     /*    $familias = FamiliaProfesional::orderBy('nombre')->get();

        $modulos = Modulo::with([
                'cicloFormativo.familiaProfesional',
                'ecosistemasLaborales' => fn($q) => $q->where('activo', true),
            ])
            ->whereHas('ecosistemasLaborales', fn($q) => $q->where('activo', true))
            ->whereHas('matriculas', fn($q) => $q->where('estudiante_id', auth()->id()))
            ->when($request->filled('familia'), fn($q) =>
                $q->whereHas('cicloFormativo',
                    fn($q2) => $q2->where('familia_profesional_id', $request->familia))
            )
            ->orderBy('codigo')
            ->paginate(15);

        return view('publico.modulos.index', compact('modulos', 'familias')); */
    /* } */
}

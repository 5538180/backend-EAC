<?php

namespace Tests\Feature;

use App\Models\CriterioEvaluacion;
use App\Models\EcosistemaLaboral;
use App\Models\Matricula;
use App\Models\PerfilHabilitacion;
use App\Models\ResultadoAprendizaje;
use App\Models\Role;
use App\Models\SituacionCompetencia;
use App\Models\User;
use App\Services\CalificacionService;
use App\Services\EACAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class VisualizacionCompetencialTest extends TestCase
{
    use RefreshDatabase;

    public function test_analytics_service_returns_chart_data(): void
    {
        [$ecosistema, $perfil] = $this->crearEcosistemaConConquista();

        app(CalificacionService::class)->calcularYPersistir($perfil->fresh());

        $service = app(EACAnalyticsService::class);

        $ranking = $service->rankingConquistas($ecosistema);
        $gradientes = $service->distribucionGradiente($ecosistema);
        $evolucion = $service->evolucionTemporal($ecosistema, 4);
        $radar = $service->radarHuella($perfil->fresh());

        $this->assertSame(['SC-01', 'SC-02'], $ranking['labels']);
        $this->assertSame([1, 0], $ranking['data']);

        $this->assertSame(['Asistido', 'Guiado', 'Supervisado', 'Autonomo'], $gradientes['labels']);
        $this->assertSame([0, 0, 0, 1], $gradientes['data']);

        $this->assertCount(4, $evolucion['labels']);
        $this->assertSame(1, array_sum($evolucion['data']));

        $this->assertSame(['RA1'], $radar['labels']);
        $this->assertSame([90.0], $radar['data']);
    }

    public function test_docente_can_view_analytics_dashboard(): void
    {
        [$ecosistema] = $this->crearEcosistemaConConquista();
        $docente = User::factory()->create();
        $this->asignarRol($docente, 'docente', $ecosistema);

        $response = $this->actingAs($docente)
            ->get(route('docente.ecosistemas.analytics', $ecosistema));

        $response->assertOk()
            ->assertSee('Visualizacion competencial')
            ->assertSee('Ranking de SC conquistadas')
            ->assertSee('<canvas', false);

        $this->assertSame(3, substr_count($response->getContent(), '<canvas'));
    }

    public function test_estudiante_can_view_huella_radar(): void
    {
        [$ecosistema, $perfil, $estudiante] = $this->crearEcosistemaConConquista();
        $this->asignarRol($estudiante, 'estudiante', $ecosistema);

        app(CalificacionService::class)->calcularYPersistir($perfil->fresh());

        $response = $this->actingAs($estudiante)
            ->get(route('estudiante.huella-radar', $ecosistema));

        $response->assertOk()
            ->assertSee('Radar de Huella de Talento')
            ->assertSee('9.00')
            ->assertSee('<canvas', false);

        $this->assertSame(1, substr_count($response->getContent(), '<canvas'));
    }

    private function crearEcosistemaConConquista(): array
    {
        $ecosistema = EcosistemaLaboral::factory()->create(['activo' => true]);
        $estudiante = User::factory()->create();

        Matricula::create([
            'estudiante_id' => $estudiante->id,
            'modulo_id' => $ecosistema->modulo_id,
        ]);

        $perfil = PerfilHabilitacion::create([
            'estudiante_id' => $estudiante->id,
            'ecosistema_laboral_id' => $ecosistema->id,
            'calificacion_actual' => 0,
        ]);

        $ra = ResultadoAprendizaje::factory()->create([
            'modulo_id' => $ecosistema->modulo_id,
            'codigo' => 'RA1',
        ]);

        $ce = CriterioEvaluacion::factory()->create([
            'resultado_aprendizaje_id' => $ra->id,
            'codigo' => 'CE1',
        ]);

        $scConquistada = SituacionCompetencia::factory()->create([
            'ecosistema_laboral_id' => $ecosistema->id,
            'codigo' => 'SC-01',
            'umbral_maestria' => 50.00,
        ]);

        SituacionCompetencia::factory()->create([
            'ecosistema_laboral_id' => $ecosistema->id,
            'codigo' => 'SC-02',
            'umbral_maestria' => 50.00,
        ]);

        $scConquistada->criteriosEvaluacion()->attach($ce->id, ['peso_en_sc' => 100]);

        $perfil->situacionesConquistadas()->attach($scConquistada->id, [
            'gradiente_autonomia' => 'autonomo',
            'puntuacion_conquista' => 90.0,
            'intentos' => 1,
            'fecha_conquista' => now(),
        ]);

        return [$ecosistema->fresh(), $perfil->fresh(), $estudiante];
    }

    private function asignarRol(User $user, string $rol, EcosistemaLaboral $ecosistema): void
    {
        $role = Role::firstOrCreate(['name' => $rol]);

        DB::table('user_roles')->insert([
            'user_id' => $user->id,
            'role_id' => $role->id,
            'ecosistema_laboral_id' => $ecosistema->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

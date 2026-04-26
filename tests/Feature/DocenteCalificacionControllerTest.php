<?php

namespace Tests\Feature\Api\V1;

use App\Models\CriterioEvaluacion;
use App\Models\EcosistemaLaboral;
use App\Models\Matricula;
use App\Models\PerfilHabilitacion;
use App\Models\ResultadoAprendizaje;
use App\Models\Role;
use App\Models\SituacionCompetencia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DocenteCalificacionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_calificacion_requires_authentication(): void
    {
        $ecosistema = EcosistemaLaboral::factory()->create(['activo' => true]);
        $estudiante = User::factory()->create();

        $this->getJson($this->urlCalificacion($ecosistema->id, $estudiante->id))
            ->assertStatus(401);
    }

    public function test_calificacion_forbidden_without_docente_role(): void
    {
        $usuario = User::factory()->create();
        $ecosistema = EcosistemaLaboral::factory()->create(['activo' => true]);
        [$estudiante] = $this->crearEstudiante($ecosistema);

        Sanctum::actingAs($usuario);

        $this->getJson($this->urlCalificacion($ecosistema->id, $estudiante->id))
            ->assertStatus(403);
    }

    public function test_calificacion_returns_zero_and_uncovered_criteria_without_conquests(): void
    {
        $ecosistema = EcosistemaLaboral::factory()->create(['activo' => true]);
        $docente = $this->crearDocente($ecosistema);
        [$estudiante] = $this->crearEstudiante($ecosistema);
        $this->crearCriterio($ecosistema);

        Sanctum::actingAs($docente);

        $response = $this->getJson($this->urlCalificacion($ecosistema->id, $estudiante->id));

        $response->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.calificacion_total', 0)
                    ->where('data.desglose_ra.0.criterios.0.cubierto', false)
                    ->has('meta')
                    ->etc()
            );
    }

    public function test_calificacion_applies_gradient_and_sc_weights(): void
    {
        $ecosistema = EcosistemaLaboral::factory()->create(['activo' => true]);
        $docente = $this->crearDocente($ecosistema);
        [$estudiante, $perfil] = $this->crearEstudiante($ecosistema);
        [, $ce] = $this->crearCriterio($ecosistema);

        $sc1 = SituacionCompetencia::factory()->create([
            'ecosistema_laboral_id' => $ecosistema->id,
            'umbral_maestria' => 50.00,
        ]);
        $sc2 = SituacionCompetencia::factory()->create([
            'ecosistema_laboral_id' => $ecosistema->id,
            'umbral_maestria' => 50.00,
        ]);

        $sc1->criteriosEvaluacion()->attach($ce->id, ['peso_en_sc' => 40]);
        $sc2->criteriosEvaluacion()->attach($ce->id, ['peso_en_sc' => 60]);

        $perfil->situacionesConquistadas()->attach($sc1->id, [
            'gradiente_autonomia' => 'autonomo',
            'puntuacion_conquista' => 60.0,
            'intentos' => 1,
            'fecha_conquista' => now(),
        ]);
        $perfil->situacionesConquistadas()->attach($sc2->id, [
            'gradiente_autonomia' => 'supervisado',
            'puntuacion_conquista' => 100.0,
            'intentos' => 1,
            'fecha_conquista' => now(),
        ]);

        Sanctum::actingAs($docente);

        $response = $this->getJson($this->urlCalificacion($ecosistema->id, $estudiante->id));

        $response->assertStatus(200);
        $this->assertEquals(78.0, $response->json('data.desglose_ra.0.criterios.0.puntuacion'));
        $this->assertEquals(7.8, $response->json('data.calificacion_total'));
    }

    private function crearDocente(EcosistemaLaboral $ecosistema): User
    {
        $docente = User::factory()->create();
        $role = Role::create(['name' => 'docente']);

        DB::table('user_roles')->insert([
            'user_id' => $docente->id,
            'role_id' => $role->id,
            'ecosistema_laboral_id' => $ecosistema->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $docente;
    }

    private function crearEstudiante(EcosistemaLaboral $ecosistema): array
    {
        $estudiante = User::factory()->create();

        Matricula::create([
            'estudiante_id' => $estudiante->id,
            'modulo_id' => $ecosistema->modulo_id,
        ]);

        $perfil = PerfilHabilitacion::create([
            'estudiante_id' => $estudiante->id,
            'ecosistema_laboral_id' => $ecosistema->id,
            'calificacion_actual' => 0.00,
        ]);

        return [$estudiante, $perfil];
    }

    private function crearCriterio(EcosistemaLaboral $ecosistema): array
    {
        $ra = ResultadoAprendizaje::factory()->create(['modulo_id' => $ecosistema->modulo_id]);
        $ce = CriterioEvaluacion::factory()->create(['resultado_aprendizaje_id' => $ra->id]);

        return [$ra, $ce];
    }

    private function urlCalificacion(int $ecosistemaId, int $estudianteId): string
    {
        return "/api/v1/docente/ecosistemas/{$ecosistemaId}/calificacion/{$estudianteId}";
    }
}

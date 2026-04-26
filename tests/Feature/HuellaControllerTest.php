<?php

namespace Tests\Feature\Api\V1;

use App\Models\CriterioEvaluacion;
use App\Models\EcosistemaLaboral;
use App\Models\HuellaTalento;
use App\Models\Matricula;
use App\Models\PerfilHabilitacion;
use App\Models\ResultadoAprendizaje;
use App\Models\SituacionCompetencia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class HuellaControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_huella_requires_authentication(): void
    {
        $ecosistema = EcosistemaLaboral::factory()->create(['activo' => true]);

        $this->getJson($this->urlHuella($ecosistema->id))
            ->assertStatus(401);
    }

    public function test_get_huella_returns_404_when_student_has_no_profile(): void
    {
        $estudiante = User::factory()->create();
        $ecosistema = EcosistemaLaboral::factory()->create(['activo' => true]);

        Sanctum::actingAs($estudiante);

        $this->getJson($this->urlHuella($ecosistema->id))
            ->assertStatus(404)
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('status', 404)->has('detail')->etc()
            );
    }

    public function test_post_huella_creates_snapshot_with_ngsi_id(): void
    {
        [$estudiante, $ecosistema] = $this->crearEscenarioBase();

        Sanctum::actingAs($estudiante);

        $response = $this->postJson($this->urlHuella($ecosistema->id));

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['ngsi_ld_id', 'calificacion', 'situaciones_conquistadas', 'desglose_curricular'],
                'meta' => ['huella_id', 'generada_en', 'ngsi_ld_id', 'version', 'timestamp'],
            ]);

        $expectedUrn = "urn:ngsi-ld:PerfilHabilitacion:estudiante-{$estudiante->id}-ecosistema-{$ecosistema->id}";

        $this->assertSame($expectedUrn, $response->json('data.ngsi_ld_id'));
        $this->assertDatabaseHas('huellas_talento', [
            'estudiante_id' => $estudiante->id,
            'ecosistema_laboral_id' => $ecosistema->id,
            'ngsi_ld_id' => $expectedUrn,
        ]);
    }

    public function test_post_huella_payload_reflects_conquered_scs_and_gradient(): void
    {
        [$estudiante, $ecosistema, $perfil] = $this->crearEscenarioBase();

        $sc = $this->crearSituacionConCriterio($ecosistema);
        $perfil->situacionesConquistadas()->attach($sc->id, [
            'gradiente_autonomia' => 'supervisado',
            'puntuacion_conquista' => 100.0,
            'intentos' => 1,
            'fecha_conquista' => now(),
        ]);

        Sanctum::actingAs($estudiante);

        $response = $this->postJson($this->urlHuella($ecosistema->id));

        $response->assertStatus(201);
        $this->assertSame($sc->codigo, $response->json('data.situaciones_conquistadas.0.codigo'));
        $this->assertSame('supervisado', $response->json('data.situaciones_conquistadas.0.gradiente_autonomia'));
        $this->assertEquals(90.0, $response->json('data.situaciones_conquistadas.0.puntuacion_efectiva'));
        $this->assertEquals(9.0, $response->json('data.calificacion'));
    }

    public function test_get_huellas_returns_only_own_history_ordered_by_date(): void
    {
        [$estudiante, $ecosistema] = $this->crearEscenarioBase();
        [$otroEstudiante] = $this->crearEscenarioBase($ecosistema);

        $older = $this->crearHuella($estudiante, $ecosistema, now()->subHour());
        $newer = $this->crearHuella($estudiante, $ecosistema, now());
        $this->crearHuella($otroEstudiante, $ecosistema, now()->addMinute());

        Sanctum::actingAs($estudiante);

        $response = $this->getJson($this->urlHuellas($ecosistema->id));

        $response->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('meta.total', 2)
                    ->has('data', 2)
                    ->etc()
            );

        $this->assertSame($newer->id, $response->json('data.0.id'));
        $this->assertSame($older->id, $response->json('data.1.id'));
    }

    private function crearEscenarioBase(?EcosistemaLaboral $ecosistema = null): array
    {
        $estudiante = User::factory()->create();
        $ecosistema ??= EcosistemaLaboral::factory()->create(['activo' => true]);

        Matricula::create([
            'estudiante_id' => $estudiante->id,
            'modulo_id' => $ecosistema->modulo_id,
        ]);

        $perfil = PerfilHabilitacion::create([
            'estudiante_id' => $estudiante->id,
            'ecosistema_laboral_id' => $ecosistema->id,
            'calificacion_actual' => 0.00,
        ]);

        return [$estudiante, $ecosistema, $perfil];
    }

    private function crearSituacionConCriterio(EcosistemaLaboral $ecosistema): SituacionCompetencia
    {
        $ra = ResultadoAprendizaje::factory()->create(['modulo_id' => $ecosistema->modulo_id]);
        $ce = CriterioEvaluacion::factory()->create(['resultado_aprendizaje_id' => $ra->id]);
        $sc = SituacionCompetencia::factory()->create([
            'ecosistema_laboral_id' => $ecosistema->id,
            'umbral_maestria' => 50.00,
        ]);

        $sc->criteriosEvaluacion()->attach($ce->id, ['peso_en_sc' => 100]);

        return $sc;
    }

    private function crearHuella(User $estudiante, EcosistemaLaboral $ecosistema, mixed $generadaEn): HuellaTalento
    {
        $ngsiLdId = "urn:ngsi-ld:PerfilHabilitacion:estudiante-{$estudiante->id}-ecosistema-{$ecosistema->id}";

        return HuellaTalento::create([
            'estudiante_id' => $estudiante->id,
            'ecosistema_laboral_id' => $ecosistema->id,
            'payload' => [
                'ngsi_ld_id' => $ngsiLdId,
                'calificacion' => 0.0,
                'situaciones_conquistadas' => [],
                'desglose_curricular' => [],
                'version' => '1.0',
                'generada_en' => $generadaEn->toIso8601String(),
            ],
            'ngsi_ld_id' => $ngsiLdId,
            'generada_en' => $generadaEn,
        ]);
    }

    private function urlHuella(int $ecosistemaId): string
    {
        return "/api/v1/estudiante/perfil/{$ecosistemaId}/huella";
    }

    private function urlHuellas(int $ecosistemaId): string
    {
        return "/api/v1/estudiante/perfil/{$ecosistemaId}/huellas";
    }
}

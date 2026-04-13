<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Backend EAC API',
    version: '1.0.0',
    description: 'Documentación OpenAPI de endpoints públicos, estudiante y docente.'
)]
#[OA\Server(
    url: 'http://backend-eac.test',
    description: 'Entorno local'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Token',
    description: 'Authorization: Bearer {token}'
)]
#[OA\Tag(name: 'Publico')]
#[OA\Tag(name: 'Estudiante')]
#[OA\Tag(name: 'Docente')]
class V1ApiSpec
{
    #[OA\Get(
        path: '/v1/modulos',
        tags: ['Publico'],
        summary: 'Catálogo paginado de módulos activos',
        responses: [new OA\Response(response: 200, description: 'OK')]
    )]
    public function modulosIndex(): void
    {
    }

    #[OA\Get(
        path: '/v1/modulos/{modulo}',
        tags: ['Publico'],
        summary: 'Detalle de módulo con RA, CE y ecosistema activo',
        parameters: [
            new OA\Parameter(
                name: 'modulo',
                description: 'ID del módulo',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function modulosShow(): void
    {
    }

    #[OA\Get(
        path: '/v1/ecosistemas/{ecosistema}',
        tags: ['Publico'],
        summary: 'Detalle del ecosistema con grafo de SCs',
        parameters: [
            new OA\Parameter(
                name: 'ecosistema',
                description: 'ID del ecosistema',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function ecosistemasShow(): void
    {
    }

    #[OA\Get(
        path: '/v1/ecosistemas/{ecosistema}/situaciones',
        tags: ['Publico'],
        summary: 'SCs del ecosistema con prerequisitos',
        parameters: [
            new OA\Parameter(
                name: 'ecosistema',
                description: 'ID del ecosistema',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function ecosistemasSituaciones(): void
    {
    }

    #[OA\Get(
        path: '/v1/estudiante/perfil',
        tags: ['Estudiante'],
        summary: 'Lista de perfiles del estudiante autenticado',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'Sin rol estudiante'),
        ]
    )]
    public function estudiantePerfilIndex(): void
    {
    }

    #[OA\Get(
        path: '/v1/estudiante/perfil/{ecosistema}',
        tags: ['Estudiante'],
        summary: 'Perfil del estudiante autenticado en un ecosistema',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'ecosistema',
                description: 'ID del ecosistema',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'Sin rol estudiante'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function estudiantePerfilShow(): void
    {
    }

    #[OA\Post(
        path: '/v1/estudiante/matriculas',
        tags: ['Estudiante'],
        summary: 'Matricularse en un módulo',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['modulo_id'],
                properties: [
                    new OA\Property(property: 'modulo_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Matrícula creada'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'Sin rol estudiante'),
            new OA\Response(response: 409, description: 'Matrícula duplicada'),
            new OA\Response(response: 422, description: 'Validación'),
        ]
    )]
    public function estudianteMatriculasStore(): void
    {
    }

    #[OA\Get(
        path: '/v1/docente/ecosistemas/{ecosistema}/progreso',
        tags: ['Docente'],
        summary: 'Progreso del grupo',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'ecosistema',
                description: 'ID del ecosistema',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'Sin rol docente'),
        ]
    )]
    public function docenteProgreso(): void
    {
    }

    #[OA\Post(
        path: '/v1/docente/ecosistemas/{ecosistema}/conquistas',
        tags: ['Docente'],
        summary: 'Registrar conquista de SC a estudiante',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'ecosistema',
                description: 'ID del ecosistema',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['estudiante_id', 'sc_codigo', 'gradiente_autonomia', 'puntuacion_conquista'],
                properties: [
                    new OA\Property(property: 'estudiante_id', type: 'integer', example: 2),
                    new OA\Property(property: 'sc_codigo', type: 'string', example: 'SC-01'),
                    new OA\Property(property: 'gradiente_autonomia', type: 'string', example: 'supervisado'),
                    new OA\Property(property: 'puntuacion_conquista', type: 'number', format: 'float', example: 84.5),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Conquista registrada'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'Sin rol docente'),
            new OA\Response(response: 422, description: 'Validación o regla de negocio'),
        ]
    )]
    public function docenteConquistas(): void
    {
    }
}


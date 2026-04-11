# SERVIDOR

## Migraciones y claves foráneas

Las FK de las migraciones están hechas de diferente manera, ¿algún problema?  
¿Cuál es mejor y por qué?

Forma antigua durante el curso:

```php
$table->unsignedBigInteger('resultado_aprendizaje_id')->nullable();
$table->foreign('resultado_aprendizaje_id')
      ->references('id')
      ->on('resultados_aprendizaje')
      ->onDelete('cascade');

Forma actual:

$table->foreignId('resultado_aprendizaje_id')
      ->constrained('resultados_aprendizaje')
      ->cascadeOnDelete();

$table->string('codigo', 5); // Ej: "CE1a", "CE1b"
$table->text('descripcion');

$table->unique(['resultado_aprendizaje_id', 'codigo']);
$table->timestamps();
Dudas generales

En visión general 1.1 pone “Origen y adapta, cambia…”, pero no se parece a algunas como tareas.

Migración usuarios hay que crearla copiada de ePortfolio.

Los números de roles y userRoles son ambos 12.

1.8.8
public function prerequisitos(): BelongsToMany {
    return $this->belongsToMany(SituacionCompetencia::class);
}

¿Por qué no this?

1.9.3 Crear seeders
ciclosFormativosSeeder.php
¿Control del si existe el código familia?
¿Hay que traer el id de familias profesionales asociado a ese código de familias profesionales?
ciclosFormativosSeeder.php
Comprobación de qué ciclos formativos pueden tener este módulo.
1.9.11 Seeders y Factorys

PerfilHabilitacionFactory se crea pero no se modifica nada
y luego en EcosistemaLaboralSeeder aparece hecho.

ModuloSeeder

Para recoger el id de ciclo formativo:

¿Necesito el módulo de ciclo formativo?

Proceso planteado:

Primera pasada: leer CSV módulos, crear objetos e insertar.
Segunda pasada: leer CSV ciclo-módulo, obtener el ciclo correspondiente y actualizar el id del ciclo según el código del módulo.
ResultadosAprendizajeSeeder

En el CSV hay campo tipo.

¿Cómo tratar las filas que tienen tipo si el modelo no tiene ese atributo?
¿Filtrar por los que no tienen el header tipo?

En el header:

¿id_ra es el código del RA o el id?
El id es autoincremental, pero no queda claro.
CriteriosEvaluacionSeeder
id_criterio es el código del criterio de evaluación.
¿Por qué se llama id y no cod_criterio?

Consulta:

¿El id de resultado aprendizaje se obtiene teniendo en cuenta el id de módulo y el código de RA?
Problemas con CSV
Cuando codigo supera 5 caracteres falla porque en la migración está limitado a 5.
Se ha controlado en el seeder, pero duda entre:
mantener ese control
o modificar migración y hacer php artisan migrate:refresh
Línea 189: codigo e( (paréntesis accidental)
id criterios con 2 letras, posible conflicto si el id real tiene más cifras.
Factories
php artisan make:factory EcosistemaLaboralFactory --model=EcosistemaLaboral
php artisan make:factory PerfilHabilitacionFactory --model=PerfilHabilitacion

Se crean vacíos.
¿No se deben completar?

EcosistemaLaboral

Problema:

Intenta crear un criterio de evaluación con otro formato.
Además busca un criterio que no existe.
1.10 Verificación con Tinker

Problema en:

// ¿Qué CE cubre SC-03?
$sc03 = App\Models\SituacionCompetencia::where('codigo', 'SC-03')->first();
$$sc03->criteriosEvaluacion()->pluck('codigo');
// → ['b', 'a', 'b']
// ¿Qué SCs son prerequisito de SC-03?
$sc03->prerequisitos()->pluck('codigo');
// → ['SC-01', 'SC-02']

Error debido a no saber hacer la relación muchos a muchos consigo mismo.

Código corregido:

public function prerequisitos(): BelongsToMany {
    return $this->belongsToMany(
        SituacionCompetencia::class,
        'sc_precedencia',
        'sc_id',
        'sc_requisito_id'
    );
}
2.6.2

En la ruta de la raíz no aparece la carpeta ecosistemas.

2.08 Rutas

Redirección tras login al dashboard del usuario logueado (línea 47).

Relación en modelo
public function perfilesHabilitacion(): HasMany
{
    return $this->hasMany(PerfilHabilitacion::class);
}
2.09 MiddlewareHelper

Error en mostrar dashboard docente:

Call to undefined relationship [ecosistemaLaboral] on model [App\Models\Role]

Se añade en Role:

public function ecosistemaLaboral(): BelongsTo
{
    return $this->belongsTo(EcosistemaLaboral::class, 'ecosistema_laboral_id');
}

Duda:

Si esta es la relación correcta, ¿debería entonces el modelo EcosistemaLaboral tener una relación con Role tipo hasMany?


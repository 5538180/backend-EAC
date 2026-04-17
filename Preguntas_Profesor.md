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

Precaucion:

En la creacion del controlador moduloController de estudiante, he intentado hacerlo yo, pero me ha resultado una inversion de tiempo muy larga, he copiado el del profe.

Duda:

En las relaciones del modelo Modulo, faltaba la de matriculas, si e suna relacion 1:N , en el modelo matriculas, no deberia tener tambien su relacion belongsto a Modulo?


2.10. ✅ Verificación final de la Unidad 2:

GET /modulos/1 muestra la ficha de Técnicas Básicas de Merchandising con sus RA y CE cargados desde $modulo->resultadosAprendizaje (relación directa, no a través del ecosistema).

Duda:

Me aparece el de Sistema informatico si pongo el 1, ya que el id de Tecnicas el 70.

Fallo:

Si no lo miro con la ia porque me da una key nula en peso porcentaje no hayo que el fallo era en el casteo del decimal a 2 valores.

Fallo:

ProgresoController para ver el progeso de los estudiantes fallaba por que al usar el metodo user_roles, ya sabia el rol del usuario, ademas la relacion no era a la tabla pivot, era  la directa 


Unidad 3: API REST EAC

Duda:

No hemos blindado la entrada de inyeccion sql con CSRF

Explicacion: 
 
Deber´ía implementarse para evitar que el formulario provenga de otro sitio para garantizar que ese formulario este hecho por mi mismo dominio


// - SEMANA 4 ------ //

# FALLO / DUDA :

Pasando el test PublicControllersTest, fallo en campo activa , porque en el seeder digiste que tenia que ser acvivo, por que via voy, cambio el test o cambio la tabla en la BBDD?


# Duda:

En la relacion del modulo User, esta el metodo para relacionar este; pero no tiene sentido, ya que matriculas, no estudiante_id no estan en la tabla de ecosistema laboral, ademas, usuarios, como se relaciona con ecosistemaLaboral? no hay relacion directa; 

public function ecosistemasMatriculado(): BelongsToMany
{
    return $this->belongsToMany(
        EcosistemaLaboral::class,
        'matriculas',
        'estudiante_id',
    )->withTimestamps();
}

Pregunta:

Tabla resultados de aprendizaje no tiene el atributo peso porcentaje. En el mermaind aparece el atributo, pero en el modelo no. 
Mas tarde en el curl publico 3ro lo pide ya que se hace en ModuloResource
"ecosistema_activo": null,
    "resultados_aprendizaje": [
      {
        "id": 1,
        "codigo": "RA1",
        "descripcion": "Evalúa sistemas informáticos, identificando sus componentes y características.",
        "peso_porcentaje": null
      },
      si que aparece y lo da nulo ademas del ecosistema activo tambien nulo.

      Reparacion:
      Cambiar migracion, anadir atributo, hacer un php artisan migrate refresh ; despues añadirla al modelo en el fillable etc

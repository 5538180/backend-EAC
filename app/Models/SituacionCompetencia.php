<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/* *  SituacionCompetencia
* Este es el modelo más rico del sistema. Incluye las relaciones con el grafo de precedencia (tanto hacia arriba como hacia abajo).

* Una vez que hemos creado el modelo SituacionCompetencia, modifícalo para incluir todas sus relaciones:

* Relación con EcosistemaLaboral (belongsTo)
* Relación con NodoRequisito (hasMany)
* Relación de prerequisitos (belongsToMany a sí mismo)
* Relación de dependientes (belongsToMany a sí mismo)
* Relación con CriterioEvaluacion (belongsToMany)
* Relación con PerfilesHabilitacion a través de PerfilSituacion (belongsToMany)

* Define, además, las siguientes propiedades estáticas:
  HECHO  * fillable con los campos editables (sin id ni timestamps)
  HECHO  * casts para convertir umbral_maestria a decimal y activa a booleano */

class SituacionCompetencia extends Model
{
       protected $fillable = [
        'ecosistema_laboral_id', 'codigo','titulo', 'descripcion', 'umbral_maestria','nivel_complejidad','activa'
        ];
    protected $casts = ['umbral_maestria'=>'decimal:2','activa'=>'boolean'];
    protected $table = 'situaciones_competencia';

    // - RELACIONES
    public function ecosistemaLaboral(): BelongsTo{
        return $this->belongsTo(EcosistemaLaboral::class);
    }

    public function nodosRequisito(): HasMany{
        return $this->hasMany(NodoRequisito::class);
}

 // ! CREAR LOOP A SI MISMO DE MUCHOS A MUCHOS
    public function prerequisitos() : BelongsToMany{
        return $this->belongsToMany(
            SituacionCompetencia::class,
            'sc_precedencia',
            'sc_id',
            'sc_requisito_id'
        );
    }

    // ! CREAR LOOP A SI MISMO DE MUCHOS A MUCHOS
    public function dependientes() : BelongsToMany{
        return $this->belongsToMany(
            SituacionCompetencia::class,
            'sc_precedencia',
            'sc_requisito_id',
            'sc_id'
        );
    }
 // ! CREAR LOOP A SI MISMO DE MUCHOS A MUCHOS
      public function criteriosEvaluacion(): BelongsToMany{
        return $this->belongsToMany(
            CriterioEvaluacion::class,
            'sc_criterios_evaluacion',
            'situacion_competencia_id',
            'criterio_evaluacion_id'
        )->withPivot('peso_en_sc');
}
        public function perfilesHabilitacion(): HasMany /* BelongsToMany */{
        return $this->hasMany(PerfilSituacion::class,'situacion_competencia_id');

          /*   return $this->belongsToMany(PerfilHabilitacion::class, 'perfil_situacion')
                ->withPivot('ecosistema_laboral_id')
                ->withTimestamps(); */
}

}

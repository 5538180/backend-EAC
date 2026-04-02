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
    protected $casts = ['umbral_maestria'=>'decimal','activa'=>'boolean'];
    protected $table = 'situaciones_competencia';

    // - RELACIONES
    public function ecosistemaLaboral(): BelongsTo{
        return $this->belongsTo(SituacionCompetencia::class);
    }

    public function nodoRequisito(): HasMany{
        return $this->hasMany(NodoRequisito::class);
}

    public function prerequisitos() : BelongsToMany{
        return $this->belongsToMany(SituacionCompetencia::class);
    }
    public function dependientes() : BelongsToMany{
        return $this->belongsToMany(SituacionCompetencia::class);
    }

      public function criterioEvaluacion(): BelongsToMany{
        return $this->belongsToMany(CriterioEvaluacion::class);
}
        public function perfilesHabilitacion(): BelongsToMany{
        return $this->belongsToMany(PerfilHabilitacion::class);
}

}
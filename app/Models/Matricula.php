<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Matricula extends Model
{
        protected $fillable = [
       'estudiante_id', 'modulo_id'
    ];
        public function modulo(): BelongsTo
   {
        return $this->belongsTo(Modulo::class);
    }

         public function estudiante(): BelongsTo
   {
        return $this->belongsTo(User::class);
    }


}

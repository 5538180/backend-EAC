<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Matricula extends Model
{
        public function modulo(): BelongsTo
   {
        return $this->belongsTo(Modulo::class);
    }
}

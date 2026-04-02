<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    /* * Pivot SC ↔ Criterios de Evaluación
       * Esta tabla cierra el círculo de trazabilidad curricular: registra qué criterios de evaluación del currículo oficial quedan cubiertos por cada SC. */

    /* ? ¿Por qué many-to-many? Una SC puede cubrir CE de distintos RA (por ejemplo, SC-01 cubre CE1a, CE1b y CE2c). 
    ? Y un CE puede ser evaluado por más de una SC (diferentes contextos de aplicación). Esta relación es la que permite calcular la calificación final del módulo a partir del Gradiente de Autonomía.

 */

    public function up(): void
    {
        Schema::create('sc_criterios_evaluacion', function (Blueprint $table) {
           // $table->id();
                    $table->foreignId('situacion_competencia_id')
              ->constrained('situaciones_competencia')
              ->cascadeOnDelete();
        $table->foreignId('criterio_evaluacion_id')
              ->constrained('criterios_evaluacion')
              ->cascadeOnDelete();

        // Peso del CE dentro de la evaluación de esta SC concreta
        $table->decimal('peso_en_sc', 5, 2)->default(0);

        $table->primary(['situacion_competencia_id', 'criterio_evaluacion_id']);
    });
       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sc_criterios_evaluacion');
    }
};

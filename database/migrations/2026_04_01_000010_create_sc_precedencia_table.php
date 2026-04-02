<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /* * Grafo de Precedencia (tabla de adyacencia)
* Esta es la tabla más característica del EAC. Define las dependencias entre SCs: si sc_requisito_id debe estar conquistada antes de poder acceder a sc_id. */

    /* ! Importante: Esta tabla no detecta ciclos en el grafo (SC-01 requiere SC-02 y SC-02 requiere SC-01). 
   ! La validación de acíclicidad se implementará en el servicio GrafoService en la Unidad 5. */
    public function up(): void
    {
        Schema::create('sc_precedencia', function (Blueprint $table) {
            // La SC que requiere un prerequisito
            $table->foreignId('sc_id')
                ->constrained('situaciones_competencia')
                ->cascadeOnDelete();
            // La SC que debe estar conquistada previamente
            $table->foreignId('sc_requisito_id')
                ->constrained('situaciones_competencia')
                ->cascadeOnDelete();

            $table->primary(['sc_id', 'sc_requisito_id']);
        });

        // Evitar que una SC sea requisito de sí misma (no válido en sqlite)
        // DB::statement('ALTER TABLE sc_precedencia ADD CONSTRAINT chk_sc_precedencia CHECK (sc_id != sc_requisito_id);');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sc_precedencia');
    }
};

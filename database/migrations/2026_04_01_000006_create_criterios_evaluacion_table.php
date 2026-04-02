<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('criterios_evaluacion', function (Blueprint $table) {
            $table->id();
    
        // ? FORMA ANTIGUA DURANTE EL CURSO DE COM HACER LA FK CON SU REFERENCIA
            /*  $table->unsignedBigInteger('resultado_aprendizaje_id')->nullable();
                $table->foreign('resultado_aprendizaje_id')->references('id')->on('resultados_aprendizaje')->onDelete('cascade'); */

            $table->foreignId('resultado_aprendizaje_id')
                ->constrained('resultados_aprendizaje')
                ->cascadeOnDelete();
            $table->string('codigo', 5);             // Ej: "CE1a", "CE1b"
            $table->text('descripcion');

            $table->unique(['resultado_aprendizaje_id', 'codigo']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criterios_evaluacion');
    }
};

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
        Schema::create('huellas_talento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('ecosistema_laboral_id')->constrained('ecosistemas_laborales')->cascadeOnDelete();
            $table->json('payload');
            $table->string('ngsi_ld_id')->nullable();

            $table->timestamp('generada_en');

            $table->timestamps();
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('huellas_talento');
    }
};

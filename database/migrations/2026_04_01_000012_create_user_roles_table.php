<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /* * User Roles (pivot con contexto)
* El rol de un usuario siempre tiene un contexto: un usuario puede ser docente en el ecosistema de Merchandising y estudiante en otro. */

    public function up(): void
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('role_id')
                ->constrained('roles')
                ->cascadeOnDelete();
            $table->foreignId('ecosistema_laboral_id')
                ->nullable()
                ->constrained('ecosistemas_laborales')
                ->nullOnDelete();
            $table->unique(['user_id', 'role_id', 'ecosistema_laboral_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};

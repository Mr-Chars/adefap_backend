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
        Schema::create('participant', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('apellido_paterno');
            $table->string('apellido_materno');
            $table->string('dni');
            $table->string('fecha_nacimiento');
            $table->string('ubigeo_nacimiento');
            $table->string('domicilio');
            $table->string('ubigeo_domicilio');
            $table->string('n_celular');
            $table->string('talla');
            $table->string('peso');
            $table->mediumText('participantPhoto');
            $table->string('id_creator');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant');
    }
};

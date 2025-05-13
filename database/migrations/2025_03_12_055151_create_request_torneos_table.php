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
        Schema::create('request_torneo', function (Blueprint $table) {
            $table->id();
            $table->string('id_participant');
            $table->string('id_creator');
            $table->string('id_centro_estudios')->nullable();
            $table->string('id_category');
            $table->integer('id_region');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_torneo');
    }
};

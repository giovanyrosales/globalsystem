<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePEstadoTable extends Migration
{
    /**
     * PARA PRESUPUESTO DE UNIDAD
     * 1- En Desarrollo
     * 2- Listo para Revisión
     * 3- Aprobado
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_estado', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('p_estado');
    }
}

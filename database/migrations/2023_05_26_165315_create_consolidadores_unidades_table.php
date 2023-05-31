<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsolidadoresUnidadesTable extends Migration
{
    /**
     * SE ASIGNA EL USUARIO Y SU UNIDAD ASIGNADA PARA CONSOLIDAR
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consolidadores_unidades', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_usuario')->unsigned();
            $table->bigInteger('id_departamento')->unsigned();

            $table->foreign('id_usuario')->references('id')->on('usuario');
            $table->foreign('id_departamento')->references('id')->on('p_departamento');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consolidadores_unidades');
    }
}

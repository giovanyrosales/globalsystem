<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePUsuarioDepartamentoTable extends Migration
{
    /**
     * ASIGNAR UN USUARIO A UN DEPARTAMENTO PARA QUE LLENE PRESUPUESTO UNIDAD
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_usuario_departamento', function (Blueprint $table) {
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
        Schema::dropIfExists('p_usuario_departamento');
    }
}

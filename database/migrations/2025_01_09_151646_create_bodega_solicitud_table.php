<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBodegaSolicitudTable extends Migration
{
    /**
     * SOLICITUD DE BODEGA POR PARTE DE USUARIO UNIDAD
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bodega_solicitud', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_usuario')->unsigned();
            $table->bigInteger('id_objespecifico')->unsigned();

            $table->dateTime('fecha');

            // para cambiar estado final y no salga en pendientes
            // 0- pendiente 1- finalizado
            // no nodra cambiar estado si los item asociados estan al menos uno pendiente
            $table->boolean('estado');

            $table->foreign('id_usuario')->references('id')->on('usuario');
            $table->foreign('id_objespecifico')->references('id')->on('obj_especifico');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bodega_solicitud');
    }
}

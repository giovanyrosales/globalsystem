<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBodegaSolicitudDetalleTable extends Migration
{
    /**
     * NO SE USARA YAP
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bodega_solicitud_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_bodesolicitud')->unsigned();
            $table->bigInteger('id_unidad')->unsigned();

            // SE ANCLA AL MATERIAL O PRODUCTO
            $table->bigInteger('id_referencia')->unsigned()->nullable();

            $table->string('nombre', 300);

            // se va ir sumando y restando la cantidad global
            $table->integer('cantidad');
            $table->integer('prioridad'); // 1- baja 2- media 3- alta  // dado por el solicitante

            // modificable por administrador de bodega
            // 1- pendiente
            // 2- entregado
            // 3- entregado/parcial
            // 4- denegado
            $table->integer('estado');
            $table->integer('cantidad_entregada');
            $table->string('nota', 300)->nullable();

            $table->foreign('id_bodesolicitud')->references('id')->on('bodega_solicitud');
            $table->foreign('id_unidad')->references('id')->on('p_unidadmedida');
            $table->foreign('id_referencia')->references('id')->on('bodega_materiales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bodega_solicitud_detalle');
    }
}

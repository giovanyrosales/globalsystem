<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequisicionAgrupadaDetalleTable extends Migration
{
    /**
     * MATERIALES DE REQUISICION, SI ESTAN AQUI, YA LOS PODRA VER UCP PARA COTIZAR
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requisicion_agrupada_detalle', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_requi_agrupada')->unsigned();

            // referenciando al material agrupado
            $table->bigInteger('id_requi_unidad_detalle')->unsigned();

            // 0: defecto
            // 1: cotizado

            $table->boolean('cotizado');


            $table->foreign('id_requi_agrupada')->references('id')->on('requisicion_agrupada');
            $table->foreign('id_requi_unidad_detalle')->references('id')->on('requisicion_unidad_detalle');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requisicion_agrupada_detalle');
    }
}

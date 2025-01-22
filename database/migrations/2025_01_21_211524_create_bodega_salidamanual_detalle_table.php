<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBodegaSalidamanualDetalleTable extends Migration
{
    /**
     * DETALLE - SALIDAS MANUALES DE BODEGA SIN SOLICITUD
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bodega_salidamanual_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_salidamanual')->unsigned();
            $table->bigInteger('id_entradadetalle')->unsigned();

            // cantidad de salida
            $table->integer('cantidad');

            $table->foreign('id_salidamanual')->references('id')->on('bodega_salidamanual');
            $table->foreign('id_entradadetalle')->references('id')->on('bodega_entradas_detalle');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bodega_salidamanual_detalle');
    }
}

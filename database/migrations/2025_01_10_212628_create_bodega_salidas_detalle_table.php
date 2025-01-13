<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBodegaSalidasDetalleTable extends Migration
{
    /**
     * DETALLE - BODEGA SALIDAS DE PRODUCTO A UNA SOLICITUD
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bodega_salidas_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_salida')->unsigned();
            $table->bigInteger('id_solidetalle')->unsigned(); // solicitud detalle

            // nunca superara la cantidad a salir con la solicitada
            $table->integer('cantidad_salida');

            $table->foreign('id_salida')->references('id')->on('bodega_salidas');
            $table->foreign('id_solidetalle')->references('id')->on('bodega_solicitud_detalle');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bodega_salidas_detalle');
    }
}

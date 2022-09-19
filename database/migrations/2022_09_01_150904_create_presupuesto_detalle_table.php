<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePresupuestoDetalleTable extends Migration
{
    /**
     * ESTA ES CREADA AL GENERAR ORDEN DE COMPRA
     *
     * @return void
     */
    public function up()
    {
        Schema::create('presupuesto_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('presupuesto_id')->unsigned();
            $table->bigInteger('id_requi_detalle')->unsigned();

            //0 salida
            //1 entrada
            $table->boolean('tipo');

            //0: LA ORDEN DE COMPRA ES VALIDA.
            //1: LA ORDEN DE COMPRA FUE CANCELADA

            $table->integer('estado');

            $table->foreign('presupuesto_id')->references('id')->on('presupuesto');
            $table->foreign('id_requi_detalle')->references('id')->on('requisicion_detalle');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('presupuesto_detalle');
    }
}

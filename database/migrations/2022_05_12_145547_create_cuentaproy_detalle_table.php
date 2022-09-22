<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentaproyDetalleTable extends Migration
{
    /**
     * GUARDA UN REGISTRO AL GENERAR ORDEN DE COMPRA, ASI BAJARA EL
     * SALDO RESTANTE
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuentaproy_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_cuentaproy')->unsigned();
            $table->bigInteger('id_requi_detalle')->unsigned();

            //0 salida
            //1 entrada
            $table->boolean('tipo');

            //0: LA ORDEN DE COMPRA ES VALIDA.
            //1: LA ORDEN DE COMPRA FUE CANCELADA

            //$table->integer('estado');

            $table->foreign('id_cuentaproy')->references('id')->on('cuentaproy');
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
        Schema::dropIfExists('cuentaproy_detalle');
    }
}

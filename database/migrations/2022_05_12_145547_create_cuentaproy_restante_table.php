<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentaproyRestanteTable extends Migration
{
    /**
     * GUARDA UN REGISTRO AL GENERAR ORDEN DE COMPRA, ASI BAJARA EL
     * SALDO RESTANTE. SI SE ANULA LA ORDEN DE COMPRA, SE DEBERA BORRAR EL REGISTRO
     * BUSCANDOSE CON LA ID_ORDEN
     *
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuentaproy_restante', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_cuentaproy')->unsigned();
            $table->bigInteger('id_requi_detalle')->unsigned();
            $table->bigInteger('id_orden')->unsigned();

            $table->foreign('id_cuentaproy')->references('id')->on('cuentaproy');
            $table->foreign('id_requi_detalle')->references('id')->on('requisicion_detalle');
            $table->foreign('id_orden')->references('id')->on('orden');
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

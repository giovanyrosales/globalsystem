<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenUnidadTable extends Migration
{
    /**
     * ORDENES PARA UNIDADES
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orden_unidad', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_cotizacion')->unsigned();
            $table->bigInteger('id_admin_contrato')->unsigned();

            $table->date('fecha_orden');
            $table->text('lugar')->nullable();

            // 0: defecto
            // 1: orden anulada

            $table->integer('estado');

            // fecha de orden Anulada
            $table->dateTime('fecha_anulada')->nullable();

            $table->foreign('id_cotizacion')->references('id')->on('cotizacion_unidad');
            $table->foreign('id_admin_contrato')->references('id')->on('administradores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orden_unidad');
    }
}

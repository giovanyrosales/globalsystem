<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentaunidadRetenidoTable extends Migration
{
    /**
     * CUENTA UNIDAD RETENIDO
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuentaunidad_retenido', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_requi_detalle')->unsigned();
            $table->bigInteger('id_cuenta_unidad')->unsigned();

            $table->foreign('id_requi_detalle')->references('id')->on('requisicion_unidad_detalle');
            $table->foreign('id_cuenta_unidad')->references('id')->on('cuenta_unidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cuentaunidad_retenido');
    }
}

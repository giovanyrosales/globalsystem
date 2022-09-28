<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentaproyRetenidoTable extends Migration
{
    /**
     * SALDO RETENIDO A LAS REQUISICIONES MATERIAL NO COTIZADAS
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuentaproy_retenido', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_requi_detalle')->unsigned();
            $table->bigInteger('id_cuentaproy')->unsigned();

            $table->foreign('id_requi_detalle')->references('id')->on('requisicion_detalle');
            $table->foreign('id_cuentaproy')->references('id')->on('cuentaproy');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cuentaproy_retenido');
    }
}

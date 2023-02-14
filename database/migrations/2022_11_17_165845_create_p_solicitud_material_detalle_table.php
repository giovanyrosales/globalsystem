<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePSolicitudMaterialDetalleTable extends Migration
{
    /**
     * PRESUPUESTO APRUEBA DINERO A UN CODIGO Y LE QUITA A OTRO CODIGO. ESTO MODIFICANDO
     * EL SALDO INICIAL SI LO PERMITE.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_solicitud_material_detalle', function (Blueprint $table) {
            $table->id();

            // id material que solicito
            $table->bigInteger('id_material')->unsigned();

            // id presupuesto unidad
            $table->bigInteger('id_presup_unidad')->unsigned();

            // cuando fue aprobada la solicitud
            $table->dateTime('fechahora');

            $table->foreign('id_material')->references('id')->on('p_materiales');
            $table->foreign('id_presup_unidad')->references('id')->on('p_presup_unidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('p_solicitud_material_detalle');
    }
}

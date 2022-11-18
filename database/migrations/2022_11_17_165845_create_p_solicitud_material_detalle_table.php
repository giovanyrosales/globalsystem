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

            // id cuenta unidad que subio dinero
            $table->bigInteger('id_cuentaunidad_sube')->unsigned();

            // id cuenta unidad que bajo dinero
            $table->bigInteger('id_cuentaunidad_baja')->unsigned();


            // unidades que solicito
            $table->decimal('unidades', 10, 2);

            // periodo que solicito
            $table->integer('periodo');

            // Este es el dinero que tenía tabla CUENTA UNIDAD antes de SUBIR DINERO SOLICITADO
            // COPIAS
            $table->decimal('copia_saldoini_antes_subir', 10, 2);

            // Este es el dinero que tenía tabla CUENTA UNIDAD antes de BAJAR DINERO SOLICITADO
            // COPIAS
            $table->decimal('copia_saldoini_antes_bajar', 10, 2);

            // dinero que subio. seria el precio unitario del material en ese momento
            $table->decimal('dinero_solicitado', 10, 2);

            // 0: esta cuenta unidad ya existia
            // 1: la cuenta unidad fue creada
            $table->boolean('cuenta_creada');


            $table->foreign('id_material')->references('id')->on('p_materiales');
            $table->foreign('id_presup_unidad')->references('id')->on('p_presup_unidad');
            $table->foreign('id_cuentaunidad_sube')->references('id')->on('cuenta_unidad');
            $table->foreign('id_cuentaunidad_baja')->references('id')->on('cuenta_unidad');
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

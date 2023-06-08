<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCotizacionUnidadTable extends Migration
{
    /**
     * COTIZACION POR UCP, DE TODOS EL AGRUPADO
     *
     * @return void
     */

    public function up()
    {
        Schema::create('cotizacion_unidad', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_proveedor')->unsigned();
            $table->bigInteger('id_agrupado')->unsigned();

            // fecha se creo la cotizacion
            $table->date('fecha');

            // fecha cuando se modifica su estado
            $table->dateTime('fecha_estado')->nullable();

            // 0: defecto
            // 1: aprobada por jefe uaci
            // 2: denegado
            $table->integer('estado');

            $table->foreign('id_proveedor')->references('id')->on('proveedores');
            $table->foreign('id_agrupado')->references('id')->on('requisicion_agrupada');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cotizacion_unidad');
    }
}

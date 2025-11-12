<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBodegaSalidasTable extends Migration
{
    /**
     * BODEGA -SALIDAS DE PRODUCTO A UNA SOLICITUD
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bodega_salidas', function (Blueprint $table) {
            $table->id();

            $table->date('fecha');
            $table->bigInteger('id_usuario')->unsigned(); // quien hizo la salida

            // se manda el id de solicitud unidad
            // PERMITO NULL PORQUE PUEDO HACER UNA SALIDA MANUAL
            $table->bigInteger('id_solicitud')->unsigned()->nullable();

            // OBSERVACION DE SALIDA
            $table->string('observacion', 300)->nullable();


            // 0- SALIDA NORMAL
            // 1- Salida con Solicitud
            // 2- Salida por Desperfecto
            $table->integer('estado_salida');

            // PARA UNA SALIDA MANUAL LE ASIGNAMOS LA UNIDAD SI FUERA NECESARIO
            // UNION DE TABLA p_departamento
            $table->bigInteger('id_unidad_manual')->nullable();

            // CAMPO NUEVO 22/8/2025
            $table->string('numero_solicitud', 300)->nullable();

            $table->foreign('id_usuario')->references('id')->on('usuario');
            $table->foreign('id_solicitud')->references('id')->on('bodega_solicitud');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bodega_salidas');
    }
}

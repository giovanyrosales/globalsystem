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
            $table->bigInteger('id_solicitud')->unsigned();

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

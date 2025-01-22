<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBodegaSalidamanualTable extends Migration
{
    /**
     * SALIDAS MANUALES DE BODEGA SIN SOLICITUD
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bodega_salidamanual', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->bigInteger('id_usuario')->unsigned();
            $table->string('observacion', 300)->nullable();

            $table->foreign('id_usuario')->references('id')->on('usuario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bodega_salidamanual');
    }
}

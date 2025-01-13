<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBodegaEntradasTable extends Migration
{
    /**
     * LISTADO DE ENTRADAS
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bodega_entradas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_usuario')->unsigned();
            $table->date('fecha');
            $table->string('observacion', 300)->nullable();
            $table->string('lote', 50)->nullable();

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
        Schema::dropIfExists('bodega_entradas');
    }
}

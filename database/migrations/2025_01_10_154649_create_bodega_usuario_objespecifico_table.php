<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBodegaUsuarioObjespecificoTable extends Migration
{
    /**
     * PARA QUE EL USUARIO ADMINISTRADOR BODEGA, VEA SU CODIGO UNICAMENTE
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bodega_usuario_objespecifico', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_usuario')->unsigned();
            $table->bigInteger('id_objespecifico')->unsigned();

            $table->foreign('id_usuario')->references('id')->on('usuario');
            $table->foreign('id_objespecifico')->references('id')->on('obj_especifico');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bodega_usuario_objespecifico');
    }
}

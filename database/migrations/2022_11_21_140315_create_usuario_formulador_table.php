<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuarioFormuladorTable extends Migration
{
    /**
     * LOS USUARIOS QUE ESTE AQUÍ REGISTRADOS, PODRÁN EDITAR EL PROYECTO
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuario_formulador', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_usuario')->unsigned();

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
        Schema::dropIfExists('usuario_formulador');
    }
}

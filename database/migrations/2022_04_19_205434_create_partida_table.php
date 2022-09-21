<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartidaTable extends Migration
{
    /**
     * partidas creadas por usuarios ingenieros
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partida', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('proyecto_id')->unsigned()->nullable();
            $table->bigInteger('id_tipopartida')->unsigned()->nullable();

            $table->string('nombre', 600);
            $table->string('cantidadp', 50)->nullable(); // cantidad partida

            $table->foreign('proyecto_id')->references('id')->on('proyectos');
            $table->foreign('id_tipopartida')->references('id')->on('tipo_partida');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partida');
    }
}

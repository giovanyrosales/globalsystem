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

            $table->integer('tipo_partida');
            $table->string('nombre', 600);
            $table->string('cantidadp', 50)->nullable(); // cantidad partida

            $table->foreign('proyecto_id')->references('id')->on('proyectos');
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

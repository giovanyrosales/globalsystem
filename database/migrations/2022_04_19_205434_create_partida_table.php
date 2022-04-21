<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartidaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partida', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('proyecto_id')->unsigned();

            $table->integer('tipo_partida');
            $table->string('nombre', 300);
            $table->decimal('cantidadp', 10,2); // cantidad partida

            $table->integer('estado');
            // 0 defecto
            // 1 aprobada

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

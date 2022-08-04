<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequisicionTable extends Migration
{
    /**
     *  peticion que hace el encargado del proyecto y en base a eso se hace la requisicion
     * estados
     *

     * @return void
     */
    public function up()
    {
        Schema::create('requisicion', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_proyecto')->unsigned();

            $table->string('destino', 300)->nullable();
            $table->date('fecha')->nullable();
            $table->text('necesidad')->nullable();


            //0: defecto
            //1: inicio su cotizacion de uno o todos los materiales
            $table->integer('estado');

            $table->foreign('id_proyecto')->references('id')->on('proyectos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requisicion');
    }
}

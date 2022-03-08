<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequisicionTable extends Migration
{
    /**
     *  peticion que hace el encargado del proyecto y en base a eso se hace la requisicion
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requisicion', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_proyecto')->unsigned();
            $table->bigInteger('id_estado')->nullable()->unsigned();

            $table->string('destino', 300)->nullable();
            $table->date('fecha')->nullable();
            $table->string('necesidad', 300)->nullable();

            $table->foreign('id_proyecto')->references('id')->on('proyectos');
            $table->foreign('id_estado')->references('id')->on('estado_proyecto');
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

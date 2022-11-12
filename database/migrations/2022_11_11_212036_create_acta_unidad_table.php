<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActaUnidadTable extends Migration
{
    /**
     * ACTA PARA UNIDADES
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acta_unidad', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_ordenunidad')->unsigned();

            $table->date('fecha_acta');
            $table->string('hora');

            // 0: defecto
            // 1: acta generada

            $table->integer('estado');

            $table->foreign('id_ordenunidad')->references('id')->on('orden_unidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acta_unidad');
    }
}

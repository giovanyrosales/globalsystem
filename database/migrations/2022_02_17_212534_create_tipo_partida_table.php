<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoPartidaTable extends Migration
{
    /**
     * diferentes tipos de partida
     *
        ***** LAS POSICIONES DEBEN QUEDAR FIJA YA QUE ALGUNOS ESTAN COLOCADOS MANUALMENTE EN CODIGO -> ID *****
     *
     * 1- materiales
     * 2- herramientas (2% de materiales)
     * 3- mano de obra (por administracion)
     * 4- aporte mano de obra
     * 5- alquiler de maquinaria
     * 6- trasporte de concreto fresco
     * 7- -------- PENDIENTE --------------
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_partida', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_partida');
    }
}

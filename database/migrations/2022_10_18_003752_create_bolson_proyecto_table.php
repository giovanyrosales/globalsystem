<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBolsonProyectoTable extends Migration
{
    /**
     * CUANDO SE APRUEBA UN PRESUPUESTO, AQUÍ SE AGREGA EL PROYECTO A UN BOLSÓN
     * Y SE BUSCA SU MONTO EN COLUMNA LLAMADA monto
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bolson_proyecto', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_bolson')->unsigned();
            $table->bigInteger('id_proyecto')->unsigned();

            $table->foreign('id_bolson')->references('id')->on('bolson');
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
        Schema::dropIfExists('bolson_proyecto');
    }
}

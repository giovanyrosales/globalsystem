<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartidaAdicionalTable extends Migration
{
    /**
     * PARTIDA ADICIONAL PARA UN PROYECTO QUE NO DEBE PASAR UN X PORCENTAJE DEL
     * MONTO (columna monto en tabla proyecto)
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partida_adicional', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_partidaadic_conte')->unsigned()->nullable();
            $table->bigInteger('id_proyecto')->unsigned()->nullable();
            $table->bigInteger('id_tipopartida')->unsigned()->nullable();

            $table->string('nombre', 600);
            $table->string('cantidadp', 50)->nullable(); // cantidad partida

            $table->foreign('id_partidaadic_conte')->references('id')->on('partida_adicional_contenedor');
            $table->foreign('id_proyecto')->references('id')->on('proyectos');
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
        Schema::dropIfExists('partida_adicional');
    }
}

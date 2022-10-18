<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartidaAdicionalDetalleTable extends Migration
{
    /**
     * CUANDO SE HACE UN REQUERIMIENTO SE HARA EN OTRA PANTALLA DONDE SERA
     * REQUERIMIENTO POR PARTIDA ADICIONAL
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partida_adicional_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_partida_adicional')->unsigned();
            $table->bigInteger('id_material')->unsigned();
            $table->decimal('cantidad', 10, 2);

            // multiplicar
            $table->integer('duplicado');

            // mismo proceso hasta orden de compra, que puede ser cotizado o no
            $table->integer('estado');

            $table->foreign('id_partida_adicional')->references('id')->on('partida_adicional');
            $table->foreign('id_material')->references('id')->on('materiales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partida_adicional_detalle');
    }
}

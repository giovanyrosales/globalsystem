<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartidaDetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partida_detalle', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('partida_id')->unsigned();
            $table->bigInteger('material_id')->unsigned();
            $table->decimal('cantidad', 10, 2);

            // multiplicar
            $table->integer('duplicado');

            // sin uso
            $table->integer('estado');

            $table->foreign('partida_id')->references('id')->on('partida');
            $table->foreign('material_id')->references('id')->on('materiales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partida_detalle');
    }
}

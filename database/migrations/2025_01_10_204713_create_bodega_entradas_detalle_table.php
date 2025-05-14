<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBodegaEntradasDetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bodega_entradas_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_entrada')->unsigned();
            $table->bigInteger('id_material')->unsigned();
            $table->integer('cantidad');
            $table->decimal('precio', 10, 4);


            // codigo de producto
            $table->string('codigo_producto', 100)->nullable();

            // solo es un nombre copia de respaldo
            $table->string('nombre_copia', 300);

            $table->integer('cantidad_entregada');

            $table->foreign('id_entrada')->references('id')->on('bodega_entradas');
            $table->foreign('id_material')->references('id')->on('bodega_materiales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bodega_entradas_detalle');
    }
}

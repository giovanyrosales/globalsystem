<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBodegaMaterialesTable extends Migration
{
    /**
     * LISTADO DE MATERIALES PARA BODEGA
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bodega_materiales', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_unidadmedida')->unsigned();
            $table->bigInteger('id_objespecifico')->unsigned();
            $table->string('nombre', 300);

            // EL USUARIO TIENE ASIGNADO UN ID BODEGA
            // 1: BODEGA INFORMATICA
            // 2: BODEGA PROVEEDURIA
            $table->integer('tipo_bodega');

            $table->foreign('id_unidadmedida')->references('id')->on('p_unidadmedida');
            $table->foreign('id_objespecifico')->references('id')->on('obj_especifico');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bodega_materiales');
    }
}

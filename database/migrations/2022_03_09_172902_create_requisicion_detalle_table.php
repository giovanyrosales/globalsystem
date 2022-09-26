<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequisicionDetalleTable extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::create('requisicion_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('requisicion_id')->unsigned();
            $table->bigInteger('material_id')->unsigned();

            $table->decimal('cantidad', 12, 2);
            $table->decimal('dinero', 10, 2);

            // CUANDO UN MATERIAL YA FUE COTIZADO, Y FUE CANCELADA, Y YA NO SE VOLVERA A COTIZAR.
            // poner a estado 1: material cancelado

            $table->boolean('cancelado');

            //0: defecto
            //1: material cotizado

            $table->boolean('estado');

            $table->foreign('requisicion_id')->references('id')->on('requisicion');
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
        Schema::dropIfExists('requisicion_detalle');
    }
}

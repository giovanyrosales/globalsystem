<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequisicionUnidadDetalleTable extends Migration
{
    /**
     *  Petición que hace el encargado de la unidad detalles
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requisicion_unidad_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_requisicion_unidad')->unsigned();
            $table->bigInteger('id_material')->unsigned();

            $table->decimal('cantidad', 10, 2);

            // Esto sera modificado cuando UACI haga la cotizacion, es decir lo que
            // se va a descontar al final de generar orden de compra
            $table->decimal('dinero', 10, 2);

            // para los historicos de la cotizacion que se hizo, esto no debe cambiar

            $table->decimal('dinero_fijo', 10, 2);

            // CUANDO UN MATERIAL YA FUE COTIZADO, Y FUE CANCELADA, Y YA NO SE VOLVERA A COTIZAR.
            // poner a estado 1: material cancelado

            $table->boolean('cancelado');

            // una descripción mas descriptiva del material
            $table->string('material_descripcion', 300);

            //0: defecto
            //1: material cotizado
            // ayuda para volver a cotizarlo si fue denegado la cotización
            $table->boolean('estado');


            // Descripción. Ingresada al crear la cotización
            $table->string('descripcion', 300);


            $table->foreign('id_requisicion_unidad')->references('id')->on('requisicion_unidad');
            $table->foreign('id_material')->references('id')->on('p_materiales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requisicion_unidad_detalle');
    }
}

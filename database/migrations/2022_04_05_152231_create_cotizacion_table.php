<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCotizacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cotizacion', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('proveedor_id')->unsigned();
            $table->bigInteger('requisicion_id')->unsigned();

            // fecha se creo la cotizacion
            $table->date('fecha');

            // fecha cuando se modifica su estado
            $table->dateTime('fecha_estado')->nullable();

            // 0: defecto
            // 1: aprobada por jefe uaci
            // 2: denegado
            $table->integer('estado');

            $table->foreign('proveedor_id')->references('id')->on('proveedores');
            $table->foreign('requisicion_id')->references('id')->on('requisicion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cotizacion');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCotizacionDetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cotizacion_detalle', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('cotizacion_id')->unsigned();
            $table->bigInteger('id_requidetalle')->unsigned();
            $table->bigInteger('material_id')->unsigned();

            // copia de seguridad para nombre, en dado caso cambia algo en }
            // catalogo de materiales
            $table->string('nombre', 300);
            $table->string('medida', 100);

            $table->decimal('cantidad', 10, 2);
            $table->decimal('precio_u', 10, 2);

            // 0: SI USO POR EL MOMENTO
            $table->integer('estado');

            $table->foreign('cotizacion_id')->references('id')->on('cotizacion');
            $table->foreign('id_requidetalle')->references('id')->on('requisicion_detalle');
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
        Schema::dropIfExists('cotizacion_detalle');
    }
}

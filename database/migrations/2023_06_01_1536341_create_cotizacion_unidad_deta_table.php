<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCotizacionUnidadDetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cotizacion_unidad_deta', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_cotizacion_unidad')->unsigned();
            $table->bigInteger('id_requi_unidaddetalle')->unsigned();

            $table->decimal('cantidad', 10, 2);
            $table->decimal('precio_u', 10, 2);

            // LA COTIZACION HECHA POR UACI LLEVA SU PROPIA DESCRIPCION DE LA COTIZACION
            $table->string('descripcion', 300);

            $table->foreign('id_cotizacion_unidad')->references('id')->on('cotizacion_unidad');
            $table->foreign('id_requi_unidaddetalle')->references('id')->on('requisicion_unidad_detalle');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cotizacion_unidad_deta');
    }
}

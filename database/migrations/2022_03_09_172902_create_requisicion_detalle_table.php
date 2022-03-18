<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequisicionDetalleTable extends Migration
{
    /**
     * los estados son
     *  *  cotizado o no
     * @return void
     */
    public function up()
    {
        Schema::create('requisicion_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('requisicion_id')->unsigned();
            $table->bigInteger('unidadmedida_id')->unsigned();

            $table->decimal('cantidad', 12, 2);
            $table->string('descripcion', 400)();

            $table->foreign('requisicion_id')->references('id')->on('requisicion');
            $table->foreign('unidadmedida_id')->references('id')->on('unidad_medida');
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

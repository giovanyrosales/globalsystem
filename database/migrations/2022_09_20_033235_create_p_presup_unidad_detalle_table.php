<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePPresupUnidadDetalleTable extends Migration
{
    /**
     * PARA PRESUPUESTO DE UNIDAD
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_presup_unidad_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_presup_unidad')->unsigned();
            $table->bigInteger('id_material')->unsigned();
            $table->decimal('cantidad', 10, 2);
            $table->decimal('precio', 10, 2);
            $table->integer('periodo');

            $table->foreign('id_presup_unidad')->references('id')->on('p_presup_unidad');
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
        Schema::dropIfExists('p_presup_unidad_detalle');
    }
}

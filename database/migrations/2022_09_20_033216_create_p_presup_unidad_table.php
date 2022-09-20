<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePPresupUnidadTable extends Migration
{
    /**
     * PARA PRESUPUESTO DE UNIDAD
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_presup_unidad', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_anio')->unsigned();
            $table->bigInteger('id_departamento')->unsigned();
            $table->bigInteger('id_estado')->unsigned();

            $table->foreign('id_anio')->references('id')->on('p_anio_presupuesto');
            $table->foreign('id_departamento')->references('id')->on('p_departamento');
            $table->foreign('id_estado')->references('id')->on('p_estado');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('p_presup_unidad');
    }
}

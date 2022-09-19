<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePMaterialesTable extends Migration
{
    /**
     * PARA PRESUPUESTO DE UNIDAD
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_materiales', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_objespecifico')->nullable()->unsigned();
            $table->bigInteger('id_unidadmedida')->nullable()->unsigned();

            $table->string('descripcion', 800);
            $table->decimal('costo', 10, 2);

            $table->foreign('id_objespecifico')->references('id')->on('obj_especifico');
            $table->foreign('id_unidadmedida')->references('id')->on('unidad_medida');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('p_materiales');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePMaterialesDetalleTable extends Migration
{
    /**
     * SOLICITUD DE NUEVOS MATERIALES
     *
     * @return void
     */
    public function up(){
        Schema::create('p_materiales_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_presup_unidad')->unsigned();
            $table->bigInteger('id_unidadmedida')->unsigned();

            $table->decimal('costo', 10, 2);
            $table->decimal('cantidad', 10, 2);
            $table->integer('periodo');
            $table->string('descripcion', 300);

            $table->foreign('id_presup_unidad')->references('id')->on('p_presup_unidad');
            $table->foreign('id_unidadmedida')->references('id')->on('p_unidadmedida');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('p_materiales_detalle');
    }
}

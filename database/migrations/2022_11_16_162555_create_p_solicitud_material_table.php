<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePSolicitudMaterialTable extends Migration
{
    /**
     * CUANDO JEFE UNIDAD QUIERE PEDIR UN MATERIAL PERO NO LO DEJO
     * EN SU PRESUPUESTO. SOLICITA EL MATERIAL PARA QUE JEFE PRESUPUESTO
     * LO AGREGUE
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_solicitud_material', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_presup_unidad')->unsigned();
            $table->bigInteger('id_material')->unsigned();

            $table->decimal('cantidad', 10, 2);
            $table->integer('periodo');

            // cuando se hizo la solicitud
            $table->dateTime('fechahora');

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
        Schema::dropIfExists('p_solicitud_material');
    }
}

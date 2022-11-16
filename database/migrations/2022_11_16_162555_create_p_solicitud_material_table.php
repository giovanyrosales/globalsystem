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
            $table->bigInteger('id_cuentaunidad')->unsigned();

            $table->decimal('cantidad', 10, 2);
            $table->integer('periodo');

            // CUENTA UNIDAD QUE SE QUITARA EL DINERO, el id material su obj especídifico
            // NO DEBERA SER IGUAL al obj específico de la unidad
            $table->decimal('id_cuentaunidad_bajara', 10, 2);

            $table->foreign('id_presup_unidad')->references('id')->on('p_presup_unidad');
            $table->foreign('id_material')->references('id')->on('p_materiales');
            $table->foreign('id_cuentaunidad')->references('id')->on('cuenta_unidad');
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

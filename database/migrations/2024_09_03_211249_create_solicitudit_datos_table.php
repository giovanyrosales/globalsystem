<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolicituditDatosTable extends Migration
{
    /**
     * SOLICITUDES IT DE COSAS
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitudit_datos', function (Blueprint $table) {
            $table->id();

            $table->date('fecha');
            $table->bigInteger('id_anio')->unsigned(); //
            $table->bigInteger('id_departamento')->unsigned();

            $table->foreign('id_anio')->references('id')->on('p_anio_presupuesto');
            $table->foreign('id_departamento')->references('id')->on('p_departamento');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('solicitudit_datos');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePProyectosPendientesTable extends Migration
{
    /**
     * CUANDO UNA UNIDAD REGISTRAR UN PROYECTO PENDIENTE
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_proyectos_pendientes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_presup_unidad')->unsigned();

            $table->string('descripcion', 300);
            $table->decimal('costo', 10, 2);

            $table->foreign('id_presup_unidad')->references('id')->on('p_presup_unidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('p_proyectos_pendientes');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePAnioPresupuestoTable extends Migration
{
    /**
     * AÑO PARA CADA PRESUPUESTO DE UNIDAD
     *
     * @return void
     */
    public function up(){
        Schema::create('p_anio_presupuesto', function (Blueprint $table) {
            $table->id();
            $table->integer('nombre');

            // permiso global para activar los requerimientos, mientras los pueda realizar.
            // y desactivar los requerimientos para determinado año

            // 0: no permiso
            // 1: si permiso

            $table->boolean('permiso');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('p_anio_presupuesto');
    }
}

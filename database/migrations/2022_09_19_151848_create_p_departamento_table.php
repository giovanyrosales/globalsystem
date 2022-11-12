<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePDepartamentoTable extends Migration
{
    /**
     * PARA PRESUPUESTO DE UNIDAD
     * Nombre de unidades.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_departamento', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 300);

            // permiso para que unidad haga un movimiento de cuenta
            //0: no
            //1: si
            $table->boolean('permiso_movi_unidad');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('p_departamento');
    }
}

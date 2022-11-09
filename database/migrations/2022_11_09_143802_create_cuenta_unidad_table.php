<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentaUnidadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuenta_unidad', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_anio')->unsigned();
            $table->bigInteger('id_departamento')->unsigned();
            $table->bigInteger('id_objespeci')->unsigned(); // objeto específico

            $table->decimal('saldo_inicial', 10,2); // no cambia nunca

            $table->foreign('id_anio')->references('id')->on('p_anio_presupuesto');
            $table->foreign('id_departamento')->references('id')->on('p_departamento');
            $table->foreign('id_objespeci')->references('id')->on('obj_especifico');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cuenta_unidad');
    }
}

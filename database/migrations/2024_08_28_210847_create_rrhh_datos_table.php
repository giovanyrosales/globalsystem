<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRrhhDatosTable extends Migration
{
    /**
     * DATOS PARA HOJA DE ACTUALIZACION DE DATOS DE PERSONAL
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_datos', function (Blueprint $table) {
            $table->id();

            $table->dateTime('fecha');

            $table->bigInteger('id_empleado')->unsigned()->nullable();
            $table->integer('check_empleado');
            $table->string('nombre', 100)->nullable();

            $table->bigInteger('id_cargo')->unsigned();
            $table->bigInteger('id_unidad')->unsigned();

            $table->string('dui', 50);
            $table->string('nit', 50)->nullable();

            $table->date('fecha_nacimiento');
            $table->string('lugar_nacimiento', 200);
            $table->integer('select_academico');
            $table->string('profesion', 100);
            $table->string('direccion_actual', 200);
            $table->string('celular', 50);
            $table->string('emergencia_llamar', 100);
            $table->string('celular_emergencia', 50);

            $table->bigInteger('id_enfermedad')->unsigned()->nullable();
            $table->integer('enfermedad_check');
            $table->string('enfermedad_nuevo', 100)->nullable();



            $table->foreign('id_empleado')->references('id')->on('rrhh_empleados');
            $table->foreign('id_cargo')->references('id')->on('rrhh_cargo');
            $table->foreign('id_unidad')->references('id')->on('rrhh_unidad');
            $table->foreign('id_enfermedad')->references('id')->on('rrhh_enfermedades');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rrhh_datos');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRrhhDatosTablaTable extends Migration
{
    /**
     * LISTA DE BENEFICIARIOS
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrhh_datos_tabla', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_datos')->unsigned();
            $table->string('nombre', 100);
            $table->string('parentesco', 100);
            $table->integer('porcentaje');

            $table->foreign('id_datos')->references('id')->on('rrhh_datos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rrhh_datos_tabla');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBodegaExtrasTable extends Migration
{
    /**
     * SOLO UNA FILA - PARA CAMPOS EN REPORTES
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bodega_extras', function (Blueprint $table) {
            $table->id();

            $table->string('nombre_gerente', 100)->nullable();
            $table->string('nombre_gerente_cargo', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bodega_extras');
    }
}

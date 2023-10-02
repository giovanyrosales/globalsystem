<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInformacionConsolidadorTable extends Migration
{
    /**
     * DATOS EXTRA PARA UN USUARIO CONSOLIDADOR
     *
     * @return void
     */
    public function up()
    {
        Schema::create('informacion_consolidador', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_usuario')->unsigned();
            $table->bigInteger('id_departamento')->unsigned();

            $table->foreign('id_usuario')->references('id')->on('usuario');
            $table->foreign('id_departamento')->references('id')->on('p_departamento');

            $table->string('cargo', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('informacion_consolidador');
    }
}

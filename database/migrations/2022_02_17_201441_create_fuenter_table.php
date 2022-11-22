<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFuenterTable extends Migration
{
    /**
     * Fuente de Recursos.
     *
     * @return void
     */
    public function up(){
        Schema::create('fuenter', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_fuentef')->unsigned();
            $table->bigInteger('id_p_anio')->unsigned()->nullable();

            $table->string('codigo', 100);
            $table->string('nombre', 300)->nullable();

            $table->foreign('id_fuentef')->references('id')->on('fuentef');
            $table->foreign('id_p_anio')->references('id')->on('p_anio_presupuesto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fuenter');
    }
}

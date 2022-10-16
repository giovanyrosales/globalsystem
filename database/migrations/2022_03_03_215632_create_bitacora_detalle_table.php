<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBitacoraDetalleTable extends Migration
{
    /**
     * Detalle de bitacora para el proyecto
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bitacora_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_bitacora')->nullable()->unsigned();
            $table->string('nombre', 300)->nullable();
            $table->string('documento', 100)->nullable();

            $table->foreign('id_bitacora')->references('id')->on('bitacora');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bitacora_detalle');
    }
}

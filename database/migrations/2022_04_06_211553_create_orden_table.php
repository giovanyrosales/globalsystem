<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orden', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('admin_contrato_id')->unsigned();
            $table->bigInteger('cotizacion_id')->unsigned();

            $table->date('fecha_orden');
            $table->text('lugar')->nullable();

            // 0: defecto
            // 1: orden anulada

            $table->integer('estado');

            // fecha de orden Anulada
            $table->dateTime('fecha_anulada')->nullable();

            $table->foreign('admin_contrato_id')->references('id')->on('administradores');
            $table->foreign('cotizacion_id')->references('id')->on('cotizacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orden');
    }
}

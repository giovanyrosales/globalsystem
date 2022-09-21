<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaldoInsuficienteIngTable extends Migration
{
    /**
     * MATERIALES QUE NO PUDIERON SER COTIZADO POR UACI - ING, ya que su
     * precio AUMENTO y debe meterle más dinero al código
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saldo_insuficiente_ing', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_requi_detalle')->unsigned();

            $table->foreign('id_requi_detalle')->references('id')->on('requisicion_detalle');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('saldo_insuficiente_ing');
    }
}

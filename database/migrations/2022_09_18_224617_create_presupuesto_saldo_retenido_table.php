<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePresupuestoSaldoRetenidoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('presupuesto_saldo_retenido', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_requi_detalle')->unsigned();
            $table->bigInteger('id_presupuesto')->unsigned();

            $table->foreign('id_requi_detalle')->references('id')->on('requisicion_detalle');
            $table->foreign('id_presupuesto')->references('id')->on('presupuesto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('presupuesto_saldo_retenido');
    }
}

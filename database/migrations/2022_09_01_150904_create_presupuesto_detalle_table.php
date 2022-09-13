<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePresupuestoDetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('presupuesto_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('presupuesto_id')->unsigned();
            //0 salida
            //1 entrada
            $table->boolean('tipo');
            $table->decimal('dinero', 10,2);

            $table->foreign('presupuesto_id')->references('id')->on('presupuesto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('presupuesto_detalle');
    }
}

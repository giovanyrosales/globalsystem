<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentaproyTable extends Migration
{
    /**
     * GUARDA LOS CODIGOS CON EL SALDO INICIAL, Y SALDO RESTANTE ES CALCULADO
     *
     * @return void
     */
    public function up(){

        Schema::create('cuentaproy', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('proyecto_id')->unsigned();
            $table->bigInteger('objespeci_id')->unsigned(); // objeto especifico
            $table->decimal('saldo_inicial', 10,2); // no cambia nunca, mismo

            // si se deniega, se borra fila
            // 0: pendiente
            // 1: autorizado
            $table->boolean('autorizado');

            $table->foreign('proyecto_id')->references('id')->on('proyectos');
            $table->foreign('objespeci_id')->references('id')->on('obj_especifico');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cuentaproy');
    }
}

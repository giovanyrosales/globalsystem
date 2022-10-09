<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovicuentaproyTable extends Migration
{
    /**
     * MOVIMIENTO DE CODIGOS, PARA TRASPASO DE DINERO
     *
     * @return void
     */
    public function up(){

        Schema::create('movicuentaproy', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_cuentaproy_sube')->unsigned();
            $table->bigInteger('id_cuentaproy_baja')->unsigned();

            $table->decimal('dinero', 10, 2);
            $table->dateTime('fecha');

            // si deniega el jefe presupuesto, se borrara la fila

            // 0: pendiente
            // 1: autorizado
            $table->boolean('autorizado');

            $table->string('reforma', 100)->nullable(); // documento pdf
            $table->foreign('id_cuentaproy_sube')->references('id')->on('cuentaproy');
            $table->foreign('id_cuentaproy_baja')->references('id')->on('cuentaproy');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movicuentaproy');
    }
}

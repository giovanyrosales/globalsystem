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
            $table->bigInteger('cuentaproy_id')->unsigned();
            $table->bigInteger('id_cuentaproy')->unsigned();

            $table->decimal('aumento', 10, 2);
            $table->decimal('disminuye', 10, 2);
            $table->dateTime('fecha');

            $table->string('reforma', 100)->nullable(); // documento pdf
            $table->foreign('id_cuentaproy')->references('id')->on('cuentaproy');
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

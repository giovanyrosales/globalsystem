<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartidaAdicionalMontoTable extends Migration
{
    /**
     * SE GUARDA LOS MONTOS DE LAS PARTIDAS ADIONALES QUE FUERON APROBADAS
     * SI HABÍA FONDOS EN BOLSÓN ASIGNADO A PROYECTO
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partida_adicional_monto', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_bolson')->unsigned();
            $table->bigInteger('id_part_adic_fecha')->unsigned();

            // de tantas partidas x no debera superar el x % del presupuesto del proyecto
            $table->decimal('monto', 10, 2);

            $table->foreign('id_bolson')->references('id')->on('bolson');
            $table->foreign('id_part_adic_fecha')->references('id')->on('partida_adicional_fecha');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partida_adicional_monto');
    }
}

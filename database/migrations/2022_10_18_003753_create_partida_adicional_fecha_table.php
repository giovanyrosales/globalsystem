<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartidaAdicionalFechaTable extends Migration
{
    /**
     * CONTENEDOR PRINCIPAL PARA ALMACENAR LAS PARTIDAS ADICIONALES,
     * ASI NOTIFICAR CUANDO SE ESTA LISTO PARA QUE PRESUPUESTO LAS REVISE
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partida_adicional_fecha', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_proyecto')->unsigned();

            $table->date('fecha');
            $table->string('documento', 100)->nullable();

            // 0: en desarrollo
            // 1: en revisión
            // 2: partida adicional aprobada y descontada de bolsón

            $table->integer('estado');

            // solo tendrá un valor cuando es aprobada la partida adicional
            $table->decimal('monto', 10, 2);

            $table->foreign('id_proyecto')->references('id')->on('proyectos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partida_adicional_fecha');
    }
}

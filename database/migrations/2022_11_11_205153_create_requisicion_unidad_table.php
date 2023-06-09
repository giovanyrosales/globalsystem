<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequisicionUnidadTable extends Migration
{
    /**
     *  Petición que hace el encargado de la unidad
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requisicion_unidad', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_presup_unidad')->unsigned();

            $table->string('destino', 300);
            $table->date('fecha');
            $table->text('necesidad')->nullable();

            // nombre de usuario quien solicito
            $table->string('solicitante', 100);

            $table->foreign('id_presup_unidad')->references('id')->on('p_presup_unidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requisicion_unidad');
    }
}

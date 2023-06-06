<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequisicionAgrupadaTable extends Migration
{
    /**
     * AGUPADAS POR UN USUARIO CONSOLIDADOR
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requisicion_agrupada', function (Blueprint $table) {
            $table->id();

            $table->dateTime('fecha');
            $table->string('descripcion', 800)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requisicion_agrupada');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSindicoTiposolicitudTable extends Migration
{
    /**
     * TIPO SOLICITUD PARA SINDICATURA
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sindico_tiposolicitud', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 200);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sindico_tiposolicitud');
    }
}

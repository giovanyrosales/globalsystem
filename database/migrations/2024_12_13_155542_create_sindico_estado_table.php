<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSindicoEstadoTable extends Migration
{
    /**
     * ESTADOS SINDICATURA
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sindico_estado', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sindico_estado');
    }
}

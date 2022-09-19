<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acta', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('orden_id')->unsigned();

            $table->date('fecha_acta');
            $table->string('hora');

            // 0: defecto
            // 1: acta generada

            $table->integer('estado');

            $table->foreign('orden_id')->references('id')->on('orden');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acta');
    }
}

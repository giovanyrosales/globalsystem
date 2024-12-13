<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBodegaSalidasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bodega_salidas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_solicitud')->unsigned();
            $table->string('descripcion', 300)->nullable();
            $table->date('fecha')->nullable();
            $table->timestamps();

            $table->foreign('id_solicitud')->references('id')->on('bodega_salidas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bodega_salidas');
    }
}

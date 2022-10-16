<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBolsonDetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bolson_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_bolson')->unsigned();
            $table->bigInteger('id_objespecifico')->unsigned();

            $table->foreign('id_bolson')->references('id')->on('bolson');
            $table->foreign('id_objespecifico')->references('id')->on('obj_especifico');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bolson_detalle');
    }
}

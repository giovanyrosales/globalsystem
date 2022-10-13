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

            $table->foreign('id_bolson')->references('id')->on('bolson');

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

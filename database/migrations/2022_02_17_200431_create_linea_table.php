<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLineaTable extends Migration
{
    /**
     * Linea de trabajo
     *
     * @return void
     */
    public function up()
    {
        Schema::create('linea', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_areagestion')->unsigned();

            $table->string('codigo', 100);
            $table->string('nombre', 300)->nullable();

            $table->foreign('id_areagestion')->references('id')->on('areagestion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('linea');
    }
}

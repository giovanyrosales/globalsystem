<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAreagestionTable extends Migration
{
    /**
     * Area de gestión
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areagestion', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_linea')->unsigned();

            $table->string('codigo', 100)->nullable();
            $table->string('nombre', 300)->nullable();

            $table->foreign('id_linea')->references('id')->on('linea');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('areagestion');
    }
}

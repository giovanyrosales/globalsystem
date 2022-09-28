<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObjEspecificoTable extends Migration
{
    /**
     * objeto especifico
     *
     * @return void
     */
    public function up()
    {
        Schema::create('obj_especifico', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_cuenta')->unsigned();
            $table->string('nombre', 800);
            $table->integer('codigo');

            $table->foreign('id_cuenta')->references('id')->on('cuenta');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('obj_especifico');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolicituditDatostablaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitudit_datostabla', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_solicitudit_datos')->unsigned(); //

            $table->string('nombre', 1000);
            $table->integer('cantidad');

            $table->foreign('id_solicitudit_datos')->references('id')->on('solicitudit_datos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('solicitudit_datostabla');
    }
}

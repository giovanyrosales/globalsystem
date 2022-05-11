<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovibolsonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movibolson', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bolson_id')->unsigned();
            $table->bigInteger('tipomovi_id')->unsigned();
            $table->bigInteger('proyecto_id')->unsigned();

            $table->decimal('aumenta', 10,2);
            $table->decimal('disminuye', 10,2);

            $table->date('fecha');

            $table->foreign('bolson_id')->references('id')->on('bolson');
            $table->foreign('tipomovi_id')->references('id')->on('tipomovi');
            $table->foreign('proyecto_id')->references('id')->on('proyectos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movibolson');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovicuentaproyTable extends Migration
{
    /**
     * Movimiento cuenta proyecto
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movicuentaproy', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cuentaproy_id')->unsigned();
            $table->bigInteger('proyecto_id')->unsigned();
            $table->decimal('aumenta', 10, 2)->nullable();
            $table->decimal('disminuye', 10, 2)->nullable();
            $table->date('fecha')->nullable();
            $table->string('reforma', 100)->nullable(); // documento pdf

            $table->foreign('cuentaproy_id')->references('id')->on('cuentaproy');
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
        Schema::dropIfExists('movicuentaproy');
    }
}

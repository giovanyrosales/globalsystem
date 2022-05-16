<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentaproyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuentaproy', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('proyecto_id')->unsigned();
            $table->bigInteger('cuenta_id')->unsigned();
            $table->decimal('montoini', 10, 2);
            $table->decimal('saldo', 10, 2)->nullable();
            $table->integer('estado');

            $table->foreign('proyecto_id')->references('id')->on('proyectos');
            $table->foreign('cuenta_id')->references('id')->on('cuenta');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cuentaproy');
    }
}

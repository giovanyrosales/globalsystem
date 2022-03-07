<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBolsonTable extends Migration
{
    /**
     * Bolson
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bolson', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_cuenta')->unsigned();

            $table->string('nombre', 300);
            $table->string('numero', 300);
            $table->date('fecha');
            $table->decimal('montoini', 12, 2);
            $table->decimal('saldo', 12, 2);
            $table->string('estado');

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
        Schema::dropIfExists('bolson');
    }
}

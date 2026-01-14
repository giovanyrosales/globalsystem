<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBodegaGuardadopdfDetaTable extends Migration
{
    /**
     * GUARDADO DE PDF PARA PROVEEDURIA Y BODEGA
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bodega_guardadopdf_deta', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_guardadopdf')->unsigned();
            $table->string('nombre', 300)->nullable();
            $table->string('unidad', 300)->nullable();
            $table->string('cantidad', 100)->nullable();
            $table->string('precio_unitario', 100)->nullable();
            $table->string('total', 100)->nullable();

            $table->foreign('id_guardadopdf')->references('id')->on('bodega_guardadopdf');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bodega_guardadopdf_deta');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBodegaMaterialesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bodega_materiales', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_unidadmedida')->unsigned();
            $table->bigInteger('id_objespecifico')->unsigned();
            $table->timestamps();

            $table->string('nombre', 300);
            $table->decimal('precio', 10, 2)->nullable();
            $table->decimal('cantidad', 10, 2)->nullable();

            $table->foreign('id_unidadmedida')->references('id')->on('unidad_medida');
            $table->foreign('id_objespecifico')->references('id')->on('obj_especifico');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bodega_materiales');
    }
}

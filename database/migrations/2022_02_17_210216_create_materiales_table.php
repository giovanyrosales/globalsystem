<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialesTable extends Migration
{
    /**
     * materiales
     *
     * @return void
     */
    public function up()
    {
        Schema::create('materiales', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_objespecifico')->unsigned();
            $table->bigInteger('id_unidadmedida')->unsigned();
            $table->bigInteger('id_clasificacion')->unsigned(); // código específico

            $table->string('nombre', 300);
            $table->decimal('pu', 12, 2); // precio unitario

            $table->foreign('id_objespecifico')->references('id')->on('obj_especifico');
            $table->foreign('id_unidadmedida')->references('id')->on('unidad_medida');
            $table->foreign('id_clasificacion')->references('id')->on('clasificaciones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('materiales');
    }
}

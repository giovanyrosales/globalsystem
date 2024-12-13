<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBodegaDetalleSalidaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bodega_detalle_salida', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_salida')->unsigned();
            $table->bigInteger('id_material')->unsigned();
            $table->decimal('cantidad', 10, 2);
            $table->timestamps();
            
            $table->foreign('id_salida')->references('id')->on('bodega_salidas');
            $table->foreign('id_material')->references('id')->on('bodega_materiales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bodega_detalle_salida');
    }
}

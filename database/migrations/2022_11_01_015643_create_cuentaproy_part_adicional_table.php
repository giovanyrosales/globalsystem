<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentaproyPartAdicionalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuentaproy_part_adicional', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_proyecto')->unsigned();
            $table->bigInteger('objespeci_id')->unsigned(); // objeto específico

            $table->decimal('monto', 10,2); // no cambia nunca


            $table->foreign('id_proyecto')->references('id')->on('proyectos');
            $table->foreign('objespeci_id')->references('id')->on('obj_especifico');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cuentaproy_part_adicional');
    }
}

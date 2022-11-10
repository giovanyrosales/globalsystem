<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePProyectosAprobadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_proyectos_aprobados', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_presup_unidad')->unsigned();
            $table->bigInteger('id_objespeci')->unsigned();
            $table->bigInteger('id_fuenter')->unsigned();
            $table->bigInteger('id_lineatrabajo')->unsigned();
            $table->bigInteger('id_areagestion')->unsigned();

            $table->string('descripcion', 300);
            $table->decimal('costo', 10, 2);

            $table->foreign('id_presup_unidad')->references('id')->on('p_presup_unidad');
            $table->foreign('id_objespeci')->references('id')->on('obj_especifico');
            $table->foreign('id_fuenter')->references('id')->on('fuenter');
            $table->foreign('id_lineatrabajo')->references('id')->on('linea');
            $table->foreign('id_areagestion')->references('id')->on('areagestion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('p_proyectos_aprobados');
    }
}

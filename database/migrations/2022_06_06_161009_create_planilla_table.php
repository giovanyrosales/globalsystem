<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanillaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planilla', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('proyecto_id')->unsigned();
            $table->date('fecha_de');
            $table->date('fecha_hasta');
            $table->decimal('salario_total', 10, 2);
            $table->decimal('horas_extra', 10, 2);

            // isss
            $table->decimal('isss_laboral', 10, 2);
            $table->decimal('isss_patronal', 10, 2);

            // afp confia
            $table->decimal('afpconfia_laboral', 10, 2);
            $table->decimal('afpconfia_patronal', 10, 2);

            // afp crecer
            $table->decimal('afpcrecer_laboral', 10, 2);
            $table->decimal('afpcrecer_patronal', 10, 2);

            // insaforp
            $table->decimal('insaforp', 10, 2);

            // total devengado es simado salario total + horas extras

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
        Schema::dropIfExists('planilla');
    }
}

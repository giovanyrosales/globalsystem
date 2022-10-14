<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBolsonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bolson', function (Blueprint $table) {
            $table->id();

            // año de presupuesto unidades
            $table->bigInteger('id_anio')->unsigned();

            // nombre de la cuenta bolsón
            $table->string('nombre', 200);
            $table->string('numero', 100)->nullable(); // num cuenta

            // fecha creación
            $table->date('fecha');

            // será la suma de objetos específicos, del año de presupuesto de unidad
            $table->decimal('monto_inicial', 10, 2);

            $table->foreign('id_anio')->references('id')->on('cuenta');
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

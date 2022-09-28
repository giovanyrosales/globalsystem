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

            $table->string('nombre'); // nombre de la cuenta bancaria
            $table->string('numero'); // num cuenta
            $table->date('fecha'); // creacion

            // sera la suma de objetos específicos
            $table->decimal('montoini', 8, 2);
            //$table->decimal('saldo', 10, 2); // saldo restante
            $table->string('estado'); // 1: activa por defecto, 0: inactiva

            // con esta cuenta, se va a buscar el dinero del presupuesto General
            // que se mete en presupuesto,

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

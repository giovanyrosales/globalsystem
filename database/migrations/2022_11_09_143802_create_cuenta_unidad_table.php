<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentaUnidadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuenta_unidad', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_presup_unidad')->unsigned();
            $table->bigInteger('id_objespeci')->unsigned(); // objeto específico

            // ESTE ES EL SALDO DE LA CUENTA, PUEDE SER MODIFICADO
            // EJEMPLO EN MOVIMIENTOS DE CUENTA SE BAJARA O SUBIRA DIRECTAMENTE EL
            // SALDO, en la tabla movicuenta_unidad cuando se apruebe por jefe de presupuesto
            // se bajara directamente aqui el saldo, ya no se hara calculado.

            $table->decimal('saldo_inicial', 10,2); // PUEDE CAMBIAR

            // NO CAMBIA NUNCA, ES FIJO
            $table->decimal('saldo_inicial_fijo', 10,2);

            $table->foreign('id_presup_unidad')->references('id')->on('p_presup_unidad');
            $table->foreign('id_objespeci')->references('id')->on('obj_especifico');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cuenta_unidad');
    }
}

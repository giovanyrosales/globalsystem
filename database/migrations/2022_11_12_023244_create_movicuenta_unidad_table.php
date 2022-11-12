<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovicuentaUnidadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movicuenta_unidad', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_cuentaunidad_sube')->unsigned();
            $table->bigInteger('id_cuentaunidad_baja')->unsigned();

            $table->decimal('dinero', 10, 2);
            $table->date('fecha');

            // si deniega el jefe presupuesto, se borrara la fila

            // 0: pendiente
            // 1: autorizado
            $table->boolean('autorizado');

            $table->string('reforma', 100)->nullable(); // documento pdf
            $table->foreign('id_cuentaunidad_sube')->references('id')->on('cuenta_unidad');
            $table->foreign('id_cuentaunidad_baja')->references('id')->on('cuenta_unidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movicuenta_unidad');
    }
}

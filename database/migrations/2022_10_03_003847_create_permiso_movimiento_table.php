<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermisoMovimientoTable extends Migration
{
    /**
     * UTILIZADO PARA QUE PRESUPUESTO AUTORICE UN MOVIMIENTO DE CUENTA
     * se solicita cuenta proy a aumentar y afectar
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permiso_movimiento', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_cuentaproy')->unsigned();

            // dinero que se afectara
            $table->decimal('dinero', 10, 2);

            $table->foreign('id_cuentaproy')->references('id')->on('cuentaproy');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permiso_movimiento');
    }
}

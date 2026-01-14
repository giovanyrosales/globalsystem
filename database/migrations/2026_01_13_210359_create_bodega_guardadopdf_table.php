<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBodegaGuardadopdfTable extends Migration
{
    /**
     * GUARDADO DE PDF PARA PROVEEDURIA Y BODEGA
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bodega_guardadopdf', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_usuario')->unsigned();
            $table->bigInteger('id_pdepartamento')->unsigned();
            $table->string('descripcion', 300)->nullable();
            $table->string('numero_solicitud', 100)->nullable();

            $table->date('fecha_desde')->nullable();
            $table->date('fecha_hasta')->nullable();
            $table->date('fecha_generada')->nullable();

            $table->string('monto_total', 100)->nullable();

            $table->foreign('id_usuario')->references('id')->on('usuario');
            $table->foreign('id_pdepartamento')->references('id')->on('p_departamento');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bodega_guardadopdf');
    }
}

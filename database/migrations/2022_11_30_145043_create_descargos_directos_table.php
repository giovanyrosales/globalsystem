<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDescargosDirectosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('descargos_directos', function (Blueprint $table) {
            $table->id();

            // fecha al momento de guardar
            $table->dateTime('fecha');

            //1- proveedor
            //2- proyecto
            //3- contribución

            $table->integer('tipodescargo');


            //*********** CUENTA PROYECTO

            // SI ES TIPO PROYECTO NOMAS
            $table->bigInteger('cuentaproy_id')->unsigned()->nullable();

            // SALDO INICIAL TENIA ANTES DE BAJAR CUENTA PROY
            $table->decimal('saldo_cuentaproy_tenia', 10, 2)->nullable();

            // número de orden, puede ser para proyectos o unidades
            $table->bigInteger('numero_orden')->nullable();

            //*********** CUENTA UNIDAD

            $table->bigInteger('cuentaunidad_id')->unsigned()->nullable();

            // SALDO INICIAL TENIA ANTES DE BAJAR CUENTA UNIDAD
            $table->decimal('saldo_cuentaunidad_tenia', 10, 2)->nullable();


            //********************

            // SI ES TIPO PROVEEDORES
            $table->bigInteger('proveedores_id')->unsigned()->nullable();

            $table->string('numero_acuerdo', 300);


            // linea de trabajo
            $table->bigInteger('lineatrabajo_id')->unsigned();

            // fuente financiamiento
            $table->bigInteger('fuentef_id')->unsigned();

            $table->text('concepto')->nullable();

            //MONTO QUE SE DESCUENTA
            $table->decimal('montodescontar', 10, 2);


            $table->string('beneficiario', 300)->nullable();


            $table->foreign('cuentaproy_id')->references('id')->on('cuentaproy');

            $table->foreign('lineatrabajo_id')->references('id')->on('linea');
            $table->foreign('fuentef_id')->references('id')->on('fuentef');

            $table->foreign('proveedores_id')->references('id')->on('proveedores');

            $table->foreign('cuentaunidad_id')->references('id')->on('cuenta_unidad');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('descargos_directos');
    }
}

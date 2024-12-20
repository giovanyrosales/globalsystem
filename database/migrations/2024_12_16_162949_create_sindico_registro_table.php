<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSindicoRegistroTable extends Migration
{
    /**
     * TABLA DE REGISTROS PARA SINDICATURA
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sindico_registro', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_tiposolicitud')->unsigned();
            $table->bigInteger('id_estado')->unsigned()->nullable();
            $table->bigInteger('id_tipodeligencia')->unsigned()->nullable();
            $table->bigInteger('id_adesco')->unsigned()->nullable();

            $table->date('fecha_general')->nullable();

            // BLOQUE 1
            $table->date('fecha_reunion')->nullable();
            $table->string('asesoria', 500)->nullable();
            $table->date('fecha_informe')->nullable();

            // BLOQUE 2
            $table->string('ubicacion', 500)->nullable();
            $table->string('zonas_pendientes', 500)->nullable();

            // BLOQUE 3
            $table->string('matricula', 500)->nullable();
            $table->date('fecha_legalizacion')->nullable();
            $table->string('muebles_pendientes', 500)->nullable();

            // BLOQUE 4
            $table->string('realizado_por', 500)->nullable();
            $table->decimal('monto', 10,2)->nullable();

            // BLOQUE 5
            $table->date('fecha_recepcion')->nullable();
            $table->string('nombre_solicitante', 100)->nullable();
            $table->string('dui_solicitante', 50)->nullable();
            $table->date('fecha_revision')->nullable();
            $table->string('observacion', 500)->nullable();
            $table->date('fecha_emision_diligencia')->nullable();
            $table->date('fecha_entrega')->nullable();
            $table->string('recibe', 100)->nullable();
            $table->string('nombre', 100)->nullable();
            $table->string('dui', 50)->nullable();

            // BLOQUE 6
            $table->date('fecha_finalizacion')->nullable();

            // BLOQUE 7
            $table->date('fecha_inspeccion')->nullable();
            $table->string('nombre_tecnico', 100)->nullable();
            $table->string('resultado', 500)->nullable();

            // BLOQUE 8
            $table->string('informe_meses', 500)->nullable();

            // BLOQUE 9
            $table->string('numero_empresas', 50)->nullable();
            $table->string('numero_inmuebles', 50)->nullable();

            // BLOQUE 10
            $table->string('total_doc', 50)->nullable();
            $table->string('total_doc_aprobados', 50)->nullable();

            $table->string('inmueble', 500)->nullable();


            $table->foreign('id_tiposolicitud')->references('id')->on('sindico_tiposolicitud');
            $table->foreign('id_estado')->references('id')->on('sindico_estado');
            $table->foreign('id_tipodeligencia')->references('id')->on('sindico_tipodeligencia');
            $table->foreign('id_adesco')->references('id')->on('adescos');




        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sindico_registro');
    }
}

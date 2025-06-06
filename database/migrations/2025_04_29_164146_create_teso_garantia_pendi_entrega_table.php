<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTesoGarantiaPendiEntregaTable extends Migration
{
    /**
     * TESORERIA - GARANTIAS PENDIENTES DE ENTREGA
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teso_garantia_pendi_entrega', function (Blueprint $table) {
            $table->id();

            // FECHA SOLO DE REGISTRO PARA LLEVAR CONTEO AUTOMATICO
            $table->date('fecha_registro');

            $table->string('control_interno',50)->nullable();
            $table->string('referencia',100)->nullable();
            $table->string('descripcion_licitacion',300)->nullable();
            $table->bigInteger('id_proveedor')->unsigned();
            $table->bigInteger('id_garantia')->unsigned();
            $table->bigInteger('id_tipo_garantia')->unsigned();

            // ESTADO DE GARANTIA, VIGENTE, VENCIDAS, ENTREGADA A UCP
            $table->bigInteger('id_estado')->unsigned();

            $table->decimal('monto_garantia',10,2)->nullable();
            $table->string('aseguradora',300)->nullable();

            $table->date('vigencia_desde')->nullable();
            $table->date('vigencia_hasta')->nullable();
            $table->date('fecha_recibida')->nullable();
            $table->date('fecha_entrega')->nullable();
            $table->date('fecha_entrega_ucp')->nullable();

            $table->foreign('id_proveedor')->references('id')->on('teso_proveedor');
            $table->foreign('id_garantia')->references('id')->on('teso_garantia');
            $table->foreign('id_tipo_garantia')->references('id')->on('teso_tipo_garantia');
            $table->foreign('id_estado')->references('id')->on('teso_estado');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teso_garantia_pendi_entrega');
    }
}

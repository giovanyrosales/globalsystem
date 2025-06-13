<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTesoGarantiasEstadosTable extends Migration
{
    /**
     * UNA GARANTIA PUEDE TENER VARIOS ESTADOS
     * UCP
     * PROVEEDOR
     *
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teso_garantias_estados', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_garantia_pendi')->unsigned();
            $table->bigInteger('id_estado')->unsigned();

            $table->foreign('id_garantia_pendi')->references('id')->on('teso_garantia_pendi_entrega');
            $table->foreign('id_estado')->references('id')->on('teso_estados');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teso_garantias_estados');
    }
}

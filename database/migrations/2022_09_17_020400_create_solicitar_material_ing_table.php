<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolicitarMaterialIngTable extends Migration
{
    /**
     * Materiales solicitados por ingenieria para presupuesto
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitar_material_ing', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 300);
            $table->string('medida', 100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('solicitar_material_ing');
    }
}

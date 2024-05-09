<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSecretariaDespachoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('secretaria_despacho', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 300);
            $table->date('fecha');

            $table->string('telefono', 100)->nullable();
            $table->string('direccion', 500)->nullable();

            $table->text('descripcion')->nullable();
            $table->integer('tiposolicitud')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('secretaria_despacho');
    }
}

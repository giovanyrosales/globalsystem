<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->boolean('activo');
            $table->string('usuario', 50);
            $table->string('password', 255);

            // PARA MODULOS DE BODEGA

            // utlizado en reportes. Ejemplo CARGO: Encargada de la unidad de proveeduría y bodega
            $table->string('cargo', 200)->nullable();
            // utilizado en cabecera para establecer el cargo del usuario bodeguero
            $table->string('cargo2', 200)->nullable();

            // EL USUARIO TIENE ASIGNADO UN ID BODEGA
            // 1: BODEGA INFORMATICA
            // 2: BODEGA PROVEEDURIA
            $table->integer('tipo_bodega')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuario');
    }
}

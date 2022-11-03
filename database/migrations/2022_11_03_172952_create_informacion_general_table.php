<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInformacionGeneralTable extends Migration
{
    /**
     * DATOS PARA PROYECTO, DONDE SE OBTIENE UNA COPIA DE IMPREVISTO
     * Y HERRAMIENTAS X PORCIENTO
     *
     * @return void
     */
    public function up()
    {
        Schema::create('informacion_general', function (Blueprint $table) {
            $table->id();
            $table->decimal('imprevisto_modificable', 10, 2);
            $table->decimal('porcentaje_herramienta', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('informacion_general');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTesoAseguradoraTable extends Migration
{
    /**
     * ASEGURADORAS
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teso_aseguradora', function (Blueprint $table) {
            $table->id();
            $table->string('nombre',300);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teso_aseguradora');
    }
}

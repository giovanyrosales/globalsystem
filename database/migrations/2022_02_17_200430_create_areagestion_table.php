<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAreagestionTable extends Migration
{
    /**
     * Area de gestión
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areagestion', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 100)->nullable();
            $table->string('nombre', 300)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('areagestion');
    }
}

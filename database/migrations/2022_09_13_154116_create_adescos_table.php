<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdescosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adescos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('presidente', 250)->nullable();
            $table->string('dui',25)->nullable();
            $table->string('tel', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adescos');
    }
}

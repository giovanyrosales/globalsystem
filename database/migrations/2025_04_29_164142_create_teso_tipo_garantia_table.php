<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTesoTipoGarantiaTable extends Migration
{
    /**
     * TESORERIA - TIPO GARANTIA
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teso_tipo_garantia', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 300);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teso_tipo_garantia');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProyectosTable extends Migration
{
    /**
     * proyectos
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_linea')->nullable()->unsigned();
            $table->bigInteger('id_fuentef')->nullable()->unsigned();
            $table->bigInteger('id_fuenter')->nullable()->unsigned();
            $table->bigInteger('id_areagestion')->nullable()->unsigned();
            $table->bigInteger('id_naturaleza')->nullable()->unsigned();
            $table->bigInteger('id_estado')->nullable()->unsigned();
            $table->bigInteger('id_bolson')->nullable()->unsigned();

            $table->string('codigo',100)->unique();
            $table->string('nombre',300);
            $table->string('ubicacion',300);
            $table->string('contraparte',300)->nullable();
            $table->date('fechaini')->nullable();
            $table->date('fechafin')->nullable();
            $table->date('fecha');
            $table->string('ejecutor',300)->nullable();
            $table->string('formulador',300)->nullable();
            $table->string('supervisor',300)->nullable();
            $table->string('encargado',300)->nullable();
            $table->string('codcontable', 150)->nullable();
            $table->string('acuerdoapertura', 100)->nullable(); // file
            $table->string('acuerdocierre', 100)->nullable(); // file
            $table->decimal('monto', 12, 2)->nullable();

            // el imprevisto para aumentar dinero a proyecto
            $table->integer('imprevisto');

            // para aprobar las partidas presupuesto
            // 0: default
            // 1: listo para revisión
            // 2: aprobado
            $table->integer('presu_aprobado');
            $table->dateTime('fecha_aprobado')->nullable();

            // utilizado para que jefe presupuesto de permiso de 1 movimiento de cuenta
            // 0: no permiso
            // 1: permiso
            $table->boolean('permiso');

            $table->foreign('id_linea')->references('id')->on('linea');
            $table->foreign('id_fuentef')->references('id')->on('fuentef');
            $table->foreign('id_fuenter')->references('id')->on('fuenter');
            $table->foreign('id_areagestion')->references('id')->on('areagestion');
            $table->foreign('id_naturaleza')->references('id')->on('naturaleza');
            $table->foreign('id_estado')->references('id')->on('estado_proyecto');
            $table->foreign('id_bolson')->references('id')->on('bolson');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proyectos');
    }
}

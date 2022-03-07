<?php

namespace Database\Seeders;

use App\Models\EstadoProyecto;
use Illuminate\Database\Seeder;

class EstadoProyectoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EstadoProyecto::create([
            'nombre' => 'Priorizado'
        ]);

        EstadoProyecto::create([
            'nombre' => 'Iniciado'
        ]);

        EstadoProyecto::create([
            'nombre' => 'En Pausa'
        ]);

        EstadoProyecto::create([
            'nombre' => 'Finalizado'
        ]);

    }
}

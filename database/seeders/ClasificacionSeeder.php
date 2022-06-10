<?php

namespace Database\Seeders;

use App\Models\Clasificaciones;
use Illuminate\Database\Seeder;

class ClasificacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Clasificaciones::create([
            'nombre' => 'MATERIALES DE FERRETERIA'
        ]);

        Clasificaciones::create([
            'nombre' => 'MATERIALES ELECTRICOS'
        ]);

        Clasificaciones::create([
            'nombre' => 'MATERIALES PETREOS'
        ]);

        Clasificaciones::create([
            'nombre' => 'MATERIALES Y ACC DE PVC'
        ]);

        Clasificaciones::create([
            'nombre' => 'LAMINA'
        ]);

        Clasificaciones::create([
            'nombre' => 'POSTES DE CONCRETO'
        ]);

        Clasificaciones::create([
            'nombre' => 'VALVULAS PARA AGUA POTABLE'
        ]);

        Clasificaciones::create([
            'nombre' => 'SERVICIOS (DEPENDIENDO CUAL SEA LA SOLICITUD)'
        ]);

        Clasificaciones::create([
            'nombre' => 'TUBERIA Y ACC. GALVANIZADOS'
        ]);
    }
}

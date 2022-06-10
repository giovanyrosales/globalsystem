<?php

namespace Database\Seeders;

use App\Models\FuenteFinanciamiento;
use Illuminate\Database\Seeder;

class FFinanciamientoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FuenteFinanciamiento::create([
            'codigo' => '1',
            'nombre' => 'Fondo General'
        ]);

        FuenteFinanciamiento::create([
            'codigo' => '2',
            'nombre' => 'Recursos Propios'
        ]);

        FuenteFinanciamiento::create([
            'codigo' => '3',
            'nombre' => 'Prestamos Externos'
        ]);

        FuenteFinanciamiento::create([
            'codigo' => '4',
            'nombre' => 'Prestamos Internos'
        ]);

        FuenteFinanciamiento::create([
            'codigo' => '5',
            'nombre' => 'Donaciones'
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\FuenteRecursos;
use Illuminate\Database\Seeder;

class FuenteRSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FuenteRecursos::create([
            'id_fuentef' => '5',
            'codigo' => '000',
            'nombre' => 'DONACIONES'
        ]);

        FuenteRecursos::create([
            'id_fuentef' => '1',
            'codigo' => '110',
            'nombre' => 'FODES 25%'
        ]);

        FuenteRecursos::create([
            'id_fuentef' => '1',
            'codigo' => '111',
            'nombre' => 'FODES 75%'
        ]);

        FuenteRecursos::create([
            'id_fuentef' => '1',
            'codigo' => '112',
            'nombre' => 'FISDL'
        ]);

        FuenteRecursos::create([
            'id_fuentef' => '1',
            'codigo' => '118',
            'nombre' => 'FONAES'
        ]);

        FuenteRecursos::create([
            'id_fuentef' => '1',
            'codigo' => '000',
            'nombre' => 'FONDO GENERAL'
        ]);

        FuenteRecursos::create([
            'id_fuentef' => '1',
            'codigo' => '120',
            'nombre' => 'FODES - LIBRE DISPONIBILIDAD'
        ]);

        FuenteRecursos::create([
            'id_fuentef' => '2',
            'codigo' => '000',
            'nombre' => 'FONDOS PROPIOS'
        ]);

        FuenteRecursos::create([
            'id_fuentef' => '3',
            'codigo' => '000',
            'nombre' => 'PRESTAMOS EXTERNOS'
        ]);

        FuenteRecursos::create([
            'id_fuentef' => '4',
            'codigo' => '000',
            'nombre' => 'PRESTAMOS INTERNOS'
        ]);

    }
}

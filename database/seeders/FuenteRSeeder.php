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
            'id_p_anio' => null,
            'codigo' => '000',
            'nombre' => 'DONACIONES',
            'activo' => 1
        ]);

        FuenteRecursos::create([
            'id_fuentef' => '1',
            'id_p_anio' => null,
            'codigo' => '110',
            'nombre' => 'FODES 25%',
            'activo' => 1
        ]);

        FuenteRecursos::create([
            'id_fuentef' => '1',
            'id_p_anio' => null,
            'codigo' => '111',
            'nombre' => 'FODES 75%',
            'activo' => 1
        ]);

        FuenteRecursos::create([
            'id_fuentef' => '1',
            'id_p_anio' => null,
            'codigo' => '112',
            'nombre' => 'FISDL',
            'activo' => 1
        ]);

        FuenteRecursos::create([
            'id_fuentef' => '1',
            'id_p_anio' => null,
            'codigo' => '118',
            'nombre' => 'FONAES',
            'activo' => 1
        ]);

        FuenteRecursos::create([
            'id_fuentef' => '1',
            'id_p_anio' => null,
            'codigo' => '000',
            'nombre' => 'FONDO GENERAL',
            'activo' => 1
        ]);

        FuenteRecursos::create([
            'id_fuentef' => '1',
            'id_p_anio' => null,
            'codigo' => '120',
            'nombre' => 'FODES - LIBRE DISPONIBILIDAD',
            'activo' => 1
        ]);

        FuenteRecursos::create([
            'id_fuentef' => '2',
            'id_p_anio' => null,
            'codigo' => '000',
            'nombre' => 'FONDOS PROPIOS',
            'activo' => 1
        ]);

        FuenteRecursos::create([
            'id_fuentef' => '3',
            'id_p_anio' => null,
            'codigo' => '000',
            'nombre' => 'PRESTAMOS EXTERNOS',
            'activo' => 1
        ]);

        FuenteRecursos::create([
            'id_fuentef' => '4',
            'id_p_anio' => null,
            'codigo' => '000',
            'nombre' => 'PRESTAMOS INTERNOS',
             'activo' => 1
        ]);

    }
}

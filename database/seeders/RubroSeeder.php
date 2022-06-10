<?php

namespace Database\Seeders;

use App\Models\Rubro;
use Illuminate\Database\Seeder;

class RubroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Rubro::create([
            'codigo' => '51',
            'nombre' => 'REMUNERACIONES',
        ]);

        Rubro::create([
            'codigo' => '54',
            'nombre' => 'ADQUISICIONES DE BIENES Y SERVICIOS',
        ]);

        Rubro::create([
            'codigo' => '55',
            'nombre' => 'GASTOS FINANCIEROS Y OTROS',
        ]);

        Rubro::create([
            'codigo' => '56',
            'nombre' => 'TRANSFERENCIAS CORRIENTES',
        ]);

        Rubro::create([
            'codigo' => '61',
            'nombre' => 'INVERSIONES EN ACTIVOS FIJOS',
        ]);

        Rubro::create([
            'codigo' => '72',
            'nombre' => 'SALDOS DE AÑOS ANTERIORES',
        ]);
    }
}

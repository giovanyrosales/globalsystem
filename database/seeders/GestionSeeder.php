<?php

namespace Database\Seeders;

use App\Models\AreaGestion;
use Illuminate\Database\Seeder;

class GestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AreaGestion::create([
            'id_linea' => '1',
            'codigo' => '01',
            'nombre' => 'Dirección y Administración Municipal'
        ]);

        AreaGestion::create([
            'id_linea' => '8',
            'codigo' => '01',
            'nombre' => 'Inversión para el Desarrollo Económico y Social'
        ]);

        AreaGestion::create([
            'id_linea' => '10',
            'codigo' => '02',
            'nombre' => 'Inversión para el Desarrollo Económico y Social'
        ]);
    }
}

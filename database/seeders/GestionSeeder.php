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
            'codigo' => '1',
            'nombre' => 'Conducción Administrativa'
        ]);

        AreaGestion::create([
            'codigo' => '3',
            'nombre' => 'Desarrollo Social'
        ]);

        AreaGestion::create([
            'codigo' => '4',
            'nombre' => 'Apoyo al Desarrollo Económico'
        ]);

        AreaGestion::create([
            'codigo' => '5',
            'nombre' => 'Deuda Pública'
        ]);

    }
}

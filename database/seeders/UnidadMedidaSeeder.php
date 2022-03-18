<?php

namespace Database\Seeders;

use App\Models\UnidadMedida;
use Illuminate\Database\Seeder;

class UnidadMedidaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UnidadMedida::create([
            'nombre' => 'Libra'
        ]);

        UnidadMedida::create([
            'nombre' => 'CM'
        ]);

        UnidadMedida::create([
            'nombre' => 'Bolsa'
        ]);

        UnidadMedida::create([
            'nombre' => 'Plg'
        ]);
    }
}

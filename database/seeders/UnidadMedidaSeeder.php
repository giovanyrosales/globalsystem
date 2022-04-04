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
            'medida' => 'Libra'
        ]);

        UnidadMedida::create([
            'medida' => 'CM'
        ]);

        UnidadMedida::create([
            'medida' => 'Bolsa'
        ]);

        UnidadMedida::create([
            'medida' => 'Plg'
        ]);
    }
}

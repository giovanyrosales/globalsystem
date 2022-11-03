<?php

namespace Database\Seeders;

use App\Models\TipoPartida;
use Illuminate\Database\Seeder;

class TipoPartidaSeeder extends Seeder
{
    /**
     * TIPOS DE PARTIDA, ALGUNOS ESTAN REFERENCIADOS DIRECTAMENTE AL ID, ASI QUE NO MOVER POSICIÓN
     *
     * @return void
     */
    public function run()
    {
        TipoPartida::create([
            'nombre' => 'Materiales',
        ]);

        TipoPartida::create([
            'nombre' => 'Mano de obra (Por Administración)',
        ]);

        TipoPartida::create([
            'nombre' => 'Alquiler de Maquinaria',
        ]);

        TipoPartida::create([
            'nombre' => 'Transporte de Concreto Fresco',
        ]);

    }
}

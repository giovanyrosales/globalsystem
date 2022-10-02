<?php

namespace Database\Seeders;

use App\Models\P_Estado;
use Illuminate\Database\Seeder;

class EstadoUnidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        P_Estado::create([
            'nombre' => 'NO APROBADO',
        ]);

        P_Estado::create([
            'nombre' => 'APROBADO',
        ]);
    }
}

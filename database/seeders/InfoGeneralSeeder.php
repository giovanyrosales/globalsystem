<?php

namespace Database\Seeders;

use App\Models\InformacionGeneral;
use Illuminate\Database\Seeder;

class InfoGeneralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        InformacionGeneral::create([
            'imprevisto_modificable' => '3',
            'porcentaje_herramienta' => '2',
        ]);
    }
}

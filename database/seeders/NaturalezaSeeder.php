<?php

namespace Database\Seeders;

use App\Models\Naturaleza;
use Illuminate\Database\Seeder;

class NaturalezaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Naturaleza::create([
            'nombre' => 'Privativo'
        ]);

        Naturaleza::create([
            'nombre' => 'Desarrollo Social'
        ]);
    }
}

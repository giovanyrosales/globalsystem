<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Usuario::create([
            'nombre' => 'Jonathan',
            'apellido' => 'Moran',
            'usuario' => 'jonathan',
            'password' => bcrypt('admin'),
            'activo' => 1,
        ])->assignRole('Encargado-Administrador');

    }
}

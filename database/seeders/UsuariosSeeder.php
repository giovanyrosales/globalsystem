<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;

class UsuariosSeeder extends Seeder
{
    /**
     * usuarios por defecto
     *
     * @return void
     */
    public function run()
    {
        Usuario::create([
            'nombre' => 'Jonathan',
            'apellido' => 'Moran',
            'usuario' => 'jonathan',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('admin');

        Usuario::create([
            'nombre' => 'Heidi',
            'apellido' => 'Monzon',
            'usuario' => 'uaci',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('uaci');

        Usuario::create([
            'nombre' => 'Rina',
            'apellido' => 'Tejada',
            'usuario' => 'presupuesto',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('presupuesto');

        Usuario::create([
            'nombre' => 'Giovany',
            'apellido' => 'Rosales',
            'usuario' => 'ingenieria',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('formulador');

        Usuario::create([
            'nombre' => 'Heidi',
            'apellido' => 'Chinchilla',
            'usuario' => 'jefeuaci',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('jefeuaci');

        Usuario::create([
            'nombre' => 'Ruby',
            'apellido' => 'Ruby',
            'usuario' => 'admin',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('administrador');

        Usuario::create([
            'nombre' => 'Secretaria',
            'apellido' => 'Secre',
            'usuario' => 'secretaria',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('secretaria');


    }
}

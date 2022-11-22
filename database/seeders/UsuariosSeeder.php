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
            'usuario' => 'jonathan',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('admin');

        Usuario::create([
            'nombre' => 'Heidi Monzon',
            'usuario' => 'uaci',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('uaci');

        Usuario::create([
            'nombre' => 'Rina Tejada',
            'usuario' => 'presupuesto',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('presupuesto');

        Usuario::create([
            'nombre' => 'Ingeniero',
            'usuario' => 'ingenieria',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('formulador');

        Usuario::create([
            'nombre' => 'Heidi Chinchilla',
            'usuario' => 'jefeuaci',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('jefeuaci');

        Usuario::create([
            'nombre' => 'Ruby Ruby',
            'usuario' => 'ruby',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('administrador');

        Usuario::create([
            'nombre' => 'Secretaria',
            'usuario' => 'secretaria',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('secretaria');

        Usuario::create([
            'nombre' => 'Uaci',
            'usuario' => 'uaciunidad',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('uaciunidad');



    }
}

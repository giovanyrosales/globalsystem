<?php

namespace Database\Seeders;

use App\Models\LineaTrabajo;
use Illuminate\Database\Seeder;

class LineaTrabajoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LineaTrabajo::create([
            'id_areagestion' => 1,
            'codigo' => '0101',
            'nombre' => 'Dirección y Administración Municipal'
        ]);

        LineaTrabajo::create([
            'id_areagestion' => 1,
            'codigo' => '0102',
            'nombre' => 'Dirección y Administración Municipal - 120 25%'
        ]);

        LineaTrabajo::create([
            'id_areagestion' => 1,
            'codigo' => '0103',
            'nombre' => 'Dirección y Administración Municipal - 120 1.5%'
        ]);

        LineaTrabajo::create([
            'id_areagestion' => 1,
            'codigo' => '0201',
            'nombre' => 'Aseo Público'
        ]);

        LineaTrabajo::create([
            'id_areagestion' => 1,
            'codigo' => '0202',
            'nombre' => 'Servicios Jurídicos'
        ]);

        LineaTrabajo::create([
            'id_areagestion' => 1,
            'codigo' => '0203',
            'nombre' => 'Servicios Municipales Diversos'
        ]);

        LineaTrabajo::create([
            'id_areagestion' => 2,
            'codigo' => '0309',
            'nombre' => 'Inversión para el Desarrollo Económico y Social - FODES 120 75%'
        ]);

        LineaTrabajo::create([
            'id_areagestion' => 2,
            'codigo' => '0301',
            'nombre' => 'Inversión para el Desarrollo Económico y Social – Fondos Propios'
        ]);

        LineaTrabajo::create([
            'id_areagestion' => 2,
            'codigo' => '0302',
            'nombre' => 'Inversión para el Desarrollo Económico y Social - FODES 75%'
        ]);

        LineaTrabajo::create([
            'id_areagestion' => 2,
            'codigo' => '0302',
            'nombre' => 'Inversión para el Desarrollo Económico y Social - FODES 2%'
        ]);

        LineaTrabajo::create([
            'id_areagestion' => 2,
            'codigo' => '0303',
            'nombre' => 'Inversión Fideicomiso Arturo'
        ]);

        LineaTrabajo::create([
            'id_areagestion' => 2,
            'codigo' => '0306',
            'nombre' => 'Inversión para el Desarrollo Económico y Social - Préstamos Internos'
        ]);

        LineaTrabajo::create([
            'id_areagestion' => 2,
            'codigo' => '0304',
            'nombre' => 'Proyecto PTARM – Prestamos Internos'
        ]);

        LineaTrabajo::create([
            'id_areagestion' => 2,
            'codigo' => '5107',
            'nombre' => 'Programas de Apoyo Social Diversos'
        ]);

        LineaTrabajo::create([
            'id_areagestion' => 2,
            'codigo' => '0310',
            'nombre' => 'Inversión para el Desarrollo Económico y Social - FODES 120 2%'
        ]);

        LineaTrabajo::create([
            'id_areagestion' => 2,
            'codigo' => '0308',
            'nombre' => 'Proyectos Mancomunados'
        ]);

        LineaTrabajo::create([
            'id_areagestion' => 4,
            'codigo' => '0501',
            'nombre' => 'Amortización de la Deuda Pública Municipal - Fondos Propios'
        ]);

        LineaTrabajo::create([
            'id_areagestion' => 4,
            'codigo' => '0501',
            'nombre' => 'Amortización de la Deuda Pública Municipal – FODES 75%'
        ]);
    }
}

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
            'codigo' => '0101',
            'nombre' => 'Dirección y Administración Municipal'
        ]);

        LineaTrabajo::create([
            'codigo' => '0102',
            'nombre' => 'Administración Financiera y Tributaria'
        ]);

        LineaTrabajo::create([
            'codigo' => '0103',
            'nombre' => 'Unidades Administrativas de Apoyo'
        ]);

        LineaTrabajo::create([
            'codigo' => '0201',
            'nombre' => 'Aseo Público'
        ]);

        LineaTrabajo::create([
            'codigo' => '0202',
            'nombre' => 'Servicios Jurídicos'
        ]);

        LineaTrabajo::create([
            'codigo' => '0203',
            'nombre' => 'Servicios Municipales Diversos'
        ]);

        LineaTrabajo::create([
            'codigo' => '0301',
            'nombre' => 'Proyectos Sociales Fondos Propios'
        ]);

        LineaTrabajo::create([
            'codigo' => '0301',
            'nombre' => 'Inversión para el Desarrollo Económico y Social – Fondos Propios'
        ]);

        LineaTrabajo::create([
            'codigo' => '0302',
            'nombre' => 'Proyectos Sociales FODES'
        ]);

        LineaTrabajo::create([
            'codigo' => '0302',
            'nombre' => 'Inversión para el Desarrollo Económico y Social - Fondo General (Fodes 75%)'
        ]);

        LineaTrabajo::create([
            'codigo' => '0303',
            'nombre' => 'Inversión Fideicomiso Arturo'
        ]);

        LineaTrabajo::create([
            'codigo' => '0304',
            'nombre' => 'PLANTA DE TRATAMIENTO DE AGUAS RESIDUALES'
        ]);

        LineaTrabajo::create([
            'codigo' => '0304',
            'nombre' => 'Proyecto PTARM – Prestamos Internos'
        ]);

        LineaTrabajo::create([
            'codigo' => '0305',
            'nombre' => 'Proyectos Mancomunados'
        ]);

        LineaTrabajo::create([
            'codigo' => '0306',
            'nombre' => 'INVERSION PARA EL DESARROLLO SOCIAL Y ECONOMICO-PRESTAMOS INTERNOS'
        ]);

        LineaTrabajo::create([
            'codigo' => '0401',
            'nombre' => 'Proyectos PFGL'
        ]);

        LineaTrabajo::create([
            'codigo' => '0501',
            'nombre' => 'Amortización de la Deuda Pública Municipal'
        ]);

        LineaTrabajo::create([
            'codigo' => '0501',
            'nombre' => 'Amortización de la Deuda Pública Municipal – Fondo General (Fodes 75%)'
        ]);
    }
}

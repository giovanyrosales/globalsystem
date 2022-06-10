<?php

namespace Database\Seeders;

use App\Models\Cuenta;
use Illuminate\Database\Seeder;

class CuentaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Cuenta::create([
            'id_rubro' => '1',
            'codigo' => '511',
            'nombre' => 'REMUNERACIONES PERMANENTES'
        ]);

        Cuenta::create([
            'id_rubro' => '1',
            'codigo' => '512',
            'nombre' => 'REMUNERACIONES EVENTUALES'
        ]);

        Cuenta::create([
            'id_rubro' => '1',
            'codigo' => '513',
            'nombre' => 'REMUNERACIONES EXTRAORDINARIAS'
        ]);

        Cuenta::create([
            'id_rubro' => '1',
            'codigo' => '514',
            'nombre' => 'CONTRIBUCIONES PATRONALES A'
        ]);

        Cuenta::create([
            'id_rubro' => '1',
            'codigo' => '515',
            'nombre' => 'CONTRIBUCIONES PATRONALES A'
        ]);

        Cuenta::create([
            'id_rubro' => '1',
            'codigo' => '517',
            'nombre' => 'INDEMNIZACIONES'
        ]);

        Cuenta::create([
            'id_rubro' => '1',
            'codigo' => '518',
            'nombre' => 'COMISIONES POR SERVICIOS PERSONALES'
        ]);

        Cuenta::create([
            'id_rubro' => '1',
            'codigo' => '519',
            'nombre' => 'REMUNERACIONES DIVERSAS'
        ]);

        Cuenta::create([
            'id_rubro' => '2',
            'codigo' => '541',
            'nombre' => 'BIENES DE USO Y CONSUMO'
        ]);

        Cuenta::create([
            'id_rubro' => '2',
            'codigo' => '542',
            'nombre' => 'SERVICIOS BASICOS'
        ]);

        Cuenta::create([
            'id_rubro' => '2',
            'codigo' => '543',
            'nombre' => 'SERVICIOS GENERALES Y ARRENDAMIENTOS'
        ]);

        Cuenta::create([
            'id_rubro' => '2',
            'codigo' => '544',
            'nombre' => 'PASAJES Y VIATICOS'
        ]);

        Cuenta::create([
            'id_rubro' => '2',
            'codigo' => '545',
            'nombre' => 'CONSULTORIAS, ESTUDIOS E'
        ]);

        Cuenta::create([
            'id_rubro' => '2',
            'codigo' => '546',
            'nombre' => 'TRATAMIENTO DE DESECHOS'
        ]);

        Cuenta::create([
            'id_rubro' => '3',
            'codigo' => '555',
            'nombre' => 'IMPUESTOS, TASAS Y DERECHOS'
        ]);

        Cuenta::create([
            'id_rubro' => '3',
            'codigo' => '556',
            'nombre' => 'SEGUROS, COMISIONES Y GASTOS'
        ]);

        Cuenta::create([
            'id_rubro' => '3',
            'codigo' => '557',
            'nombre' => 'OTROS GASTOS NO CLASIFICADOS'
        ]);

        Cuenta::create([
            'id_rubro' => '4',
            'codigo' => '562',
            'nombre' => 'TRANSFERENCIAS CORRIENTES AL SECTOR'
        ]);

        Cuenta::create([
            'id_rubro' => '4',
            'codigo' => '563',
            'nombre' => 'TRANSFERENCIAS CORRIENTES AL SECTOR'
        ]);

        Cuenta::create([
            'id_rubro' => '5',
            'codigo' => '611',
            'nombre' => 'BIENES MUEBLES'
        ]);

        Cuenta::create([  // 21
            'id_rubro' => '5',
            'codigo' => '612',
            'nombre' => 'BIENES INMUEBLES'
        ]);

        Cuenta::create([  // 22
            'id_rubro' => '5',
            'codigo' => '614',
            'nombre' => 'INTANGIBLES'
        ]);

        Cuenta::create([  // 23
            'id_rubro' => '5',
            'codigo' => '721',
            'nombre' => 'CUENTAS POR PAGAR DE AÑOS ANTERIORES'
        ]);


    }
}

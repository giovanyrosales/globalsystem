<?php

namespace Database\Seeders;

use App\Models\ObjEspecifico;
use Illuminate\Database\Seeder;

class ObjetoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        ObjEspecifico::create([
            'id_cuenta' => '1',
            'codigo' => '51101',
            'nombre' => 'SUELDOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '1',
            'codigo' => '51103',
            'nombre' => 'AGUINALDOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '1',
            'codigo' => '51105',
            'nombre' => 'DIETAS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '1',
            'codigo' => '51107',
            'nombre' => 'BENEFICIOS ADICIONALES',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '2',
            'codigo' => '51201',
            'nombre' => 'SUELDOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '2',
            'codigo' => '51202',
            'nombre' => 'SALARIOS POR JORNAL',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '3',
            'codigo' => '51301',
            'nombre' => 'HORAS EXTRAORDINARIAS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '3',
            'codigo' => '51302',
            'nombre' => 'BENEFICIOS EXTRAORDINARIOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '4',
            'codigo' => '51401',
            'nombre' => 'POR REMUNERACIONES PERMANENTES',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '4',
            'codigo' => '51402',
            'nombre' => 'POR REMUNERACIONES EVENTUALES',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '4',
            'codigo' => '51403',
            'nombre' => 'POR REMUNERACIONES EXTRAORDINARIAS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '5',
            'codigo' => '51501',
            'nombre' => 'POR REMUNERACIONES PERMANENTES',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '5',
            'codigo' => '51502',
            'nombre' => 'POR REMUNERACIONES EVENTUALES',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '5',
            'codigo' => '51503',
            'nombre' => 'POR REMUNERACIONES EXTRAORDINARIAS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '6',
            'codigo' => '51701',
            'nombre' => 'AL PERSONAL DE SERVICIOS PERMANENTES',
        ]);
        ObjEspecifico::create([
            'id_cuenta' => '6',
            'codigo' => '51702',
            'nombre' => 'AL PERSONAL DE SERVICIOS EVENTUALES',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '7',
            'codigo' => '51803',
            'nombre' => 'COMISIONES POR RECUPERACION DE',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '8',
            'codigo' => '51901',
            'nombre' => 'HONORARIOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '8',
            'codigo' => '51999',
            'nombre' => 'REMUNERACIONES DIVERSAS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '9',
            'codigo' => '54101',
            'nombre' => 'PRODUCTOS ALIMENTICIOS PARA PERSONAS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '9',
            'codigo' => '54102',
            'nombre' => 'PRODUCTOS ALIMENTICIOS PARA ANIMALES',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '9',
            'codigo' => '54103',
            'nombre' => 'PRODUCTOS AGROPECUARIOS Y FORESTALES',
        ]);


        ObjEspecifico::create([
            'id_cuenta' => '9',
            'codigo' => '54104',
            'nombre' => 'PRODUCTOS TEXTILES Y VESTUARIOS',
        ]);
        ObjEspecifico::create([
            'id_cuenta' => '9',
            'codigo' => '54105',
            'nombre' => 'PRODUCTOS DE PAPEL Y CARTON',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '9',
            'codigo' => '54106',
            'nombre' => 'PRODUCTOS DE CUERO Y CAUCHO',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '9',
            'codigo' => '54107',
            'nombre' => 'PRODUCTOS QUIMICOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '9',
            'codigo' => '54108',
            'nombre' => 'PRODUCTOS FARMACEUTICOS Y MEDICINALES',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '9',
            'codigo' => '54109',
            'nombre' => 'LLANTAS Y NEUMATICOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '9',
            'codigo' => '54110',
            'nombre' => 'COMBUSTIBLES Y LUBRICANTES',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '9',
            'codigo' => '54111',
            'nombre' => 'MINERALES NO METALICOS Y PRODUCTOS DERIVADOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '9',
            'codigo' => '54112',
            'nombre' => 'MINERALES METALICOS Y PRODUCTOS DEVR.',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '9',
            'codigo' => '54113',
            'nombre' => 'MATERIALES E INSTRUMENTAL DE LABORATORIOS Y USO MÉDICO',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '9',
            'codigo' => '54114',
            'nombre' => 'MATERIALES DE OFICINA',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '9',
            'codigo' => '54115',
            'nombre' => 'MATERIALES INFORMATICOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '9',
            'codigo' => '54116',
            'nombre' => 'LIBROS, TEXTOS, UTILES DE ENSEÑANZA Y',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '9',
            'codigo' => '54117',
            'nombre' => 'MATERIALES DE DEFENSA Y SEGURIDAD',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '9',
            'codigo' => '54118',
            'nombre' => 'HERRAMIENTAS, REPUESTOS Y ACCESORIOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '9',
            'codigo' => '54119',
            'nombre' => 'MATERIALES ELECTRICOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '9',
            'codigo' => '54199',
            'nombre' => 'BIENES DE USO Y CONSUMO DIVERSOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '10',
            'codigo' => '54201',
            'nombre' => 'SERVICIOS DE ENERGIA ELECTRICA',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '10',
            'codigo' => '54202',
            'nombre' => 'SERVICIOS DE AGUA',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '10',
            'codigo' => '54203',
            'nombre' => 'SERVICIOS DE TELECOMUNICACIONES',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '10',
            'codigo' => '54204',
            'nombre' => 'SERVICIOS DE CORREOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '10',
            'codigo' => '54205',
            'nombre' => 'ALUMBRADO PUBLICO',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '11',
            'codigo' => '54301',
            'nombre' => 'MANTENIMIENTOS Y REPARACIONES DE BIENES MUEBLES',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '11',
            'codigo' => '54302',
            'nombre' => 'MANTENIMIENTOS Y REPARACIONES DE VEHICULOS',
        ]);
        ObjEspecifico::create([
            'id_cuenta' => '11',
            'codigo' => '54303',
            'nombre' => 'MANTENIMIENTOS Y REPARACIONES DE',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '11',
            'codigo' => '54304',
            'nombre' => 'TRANSPORTES, FLETES Y ALMACENAMIENTOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '11',
            'codigo' => '54305',
            'nombre' => 'SERVICIOS DE PUBLICIDAD',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '11',
            'codigo' => '54307',
            'nombre' => 'SERVICIOS DE LIMPIEZA Y FUMIGACIONES',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '11',
            'codigo' => '54309',
            'nombre' => 'SERVICIOS DE LABORATORIOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '11',
            'codigo' => '54313',
            'nombre' => 'IMPRESIONES, PUBLICACIONES Y REPRODUCCIONES',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '11',
            'codigo' => '54314',
            'nombre' => 'ATENCIONES OFICIALES',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '11',
            'codigo' => '54316',
            'nombre' => 'ARRENDAMIENTO DE BIENES MUEBLES',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '11',
            'codigo' => '54317',
            'nombre' => 'ARRENDAMIENTO DE BIENES INMUEBLES',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '11',
            'codigo' => '54318',
            'nombre' => 'ARRENDAMIENTO POR EL USO DE BIENES',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '11',
            'codigo' => '54399',
            'nombre' => 'SERVICIOS GENERALES Y ARRENDAMIENTOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '12',
            'codigo' => '54402',
            'nombre' => 'PASAJES AL EXTERIOR',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '12',
            'codigo' => '54403',
            'nombre' => 'VIATICOS POR COMISION INTERNA',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '12',
            'codigo' => '54404',
            'nombre' => 'VIATICOS POR COMISION EXTERNA',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '13',
            'codigo' => '54503',
            'nombre' => 'SERVICIOS JURIDICOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '13',
            'codigo' => '54504',
            'nombre' => 'SERVICIOS DE CONTABILIDAD Y AUDITORIA',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '13',
            'codigo' => '54505',
            'nombre' => 'SERVICIOS DE CAPACITACION',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '13',
            'codigo' => '54507',
            'nombre' => 'DESARROLLOS INFORMATICOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '13',
            'codigo' => '54508',
            'nombre' => 'ESTUDIOS E INVESTIGACIONES',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '13',
            'codigo' => '54599',
            'nombre' => 'CONSULTORIAS, ESTUDIOS E',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '14',
            'codigo' => '54602',
            'nombre' => 'DEPOSITO DESECHOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '15',
            'codigo' => '55599',
            'nombre' => 'IMPUESTOS, TASAS Y DERECHOS DIVERSOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '16',
            'codigo' => '55601',
            'nombre' => 'PRIMAS Y GASTOS DE SEGUROS DE',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '16',
            'codigo' => '55602',
            'nombre' => 'PRIMAS Y GASTOS DE SEGUROS DE BIENES',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '16',
            'codigo' => '55603',
            'nombre' => 'COMISIONES Y GASTOS BANCARIOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '17',
            'codigo' => '55703',
            'nombre' => 'MULTAS Y COSTAS JUDICIALES',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '17',
            'codigo' => '55799',
            'nombre' => 'GASTOS DIVERSOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '18',
            'codigo' => '56201',
            'nombre' => 'TRANSFERENCIAS CORRIENTES AL SP',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '19',
            'codigo' => '56303',
            'nombre' => 'A ORGANISMOS SIN FINES DE LUCRO',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '19',
            'codigo' => '56304',
            'nombre' => 'A PERSONAS NATURALES',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '19',
            'codigo' => '56305',
            'nombre' => 'BECAS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '20',
            'codigo' => '61101',
            'nombre' => 'MOBILIARIOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '20',
            'codigo' => '61102',
            'nombre' => 'MAQUINARIAS Y EQUIPOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '20',
            'codigo' => '61103',
            'nombre' => 'EQUIPOS MEDICOS Y DE LABORATORIOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '20',
            'codigo' => '61104',
            'nombre' => 'EQUIPOS INFORMÁTICOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '20',
            'codigo' => '61105',
            'nombre' => 'VEHICULOS DE TRANSPORTE',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '20',
            'codigo' => '61108',
            'nombre' => 'HERRAMIENTAS Y REPUESTOS PRINCIPALES',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '20',
            'codigo' => '61109',
            'nombre' => 'MAQUINARIA Y EQUIPO DE PRODUCCION',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '20',
            'codigo' => '61199',
            'nombre' => 'BIENES MUEBLES DIVERSOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '21',
            'codigo' => '61201',
            'nombre' => 'TERRENOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '22',
            'codigo' => '61403',
            'nombre' => 'DERECHOS DE PROPIEDAD INTELECTUAL',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '23',
            'codigo' => '72101',
            'nombre' => 'CUENTAS POR PAGAR DE AÑOS ANTERIORES',
        ]);


        ObjEspecifico::create([
            'id_cuenta' => '9',
            'codigo' => '54121',
            'nombre' => 'ESPECIES MUNICIPALES DIVERSAS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '12',
            'codigo' => '54401',
            'nombre' => 'PASAJES AL INTERIOR',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '24',
            'codigo' => '61603',
            'nombre' => 'DE EDUCACION Y RECREACION',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '24',
            'codigo' => '61601',
            'nombre' => 'VIALES',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '24',
            'codigo' => '61602',
            'nombre' => 'DE SALUD Y SANEAMIENTO AMBIENTAL',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '24',
            'codigo' => '61604',
            'nombre' => 'DE VIVIENDA Y OFICINA',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '24',
            'codigo' => '61606',
            'nombre' => 'ELECTRICAS Y COMUNICACIONES',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '24',
            'codigo' => '61607',
            'nombre' => 'DE PRODUCCION DE BIENES Y SERVICIOS',
        ]);

        ObjEspecifico::create([
            'id_cuenta' => '24',
            'codigo' => '61699',
            'nombre' => 'OBRAS DE INFRAESTRUCTURA DIVERSAS',
        ]);

    }
}

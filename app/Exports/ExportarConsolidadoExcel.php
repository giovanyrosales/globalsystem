<?php

namespace App\Exports;

use App\Models\Cuenta;
use App\Models\ObjEspecifico;
use App\Models\P_Materiales;
use App\Models\P_PresupUnidad;
use App\Models\P_PresupUnidadDetalle;
use App\Models\Rubro;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportarConsolidadoExcel implements FromCollection, WithHeadings
{
    public function __construct($anio)
    {
        $this->anio = $anio;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(){

        $rubro = Rubro::orderBy('codigo')->get();

        $resultsBloque = array();
        $index = 0;
        $resultsBloque2 = array();
        $index2 = 0;
        $resultsBloque3 = array();
        $index3 = 0;

        ini_set('max_execution_time', '300');
        ini_set("pcre.backtrack_limit", "5000000");

        // listado de presupuesto por anio
        $listadoPresupuesto = P_PresupUnidad::where('id_anio', $this->anio)->get();

        $pila = array();

        $totalobj = 0;
        $totalcuenta = 0;
        $totalrubro = 0;

        foreach ($listadoPresupuesto as $lp){
            array_push($pila, $lp->id);
        }

        // agregar cuentas
        foreach($rubro as $secciones){
            array_push($resultsBloque,$secciones);

            $sumaRubro = 0;

            $subSecciones = Cuenta::where('id_rubro', $secciones->id)
                ->orderBy('codigo', 'ASC')
                ->get();

            // agregar objetos
            foreach ($subSecciones as $lista){

                array_push($resultsBloque2, $lista);

                $sumaObjetoTotal = 0;

                $subSecciones2 = ObjEspecifico::where('id_cuenta', $lista->id)
                    ->orderBy('codigo', 'ASC')
                    ->get();

                // agregar materiales
                foreach ($subSecciones2 as $ll){

                    array_push($resultsBloque3, $ll);

                    $subSecciones3 = P_Materiales::where('id_objespecifico', $ll->id)
                        ->orderBy('descripcion', 'ASC')
                        ->get();

                    $sumaObjeto = 0;

                    foreach ($subSecciones3 as $subLista){

                        $sumaunidades = 0;
                        $sumaperiodos = 0;
                        $multiunidades = 0;

                        $listaMateriales = P_PresupUnidadDetalle::whereIn('id_presup_unidad', $pila)
                            ->where('id_material', $subLista->id)
                            ->get();

                        foreach ($listaMateriales as $lm){
                            $sumaunidades = $sumaunidades + $lm->cantidad;
                            $sumaperiodos = $sumaperiodos + $lm->periodo;
                            $multiunidades = $multiunidades + (($lm->cantidad * $subLista->costo) * $lm->periodo);
                        }

                        $sumaObjeto = $sumaObjeto + $multiunidades;

                        $subLista->sumaunidades = number_format((float)$sumaunidades, 2, '.', ',');
                        $subLista->sumaperiodos = number_format((float)$sumaperiodos, 2, '.', ',');
                        $subLista->multiunidad = number_format((float)$multiunidades, 2, '.', ',');
                    }

                    $sumaObjetoTotal = $sumaObjetoTotal + $sumaObjeto;
                    $totalobj = $totalobj + $sumaObjeto;

                    $ll->sumaobjeto = number_format((float)$sumaObjeto, 2, '.', ',');

                    $resultsBloque3[$index3]->material = $subSecciones3;
                    $index3++;
                }

                $sumaRubro = $sumaRubro + $sumaObjetoTotal;
                $totalcuenta = $totalcuenta + $sumaObjetoTotal;

                $lista->sumaobjetototal = number_format((float)$sumaObjetoTotal, 2, '.', ',');

                $resultsBloque2[$index2]->objeto = $subSecciones2;
                $index2++;
            }

            $totalrubro = $totalrubro + $sumaRubro;
            $secciones->sumarubro = number_format((float)$sumaRubro, 2, '.', ',');

            $resultsBloque[$index]->cuenta = $subSecciones;
            $index++;
        }

        $totalobj = number_format((float)$totalobj, 2, '.', ',');
        $totalcuenta = number_format((float)$totalcuenta, 2, '.', ',');
        $totalrubro = number_format((float)$totalrubro, 2, '.', ',');

        foreach($rubro as $item){
            $dataArray[] = [
                'codigo' => $item->numero,
                'especifico' => $item->nombre,
                'obj_especifico' => "",
                'cuenta' => "",
                'rubro' => "$".$item->sumarubro,
            ];

            foreach($item->cuenta as $cc){

                $dataArray[] = [
                    'codigo' => $cc->numero,
                    'especifico' => $cc->nombre,
                    'obj_especifico' => "",
                    'cuenta' => "$".$cc->sumaobjetototal,
                    'rubro' => "",
                ];

                foreach($cc->objeto as $obj){

                    $dataArray[] = [
                        'codigo' => $obj->numero,
                        'especifico' => $obj->nombre,
                        'obj_especifico' => "$".$obj->sumaobjeto,
                        'cuenta' => "",
                        'rubro' => "",
                    ];

                }
            }
        }

        $dataArray[] = [
            'codigo' => "",
            'especifico' => "TOTAL",
            'obj_especifico' => "$".$totalobj,
            'cuenta' => "$".$totalcuenta,
            'rubro' => "$".$totalrubro,
        ];

        return collect($dataArray);
    }

    public function headings() :array
    {
        return ["CODIGO", "ESPECIFICO", "OBJ. ESPECIFICO", "CUENTA", "RUBRO"];
    }
}

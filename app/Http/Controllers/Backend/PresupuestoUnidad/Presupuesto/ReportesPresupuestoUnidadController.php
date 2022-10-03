<?php

namespace App\Http\Controllers\Backend\PresupuestoUnidad\Presupuesto;

use App\Exports\ExportarConsolidadoExcel;
use App\Exports\ExportarPorUnidadesExcel;
use App\Exports\ExportarTotalesExcel;
use App\Http\Controllers\Controller;
use App\Models\Cuenta;
use App\Models\ObjEspecifico;
use App\Models\P_AnioPresupuesto;
use App\Models\P_Departamento;
use App\Models\P_Materiales;
use App\Models\P_PresupUnidad;
use App\Models\P_PresupUnidadDetalle;
use App\Models\Rubro;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportesPresupuestoUnidadController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    // retornar PDF con los totales, se envía el ID año
    public function generarTotalesPdfPresupuesto($idanio){

        // obtener todos los departamentos, que han creado el presupuesto
        $presupuesto = P_PresupUnidad::where('id_anio', $idanio)
            ->where('id_estado', 3) // solo presupuestos aprobados
            ->orderBy('id', 'ASC')
            ->get();

        $dataArray = array();

        // listado
        $materiales = P_Materiales::orderBy('descripcion')->get();

        $fechaanio = P_AnioPresupuesto::where('id', $idanio)->pluck('nombre')->first();

        ini_set("pcre.backtrack_limit", "5000000");
        //ini_set('max_execution_time', 180); //3 minutes

        //$mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf->SetTitle('Consolidado Totales');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/logo.png';

        $sumaCantidadGlobal = 0;
        $sumaCostoGlobal = 0;
        $sumaTotalGlobal = 0;

        // recorrer cada material
        foreach ($materiales as $mm) {

            $sumacantidad = 0;

            $codigo = ObjEspecifico::where('id', $mm->id_objespecifico)->first();

            // recorrer cada departamento y buscar
            foreach ($presupuesto as $pp) {

                if ($info = P_PresupUnidadDetalle::where('id_presup_unidad', $pp->id)
                    ->where('id_material', $mm->id)
                    ->first()) {
                    $multip = $info->cantidad * $info->periodo;
                    $sumacantidad = $sumacantidad + $multip;

                    $sumaCantidadGlobal = $sumaCantidadGlobal + $sumacantidad;
                }
            }

            if($sumacantidad > 0){

                $sumaCostoGlobal = $sumaCostoGlobal + $mm->costo;
                $sumaTotalGlobal = $sumaTotalGlobal + ($sumacantidad * $mm->costo);

                $total = number_format((float)($sumacantidad * $mm->costo), 2, '.', ',');

                $sumacantidad = number_format((float)($sumacantidad), 2, '.', ',');

                $dataArray[] = [
                    'codigo' => $codigo->codigo,
                    'descripcion' => $mm->descripcion,
                    'sumacantidad' => $sumacantidad,
                    'costo' => $mm->costo,
                    'total' => $total,
                ];
            }
        }

        usort($dataArray, function ($a, $b) {
            return $a['codigo'] <=> $b['codigo'] ?: $a['descripcion'] <=> $b['descripcion'];
        });

        $sumaCantidadGlobal = number_format((float)($sumaCantidadGlobal), 2, '.', ',');
        $sumaCostoGlobal = number_format((float)($sumaCostoGlobal), 2, '.', ',');
        $sumaTotalGlobal = number_format((float)($sumaTotalGlobal), 2, '.', ',');

        $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            REPORTE CONSOLIDADO TOTALES
            </p>
            </div>";

        $tabla .= "
                <p class='fecha'><strong>Año: $fechaanio</strong></p>
        <table id='tablaFor' style='width: 100%'>
        <tbody>
        <tr>
            <th style='text-align: center; font-size:13px; width: 12%'>COD. ESPEC.</th>
            <th style='text-align: center; font-size:13px; width: 20%'>NOMBRE</th>
            <th style='text-align: center; font-size:13px; width: 9%'>CANTIDAD</th>
            <th style='text-align: center; font-size:13px; width: 10%'>PREC. UNI.</th>
            <th style='text-align: center; font-size:13px; width: 9%'>TOTAL</th>
        </tr>";

        foreach ($dataArray as $dd) {

            $tabla .= "<tr>
                <td style='font-size:11px; text-align: center'>" . $dd['codigo'] . "</td>
                <td style='font-size:11px; text-align: center'>" . $dd['descripcion'] . "</td>
                <td style='font-size:11px; text-align: center'>" . $dd['sumacantidad'] . "</td>
                <td style='font-size:11px; text-align: center'>$" . $dd['costo'] . "</td>
                <td style='font-size:11px; text-align: center'>$" . $dd['total'] . "</td>
            </tr>";
        }

        $tabla .= "<tr>
                <td style='font-size:11px; text-align: center'></td>
                <td style='font-size:11px; text-align: center'></td>
                <td style='font-size:11px; text-align: center'>$sumaCantidadGlobal</td>
                <td style='font-size:11px; text-align: center'>$ $sumaCostoGlobal</td>
                <td style='font-size:11px; text-align: center'>$ $sumaTotalGlobal</td>
            </tr>";

        $tabla .= "</tbody></table>";

        $stylesheet = file_get_contents('css/csspdftotales.css');
        $mpdf->WriteHTML($stylesheet, 1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');

        $mpdf->WriteHTML($tabla, 2);
        $mpdf->Output();
    }

    // retorna Excel con los totales, se envía el ID año
    public function generarTotalesExcelPresupuesto($anio){
        $nombre = 'totales.xlsx';
        return Excel::download(new ExportarTotalesExcel($anio), $nombre);
    }

    // retorna PDF con el consolidado, todos los presupuestos ya están aprobados
    public function generarConsolidadoPdfPresupuesto($anio){

        $rubro = Rubro::orderBy('codigo')->get();

        $resultsBloque = array();
        $index = 0;
        $resultsBloque2 = array();
        $index2 = 0;
        $resultsBloque3 = array();
        $index3 = 0;

        //ini_set('max_execution_time', '300');
        ini_set("pcre.backtrack_limit", "5000000");

        // listado de presupuesto por anio
        $listadoPresupuesto = P_PresupUnidad::where('id_anio', $anio)->get();
        $fechaanio = P_AnioPresupuesto::where('id', $anio)->pluck('nombre')->first();

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

        $mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        //$mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        $mpdf->SetTitle('Consolidado Totales');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/logo.png';

        $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            REPORTE CONSOLIDADO
            </p>
            </div>";

        $tabla .= "
                <p class='fecha'><strong>Año: $fechaanio</strong></p>
        <table id='tablaFor' style='width: 100%'>
        <tbody>
        <tr>
            <th style='text-align: center; font-size:13px; width: 11%'>COD.</th>
            <th style='text-align: center; font-size:13px; width: 30%'>ESPECIFICO</th>
            <th style='text-align: center; font-size:13px; width: 14%'>OBJ.ESPECIFICO</th>
            <th style='text-align: center; font-size:13px; width: 14%'>CUENTA</th>
            <th style='text-align: center; font-size:13px; width: 14%'>RUBRO</th>
        </tr>";

        foreach($rubro as $item){
            $tabla .= "
            <tr>
                <td style='font-size:11px; text-align: left'>$item->codigo</td>
                <td style='font-size:11px; text-align: left'>$item->nombre</td>
                <td></td>
                <td></td>
                <td style='font-size:11px; text-align: right'>$ $item->sumarubro</td>
            </tr>";

            foreach($item->cuenta as $cc){

                $tabla .= "<tr>
                    <td style='font-size:11px; text-align: left'>$cc->codigo</td>
                    <td style='font-size:11px; text-align: left'>$cc->nombre</td>
                    <td></td>
                    <td style='font-size:11px; text-align: right'>$ $cc->sumaobjetototal</td>
                    <td></td>
                </tr>";

                foreach($cc->objeto as $obj){

                    $tabla .= "<tr>
                        <td style='font-size:11px; text-align: left'>$obj->codigo</td>
                        <td style='font-size:11px; text-align: left'>$obj->nombre</td>
                        <td style='font-size:11px; text-align: right'>$ $obj->sumaobjeto</td>
                        <td></td>
                        <td></td>
                    </tr>";

                }
            }
        }

        $tabla .= "<tr>
            <td style='border: none'></td>
            <td style='font-size:13px; text-align: center; font-weight: bold; border: none'>TOTAL</td>
            <td style='font-size:13px; text-align: right'>$ $totalobj</td>
            <td style='font-size:13px; text-align: right'>$ $totalcuenta</td>
            <td style='font-size:13px; text-align: right'>$ $totalrubro</td>
        </tr>";

        $tabla .= "</tbody></table>";

        $stylesheet = file_get_contents('css/cssconsolidado.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');

        $mpdf->WriteHTML($tabla,2);
        $mpdf->Output();
    }


    // retorna Excel con el consolidado, todos los presupuestos ya están aprobados
    public function generarConsolidadoExcelPresupuesto($anio){
        $nombre = 'consolidado.xlsx';
        return Excel::download(new ExportarConsolidadoExcel($anio), $nombre);
    }


    // retorna PDF con los totales por unidad que se seleccionó
    public function generarTotalPdfPorUnidades($anio, $unidades){

        $porciones = explode("-", $unidades);

        $presupuesto = P_PresupUnidad::where('id_anio', $anio)
            ->whereIn('id_departamento', $porciones)
            ->where('id_estado', 3) // solo Presupuestos aprobados
            ->orderBy('id', 'ASC')
            ->get();

        $dataUnidades = P_Departamento::whereIn('id', $porciones)
            ->orderBy('nombre')
            ->get();

        $dataArray = array();

        // listado
        $materiales = P_Materiales::orderBy('descripcion')->get();

        $fechaanio = P_AnioPresupuesto::where('id', $anio)->pluck('nombre')->first();

        ini_set("pcre.backtrack_limit", "5000000");
        //ini_set('max_execution_time', 180); //3 minutes

        //$mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf->SetTitle('Consolidado Totales');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/logo.png';

        $sumaCantidadGlobal = 0;
        $sumaPrecioTotal = 0;
        $sumaTotal = 0;

        // recorrer cada material
        foreach ($materiales as $mm) {

            $sumacantidad = 0;

            $codigo = ObjEspecifico::where('id', $mm->id_objespecifico)->first();

            // recorrer cada departamento y buscar
            foreach ($presupuesto as $pp) {

                if ($info = P_PresupUnidadDetalle::where('id_presup_unidad', $pp->id)
                    ->where('id_material', $mm->id)
                    ->first()) {
                    $multip = $info->cantidad * $info->periodo;
                    $sumacantidad = $sumacantidad + $multip;

                    $sumaPrecioTotal = $sumaPrecioTotal + $mm->costo;
                }
            }

            $sumaTotal = $sumaTotal + ($sumacantidad * $mm->costo);

            $total = number_format((float)($sumacantidad * $mm->costo), 2, '.', ',');

            $sumaCantidadGlobal = $sumaCantidadGlobal + $sumacantidad;

            $dataArray[] = [
                'codigo' => $codigo->codigo,
                'descripcion' => $mm->descripcion,
                'sumacantidad' => $sumacantidad,
                'costo' => $mm->costo,
                'total' => $total,
            ];
        }

        usort($dataArray, function ($a, $b) {
            return $a['codigo'] <=> $b['codigo'] ?: $a['descripcion'] <=> $b['descripcion'];
        });

        $sumaCantidadGlobal = number_format((float)($sumaCantidadGlobal), 2, '.', ',');
        $sumaPrecioTotal = number_format((float)($sumaPrecioTotal), 2, '.', ',');
        $sumaTotal = number_format((float)($sumaTotal), 2, '.', ',');


        $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            REPORTE PRESUPUESTO POR UNIDAD
            </p>
            </div>";

        $tabla .= "
                <p class='fecha'><strong>Año: $fechaanio</strong></p>
                <p>Unidades.</p>";

        foreach ($dataUnidades as $dd) {
            $tabla .= "<label>$dd->nombre, </label>";
        }

        $tabla .= "<table id='tablaFor' style='width: 100%'>
        <tbody>
        <tr>
            <th style='text-align: center; font-size:13px; width: 12%'>COD. ESPEC.</th>
            <th style='text-align: center; font-size:13px; width: 20%'>NOMBRE</th>
            <th style='text-align: center; font-size:13px; width: 9%'>CANTIDAD</th>
            <th style='text-align: center; font-size:13px; width: 10%'>PREC. UNI.</th>
            <th style='text-align: center; font-size:13px; width: 9%'>TOTAL</th>
        </tr>";

        foreach ($dataArray as $dd) {

            if($dd['sumacantidad'] > 0){
                $tabla .= "<tr>
                <td style='font-size:11px; text-align: center'>" . $dd['codigo'] . "</td>
                <td style='font-size:11px; text-align: center'>" . $dd['descripcion'] . "</td>
                <td style='font-size:11px; text-align: center'>" . $dd['sumacantidad'] . "</td>
                <td style='font-size:11px; text-align: center'>$" . $dd['costo'] . "</td>
                <td style='font-size:11px; text-align: center'>$" . $dd['total'] . "</td>
            </tr>";
            }
        }

        $tabla .= "<tr>
                <td style='font-size:11px; text-align: center'></td>
                <td style='font-size:11px; text-align: center'></td>
                <td style='font-size:11px; text-align: center'>$sumaCantidadGlobal</td>
                <td style='font-size:11px; text-align: center'>$ $sumaPrecioTotal </td>
                <td style='font-size:11px; text-align: center'>$ $sumaTotal</td>
            </tr>";


        $tabla .= "</tbody></table>";

        $stylesheet = file_get_contents('css/csspdftotales.css');
        $mpdf->WriteHTML($stylesheet, 1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');

        $mpdf->WriteHTML($tabla, 2);
        $mpdf->Output();
    }


    // retorna Excel con los totales por unidad que se seleccionó
    public function generarTotalExcelPorUnidades($anio, $unidades){
        $nombre = 'unidades.xlsx';
        return Excel::download(new ExportarPorUnidadesExcel($anio, $unidades), $nombre);
    }




}

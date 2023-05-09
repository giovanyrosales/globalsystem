<?php

namespace App\Http\Controllers\Backend\PresupuestoUnidad\Presupuesto;

use App\Exports\ExportarConsolidadoExcel;
use App\Exports\ExportarPorUnidadesExcel;
use App\Exports\ExportarTotalesExcel;
use App\Exports\ExportarUnaUnidadExcel;
use App\Http\Controllers\Controller;
use App\Models\Cuenta;
use App\Models\ObjEspecifico;
use App\Models\P_AnioPresupuesto;
use App\Models\P_Departamento;
use App\Models\P_Materiales;
use App\Models\P_PresupUnidad;
use App\Models\P_PresupUnidadDetalle;
use App\Models\P_ProyectosAprobados;
use App\Models\P_UnidadMedida;
use App\Models\Rubro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportesPresupuestoUnidadController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

        // retornar PDF con los totales, se envía el ID año
        public function generarPlanPdfUaci($idanio){

            // obtener todos los departamentos, que han creado el presupuesto
            $arrayPresupuestoUni = P_PresupUnidad::where('id_anio', $idanio)
                ->where('id_estado', 3) // SOLO APROBADOS
                ->orderBy('id', 'ASC')
                ->get();

            $dataArray = array();
            $pilaArrayIdPresu = array();

            foreach ($arrayPresupuestoUni as $dd){
                array_push($pilaArrayIdPresu, $dd->id);
            }

            $listadoProyectoAprobados = P_ProyectosAprobados::whereIn('id_presup_unidad', $pilaArrayIdPresu)
                ->orderBy('descripcion', 'ASC')
                ->get();

            foreach ($listadoProyectoAprobados as $dd){

                $infoObjeto = ObjEspecifico::where('id', $dd->id_objespeci)->first();
                $infoFuenteR = ObjEspecifico::where('id', $dd->id_fuenter)->first();
                $infoLinea = ObjEspecifico::where('id', $dd->id_lineatrabajo)->first();
                $infoArea = ObjEspecifico::where('id', $dd->id_areagestion)->first();

                $dd->codigoobj = $infoObjeto->codigo;
                $dd->objeto = $infoObjeto->codigo . " - " . $infoObjeto->nombre;
                $dd->fuenterecurso = $infoFuenteR->codigo . " - " . $infoFuenteR->nombre;
                $dd->lineatrabajo = $infoLinea->codigo . " - " . $infoLinea->nombre;
                $dd->areagestion = $infoArea->codigo . " - " . $infoArea->nombre;

                $dd->costoFormat = '$' . number_format((float)$dd->costo, 2, '.', ',');
            }

            // listado
            $fechaanio = P_AnioPresupuesto::where('id', $idanio)->pluck('nombre')->first();
            ini_set("pcre.backtrack_limit", "5000000");

            // COLUMNA TOTAL GLOBAL
            $totalColumnaGlobal = 0;
            // COLUMNA TOTAL CANTIDAD
            $totalColumnaCantidad = 0;

           $materiales = DB::table('p_materiales AS ma')
           ->join('obj_especifico AS obj', 'ma.id_objespecifico', '=', 'obj.id')
           ->join('cuenta AS cuen', 'obj.id_cuenta', '=', 'cuen.id')
            ->join('rubro AS rb', 'cuen.id_rubro', '=', 'rb.id')
            ->select('ma.*')
            ->whereNotIn('cuen.codigo', [612,616,614])
            ->whereNotIn('rb.codigo', [51,55,56,72])
            ->get();

            // recorrer cada material
            foreach ($materiales as $mm) {

                // para suma de cantidad para cada fila. columna CANTIDAD
                $sumacantidad = 0;

                $infoObj = ObjEspecifico::where('id', $mm->id_objespecifico)->first();

                // dinero fila columna TOTAL
                $multiFila = 0;

                // recorrer cada departamento y buscar
                foreach ($arrayPresupuestoUni as $pp) {

                    // ya filtrado para x año y solo aprobados
                    if ($info = P_PresupUnidadDetalle::where('id_presup_unidad', $pp->id)
                        ->where('id_material', $mm->id)
                        ->first()) {

                        // PERIODO SIEMPRE SERA 1 COMO MÍNIMO
                        $resultado = ($info->cantidad * $info->precio) * $info->periodo;
                        $multiFila += $resultado;

                        // solo obtener fila de columna CANTIDAD
                        $sumacantidad += ($info->cantidad * $info->periodo);
                    }
                }

                // si es mayor a cero, es porque si hay cantidad * periodo
                if($sumacantidad > 0){

                    $totalColumnaGlobal += $multiFila;
                    $totalColumnaCantidad += $sumacantidad;

                    $infoUnidadMedida = P_UnidadMedida::where('id', $mm->id_unidadmedida)->first();

                        $dataArray[] = [
                            'idmaterial' => $mm->id,
                            'codigo' => $infoObj->numero,
                            'descripcion' => $mm->descripcion,
                            'sumacantidad' => number_format((float)($sumacantidad), 2, '.', ','),
                            'sumacantidadDeci' => $sumacantidad,
                            'unidadmedida' => $infoUnidadMedida->nombre,
                            'total' => number_format((float)($multiFila), 2, '.', ','), // dinero
                            'totalDecimal' => $multiFila
                        ];
                }
            }

            usort($dataArray, function ($a, $b) {
                return $a['codigo'] <=> $b['codigo'] ?: $a['descripcion'] <=> $b['descripcion'];
            });


            // SUMAR A CANTIDAD LOS PROYECTOS APROBADOS, YA QUE SIEMPRE SE MUESTRAN EN ESTE REPORTE
             foreach ($listadoProyectoAprobados as $lpa){
                 $totalColumnaCantidad += 1;
                 $totalColumnaGlobal += $lpa->costo;
             }

            $totalColumnaCantidad = number_format((float)($totalColumnaCantidad), 2, '.', ',');
            $totalColumnaGlobal = number_format((float)($totalColumnaGlobal), 2, '.', ',');


            $resultsBloque = array();
            $index = 0;
            $resultsBloque2 = array();
            $index2 = 0;
            $resultsBloque3 = array();
            $index3 = 0;

            $rubro = Rubro::orderBy('codigo')->whereNotIn('codigo', [51,72,56,55])->get();

            $pilaIdMaterial = array();
            foreach ($dataArray as $dd){

                if(!empty($dd['idmaterial'])) {
                    array_push($pilaIdMaterial, $dd['idmaterial']);
                }
            }

            // agregar cuentas
            foreach($rubro as $secciones){

                array_push($resultsBloque, $secciones);

                $sumaRubro = 0;

                $subSecciones = Cuenta::where('id_rubro', $secciones->id)
                    ->orderBy('codigo', 'ASC')
                    ->whereNotIn('codigo', [612,616])
                    ->get();

                // agregar objetos
                foreach ($subSecciones as $lista){

                    array_push($resultsBloque2, $lista);

                    $subSecciones2 = ObjEspecifico::where('id_cuenta', $lista->id)
                        ->orderBy('codigo', 'ASC')
                        ->get();

                    $sumaObjetoTotal = 0; // total dinero por fila

                    // agregar materiales
                    foreach ($subSecciones2 as $ll){

                        array_push($resultsBloque3, $ll);

                        if($ll->codigo == 61109){
                            $ll->nombre = $ll->nombre . " ( ACTIVOS FIJOS MENORES A $600.00 )";
                        }

                        $sumaObjeto = 0;

                        $subSecciones3Materiales = P_Materiales::whereIn('id', $pilaIdMaterial)
                            ->where('id_objespecifico', $ll->id)
                            ->orderBy('descripcion', 'ASC')
                            ->get();

                        foreach ($subSecciones3Materiales as $subLista){

                            foreach ($dataArray as $dda){
                                if($dda['idmaterial'] == $subLista->id){

                                    $subLista->codigo = $ll->codigo;
                                    $subLista->sumacantidad = $dda['sumacantidad'];
                                    $subLista->totalfila = $dda['total'];
                                    $subLista->unidadmedida = $dda['unidadmedida'];

                                    $sumaObjeto += $dda['totalDecimal'];

                                    break;
                                }
                            }
                        }


                        foreach ($listadoProyectoAprobados as $lpa){

                            // codigo de objeto especifico Comparando con
                            if ($ll->id == $lpa->id_objespeci){
                                $sumaObjeto += $lpa->costo;
                            }
                        }

                        $sumaObjetoTotal += $sumaObjeto;

                        $ll->sumaobjeto = number_format((float)$sumaObjeto, 2, '.', ',');
                        $ll->sumaobjetoDeci = $sumaObjeto;

                        $resultsBloque3[$index3]->material = $subSecciones3Materiales;
                        $index3++;
                    }

                    $sumaRubro += $sumaObjetoTotal;
                    $lista->sumaobjetototal = number_format((float)$sumaObjetoTotal, 2, '.', ',');
                    $lista->sumaobjetoDecimal = $sumaObjetoTotal;

                    $resultsBloque2[$index2]->objeto = $subSecciones2;
                    $index2++;
                }

                $secciones->sumarubro = number_format((float)$sumaRubro, 2, '.', ',');
                $secciones->sumarubroDecimal = $sumaRubro;

                $resultsBloque[$index]->cuenta = $subSecciones;
                $index++;
            }

            $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
            //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
            $mpdf->SetTitle('Plan Anual de Compras');

            // mostrar errores
            $mpdf->showImageErrors = false;

            $logoalcaldia = 'images/logo.png';

            $tabla = "<div class='content'>
                <img id='logo' src='$logoalcaldia'>
                <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
                PLAN ANUAL DE COMPRAS
                </p>
                </div>";

            $tabla .= "
                    <p class='fecha'><strong>Año: $fechaanio</strong></p>";

            // recorrer rubros que tenga dinero

            $tabla .= "<table id='tablaFor' style='width: 100%'>
                    <tbody>
                    <tr>
                        <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>COD. ESPECÍFICO</th>
                        <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>NOMBRE</th>
                        <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>UNI. MEDIDA</th>
                        <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>CANTIDAD</th>
                        <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>TOTAL</th>
                    </tr>";

            foreach ($rubro as $dataRR){
                if($dataRR->sumarubroDecimal > 0){

                    $tabla .= "<tr>
                        <td style='font-size:11px; text-align: center; font-weight: bold'>$dataRR->codigo</td>
                        <td style='font-size:11px; text-align: center; font-weight: bold'>$dataRR->nombre</td>
                        <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                        <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                        <td style='font-size:11px; text-align: center; font-weight: bold'>$$dataRR->sumarubro</td>
                    </tr>";

                    foreach ($dataRR->cuenta as $dataCC){

                        if($dataCC->sumaobjetoDecimal > 0){

                            // CUENTAS

                            $tabla .= "<tr>
                                <td style='font-size:11px; text-align: center; font-weight: bold'>$dataCC->codigo</td>
                                <td style='font-size:11px; text-align: center; font-weight: bold'>$dataCC->nombre</td>
                                <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                                <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                                <td style='font-size:11px; text-align: center; font-weight: bold'>$$dataCC->sumaobjetototal</td>
                            </tr>";

                            foreach ($dataCC->objeto as $dataObj){

                                if($dataObj->sumaobjetoDeci > 0){

                                    $tabla .= "<tr>
                                <td style='font-size:11px; text-align: center; font-weight: bold'>$dataObj->codigo</td>
                                <td style='font-size:11px; text-align: center; font-weight: bold'>$dataObj->nombre</td>
                                <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                                <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                                <td style='font-size:11px; text-align: center; font-weight: bold'>$$dataObj->sumaobjeto</td>
                                </tr>";

                                    // MATERIALES

                                    foreach ($dataObj->material as $dataMM){

                                        $tabla .= "<tr>
                                    <td style='font-size:11px; text-align: center; font-weight: normal'>$dataObj->numero</td>
                                    <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->descripcion</td>
                                    <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->unidadmedida</td>
                                    <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->sumacantidad</td>
                                    <td style='font-size:11px; text-align: center; font-weight: normal'>$$dataMM->totalfila</td>
                                    </tr>";
                                    }

                                    foreach ($listadoProyectoAprobados as $lpa){

                                        if ($dataMM->codigo == $lpa->codigoobj){

                                                $tabla .= "<tr>
                                        <td style='font-size:11px; text-align: center; font-weight: normal'></td>
                                        <td style='font-size:11px; text-align: center; font-weight: normal'>$lpa->descripcion</td>
                                        <td style='font-size:11px; text-align: center; font-weight: normal'>PROYECTO</td>
                                        <td style='font-size:11px; text-align: center; font-weight: normal'>1.0</td>
                                        <td style='font-size:11px; text-align: center; font-weight: normal'>$lpa->costoFormat</td>
                                        </tr>";

                                        }
                                    }

                                }
                            }
                        }
                    }
                }
            }

           $tabla .= "<tr>
                        <td style='font-size:11px; text-align: center; font-weight: normal'></td>
                        <td style='font-size:11px; text-align: center; font-weight: normal'>TOTALES</td>
                        <td style='font-size:11px; text-align: center; font-weight: normal'></td>
                        <td style='font-size:11px; text-align: center; font-weight: normal'>$totalColumnaCantidad</td>
                        <td style='font-size:11px; text-align: center; font-weight: normal'>$$totalColumnaGlobal</td>
                     </tr>";

            $tabla .= "</tbody></table>";

            $stylesheet = file_get_contents('css/csspdftotales.css');
            $mpdf->WriteHTML($stylesheet, 1);

            $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');

            $mpdf->WriteHTML($tabla, 2);
            $mpdf->Output();
        }

    // retornar PDF con los totales, se envía el ID año
    public function generarTotalesPdfPresupuesto($idanio){

        // obtener todos los departamentos, que han creado el presupuesto
        $arrayPresupuestoUni = P_PresupUnidad::where('id_anio', $idanio)
            ->where('id_estado', 3) // SOLO APROBADOS
            ->orderBy('id', 'ASC')
            ->get();

        $pilaIdPresu = array();
        foreach ($arrayPresupuestoUni as $dd){
            array_push($pilaIdPresu, $dd->id);
        }

        $dataArray = array();

        $listadoProyectoAprobados = P_ProyectosAprobados::
        whereIn('id_presup_unidad', $pilaIdPresu)
        ->orderBy('descripcion', 'ASC')
        ->get();

        foreach ($listadoProyectoAprobados as $dd){

            $infoObjeto = ObjEspecifico::where('id', $dd->id_objespeci)->first();
            $infoFuenteR = ObjEspecifico::where('id', $dd->id_fuenter)->first();
            $infoLinea = ObjEspecifico::where('id', $dd->id_lineatrabajo)->first();
            $infoArea = ObjEspecifico::where('id', $dd->id_areagestion)->first();

            $dd->codigoobj = $infoObjeto->codigo;
            $dd->objeto = $infoObjeto->codigo . " - " . $infoObjeto->nombre;
            $dd->fuenterecurso = $infoFuenteR->codigo . " - " . $infoFuenteR->nombre;
            $dd->lineatrabajo = $infoLinea->codigo . " - " . $infoLinea->nombre;
            $dd->areagestion = $infoArea->codigo . " - " . $infoArea->nombre;

            $dd->costoFormat = '$' . number_format((float)$dd->costo, 2, '.', ',');
        }

        // listado
        $fechaanio = P_AnioPresupuesto::where('id', $idanio)->pluck('nombre')->first();
        ini_set("pcre.backtrack_limit", "5000000");

        // COLUMNA TOTAL GLOBAL
        $totalColumnaGlobal = 0;
        // COLUMNA TOTAL CANTIDAD
        $totalColumnaCantidad = 0;

        $materiales = P_Materiales::orderBy('descripcion')->get();

        // recorrer cada material
        foreach ($materiales as $mm) {

            // para suma de cantidad para cada fila. columna CANTIDAD
            $sumacantidad = 0;

            $infoObj = ObjEspecifico::where('id', $mm->id_objespecifico)->first();

            // dinero fila columna TOTAL
            $multiFila = 0;

            // recorrer cada departamento y buscar
            foreach ($arrayPresupuestoUni as $pp) {

                // ya filtrado para x año y solo aprobados
                if ($info = P_PresupUnidadDetalle::where('id_presup_unidad', $pp->id)
                    ->where('id_material', $mm->id)
                    ->first()) {

                    // PERIODO SIEMPRE SERA 1 COMO MÍNIMO
                    $resultado = ($info->cantidad * $info->precio) * $info->periodo;
                    $multiFila += $resultado;

                    // solo obtener fila de columna CANTIDAD
                    $sumacantidad += ($info->cantidad * $info->periodo);
                }
            }

            // si es mayor a cero, es porque si hay cantidad * periodo
            if($sumacantidad > 0){

                $totalColumnaGlobal += $multiFila;
                $totalColumnaCantidad += $sumacantidad;

                $infoUnidadMedida = P_UnidadMedida::where('id', $mm->id_unidadmedida)->first();

                    $dataArray[] = [
                        'idmaterial' => $mm->id,
                        'codigo' => $infoObj->numero,
                        'descripcion' => $mm->descripcion,
                        'sumacantidad' => number_format((float)($sumacantidad), 2, '.', ','),
                        'sumacantidadDeci' => $sumacantidad,
                        'unidadmedida' => $infoUnidadMedida->nombre,
                        'total' => number_format((float)($multiFila), 2, '.', ','), // dinero
                        'totalDecimal' => $multiFila
                    ];
            }
        }

        usort($dataArray, function ($a, $b) {
            return $a['codigo'] <=> $b['codigo'] ?: $a['descripcion'] <=> $b['descripcion'];
        });


        // SUMAR A CANTIDAD LOS PROYECTOS APROBADOS, YA QUE SIEMPRE SE MUESTRAN EN ESTE REPORTE
         foreach ($listadoProyectoAprobados as $lpa){
             $totalColumnaCantidad += 1;
             $totalColumnaGlobal += $lpa->costo;
         }

        $totalColumnaCantidad = number_format((float)($totalColumnaCantidad), 2, '.', ',');
        $totalColumnaGlobal = number_format((float)($totalColumnaGlobal), 2, '.', ',');


        $resultsBloque = array();
        $index = 0;
        $resultsBloque2 = array();
        $index2 = 0;
        $resultsBloque3 = array();
        $index3 = 0;

        $rubro = Rubro::orderBy('codigo')->get();

        $pilaIdMaterial = array();
        foreach ($dataArray as $dd){

            if(!empty($dd['idmaterial'])) {
                array_push($pilaIdMaterial, $dd['idmaterial']);
            }
        }

        // agregar cuentas
        foreach($rubro as $secciones){

            array_push($resultsBloque, $secciones);

            $sumaRubro = 0;

            $subSecciones = Cuenta::where('id_rubro', $secciones->id)
                ->orderBy('codigo', 'ASC')
                ->get();

            // agregar objetos
            foreach ($subSecciones as $lista){

                array_push($resultsBloque2, $lista);

                $subSecciones2 = ObjEspecifico::where('id_cuenta', $lista->id)
                    ->orderBy('codigo', 'ASC')
                    ->get();

                $sumaObjetoTotal = 0; // total dinero por fila

                // agregar materiales
                foreach ($subSecciones2 as $ll){

                    array_push($resultsBloque3, $ll);

                    if($ll->codigo == 61109){
                        $ll->nombre = $ll->nombre . " ( ACTIVOS FIJOS MENORES A $600.00 )";
                    }

                    $sumaObjeto = 0;

                    $subSecciones3Materiales = P_Materiales::whereIn('id', $pilaIdMaterial)
                        ->where('id_objespecifico', $ll->id)
                        ->orderBy('descripcion', 'ASC')
                        ->get();

                    foreach ($subSecciones3Materiales as $subLista){

                        foreach ($dataArray as $dda){
                            if($dda['idmaterial'] == $subLista->id){

                                $subLista->codigo = $ll->codigo;
                                $subLista->sumacantidad = $dda['sumacantidad'];
                                $subLista->totalfila = $dda['total'];
                                $subLista->unidadmedida = $dda['unidadmedida'];

                                $sumaObjeto += $dda['totalDecimal'];

                                break;
                            }
                        }
                    }


                    foreach ($listadoProyectoAprobados as $lpa){

                        // codigo de objeto especifico Comparando con
                        if ($ll->id == $lpa->id_objespeci){
                            $sumaObjeto += $lpa->costo;
                        }
                    }

                    $sumaObjetoTotal += $sumaObjeto;

                    $ll->sumaobjeto = number_format((float)$sumaObjeto, 2, '.', ',');
                    $ll->sumaobjetoDeci = $sumaObjeto;

                    $resultsBloque3[$index3]->material = $subSecciones3Materiales;
                    $index3++;
                }

                $sumaRubro += $sumaObjetoTotal;
                $lista->sumaobjetototal = number_format((float)$sumaObjetoTotal, 2, '.', ',');
                $lista->sumaobjetoDecimal = $sumaObjetoTotal;

                $resultsBloque2[$index2]->objeto = $subSecciones2;
                $index2++;
            }

            $secciones->sumarubro = number_format((float)$sumaRubro, 2, '.', ',');
            $secciones->sumarubroDecimal = $sumaRubro;

            $resultsBloque[$index]->cuenta = $subSecciones;
            $index++;
        }




        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf->SetTitle('Consolidado Totales');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/logo.png';

        $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            REPORTE CONSOLIDADO TOTALES
            </p>
            </div>";

        $tabla .= "
                <p class='fecha'><strong>Año: $fechaanio</strong></p>";

        // recorrer rubros que tenga dinero

        $tabla .= "<table id='tablaFor' style='width: 100%'>
                <tbody>
                <tr>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>COD. ESPECÍFICO</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>NOMBRE</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>UNI. MEDIDA</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>CANTIDAD</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>TOTAL</th>
                </tr>";

        foreach ($rubro as $dataRR){
            if($dataRR->sumarubroDecimal > 0){

                $tabla .= "<tr>
                    <td style='font-size:11px; text-align: center; font-weight: bold'>$dataRR->codigo</td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'>$dataRR->nombre</td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'>$$dataRR->sumarubro</td>
                </tr>";

                foreach ($dataRR->cuenta as $dataCC){

                    if($dataCC->sumaobjetoDecimal > 0){

                        // CUENTAS

                        $tabla .= "<tr>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$dataCC->codigo</td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$dataCC->nombre</td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$$dataCC->sumaobjetototal</td>
                        </tr>";

                        foreach ($dataCC->objeto as $dataObj){

                            if($dataObj->sumaobjetoDeci > 0){

                                $tabla .= "<tr>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$dataObj->codigo</td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$dataObj->nombre</td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$$dataObj->sumaobjeto</td>
                            </tr>";

                                // MATERIALES

                                foreach ($dataObj->material as $dataMM){

                                    $tabla .= "<tr>
                                <td style='font-size:11px; text-align: center; font-weight: normal'>$dataObj->numero</td>
                                <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->descripcion</td>
                                <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->unidadmedida</td>
                                <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->sumacantidad</td>
                                <td style='font-size:11px; text-align: center; font-weight: normal'>$$dataMM->totalfila</td>
                                </tr>";
                                }

                                foreach ($listadoProyectoAprobados as $lpa){

                                    if ($dataMM->codigo == $lpa->codigoobj){

                                            $tabla .= "<tr>
                                    <td style='font-size:11px; text-align: center; font-weight: normal'></td>
                                    <td style='font-size:11px; text-align: center; font-weight: normal'>$lpa->descripcion</td>
                                    <td style='font-size:11px; text-align: center; font-weight: normal'>PROYECTO</td>
                                    <td style='font-size:11px; text-align: center; font-weight: normal'>1.0</td>
                                    <td style='font-size:11px; text-align: center; font-weight: normal'>$lpa->costoFormat</td>
                                    </tr>";

                                    }
                                }

                            }
                        }
                    }
                }
            }
        }

        $tabla .= "<tr>
                    <td style='font-size:11px; text-align: center; font-weight: normal'></td>
                    <td style='font-size:11px; text-align: center; font-weight: normal'>TOTALES</td>
                    <td style='font-size:11px; text-align: center; font-weight: normal'></td>
                    <td style='font-size:11px; text-align: center; font-weight: normal'>$totalColumnaCantidad</td>
                    <td style='font-size:11px; text-align: center; font-weight: normal'>$$totalColumnaGlobal</td>
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
        $arrayPresupUnidad = P_PresupUnidad::where('id_anio', $anio)->get();
        $fechaanio = P_AnioPresupuesto::where('id', $anio)->pluck('nombre')->first();

        $listadoProyectoAprobados = P_ProyectosAprobados::orderBy('descripcion', 'ASC')->get();

        foreach ($listadoProyectoAprobados as $dd){

            $infoObjeto = ObjEspecifico::where('id', $dd->id_objespeci)->first();
            $infoFuenteR = ObjEspecifico::where('id', $dd->id_fuenter)->first();
            $infoLinea = ObjEspecifico::where('id', $dd->id_lineatrabajo)->first();
            $infoArea = ObjEspecifico::where('id', $dd->id_areagestion)->first();

            $dd->codigoobj = $infoObjeto->codigo;
            $dd->objeto = $infoObjeto->codigo . " - " . $infoObjeto->nombre;
            $dd->fuenterecurso = $infoFuenteR->codigo . " - " . $infoFuenteR->nombre;
            $dd->lineatrabajo = $infoLinea->codigo . " - " . $infoLinea->nombre;
            $dd->areagestion = $infoArea->codigo . " - " . $infoArea->nombre;

            $dd->costoFormat = '$' . number_format((float)$dd->costo, 2, '.', ',');
        }


        $pilaIdPresupUnidad = array();

        $totalobj = 0;
        $totalcuenta = 0;
        $totalrubro = 0;

        foreach ($arrayPresupUnidad as $lp){
            array_push($pilaIdPresupUnidad, $lp->id);
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

                    // todos los materiales del mismo objeto, ya filtrado por año
                    $subSecciones3 = P_Materiales::where('id_objespecifico', $ll->id)
                        ->orderBy('descripcion', 'ASC')
                        ->get();

                    $sumaObjeto = 0;

                    foreach ($subSecciones3 as $subLista){

                        $sumaunidades = 0;
                        $sumaperiodos = 0;
                        $multiunidades = 0;

                        // pila (id presup unidad) ya filtrado por año
                        $listaMateriales = P_PresupUnidadDetalle::whereIn('id_presup_unidad', $pilaIdPresupUnidad)
                            ->where('id_material', $subLista->id)
                            ->get();

                        foreach ($listaMateriales as $lm){
                            $sumaunidades = $sumaunidades + $lm->cantidad;
                            $sumaperiodos = $sumaperiodos + $lm->periodo;
                            $multiunidades = $multiunidades + (($lm->cantidad * $lm->precio) * $lm->periodo);
                        }

                        $sumaObjeto = $sumaObjeto + $multiunidades;

                        $subLista->sumaunidades = number_format((float)$sumaunidades, 2, '.', ',');
                        $subLista->sumaperiodos = number_format((float)$sumaperiodos, 2, '.', ',');
                        $subLista->multiunidad = number_format((float)$multiunidades, 2, '.', ',');
                    }


                    foreach ($listadoProyectoAprobados as $lpa){
                        if($ll->codigo == $lpa->codigoobj){
                            $sumaObjeto += $lpa->costo;
                        }
                    }

                    $sumaObjetoTotal += $sumaObjeto;
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

        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
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

        // filtrado por x departamento y x año
        $arrayPresupUnidad = P_PresupUnidad::where('id_anio', $anio)
            ->whereIn('id_departamento', $porciones)
            ->orderBy('id', 'ASC')
            ->get();

        // solo para obtener los nombres
        $dataUnidades = P_Departamento::whereIn('id', $porciones)->orderBy('nombre')->get();

        $pilaArrayIdPres = array();

        foreach ($arrayPresupUnidad as $dd){
            array_push($pilaArrayIdPres, $dd->id);
        }

        $listadoProyectoAprobados = P_ProyectosAprobados::whereIn('id_presup_unidad', $pilaArrayIdPres)
            ->orderBy('descripcion', 'ASC')
            ->get();

        foreach ($listadoProyectoAprobados as $dd){

            $infoObjeto = ObjEspecifico::where('id', $dd->id_objespeci)->first();
            $infoFuenteR = ObjEspecifico::where('id', $dd->id_fuenter)->first();
            $infoLinea = ObjEspecifico::where('id', $dd->id_lineatrabajo)->first();
            $infoArea = ObjEspecifico::where('id', $dd->id_areagestion)->first();

            $dd->codigoobj = $infoObjeto->codigo;
            $dd->objeto = $infoObjeto->codigo . " - " . $infoObjeto->nombre;
            $dd->fuenterecurso = $infoFuenteR->codigo . " - " . $infoFuenteR->nombre;
            $dd->lineatrabajo = $infoLinea->codigo . " - " . $infoLinea->nombre;
            $dd->areagestion = $infoArea->codigo . " - " . $infoArea->nombre;

            $dd->costoFormat = '$' . number_format((float)$dd->costo, 2, '.', ',');
        }


        $fechaanio = P_AnioPresupuesto::where('id', $anio)->pluck('nombre')->first();

        // listado de materiales
        $materiales = P_Materiales::orderBy('descripcion')->get();

        $sumaGlobalUnidades = 0;

        $pilaArrayMateriales = array();
        $pilaArrayPresuUni = array();

        // PRIMERO OBTENER LOS ID DE MATERIALES QUE TIENE ESTA UNIDAD, UN ARRAY DE ID Y AHI SE BUSCARA
        // A CUAL RUBRO PERTENECE

        foreach ($materiales as $mm) {

            $sumacantidad = 0;

            // recorrer cada departamento y buscar
            foreach ($arrayPresupUnidad as $pp) {

                // ya filtrado para x año y solo aprobados
                if ($info = P_PresupUnidadDetalle::where('id_presup_unidad', $pp->id)
                    ->where('id_material', $mm->id)
                    ->first()) {

                    array_push($pilaArrayPresuUni, $info->id);

                    // solo obtener fila de columna CANTIDAD
                    $sumacantidad = $sumacantidad + ($info->cantidad * $info->periodo);
                }
            }

            if($sumacantidad > 0){
                array_push($pilaArrayMateriales, $mm->id);
            }
        }

        $rubro = Rubro::orderBy('codigo')->get();

        $resultsBloque = array();
        $index = 0;
        $resultsBloque2 = array();
        $index2 = 0;
        $resultsBloque3 = array();
        $index3 = 0;


        // agregar cuentas
        foreach($rubro as $secciones){
            array_push($resultsBloque, $secciones);

            $sumaRubro = 0;
            $subSecciones = Cuenta::where('id_rubro', $secciones->id)
                ->orderBy('codigo', 'ASC')
                ->get();

            // agregar objetos
            foreach ($subSecciones as $lista){

                array_push($resultsBloque2, $lista);

                $subSecciones2 = ObjEspecifico::where('id_cuenta', $lista->id)
                    ->orderBy('codigo', 'ASC')
                    ->get();

                $sumaObjetoTotal = 0;

                // agregar materiales
                foreach ($subSecciones2 as $ll){

                    array_push($resultsBloque3, $ll);

                    if($ll->codigo == 61109){
                        $ll->nombre = $ll->nombre . " ( ACTIVOS FIJOS MENORES A $600.00 )";
                    }
                    $subSecciones3 = P_Materiales::whereIn('id', $pilaArrayMateriales)
                        ->where('id_objespecifico', $ll->id)
                        ->orderBy('descripcion', 'ASC')
                        ->get();

                    $sumaObjeto = 0;

                    foreach ($subSecciones3 as $subLista){

                        $uni = P_UnidadMedida::where('id', $subLista->id_unidadmedida)->first();
                        $subLista->unimedida = $uni->nombre;

                        // buscar
                        $dataArrayPresu = P_PresupUnidadDetalle::whereIn('id', $pilaArrayPresuUni)
                            ->where('id_material', $subLista->id)->get();


                        $resul2 = 0;
                        $sumaPedida = 0;
                        foreach ($dataArrayPresu as $infoData){

                            // PERIODO SIEMPRE SERA MÍNIMO 1
                            $resul2 += ($infoData->cantidad * $infoData->precio) * $infoData->periodo;

                            $resultado = ($infoData->cantidad * $infoData->precio) * $infoData->periodo;
                            $sumaObjeto += $resultado;

                            $sumaGlobalUnidades += $resultado;

                            $sumaPedida += $infoData->cantidad  * $infoData->periodo;
                        }

                        $subLista->cantidadpedi = $sumaPedida;
                        $subLista->total = '$' . number_format((float)$resul2, 2, '.', ',');
                    }

                    foreach ($listadoProyectoAprobados as $lpa){
                        if($ll->codigo == $lpa->codigoobj){
                            $sumaObjeto += $lpa->costo;
                            $sumaGlobalUnidades += $lpa->costo;
                        }
                    }

                    $sumaObjetoTotal += $sumaObjeto;
                    $ll->sumaobjeto = number_format((float)$sumaObjeto, 2, '.', ',');
                    $ll->sumaobjetoDeci = $sumaObjeto;

                    $resultsBloque3[$index3]->material = $subSecciones3;
                    $index3++;
                }

                $sumaRubro += $sumaObjetoTotal;
                $lista->sumaobjetototal = number_format((float)$sumaObjetoTotal, 2, '.', ',');
                $lista->sumaobjetoDecimal = $sumaObjetoTotal;

                $resultsBloque2[$index2]->objeto = $subSecciones2;
                $index2++;
            }

            $secciones->sumarubro = number_format((float)$sumaRubro, 2, '.', ',');
            $secciones->sumarubroDecimal = $sumaRubro;

            $resultsBloque[$index]->cuenta = $subSecciones;
            $index++;
        }

        $sumaGlobalUnidades = number_format((float)($sumaGlobalUnidades), 2, '.', ',');

        ini_set("pcre.backtrack_limit", "5000000");
        $logoalcaldia = 'images/logo.png';

        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf->SetTitle('Consolidado Totales');

        // mostrar errores
        $mpdf->showImageErrors = false;

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

        // recorrer rubros que tenga dinero

        $tabla .= "<table id='tablaFor' style='width: 100%'>
                <tbody>
                <tr>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>COD. ESPECÍFICO</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>NOMBRE</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>UNIDAD MEDIDA</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>CANTIDAD</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>TOTAL</th>
                </tr>";

        foreach ($rubro as $dataRR){
            if($dataRR->sumarubroDecimal > 0){

                $tabla .= "<tr>
                    <td style='font-size:11px; text-align: center; font-weight: bold'>$dataRR->codigo</td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'>$dataRR->nombre</td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'>$$dataRR->sumarubro</td>
                </tr>";

                foreach ($dataRR->cuenta as $dataCC){

                    if($dataCC->sumaobjetoDecimal > 0){

                        // CUENTAS

                        $tabla .= "<tr>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$dataCC->codigo</td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$dataCC->nombre</td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$$dataCC->sumaobjetototal</td>
                        </tr>";

                        foreach ($dataCC->objeto as $dataObj){

                            if($dataObj->sumaobjetoDeci > 0){

                                $tabla .= "<tr>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$dataObj->codigo</td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$dataObj->nombre</td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$$dataObj->sumaobjeto</td>
                            </tr>";

                                // MATERIALES

                                foreach ($dataObj->material as $dataMM){

                                    // CON ESTO EVITO QUE SE SALGA EL MATERIAL QUE FUE SOLICITADO, YA QUE ESOS
                                    // AL AGREGARSE A MI PRESU DE UNIDAD ENTRAN CON CANTIDAD 0
                                    if($dataMM->cantidadpedi > 0){

                                        $tabla .= "<tr>
                                        <td style='font-size:11px; text-align: center; font-weight: normal'>$dataObj->codigo</td>
                                        <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->descripcion</td>
                                        <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->unimedida</td>
                                        <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->cantidadpedi</td>
                                        <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->total</td>
                                        </tr>";
                                        }

                                    }

                                foreach ($listadoProyectoAprobados as $lpa){

                                    if ($dataObj->codigo == $lpa->codigoobj){

                                                $tabla .= "<tr>
                                        <td style='font-size:11px; text-align: center; font-weight: normal'>$dataObj->codigo</td>
                                        <td style='font-size:11px; text-align: center; font-weight: normal'>$lpa->descripcion</td>
                                        <td style='font-size:11px; text-align: center; font-weight: normal'>PROYECTO</td>
                                        <td style='font-size:11px; text-align: center; font-weight: normal'></td>
                                        <td style='font-size:11px; text-align: center; font-weight: normal'>$lpa->costoFormat</td>
                                        </tr>";

                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $tabla .= "</tbody></table>";

        $tabla .= "<table id='tablaFor' style='width: 100%; margin-top: 30px'>
                <tbody>
                <tr>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>TOTAL UNIDAD: $$sumaGlobalUnidades</th>
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

    public function generarTotalExcelSoloUnidad($anio, $unidad){
        $nombre = 'unidad.xlsx';
        return Excel::download(new ExportarUnaUnidadExcel($anio, $unidad), $nombre);
    }


    // reporte solo para 1 unidad, ya que lleva columna precio unitario
    public function generarPdfSoloUnaUnidad($anio, $unidad){

        $presupUnidad = P_PresupUnidad::where('id_anio', $anio)
            ->where('id_departamento', $unidad)
            ->orderBy('id', 'ASC')
            ->first();

            if($presupUnidad == null){
                return "EL PRESUPUESTO NO FUE ENCONTRADO";
            }

            if($presupUnidad->id_estado != 3) {
                return "EL PRESUPUESTO DE LA UNIDAD NO ESTA APROBADO";
            }

            // solo para obtener los nombres
            $dataUnidades = P_Departamento::where('id', $presupUnidad->id_departamento)->first();

            $fechaanio = P_AnioPresupuesto::where('id', $anio)->pluck('nombre')->first();

            $sumaGlobalUnidades = 0;

            $pilaArrayMateriales = array();
            $pilaArrayPresuUni = array();

            $infoPresuUniDeta = P_PresupUnidadDetalle::where('id_presup_unidad', $presupUnidad->id)->get();

            // LISTADO DE PROYECTO APROBADOS
            $listadoProyectoAprobados = P_ProyectosAprobados::where('id_presup_unidad', $presupUnidad->id)
            ->orderBy('descripcion', 'ASC')
            ->get();

            foreach ($listadoProyectoAprobados as $dd){

                $infoObjeto = ObjEspecifico::where('id', $dd->id_objespeci)->first();
                $infoFuenteR = ObjEspecifico::where('id', $dd->id_fuenter)->first();
                $infoLinea = ObjEspecifico::where('id', $dd->id_lineatrabajo)->first();
                $infoArea = ObjEspecifico::where('id', $dd->id_areagestion)->first();

                $dd->codigoobj = $infoObjeto->codigo;
                $dd->objeto = $infoObjeto->codigo . " - " . $infoObjeto->nombre;
                $dd->fuenterecurso = $infoFuenteR->codigo . " - " . $infoFuenteR->nombre;
                $dd->lineatrabajo = $infoLinea->codigo . " - " . $infoLinea->nombre;
                $dd->areagestion = $infoArea->codigo . " - " . $infoArea->nombre;

                $dd->costoFormat = '$' . number_format((float)$dd->costo, 2, '.', ',');
            }

            foreach ($infoPresuUniDeta as $dd){
                array_push($pilaArrayPresuUni, $dd->id);

                array_push($pilaArrayMateriales, $dd->id_material);
            }

            $rubro = Rubro::orderBy('codigo')->get();

            $resultsBloque = array();
            $index = 0;
            $resultsBloque2 = array();
            $index2 = 0;
            $resultsBloque3 = array();
            $index3 = 0;

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

                    $subSecciones2 = ObjEspecifico::where('id_cuenta', $lista->id)
                        ->orderBy('codigo', 'ASC')
                        ->get();

                    $sumaObjetoTotal = 0;

                    // agregar materiales
                    foreach ($subSecciones2 as $ll){

                        array_push($resultsBloque3, $ll);

                        if($ll->codigo == 61109){
                            $ll->nombre = $ll->nombre . " ( ACTIVOS FIJOS MENORES A $600.00 )";
                        }
                        $subSecciones3 = P_Materiales::whereIn('id', $pilaArrayMateriales)
                            ->where('id_objespecifico', $ll->id)
                            ->orderBy('descripcion', 'ASC')
                            ->get();

                        $sumaObjeto = 0;

                        foreach ($subSecciones3 as $subLista){

                            $uni = P_UnidadMedida::where('id', $subLista->id_unidadmedida)->first();
                            $subLista->unimedida = $uni->nombre;

                            // buscar
                            $dataArrayPresu = P_PresupUnidadDetalle::whereIn('id', $pilaArrayPresuUni)
                                ->where('id_material', $subLista->id)->get();

                            foreach ($dataArrayPresu as $infoData){

                                // PERIODO SIEMPRE SERA MÍNIMO 1
                                $resultado = ($infoData->cantidad * $infoData->precio) * $infoData->periodo;
                                $sumaObjeto += $resultado;

                                $sumaGlobalUnidades += $resultado;

                                $subLista->cantidadpedi = $infoData->cantidad  * $infoData->periodo;

                                $subLista->precunitario = '$' . number_format((float)$infoData->precio, 2, '.', ',');

                                $subLista->total = '$' . number_format((float)$resultado, 2, '.', ',');
                            }
                        }

                        foreach ($listadoProyectoAprobados as $lpa){
                            if($ll->codigo == $lpa->codigoobj){
                                $sumaObjeto += $lpa->costo;
                                $sumaGlobalUnidades += $lpa->costo;
                            }
                        }

                        $sumaObjetoTotal += $sumaObjeto;
                        $ll->sumaobjeto = number_format((float)$sumaObjeto, 2, '.', ',');
                        $ll->sumaobjetoDeci = $sumaObjeto;

                        $resultsBloque3[$index3]->material = $subSecciones3;
                        $index3++;
                    }

                    $sumaRubro += $sumaObjetoTotal;
                    $lista->sumaobjetototal = number_format((float)$sumaObjetoTotal, 2, '.', ',');
                    $lista->sumaobjetoDecimal = $sumaObjetoTotal;

                    $resultsBloque2[$index2]->objeto = $subSecciones2;
                    $index2++;
                }

                $secciones->sumarubro = number_format((float)$sumaRubro, 2, '.', ',');
                $secciones->sumarubroDecimal = $sumaRubro;

                $resultsBloque[$index]->cuenta = $subSecciones;
                $index++;
            }

            $sumaGlobalUnidades = number_format((float)($sumaGlobalUnidades), 2, '.', ',');


            ini_set("pcre.backtrack_limit", "5000000");
            $logoalcaldia = 'images/logo.png';

            $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
            //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
            $mpdf->SetTitle('Presupuesto Unidad');

            // mostrar errores
            $mpdf->showImageErrors = false;

            $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            REPORTE PRESUPUESTO POR UNIDAD
            </p>
            </div>";

            $tabla .= "
                <p class='fecha'><strong>Año: $fechaanio</strong></p>
                <p>Unidad.</p>";


            $tabla .= "<label>$dataUnidades->nombre</label>";

            // recorrer rubros que tenga dinero

            $tabla .= "<table id='tablaFor' style='width: 100%'>
                <tbody>
                <tr>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>COD. ESPECÍFICO</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>NOMBRE</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>UNIDAD MEDIDA</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>PRECIO UNI.</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>CANTIDAD</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>TOTAL</th>
                </tr>";

            foreach ($rubro as $dataRR){

                if($dataRR->sumarubroDecimal > 0){

                    $tabla .= "<tr>
                    <td style='font-size:11px; text-align: center; font-weight: bold'>$dataRR->codigo</td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'>$dataRR->nombre</td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                    <td style='font-size:11px; text-align: center; font-weight: bold'>$$dataRR->sumarubro</td>
                </tr>";

                    foreach ($dataRR->cuenta as $dataCC){

                        if($dataCC->sumaobjetoDecimal > 0) {

                            // CUENTAS

                            $tabla .= "<tr>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$dataCC->codigo</td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$dataCC->nombre</td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                            <td style='font-size:11px; text-align: center; font-weight: bold'>$$dataCC->sumaobjetototal</td>
                        </tr>";

                            foreach ($dataCC->objeto as $dataObj) {

                                if ($dataObj->sumaobjetoDeci > 0) {

                                    $tabla .= "<tr>
                                        <td style='font-size:11px; text-align: center; font-weight: bold'>$dataObj->codigo</td>
                                        <td style='font-size:11px; text-align: center; font-weight: bold'>$dataObj->nombre</td>
                                        <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                                        <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                                        <td style='font-size:11px; text-align: center; font-weight: bold'></td>
                                        <td style='font-size:11px; text-align: center; font-weight: bold'>$$dataObj->sumaobjeto</td>
                                        </tr>";

                                    // MATERIALES

                                    foreach ($dataObj->material as $dataMM) {

                                        // EVITAR QUE MATERIALES SOLICITADOS CON COSTO $0.00 Y UNIDADES
                                        // SOLICITADAS 0, SEA VISIBLE.
                                        // NO APARECEN SI SOLO HAY 1 MATERIAL COSTO $0.00 EN CONTENEDOR DE OBJ ESPECIFICO.
                                        if($dataMM->cantidadpedi > 0) {
                                            $tabla .= "<tr>
                                        <td style='font-size:11px; text-align: center; font-weight: normal'>$dataObj->codigo</td>
                                        <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->descripcion</td>
                                        <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->unimedida</td>
                                        <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->precunitario</td>
                                        <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->cantidadpedi</td>
                                        <td style='font-size:11px; text-align: center; font-weight: normal'>$dataMM->total</td>
                                        </tr>";
                                        }
                                    }

                                    foreach ($listadoProyectoAprobados as $lpa) {

                                        if ($dataObj->codigo == $lpa->codigoobj) {

                                            $tabla .= "<tr>
                                            <td style='font-size:11px; text-align: center; font-weight: normal'>$dataObj->codigo</td>
                                            <td style='font-size:11px; text-align: center; font-weight: normal'>$lpa->descripcion</td>
                                            <td style='font-size:11px; text-align: center; font-weight: normal'>PROYECTO</td>
                                            <td style='font-size:11px; text-align: center; font-weight: normal'></td>
                                            <td style='font-size:11px; text-align: center; font-weight: normal'></td>
                                            <td style='font-size:11px; text-align: center; font-weight: normal'>$lpa->costoFormat</td>
                                            </tr>";
                                        }
                                    }

                                }
                            }
                        }
                    }
                }
            }

            $tabla .= "</tbody></table>";

            $tabla .= "<table id='tablaFor' style='width: 100%; margin-top: 30px'>
                <tbody>
                <tr>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>TOTAL UNIDAD: $$sumaGlobalUnidades</th>
                </tr>";
            $tabla .= "</tbody></table>";

            $stylesheet = file_get_contents('css/csspdftotales.css');
            $mpdf->WriteHTML($stylesheet, 1);

            $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');

            $mpdf->WriteHTML($tabla, 2);
            $mpdf->Output();
    }




}

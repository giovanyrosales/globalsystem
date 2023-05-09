<?php

namespace App\Exports;

use App\Models\Cuenta;
use App\Models\ObjEspecifico;
use App\Models\P_Materiales;
use App\Models\P_PresupUnidad;
use App\Models\P_PresupUnidadDetalle;
use App\Models\P_ProyectosAprobados;
use App\Models\P_UnidadMedida;
use App\Models\Rubro;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportarPorUnidadesExcel implements FromCollection, WithHeadings, WithStyles
{
    public function __construct($anio, $unidades){
        $this->anio = $anio;
        $this->unidades = $unidades;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(){

        $porciones = explode("-", $this->unidades);

        // filtrado por x departamento y x año
        $arrayPresupUnidad = P_PresupUnidad::where('id_anio', $this->anio)
            ->whereIn('id_departamento', $porciones)
            ->where('id_estado', 3) // SOLO APROBADOS
            ->orderBy('id', 'ASC')
            ->get();

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

        // listado de materiales
        $materiales = P_Materiales::orderBy('descripcion')->get();

        $sumaGlobalUnidades = 0;

        $pilaArrayMateriales = array();
        $pilaArrayPresuUni = array();

        // PRIMERO OBTENER LOS ID DE MATERIALES QUE TIENE ESTA UNIDAD, UN ARRAY DE ID Y AHI SE BUSCARA
        // A CUAL RUBRO PERTENECE

        $dataArray = array();

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

        $totalvalor = 0;

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

                        $resul2 = 0;
                        $cantidadPedida = 0;
                        foreach ($dataArrayPresu as $infoData){

                            // PERIODO SERA COMO MÍNIMO 1
                            $resul2 += ($infoData->cantidad * $infoData->precio) * $infoData->periodo;

                            $resultado = ($infoData->cantidad * $infoData->precio) * $infoData->periodo;
                            $sumaObjeto += $resultado;

                            $sumaGlobalUnidades += $resultado;

                            $cantidadPedida += $infoData->cantidad  * $infoData->periodo;

                        }
                        $subLista->cantidadpedi = $cantidadPedida;
                        $subLista->total = number_format((float)$resul2, 2, '.', ',');
                    }

                    foreach ($listadoProyectoAprobados as $lpa){
                        if($ll->codigo == $lpa->codigoobj){
                            $sumaObjeto += $lpa->costo;
                            $sumaGlobalUnidades += $lpa->costo;
                        }
                    }

                    $sumaObjetoTotal += $sumaObjeto;
                    $ll->sumaobjeto = $sumaObjeto;
                    $ll->sumaobjetoDeci = $sumaObjeto;

                    $resultsBloque3[$index3]->material = $subSecciones3;
                    $index3++;
                }

                $sumaRubro = $sumaRubro + $sumaObjetoTotal;
                $lista->sumaobjetototal = number_format((float)$sumaObjetoTotal, 2, '.', ',');
                $lista->sumaobjetoDecimal = $sumaObjetoTotal;

                $resultsBloque2[$index2]->objeto = $subSecciones2;
                $index2++;
            }

            $totalvalor = $totalvalor + $sumaRubro;
            $secciones->sumarubro = number_format((float)$sumaRubro, 2, '.', ',');
            $secciones->sumarubroDecimal = $sumaRubro;

            $resultsBloque[$index]->cuenta = $subSecciones;
            $index++;
        }

        $sumaGlobalUnidades = number_format((float)($sumaGlobalUnidades), 2, '.', ',');


        //$filaBold = 0;


        foreach ($rubro as $dataRR){
            if($dataRR->sumarubroDecimal > 0){

                //$filaBold += 1;

                $dataArray[] = [
                    'codigo' => $dataRR->codigo,
                    'descripcion' => $dataRR->nombre,
                    'medida' => "",
                    'cantidad' => "",
                    'total' => "$".$dataRR->sumarubro,
                ];

                foreach ($dataRR->cuenta as $dataCC){

                    if($dataCC->sumaobjetoDecimal > 0){

                        // CUENTAS
                        //$filaBold += 1;

                        $dataArray[] = [
                            'codigo' => $dataCC->codigo,
                            'descripcion' => $dataCC->nombre,
                            'medida' => "",
                            'cantidad' => "",
                            'total' => "$". $dataCC->sumaobjetototal,
                        ];

                        foreach ($dataCC->objeto as $dataObj){

                            if($dataObj->sumaobjetoDeci > 0){
                                //$filaBold += 1;

                                $dataArray[] = [
                                    'codigo' => $dataObj->codigo,
                                    'descripcion' => $dataObj->nombre,
                                    'medida' => "",
                                    'cantidad' => "",
                                    'total' => "$" . number_format((float)$dataObj->sumaobjeto, 2, '.', ',')
                                ];

                                // MATERIALES

                                foreach ($dataObj->material as $dataMM){


                                    if($dataMM->cantidadpedi > 0){
                                        // CON ESTO EVITO QUE SE SALGA EL MATERIAL QUE FUE SOLICITADO, YA QUE ESOS
                                        // AL AGREGARSE A MI PRESU DE UNIDAD ENTRAN CON CANTIDAD 0

                                        $dataArray[] = [
                                            'codigo' => $dataObj->codigo,
                                            'descripcion' => $dataMM->descripcion,
                                            'medida' => $dataMM->unimedida,
                                            'cantidad' => $dataMM->cantidadpedi,
                                            'total' => $dataMM->total,
                                        ];
                                    }
                                }


                                foreach ($listadoProyectoAprobados as $lpa){

                                    if ($dataObj->codigo == $lpa->codigoobj){

                                        $dataArray[] = [
                                            'codigo' => $dataObj->codigo,
                                            'descripcion' => $lpa->descripcion,
                                            'medida' => 'PROYECTO',
                                            'cantidad' => '',
                                            'total' => $lpa->costoFormat,
                                        ];
                                    }
                                }

                            }
                        }
                    }
                }
            }
        }

        $dataArray[] = [
            'codigo' => "",
            'descripcion' => "",
            'medida' => "",
            'cantidad' => "",
            'total' => "",
        ];

        $dataArray[] = [
            'codigo' => "",
            'descripcion' => "TOTAL",
            'medida' => "",
            'cantidad' => "",
            'total' => "$" . $sumaGlobalUnidades,
        ];

        return collect($dataArray);
    }

    public function headings(): array
    {
        return ["COD. ESPECIFICO", "NOMBRE", "U. MEDIDA", "CANTIDAD", "TOTAL"];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
            2    => ['font' => ['bold' => true]],
            3    => ['font' => ['bold' => true]],
            4    => ['font' => ['bold' => true]],
        ];
    }
}



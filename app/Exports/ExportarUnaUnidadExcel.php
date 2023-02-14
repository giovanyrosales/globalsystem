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

class ExportarUnaUnidadExcel implements FromCollection, WithHeadings, WithStyles
{

    public function __construct($anio, $unidad){
        $this->anio = $anio;
        $this->unidad = $unidad;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(){

        // filtrado por x departamento y x año
        $presupUnidad = P_PresupUnidad::where('id_anio', $this->anio)
            ->where('id_departamento', $this->unidad)
            ->first();

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


        $dataArray = array();


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

                        foreach ($dataArrayPresu as $infoData){

                            // PERIODO SERA COMO MÍNIMO 1
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

        foreach ($rubro as $dataRR){
            if($dataRR->sumarubroDecimal > 0){

                $dataArray[] = [
                    'codigo' => $dataRR->codigo,
                    'descripcion' => $dataRR->nombre,
                    'medida' => "",
                    'precunitario' => "",
                    'cantidad' => "",
                    'total' => "$".$dataRR->sumarubro,
                ];

                foreach ($dataRR->cuenta as $dataCC){

                    if($dataCC->sumaobjetoDecimal > 0){

                        // CUENTAS

                        $dataArray[] = [
                            'codigo' => $dataCC->codigo,
                            'descripcion' => $dataCC->nombre,
                            'medida' => "",
                            'precunitario' => "",
                            'cantidad' => "",
                            'total' => "$". $dataCC->sumaobjetototal,
                        ];

                        foreach ($dataCC->objeto as $dataObj){

                            if($dataObj->sumaobjetoDeci > 0){

                                $dataArray[] = [
                                    'codigo' => $dataObj->codigo,
                                    'descripcion' => $dataObj->nombre,
                                    'medida' => "",
                                    'precunitario' => "",
                                    'cantidad' => "",
                                    'total' => "$" . number_format((float)$dataObj->sumaobjeto, 2, '.', ',')
                                ];

                                // MATERIALES

                                foreach ($dataObj->material as $dataMM){

                                    // CON ESTO EVITO QUE SE SALGA EL MATERIAL QUE FUE SOLICITADO, YA QUE ESOS
                                    // AL AGREGARSE A MI PRESU DE UNIDAD ENTRAN CON CANTIDAD 0
                                    if($dataMM->cantidadpedi > 0){
                                        $dataArray[] = [
                                            'codigo' => $dataObj->codigo,
                                            'descripcion' => $dataMM->descripcion,
                                            'medida' => $dataMM->unimedida,
                                            'precunitario' => $dataMM->precunitario,
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
                                            'precunitario' => '',
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
            'precunitario' => "",
            'cantidad' => "",
            'total' => "",
        ];

        $dataArray[] = [
            'codigo' => "",
            'descripcion' => "TOTAL",
            'medida' => "",
            'precunitario' => "",
            'cantidad' => "",
            'total' => "$" . $sumaGlobalUnidades,
        ];

        return collect($dataArray);
    }


    public function headings(): array
    {
        return ["COD. ESPECIFICO", "NOMBRE", "U. MEDIDA", "PRECIO UNITARIO", "CANTIDAD", "TOTAL"];
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

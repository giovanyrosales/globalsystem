<?php

namespace App\Exports;

use App\Models\ObjEspecifico;
use App\Models\P_Materiales;
use App\Models\P_PresupUnidad;
use App\Models\P_PresupUnidadDetalle;
use App\Models\P_ProyectosAprobados;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportarTotalesExcel implements FromCollection, WithHeadings
{
    public function __construct($anio)
    {
        $this->anio = $anio;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // obtener todos los departamentos, que han creado el presupuesto
        $arrayPresupuestoUni = P_PresupUnidad::where('id_anio', $this->anio)
            ->where('id_estado', 3) // solo aprobados
            ->orderBy('id', 'ASC')
            ->get();

        $pilaArrayPresuUni = array();

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

            $dd->costoFormat = $dd->costo;
        }


        foreach ($arrayPresupuestoUni as $p){
            array_push($pilaArrayPresuUni, $p->id);
        }

        $dataArray = array();

        $materiales = P_Materiales::orderBy('descripcion')->get();

        // recorrer cada material
        foreach ($materiales as $mm) {

            $sumacantidad = 0;

            $codigo = ObjEspecifico::where('id', $mm->id_objespecifico)->first();

            // dinero fila columna TOTAL
            $multiFila = 0;

            // recorrer cada departamento y buscar
            foreach ($arrayPresupuestoUni as $pp) {

                if ($info = P_PresupUnidadDetalle::where('id_presup_unidad', $pp->id)
                    ->where('id_material', $mm->id)
                    ->first()) {

                    $resultado = ($info->cantidad * $info->precio) * $info->periodo;
                    $multiFila = $multiFila + $resultado;

                    // solo obtener fila de columna CANTIDAD
                    $sumacantidad = $sumacantidad + ($info->cantidad * $info->periodo);
                }


            }

            if($sumacantidad > 0){

                //$multiFila = number_format((float)($multiFila), 2, '.', ',');
                //$sumacantidad = number_format((float)($sumacantidad), 2, '.', ',');

                $dataArray[] = [
                    'codigo' => $codigo->codigo,
                    'descripcion' => $mm->descripcion,
                    'sumacantidad' => $sumacantidad,
                    'total' => $multiFila,
                ];
            }
        }

        foreach ($listadoProyectoAprobados as $lpa){
            $dataArray[] = [
                'codigo' => $lpa->codigoobj,
                'descripcion' => $lpa->descripcion,
                'sumacantidad' => 'PROYECTO',
                'total' => $lpa->costoFormat,
            ];
        }

        usort($dataArray, function ($a, $b) {
            return $a['codigo'] <=> $b['codigo'] ?: $a['descripcion'] <=> $b['descripcion'];
        });


        return collect($dataArray);
    }

    public function headings(): array
    {
        return ["COD. ESPECIFICO", "NOMBRE", "CANTIDAD", "TOTAL"];
    }

}

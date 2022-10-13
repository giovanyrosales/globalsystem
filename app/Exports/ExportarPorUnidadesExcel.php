<?php

namespace App\Exports;

use App\Models\ObjEspecifico;
use App\Models\P_Departamento;
use App\Models\P_Materiales;
use App\Models\P_PresupUnidad;
use App\Models\P_PresupUnidadDetalle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportarPorUnidadesExcel implements FromCollection, WithHeadings
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
            ->where('id_estado', 3) // solo aprobados
            ->orderBy('id', 'ASC')
            ->get();

        $pilaArrayPresuUni = array();

        foreach ($arrayPresupUnidad as $p){
            array_push($pilaArrayPresuUni, $p->id);
        }

        $dataArray = array();

        // listado
        $materiales = P_Materiales::orderBy('descripcion')->get();

        $sumaCantidadGlobal = 0;
        $sumaTotalGlobal = 0;

        // recorrer cada material
        foreach ($materiales as $mm) {

            // para suma de cantidad para cada fila. columna CANTIDAD
            $sumacantidad = 0;

            $infoObj = ObjEspecifico::where('id', $mm->id_objespecifico)->first();

            // dinero fila columna TOTAL
            $multiFila = 0;

            // recorrer cada departamento y buscar
            foreach ($arrayPresupUnidad as $pp) {

                // ya filtrado para x año y solo aprobados
                if ($info = P_PresupUnidadDetalle::where('id_presup_unidad', $pp->id)
                    ->where('id_material', $mm->id)
                    ->first()) {

                    $resultado = ($info->cantidad * $info->precio) * $info->periodo;
                    $multiFila = $multiFila + $resultado;

                    // solo obtener fila de columna CANTIDAD
                    $sumacantidad = $sumacantidad + ($info->cantidad * $info->periodo);

                    // para colocar CANTIDAD TOTAL al final de la columna
                    $sumaCantidadGlobal = $sumaCantidadGlobal + $sumacantidad;
                }
            }

            if($sumacantidad > 0){

                $multiFila = number_format((float)($multiFila), 2, '.', ',');

                // para fila de columna CANTIDAD
                $sumacantidad = number_format((float)($sumacantidad), 2, '.', ',');

                $dataArray[] = [
                    'codigo' => $infoObj->codigo,
                    'descripcion' => $mm->descripcion,
                    'sumacantidad' => $sumacantidad,
                    'total' => $multiFila,
                ];
            }
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

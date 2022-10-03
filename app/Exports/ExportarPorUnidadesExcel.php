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

        $presupuesto = P_PresupUnidad::where('id_anio', $this->anio)
            ->whereIn('id_departamento', $porciones)
            ->where('id_estado', 3) // solo Presupuesto aprobados
            ->orderBy('id', 'ASC')
            ->get();

        $dataArray = array();

        // listado
        $materiales = P_Materiales::orderBy('descripcion')->get();

        // recorrer cada material
        foreach ($materiales as $mm) {

            $sumacantidad = 0;

            $infoObjeto = ObjEspecifico::where('id', $mm->id_objespecifico)->first();

            // recorrer cada departamento y buscar
            foreach ($presupuesto as $pp) {

                if ($info = P_PresupUnidadDetalle::where('id_presup_unidad', $pp->id)
                    ->where('id_material', $mm->id)
                    ->first()) {
                    $multip = $info->cantidad * $info->periodo;
                    $sumacantidad = $sumacantidad + $multip;
                }
            }

            $total = number_format((float)($sumacantidad * $mm->costo), 2, '.', ',');

            if($sumacantidad > 0){
                $dataArray[] = [
                    'codigo' => $infoObjeto->codigo,
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

        return collect($dataArray);
    }

    public function headings(): array
    {
        return ["COD. ESPECIFICO", "NOMBRE", "CANTIDAD", "PRECIO UNITARIO", "TOTAL"];
    }
}

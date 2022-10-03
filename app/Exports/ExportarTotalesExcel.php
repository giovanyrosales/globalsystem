<?php

namespace App\Exports;

use App\Models\ObjEspecifico;
use App\Models\P_Materiales;
use App\Models\P_PresupUnidad;
use App\Models\P_PresupUnidadDetalle;
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
        $presupuesto = P_PresupUnidad::where('id_anio', $this->anio)
            ->where('id_estado', 3) // solo Presupuestos Aprobados
            ->orderBy('id', 'ASC')
            ->get();

        $dataArray = array();

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

            if ($sumacantidad > 0) {
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

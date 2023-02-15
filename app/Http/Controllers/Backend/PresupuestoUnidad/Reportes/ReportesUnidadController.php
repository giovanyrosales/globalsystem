<?php

namespace App\Http\Controllers\Backend\PresupuestoUnidad\Reportes;

use App\Http\Controllers\Controller;
use App\Models\ObjEspecifico;
use App\Models\P_AnioPresupuesto;
use App\Models\P_Departamento;
use App\Models\P_PresupUnidad;
use App\Models\P_PresupUnidadDetalle;
use App\Models\P_UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportesUnidadController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    // generar pdf con el catalogo de materiales de cada unidad. Esto cada unidad puede sacarlo
    public function pdfCatalogoMaterialesUnidad($idpresup){

        $infoPresuUni = P_PresupUnidad::where('id', $idpresup)->first();
        $infoAnio = P_AnioPresupuesto::where('id', $infoPresuUni->id_anio)->first();
        $infoDepa = P_Departamento::where('id', $infoPresuUni->id_departamento)->first();

        $arrayCatalogo = DB::table('p_presup_unidad_detalle AS pud')
            ->join('p_materiales AS m', 'pud.id_material', '=', 'm.id')
            ->select('m.descripcion', 'pud.cantidad', 'pud.precio', 'pud.periodo', 'm.id_objespecifico',
                        'm.id_unidadmedida')
            ->where('id_presup_unidad', $idpresup)
            ->orderBy('m.descripcion', 'ASC')
            ->get();

        $totalGlobal = 0;

        foreach ($arrayCatalogo as $dd){

            $infoObj = ObjEspecifico::where('id', $dd->id_objespecifico)->first();
            $infoUnidad = P_UnidadMedida::where('id', $dd->id_unidadmedida)->first();

            $dd->objcodigo = $infoObj->codigo;
            $dd->unidadmedida = $infoUnidad->nombre;

            $multifila = ($dd->cantidad * $dd->precio) * $dd->periodo;

            $totalGlobal += $multifila;

            $dd->precio = '$' . number_format((float)$dd->precio, 2, '.', ',');

            $dd->totalfila = '$' . number_format((float)$multifila, 2, '.', ',');
        }

        $totalGlobal = '$' . number_format((float)$totalGlobal, 2, '.', ',');

        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        $mpdf->SetTitle('Catálogo de Materiales');
        $stylesheet = file_get_contents('css/csspresupuesto.css');

        // mostrar errores
        $mpdf->showImageErrors = false;
        $logoalcaldia = 'images/logo.png';

        $tabla = "<div class='content'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            CATÁLOGO DE MATERIALES
            </p>
            </div>";

        $tabla .= "
                <p><strong>Año: $infoAnio->nombre</strong></p>
                <p><strong>Departamento: $infoDepa->nombre</strong></p>";

        // recorrer rubros que tenga dinero

        $tabla .= "<table id='tablaFor' style='width: 100%'>
                <tbody>
                <tr>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>OBJ. ESPEC.</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>MATERIAL</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>CANTIDAD</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>PRECIO</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>PERIODO</th>
                    <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold'>TOTAL</th>
                </tr>";

            foreach ($arrayCatalogo as $dataRR){
                    $tabla .= "<tr>
                        <td style='font-size:11px; text-align: center; font-weight: normal'>$dataRR->objcodigo</td>
                        <td style='font-size:11px; text-align: center; font-weight: normal'>$dataRR->descripcion</td>
                        <td style='font-size:11px; text-align: center; font-weight: normal'>$dataRR->cantidad</td>
                        <td style='font-size:11px; text-align: center; font-weight: normal'>$dataRR->precio</td>
                        <td style='font-size:11px; text-align: center; font-weight: normal'>$dataRR->periodo</td>
                        <td style='font-size:11px; text-align: center; font-weight: normal'>$dataRR->totalfila</td>
                    </tr>";
            }

        $tabla .= "<tr>
                        <td colspan='5' style=';font-size:12px; text-align: center; font-weight: bold'>TOTAL</td>
                        <td style='font-size:12px; text-align: center; font-weight: bold'>$totalGlobal</td>
                    </tr>";


        $tabla .= "</tbody></table>";

        $stylesheet = file_get_contents('css/csspdftotales.css');
        $mpdf->WriteHTML($stylesheet, 1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');

        $mpdf->WriteHTML($tabla, 2);
        $mpdf->Output();
    }


}

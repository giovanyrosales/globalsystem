<?php

namespace App\Http\Controllers\Backend\Pdf;

use App\Http\Controllers\Controller;
use App\Models\CatalogoMateriales;
use App\Models\FuenteRecursos;
use App\Models\Partida;
use App\Models\PartidaDetalle;
use App\Models\Proyecto;
use App\Models\UnidadMedida;
use Carbon\Carbon;

class ControlPdfController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    public function generarPrespuestoPdf($id){

        // obtener todas los presupuesto por tipo_partida
        // 1- Materiales
        // 2- Herramientas (2% de Materiales)
        // 3- Mano de obra (Por Administración)
        // 4- Aporte Mano de Obra
        // 5- Alquiler de Maquinaria
        // 6- Transporte de Concreto Fresco

        $partida1 = Partida::where('proyecto_id', $id)
            ->whereIn('tipo_partida', [1, 2, 5])
            ->orderBy('id', 'ASC')
            ->get();

        $infoPro = Proyecto::where('id', $id)->first();

        if ($infoFuenteR = FuenteRecursos::where('id', $infoPro->id_fuenter)->first()) {
            $fuenter = $infoFuenteR->nombre;
        } else {
            $fuenter = "";
        }

        $resultsBloque = array();
        $index = 0;
        $resultsBloque3 = array();
        $index3 = 0;

        // Fechas
        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $fecha = Carbon::parse(Carbon::now());
        $anio = Carbon::now()->format('Y');
        $mes = $meses[($fecha->format('n')) - 1] . " del " . $anio;

        $item = 0;
        $sumaMateriales = 0;

        foreach ($partida1 as $secciones) {
            array_push($resultsBloque, $secciones);
            $item = $item + 1;
            $secciones->item = $item;

            $secciones->cantidadp = number_format((float)$secciones->cantidadp, 2, '.', ',');

            $detalle1 = PartidaDetalle::where('partida_id', $secciones->id)->get();

            $total = 0;

            foreach ($detalle1 as $lista) {

                $infomaterial = CatalogoMateriales::where('id', $lista->material_id)->first();
                $infomedida = UnidadMedida::where('id', $infomaterial->id_unidadmedida)->first();

                $lista->medida = $infomedida->medida;
                if ($lista->duplicado != 0) {
                    $lista->material = $infomaterial->nombre . " (" . $lista->duplicado . ")";
                } else {
                    $lista->material = $infomaterial->nombre;
                }

                if($lista->duplicado > 0){
                    $multi = ($lista->cantidad * $infomaterial->pu) * $lista->duplicado;
                }else{
                    $multi = $lista->cantidad * $infomaterial->pu;
                }

                $lista->cantidad = number_format((float)$lista->cantidad, 2, '.', ',');
                $lista->pu = "$" . number_format((float)$infomaterial->pu, 2, '.', ',');
                $lista->subtotal = "$" . number_format((float)$multi, 2, '.', ',');

                // se sumara solo materiales
                if($secciones->tipo_partida == 1){
                    $sumaMateriales = $sumaMateriales + $multi;
                }

                $total = $total + $multi;
            }

            $secciones->total = "$" . number_format((float)$total, 2, '.', ',');

            $resultsBloque[$index]->bloque1 = $detalle1;
            $index++;
        }

        $manoobra = Partida::where('proyecto_id', $id)
            ->where('tipo_partida', 3)
            ->orderBy('id', 'ASC')
            ->get();

        $totalManoObra = 0;

        foreach ($manoobra as $secciones3) {
            array_push($resultsBloque3, $secciones3);
            $item = $item + 1;
            $secciones3->item = $item;

            $secciones3->cantidadp = number_format((float)$secciones3->cantidadp, 2, '.', ',');

            $detalle3 = PartidaDetalle::where('partida_id', $secciones3->id)->get();

            $total3 = 0;

            foreach ($detalle3 as $lista) {

                $infomaterial = CatalogoMateriales::where('id', $lista->material_id)->first();
                $infomedida = UnidadMedida::where('id', $infomaterial->id_unidadmedida)->first();

                $lista->medida = $infomedida->medida;
                if ($lista->duplicado != 0) {
                    $lista->material = $infomaterial->nombre . " (" . $lista->duplicado . ")";
                } else {
                    $lista->material = $infomaterial->nombre;
                }

                $multi = $lista->cantidad * $infomaterial->pu;
                if ($lista->duplicado != 0) {
                    $multi = $multi * $lista->duplicado;
                }

                $lista->cantidad = number_format((float)$lista->cantidad, 2, '.', ',');
                $lista->pu = "$" . number_format((float)$infomaterial->pu, 2, '.', ',');
                $lista->subtotal = "$" . number_format((float)$multi, 2, '.', ',');

                $totalManoObra = $totalManoObra + $multi;
                $total3 = $total3 + $multi;
            }

            $secciones3->total = "$" . number_format((float)$total3, 2, '.', ',');

            $resultsBloque3[$index3]->bloque3 = $detalle3;
            $index3++;
        }

        // APORTE DE MANO DE OBRA

        $aporteManoObra = Partida::where('proyecto_id', $id)
            ->where('tipo_partida', 4)
            ->get();

        $totalAporteManoObra = 0;

        foreach ($aporteManoObra as $secciones3) {

            $detalle4 = PartidaDetalle::where('partida_id', $secciones3->id)->get();

            foreach ($detalle4 as $lista) {

                $infomaterial = CatalogoMateriales::where('id', $lista->material_id)->first();
                if ($lista->duplicado != 0) {
                    $lista->material = $infomaterial->nombre . " (" . $lista->duplicado . ")";
                } else {
                    $lista->material = $infomaterial->nombre;
                }

                $multi = $lista->cantidad * $infomaterial->pu;

                $totalAporteManoObra = $totalAporteManoObra + $multi;
            }
        }

        // ALQUILER DE MAQUINARIA

        $alquilerMaquinaria = Partida::where('proyecto_id', $id)
            ->where('tipo_partida', 5)
            ->get();

        $totalAlquilerMaquinaria = 0;

        foreach ($alquilerMaquinaria as $secciones3) {

            $detalle4 = PartidaDetalle::where('partida_id', $secciones3->id)->get();

            foreach ($detalle4 as $lista) {

                $infomaterial = CatalogoMateriales::where('id', $lista->material_id)->first();
                $lista->material = $infomaterial->nombre;
                $multi = $lista->cantidad * $infomaterial->pu;

                $totalAlquilerMaquinaria = $totalAporteManoObra + $multi;
            }
        }

        // TRANSPORTE CONCRETO FRESCO

        $trasportePesado = Partida::where('proyecto_id', $id)
            ->where('tipo_partida', 6)
            ->get();

        $totalTransportePesado = 0;

        foreach ($trasportePesado as $secciones3) {

            $detalle4 = PartidaDetalle::where('partida_id', $secciones3->id)->get();

            foreach ($detalle4 as $lista) {

                $infomaterial = CatalogoMateriales::where('id', $lista->material_id)->first();
                $lista->material = $infomaterial->nombre;
                $multi = $lista->cantidad * $infomaterial->pu;

                $totalTransportePesado = $totalAporteManoObra + $multi;
            }
        }

        $afp = ($totalManoObra * 7.75) / 100;
        $isss = ($totalManoObra * 7.5) / 100;
        $insaforp = ($totalManoObra * 1) / 100;

        $totalDescuento = ($afp + $isss + $insaforp);
        $herramienta2Porciento = ($sumaMateriales * 2) / 100;

        // subtotal del presupuesto partida
        $subtotalPartida = ($sumaMateriales + $herramienta2Porciento + $totalManoObra + $totalDescuento
            + $totalAlquilerMaquinaria + $totalTransportePesado);

        // imprevisto del 5%
        $imprevisto = ($subtotalPartida * 5) / 100;

        // total de la partida final
        $totalPartidaFinal = $subtotalPartida + $imprevisto;

        $totalDescuento = "$" . number_format((float)$totalDescuento, 2, '.', ',');
        $afp = "$" . number_format((float)$afp, 2, '.', ',');
        $isss = "$" . number_format((float)$isss, 2, '.', ',');
        $insaforp = "$" . number_format((float)$insaforp, 2, '.', ',');
        $sumaMateriales = "$" . number_format((float)$sumaMateriales, 2, '.', ',');
        $herramienta2Porciento = "$" . number_format((float)$herramienta2Porciento, 2, '.', ',');
        $totalManoObra = "$" . number_format((float)$totalManoObra, 2, '.', ',');

        $totalAporteManoObra = "$" . number_format((float)$totalAporteManoObra, 2, '.', ',');
        $totalAlquilerMaquinaria = "$" . number_format((float)$totalAlquilerMaquinaria, 2, '.', ',');
        $totalTransportePesado = "$" . number_format((float)$totalTransportePesado, 2, '.', ',');
        $subtotalPartida = "$" . number_format((float)$subtotalPartida, 2, '.', ',');
        $imprevisto = "$" . number_format((float)$imprevisto, 2, '.', ',');
        $totalPartidaFinal = "$" . number_format((float)$totalPartidaFinal, 2, '.', ',');

        $mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf->SetTitle('Presupuesto -' . $mes);

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/logo2.png';

        $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            Fondo: $fuenter <br>
            Hoja de presupuesto <br>
            Fecha: $mes <br></p>
            </div>";

        $tabla .= "<table width='100%' id='tablaFor'>
            <tbody>";

        foreach ($partida1 as $dd) {

            if ($partida1->last() == $dd) {
                $tabla .= "<tr>
                    <td width='100%' colspan='6'></td>
                    </tr>>";
            }

            $tabla .= "<tr>
                    <td colspan='1' width='10%'>Item</td>
                    <td colspan='3' width='30%'>Partida</td>
                    <td colspan='2' width='20%'>Cantidad P.</td>
                </tr>

                <tr>
                    <td colspan='1' width='10%'>$dd->item</td>
                    <td colspan='3' width='30%'>$dd->nombre</td>
                    <td colspan='2' width='20%'>$dd->cantidadp</td>
                </tr>

                <tr>
                    <td width='15%'>Material</td>
                    <td width='11'>U/M</td>
                    <td width='12%'>Cantidad</td>
                    <td width='10%'>P.U</td>
                    <td width='12%'>Sub Total</td>
                    <td width='20%'>Total</td>
                </tr>
                ";

            foreach ($dd->bloque1 as $gg) {

                $tabla .= "<tr>
                    <td width='15%'>$gg->material</td>
                    <td width='10%'>$gg->medida</td>
                    <td width='10%'>$gg->cantidad</td>
                    <td width='10%'>$gg->pu</td>
                    <td width='12%'>$gg->subtotal</td>
                    <td width='20%'></td>
                </tr>";

                if ($dd->bloque1->last() == $gg) {
                    $tabla .= "
                        <tr>
                            <td width='15%'></td>
                            <td width='10%'></td>
                            <td width='10%'></td>
                            <td width='10%'></td>
                            <td width='10%'></td>
                            <td width='20%' style='font-weight: bold; size: 15px'>$dd->total</td>
                        </tr>";
                }
            }
        }

        $tabla .= "</tbody></table>";


        $tabla .= "<table id='tablaFor' style='width: 100%'><tbody>";

        $vuelta = false;

        foreach ($manoobra as $dd) {

            if ($vuelta) {
                $tabla .= "<tr>
                    <td width = '100%' colspan='6'></td>
                </tr>";
            }

            $vuelta = true;

            $tabla .= "<tr>
                <td colspan='6'>MANO DE OBRA POR ADMINISTRACIÓN</td>
            </tr>

            <tr>
                <td colspan='1' width='10%'>Item</td>
                <td colspan='3' width='30%'>Partida</td>
                <td colspan='2' width='20%'>Cantidad P.</td>
            </tr>

            <tr>
                <td colspan='1' width='10%'>$dd->item</td>
                <td colspan='3' width='30%'>$dd->nombre</td>
                <td colspan='2' width='20%'>$dd->cantidadp</td>
            </tr>

            <tr>
                <td width='15%'>Material</td>
                <td width='11'>U/M</td>
                <td width='12%'>Cantidad</td>
                <td width='10%'>P.U</td>
                <td width='12%'>Sub Total</td>
                <td width='20%'>Total</td>
            </tr>
            ";

            foreach ($dd->bloque3 as $gg) {

                $tabla .= "
                <tr>
                    <td width='15%'>$gg->material</td>
                    <td width='10%'>$gg->medida</td>
                    <td width='10%'>$gg->cantidad</td>
                    <td width='10%'>$gg->pu</td>
                    <td width='12%'>$gg->subtotal</td>
                    <td width='20%'></td>
                </tr>
                ";

                if ($dd->bloque3->last() == $gg) {

                    $tabla .= "<tr>
                        <td width='15%'></td>
                        <td width='10%'></td>
                        <td width='10%'></td>
                        <td width='10%'></td>
                        <td width='10%'></td>
                        <td width='20%' style='font-weight: bold'>$dd->total</td>
                    </tr>";
                }
            }
        }

        $tabla .="</tbody>
            </table>
            <br>
            <br>";

        $tabla .= "<table id='tablaFor' style='width: 100%'><tbody>";

        $tabla .= "
        <tr>
            <td colspan='3'>APORTE PATRONAL</td>
        </tr>

        <tr>
            <td width='20%'>Descripción</td>
            <td width='12%'>Sub Total</td>
            <td width='20%'>Total</td>
        </tr>

        <tr>
            <td width='20%'>ISSS (7.5% mano de obra)</td>
            <td width='12%'>$isss</td>
            <td width='20%'></td>
        </tr>
        <tr>
            <td width='20%'>AFP (7.75% mano de obra)</td>
            <td width='12%'>$afp</td>
            <td width='20%'></td>
        </tr>
        <tr>
            <td width='20%'>INSAFOR (1.0% mano de obra)</td>
            <td width='12%'>$insaforp</td>
            <td width='20%'></td>
        </tr>

        <tr>
            <td width='20%'></td>
            <td width='12%'></td>
            <td width='20%'><strong>$totalDescuento</strong></td>
        </tr>
    </tbody>
</table>";


        $tabla2 = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            Fondo: " . $fuenter . " <br>
            Hoja de presupuesto <br>
            Fecha: " . $mes . " <br></p>
            </div>";

        $tabla2 .= "<table style='width: 75%; margin: 0 auto' id='tablaFor'>
            <tbody>

             <tr>
        <td colspan='2'>RESUMEN DE PARTIDA</td>
    </tr>

    <tr>
        <td width='20%'>MATERIALES</td>
        <td width='12%'>$sumaMateriales</td>
    </tr>

    <tr>
        <td width='20%'>HERRAMIENTA (2% DE MAT.)</td>
        <td width='12%'>$herramienta2Porciento</td>
    </tr>

    <tr>
        <td width='20%'>ALQUILER DE MAQUINARIA</td>
        <td width='12%'>$totalAlquilerMaquinaria</td>
    </tr>

    <tr>
        <td width='20%'>MANO DE OBRA (POR ADMINISTRACIÓN)</td>
        <td width='12%'>$totalManoObra</td>
    </tr>

     <tr>
        <td width='20%'>APORTE MANO DE OBRA (PATRONAL)</td>
        <td width='12%'>$totalDescuento</td>
    </tr>

     <tr>
        <td width='20%'>TRANSPORTE DE CONCRETO FRESCO</td>
        <td width='12%'>$totalTransportePesado</td>
    </tr>

    <tr>
        <td width='20%' style='font-weight: bold'>SUB TOTAL</td>
        <td width='12%' style='font-weight: bold'>$subtotalPartida</td>
    </tr>

    <tr>
        <td width='20%' style='font-weight: bold'>IMPREVISTOS (5% de sub total)</td>
        <td width='12%' style='font-weight: bold'>$imprevisto</td>
    </tr>

    <tr>
        <td width='20%' style='font-weight: bold'>TOTAL</td>
        <td width='12%' style='font-weight: bold'>$totalPartidaFinal</td>
    </tr>
    </tbody>
</table> ";


        $stylesheet = file_get_contents('css/csspresupuesto.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla,2);
        $mpdf->AddPage();
        $mpdf->WriteHTML($tabla2,2);

        $mpdf->Output();
    }


}

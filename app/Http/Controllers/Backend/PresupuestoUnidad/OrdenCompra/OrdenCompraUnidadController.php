<?php

namespace App\Http\Controllers\Backend\PresupuestoUnidad\OrdenCompra;

use App\Http\Controllers\Controller;
use App\Models\ActaUnidad;
use App\Models\Administradores;
use App\Models\CotizacionUnidad;
use App\Models\CotizacionUnidadDetalle;
use App\Models\CuentaUnidad;
use App\Models\InformacionConsolidador;
use App\Models\ObjEspecifico;
use App\Models\OrdenUnidad;
use App\Models\P_AnioPresupuesto;
use App\Models\P_Departamento;
use App\Models\P_Materiales;
use App\Models\P_PresupUnidad;
use App\Models\P_UnidadMedida;
use App\Models\P_UsuarioDepartamento;
use App\Models\Proveedores;
use App\Models\Referencias;
use App\Models\RequisicionAgrupada;
use App\Models\RequisicionUnidad;
use App\Models\RequisicionUnidadDetalle;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use function Illuminate\Events\queueable;

// Definir zona horaria o region.
date_default_timezone_set('America/El_Salvador');
setlocale(LC_TIME, "spanish");


class OrdenCompraUnidadController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function generarOrdenCompraUnidades(Request $request){

        $regla = array(
            'idcoti' => 'required',
            'fecha' => 'required',
            'numacta' => 'required',
            'numacuerdo' => 'required',
            'referencia' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        DB::beginTransaction();
        try {

            if($infoOrden = OrdenUnidad::where('id_cotizacion', $request->idcoti)->first()){
                // YA ESTA REGISTRADA Y NO HACER NADA

                $idorden = $infoOrden->id;

            }else{
                $or = new OrdenUnidad();
                $or->id_cotizacion = $request->idcoti;
                $or->fecha_orden = $request->fecha;
                $or->numero_acta = $request->numacta;
                $or->numero_acuerdo = $request->numacuerdo;
                $or->id_referencia = $request->referencia;
                $or->codigo_proyecto = $request->codigoproy;
                $or->save();

                $idorden = $or->id;

                DB::commit();
            }

            return ['success' => 1, 'id' => $idorden];

        }catch(\Throwable $e){
            Log::info('error: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // generar PDF de orden de compra de unidades y variable {cantidad} es # de material por hoja
    public function vistaPdfOrdenUnidad($id, $cantidad){ // id de la orden

        $orden = OrdenUnidad::where('id', $id)->first();
        $idorden = $orden->id;

        $anioOrden = date("Y", strtotime($orden->fecha_orden));


        $cotizacion = CotizacionUnidad::where('id', $orden->id_cotizacion)->first();
        $proveedor =  Proveedores::where('id',  $cotizacion->id_proveedor)->first();
        $det_cotizacion = CotizacionUnidadDetalle::where('id_cotizacion_unidad',  $orden->id_cotizacion)->get();

        $infoAgrupada = RequisicionAgrupada::where('id', $cotizacion->id_agrupado)->first();
        $destino = $infoAgrupada->nombreodestino;

        $codigoproyecto = $orden->codigo_proyecto;

        $lugarDeEntrega = $infoAgrupada->lugar;
        $formaDePago = $infoAgrupada->forma;
        $plazoEntrega = $infoAgrupada->plazo;
        $otrosPresentar = $infoAgrupada->otros;


        $nombreAdminContrato = "";

        if($infoAd = Administradores::where('id', $infoAgrupada->id_contrato)->first()){
            $nombreAdminContrato = $infoAd->nombre;
        }

        $textoReferencia = "";


        // REFERENCIA
        if($infoReferencia = Referencias::where('id', $orden->id_referencia)->first()){

            $ceros = str_repeat("0", 5);

            if ($idorden <= 9) {
                $numerales = $ceros[0] . $ceros[1] . $ceros[2] . $ceros[3] . $ceros[4] . $idorden;
            } else if ($idorden <= 99) {
                $numerales = $ceros[0] . $ceros[1] . $ceros[2] . $ceros[3] . $idorden;
            } else if ($idorden <= 999) {
                $numerales = $ceros[0] . $ceros[1] . $ceros[2] . $idorden;
            }
            else if ($idorden <= 9999) {
                $numerales = $ceros[0] . $ceros[1] . $idorden;
            }
            else if ($idorden <= 99999) {
                $numerales = $ceros[0] . $idorden;
            }
            else {
                $numerales = $idorden;
            }

            $textoReferencia = $infoReferencia->nombre . "-" . $numerales . "-" . $anioOrden . "AMM";

        }


        $total = 0;

        $dataArray = array();
        $dataArrayCodigo = array();

        $arraycodigos = "";

        foreach ($det_cotizacion as $dd){

            $infoRequiDetalle = RequisicionUnidadDetalle::where('id', $dd->id_requi_unidaddetalle)->first();
            $infoMaterial = P_Materiales::where('id', $infoRequiDetalle->id_material)->first();
            $infoObjeto = ObjEspecifico::where('id', $infoMaterial->id_objespecifico)->first();

            if(strlen($infoMaterial->descripcion) >= 25){
                $subcadena = substr($dd->descripcion, 0, 25);
            }else{
                $subcadena = $dd->descripcion;
            }

            $multi = $dd->cantidad * $dd->precio_u;
            $total = $total + $multi;

            $precioFormat = number_format((float)$dd->precio_u, 2, '.', ',');
            $multiFormat = number_format((float)$multi, 2, '.', ',');

            $dataArray[] = [
                'cantidad' => $dd->cantidad,
                'nombre' => $subcadena,
                'cod_presup' => $infoObjeto->codigo,
                'precio_u' => $precioFormat,
                'multi' => $multiFormat
            ];

            $boolCo = true;
            $_suma = 0;

            // verificar si el codigo existe
            foreach($dataArrayCodigo as $info){
                if($info['codigo'] == $infoObjeto->codigo){
                    $boolCo = false;
                    // sí existe, solo hacer break
                    break;
                }
            }

            if($boolCo){
                foreach ($det_cotizacion as $_deta){

                    $_infoRequiDetalle = RequisicionUnidadDetalle::where('id', $_deta->id_requi_unidaddetalle)->first();
                    $_infoMaterial = P_Materiales::where('id', $_infoRequiDetalle->id_material)->first();
                    $_infoObjeto = ObjEspecifico::where('id', $_infoMaterial->id_objespecifico)->first();

                    if($_infoObjeto->codigo == $infoObjeto->codigo){
                        $multi = $_deta->cantidad * $_deta->precio_u;
                        $_suma += $multi;
                    }
                }

                $_suma = number_format((float)$_suma, 2, '.', ',');

                // para no volver a repetir codigo
                $dataArrayCodigo[] = [
                    'codigo' => $infoObjeto->codigo,
                ];

                $arraycodigos .= " " . $infoObjeto->codigo . "=" . " $" . $_suma;
            }
        }

        // PASAR A LETRAS

        $formatterES = new \NumberFormatter("es-ES", \NumberFormatter::SPELLOUT);
        $izquierda = intval(floor($total));
        //$derecha = intval(($total - floor($total)) * 100);
        $totalEnLetras = strtoupper($formatterES->format($izquierda)) . " DOLARES";


        // OBTENER SOLO LA PARTE DECIMAL
        $decimales = explode(".",number_format((float)$total, 2, '.', ','));
        $totalSoloDecimal = $decimales[1];

        $total = number_format((float)$total, 2, '.', ',');




        //$fecha = strftime("%d-%B-%Y", strtotime($orden->fechaorden));
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $fecha = array(date("d", strtotime($orden->fecha_orden) ), $meses[date("n", strtotime($orden->fecha_orden) )-1], date("Y", strtotime($orden->fecha_orden) ) );

        $dia = $fecha[0];
        $mes = $fecha[1];

        $anio = substr($fecha[2], -2);

        Carbon::now()->format('y');




        $acta_acuerdo = "Acta #" . $orden->numero_acta . " Acuerdo #" . $orden->numero_acuerdo;


        // Informacion del consolidador

        $cargoConsolidador = "";
        $nombreConsolidador = "";
        $depaConsolidador = "";


        if($datoConsolidador = InformacionConsolidador::where('id_usuario', $infoAgrupada->id_usuario)->first()){

            $infoUsuario = Usuario::where('id', $datoConsolidador->id_usuario)->first();
            $infoDepa = P_Departamento::where('id', $datoConsolidador->id_departamento)->first();


            $nombreConsolidador = $infoUsuario->nombre;
            $depaConsolidador = $infoDepa->nombre;
            $cargoConsolidador = $datoConsolidador->cargo;
        }



        $pdf = PDF::loadView('backend.admin.presupuestounidad.reportes.pdfordencompraunidades', compact('orden',
            'cotizacion', 'dia','mes', 'anio','proveedor','dataArray',
             'total', 'idorden', 'arraycodigos',  'acta_acuerdo',
                'destino', 'cargoConsolidador', 'nombreConsolidador', 'depaConsolidador',
            'codigoproyecto', 'totalEnLetras', 'totalSoloDecimal', 'nombreAdminContrato', 'formaDePago',
                'lugarDeEntrega', 'plazoEntrega', 'otrosPresentar', 'textoReferencia'));
        //$customPaper = array(0,0,470.61,612.36);
        //$customPaper = array(0,0,470.61,612.36);
        $pdf->setPaper('Letter', 'portrait')->setWarnings(false);
        return $pdf->stream('Orden_Compra.pdf');
    }




    public function vistaAñoOrdenesComprasUnidadesAprobadas(){
        $anios = P_AnioPresupuesto::orderBy('id', 'DESC')->get();

        return view('backend.admin.presupuestounidad.ordenes.ordenesaprobadas.vistaanioordenesaprobadasunidades', compact('anios'));
    }


    // retorna vista con las ordenes de compras para unidades
    public function indexOrdenesComprasAprobadasUnidades($idanio){

        return view('backend.admin.presupuestounidad.ordenes.ordenesaprobadas.vistaordenesunidadcompraaprobada', compact('idanio'));
    }

    // retorna tabla con las ordenes de compras
    public function tablaOrdenesComprasAprobadasUnidades($idanio){

        $infoAnio = P_AnioPresupuesto::where('id', $idanio)->first();

        $arrayOrdenUnidad = OrdenUnidad::whereYear('fecha_orden', $infoAnio->nombre)
            ->orderBy('fecha_orden')
            ->get();

        foreach($arrayOrdenUnidad as $info){

            $info->fecha_orden = date("d-m-Y", strtotime($info->fecha_orden));

            $idorden = $info->id;

            $textoReferencia = "";

            $anioOrden = date("Y", strtotime($info->fecha_orden));

            // REFERENCIA
            if($infoReferencia = Referencias::where('id', $info->id_referencia)->first()){

                $ceros = str_repeat("0", 5);

                if ($idorden <= 9) {
                    $numerales = $ceros[0] . $ceros[1] . $ceros[2] . $ceros[3] . $ceros[4] . $idorden;
                } else if ($idorden <= 99) {
                    $numerales = $ceros[0] . $ceros[1] . $ceros[2] . $ceros[3] . $idorden;
                } else if ($idorden <= 999) {
                    $numerales = $ceros[0] . $ceros[1] . $ceros[2] . $idorden;
                }
                else if ($idorden <= 9999) {
                    $numerales = $ceros[0] . $ceros[1] . $idorden;
                }
                else if ($idorden <= 99999) {
                    $numerales = $ceros[0] . $idorden;
                }
                else {
                    $numerales = $idorden;
                }

                $textoReferencia = $infoReferencia->nombre . "-" . $numerales . "-" . $anioOrden . "AMM";

            }


            $info->referencia = $textoReferencia;



            $hayActa = 0;
            $idActa = 0;
            if($infoacta = ActaUnidad::where('id_ordenunidad', $info->id)->first()){
              $hayActa = 1;
              $idActa = $infoacta->id;
            }

            $info->idActa = $idActa;
            $info->hayActa = $hayActa;
        }

        return view('backend.admin.presupuestounidad.ordenes.ordenesaprobadas.tablaordenesunidadcompraaprobada', compact('arrayOrdenUnidad'));
    }

    // vista detalle de una cotización unidad, esto se mira desde las ordenes de compra
    public function vistaDetalleCotizacionUnidadOrden($idcoti){
        // id de cotizacion

        $infoCotizacion = CotizacionUnidad::where('id', $idcoti)->first();
        $infoAgrupado = RequisicionAgrupada::where('id', $infoCotizacion->id_agrupado)->first();
        $infoProveedor = Proveedores::where('id', $infoCotizacion->id_proveedor)->first();

        $proveedor = $infoProveedor->nombre;

        $infoCotiDetalle = CotizacionUnidadDetalle::where('id_cotizacion_unidad', $infoCotizacion->id)->get();
        $conteo = 0;
        $fecha = date("d-m-Y", strtotime($infoCotizacion->fecha));

        $totalCantidad = 0;
        $totalPrecio = 0;
        $totalTotal = 0;

        foreach ($infoCotiDetalle as $de){

            $conteo += 1;
            $de->conteo = $conteo;

            $multi = $de->cantidad * $de->precio_u;
            $totalCantidad = $totalCantidad + $de->cantidad;
            $totalPrecio = $totalPrecio + $de->precio_u;
            $totalTotal = $totalTotal + $multi;

            $infoRequiDetalle = RequisicionUnidadDetalle::where('id', $de->id_requi_unidaddetalle)->first();
            $infoMaterial = P_Materiales::where('id', $infoRequiDetalle->id_material)->first();

            $infoUnidad = P_UnidadMedida::where('id', $infoMaterial->id_unidadmedida)->first();
            $de->unidadmedida = $infoUnidad->nombre;

            $infoObjeto = ObjEspecifico::where('id', $infoMaterial->id_objespecifico)->first();
            $de->objeto = $infoObjeto->codigo . " - " . $infoObjeto->nombre;

            $de->precio_u = number_format((float)$de->precio_u, 2, '.', ',');
            $de->total = number_format((float)$multi, 2, '.', ',');
        }

        $totalCantidad = number_format((float)$totalCantidad, 2, '.', ',');
        $totalPrecio = number_format((float)$totalPrecio, 2, '.', ',');
        $totalTotal = number_format((float)$totalTotal, 2, '.', ',');

        return view('backend.admin.presupuestounidad.ordenes.detalleorden.vistadetalleordenunidad', compact('idcoti', 'infoAgrupado',
            'proveedor', 'infoCotiDetalle', 'fecha', 'totalCantidad', 'totalPrecio', 'totalTotal'));
    }


    // generar acta de una orden de compra para unidad
    function generarActadeCompraUnidades(Request $request){

        $regla = array(
            'idorden' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }


        // EVITAR DUPLICADOS
        if($info = ActaUnidad::where('id_ordenunidad', $request->idorden)->first()){
            return ['success' => 1, 'actaid'=> $info->id];
        }else{
            $acta = new ActaUnidad();
            $acta->id_ordenunidad = $request->idorden;

            if($acta->save()) {
                return ['success' => 1, 'actaid'=> $acta->id];
            }else {
                return ['success' => 99];
            }
        }
    }

    // generar PDF de la acta de compra para unidad
    public function reporteActaGeneradaUnidades($id){

        $acta = ActaUnidad::where('id',  $id)->first();
        $orden = OrdenUnidad::where('id',  $acta->id_ordenunidad)->first();
        $cotizacion = CotizacionUnidad::where('id', $orden->id_cotizacion)->first();
        $proveedor =  Proveedores::where('id',  $cotizacion->id_proveedor)->first();
        $administrador = Administradores::where('id',  $orden->id_admin_contrato)->first();




        $lugar = $orden->lugar;


        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf->SetTitle('Acta');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/logo2.png';

        $tabla = "
     <TABLE BORDER>
	<TR>
		<TD ROWSPAN=2> <img id='logo' src='$logoalcaldia'> </TD>
	    	<TD style='padding-left: 20px; font-weight: bold; font-size: 20px;'>ACTA DE RECEPCIONES DE BIENES, SERVICIOS Y OBRAS.</TD>
	</TR>
	<TR>
		<TD style='text-align: center; font-size: 18px; font-weight: bold; padding-top: 15px'>Alcaldía Municipal de Metapán.</TD>
	</TR>
</TABLE>
        ";

        $tabla .= "
        <div style='margin-top: 90px; margin-left: 20px; margin-right: 20px'>
       <label style='font-size: 15px; text-align:justify;'>Reunidos en las instalaciones de </label>
            <label style='font-weight: bold; font-size: 15px;  text-align:justify;'>$lugar</label>
            <label style=' font-size: 15px;  text-align:justify;'>, a las  _______________</label>
            <label style='font-weight: bold; font-size: 15px;  text-align:justify;'></label>
            <label style=' font-size: 15px;  text-align:justify;'>del día  _______________</label>
            <label style=' font-size: 15px;  text-align:justify;'>; con el propósito de hacer entrega formal por parte de </label>
            <label style='font-weight: normal; font-size: 15px;  text-align:justify;'>xxx</label>
            <label style=' font-size: 15px;  text-align:justify;'>.</label></div>
        ";

        $tabla .= " <div style='margin-top: 50px; margin-left: 20px; margin-right: 20px'>
            <label style=' font-size: 15px;  text-align:justify;'>Todo lo correspondiente a la orden No.</label>
            <label style='font-weight: normal; font-size: 15px;  text-align:justify;'>$orden->id</label>
            <label style=' font-size: 15px;  text-align:justify;'> y con base a lo solicitado; presente los señores</label>
            <label style='font-weight: normal; font-size: 15px;  text-align:justify;'>xxx</label>
            <label style=' font-size: 15px;  text-align:justify;'>, por parte del proveedor; </label>
            <label style='font-weight: bold; font-size: 15px;  text-align:justify;'>xxx.</label>
            <label style=' font-size: 15px;  text-align:justify;'> en calidad de administrador de contrato.</label>
            </div>
        ";

        $tabla .= "<div style='margin-top: 50px; margin-left: 20px; margin-right: 20px'>
               <label style='font-size: 15px; text-align:justify;'>Cabe mencionar que dichos bienes, servicios u obras cumple con las especificaciones previamente definidas en el contrato u orden de compra.</label>
        </div>";

        $tabla .= "<div style='margin-top: 50px; margin-left: 20px; margin-right: 20px'>
            <label style='font-size: 15px;  text-align:justify;'>Y no habiendo más que hacer constar, firmamos y ratificamos la presente acta.</label>
        </div>";


         $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: 90px; margin-left: 20px'>
            <tbody>

             <tr>
                    <td width='25%' style='font-weight: normal'>ENTREGA</td>
                    <td width='25%' style='font-weight: normal'>RECIBE</td>
             </tr>

                  </tbody></table>
             ";



        $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: 70px; margin-left: 20px'>
            <tbody>";



        $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold; color: black'>_________________________________</td>
                    <td width='25%' style='font-weight: bold; color: black'>_________________________________</td>
                    </tr>";

        $tabla .= "</tbody></table>";

        $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: 15px; margin-left: 20px'>
            <tbody>";

        $tabla .= "<tr>
                    <td width='25%' style='font-weight: normal; font-size: 14px'>Proveedor: </td>
                    <td width='25%' style='font-weight: normal; font-size: 14px; margin-left: 15px'>Administrador de Contrato</td>
                    </tr>";

        $tabla .= "<tr>
                    <td width='25%' style='font-size: 12px'>xxx</td>
                    <td width='25%' style='font-size: 14px; margin-left: 15px'>xxx</td>
                    </tr>";

        $tabla .= "</tbody></table>";


        $tabla .= "<table width='50%' style='margin-top: 70px; margin-left: 360px'>
            <tbody>";

        $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold; color: black; padding-left: 20px'>_________________________________</td>
                    </tr>";

        $tabla .= "<tr>
                    <td width='25%' style='font-weight: normal; font-size: 14px; padding-left: 90px'>Solicitante</td>
                    </tr>";

        $tabla .= "<tr>
                    <td width='25%' style='font-size: 14px; padding-left: 15px'>Nombre: xxx</td>
                    </tr>";

        $tabla .= "</tbody></table>";







        $stylesheet = file_get_contents('css/cssacta.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->WriteHTML($tabla,2);

        $mpdf->Output();





    }


}

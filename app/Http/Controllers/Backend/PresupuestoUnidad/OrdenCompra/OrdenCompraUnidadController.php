<?php

namespace App\Http\Controllers\Backend\PresupuestoUnidad\OrdenCompra;

use App\Http\Controllers\Controller;
use App\Models\ActaUnidad;
use App\Models\Administradores;
use App\Models\CotizacionUnidad;
use App\Models\CotizacionUnidadDetalle;
use App\Models\CuentaUnidad;
use App\Models\CuentaUnidadRestante;
use App\Models\CuentaUnidadRetenido;
use App\Models\ObjEspecifico;
use App\Models\OrdenUnidad;
use App\Models\P_AnioPresupuesto;
use App\Models\P_Departamento;
use App\Models\P_Materiales;
use App\Models\P_PresupUnidad;
use App\Models\P_UnidadMedida;
use App\Models\Proveedores;
use App\Models\RequisicionUnidad;
use App\Models\RequisicionUnidadDetalle;
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

//Estados de las Ordenes
// 0 - Default
// 1 - Activa
// 2 - Anulada

//Estados de las Cotizaciones
// 0 - Default
// 1 - Aprobada
// 2 - Denegada


class OrdenCompraUnidadController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function generarOrdenCompraUnidades(Request $request){

        $regla = array(
            'fecha' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        if($info = OrdenUnidad::where('id_cotizacion', $request->idcoti)->first()){
            // no hacer nada, evita ordenes duplicadas
            return ['success' => 1, 'id' => $info->id];
        }

        if($infocoti = CotizacionUnidad::where('id', $request->idcoti)->first()){
            if($infocoti->estado != 1){
                // por seguridad solo queremos cotizaciones aprobadas
                return ['success' => 2];
            }
        }

        DB::beginTransaction();
        try {

            $infoCoti = CotizacionUnidad::where('id', $request->idcoti)->first();
            $infoRequi = RequisicionUnidad::where('id', $infoCoti->id_requisicion_unidad)->first();
            $infoPresupUni = P_PresupUnidad::where('id', $infoRequi->id_presup_unidad)->first();
            $infoAnio = P_AnioPresupuesto::where('id', $infoPresupUni->id_anio)->first();

            if($infoAnio->permiso == 0){
                // sin permiso
                return ['success' => 3];
            }

            $or = new OrdenUnidad();
            $or->id_admin_contrato = $request->admin;
            $or->id_cotizacion = $request->idcoti;
            $or->fecha_orden = $request->fecha;
            $or->lugar = $request->lugar;
            $or->estado = 0;
            $or->fecha_anulada = null;
            $or->save();

            $detalle = CotizacionUnidadDetalle::where('id_cotizacion_unidad', $request->idcoti)->get();

            foreach ($detalle as $dd){

                $infoRequiDeta = RequisicionUnidadDetalle::where('id', $dd->id_requi_unidaddetalle)->first();
                $infoMaterial = P_Materiales::where('id', $infoRequiDeta->id_material)->first();

                $infoCuenta = CuentaUnidad::where('id_presup_unidad', $infoPresupUni->id)
                    ->where('id_objespeci', $infoMaterial->id_objespecifico)
                    ->first();

                $cuenta = new CuentaUnidadRestante();
                $cuenta->id_cuenta_unidad = $infoCuenta->id;
                $cuenta->id_requi_detalle = $infoRequiDeta->id;
                $cuenta->id_ordenunidad = $or->id;
                $cuenta->save();

                // BORRAR MATERIAL RETENIDO, pero si es anulada, volvera a retenido

                CuentaUnidadRetenido::where('id_cuenta_unidad', $infoCuenta->id)
                    ->where('id_requi_detalle', $infoRequiDeta->id)
                    ->delete();
            }

            DB::commit();
            return ['success' => 1, 'id' => $or->id];

        }catch(\Throwable $e){
            Log::info('error: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // generar PDF de orden de compra de unidades y variable {cantidad} es # de material por hoja
    public function vistaPdfOrdenUnidad($id, $cantidad){ // id de la orden

        $orden = OrdenUnidad::where('id', $id)->first();

        $cotizacion = CotizacionUnidad::where('id', $orden->id_cotizacion)->first();
       // $requisicion = RequisicionUnidad::where('id',  $cotizacion->id_requisicion_unidad)->first();
        $proveedor =  Proveedores::where('id',  $cotizacion->id_proveedor)->first();
        $administrador = Administradores::where('id',  $orden->id_admin_contrato)->first();
        $det_cotizacion = CotizacionUnidadDetalle::where('id_cotizacion_unidad',  $orden->id_cotizacion)->get();

        $total = 0;

        $dataArray = array();
        $array_merged = array();
        $vuelta = 0;

        foreach ($det_cotizacion as $dd){
            $vuelta += 1;

            $infoRequiDetalle = RequisicionUnidadDetalle::where('id', $dd->id_requi_unidaddetalle)->first();
            $infoMaterial = P_Materiales::where('id', $infoRequiDetalle->id_material)->first();
            $infoObjeto = ObjEspecifico::where('id', $infoMaterial->id_objespecifico)->first();

            if(strlen($infoMaterial->descripcion) >= 25){
                $subcadena = substr($infoRequiDetalle->material_descripcion, 0, 25);
            }else{
                $subcadena = $infoRequiDetalle->material_descripcion;
            }

            $multi = $dd->cantidad * $dd->precio_u;
            $total = $total + $multi;

            $precio_u = number_format((float)$dd->precio_u, 2, '.', ',');
            $multi = number_format((float)$multi, 2, '.', ',');

            $dataArray[] = [
                'cantidad' => $dd->cantidad,
                'nombre' => $subcadena,
                'cod_presup' => $infoObjeto->codigo,
                'precio_u' => $precio_u,
                'multi' => $multi
            ];

            // CANTIDAD POR HOJA
            if($vuelta == $cantidad){
                $array_merged[] = array_merge($dataArray);
                $dataArray = array();
                $vuelta = 0;
            }
        }

        if(!empty($dataArray)){
            $array_merged[] = array_merge($dataArray);
        }


        $total = number_format((float)$total, 2, '.', ',');

        //$fecha = strftime("%d-%B-%Y", strtotime($orden->fechaorden));
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $fecha = array(date("d", strtotime($orden->fecha_orden) ), $meses[date("n", strtotime($orden->fecha_orden) )-1], date("Y", strtotime($orden->fecha_orden) ) );

        $dia = $fecha[0];
        $mes = $fecha[1];
        $anio = $fecha[2];

        $pdf = PDF::loadView('backend.admin.presupuestounidad.reportes.pdfordencompraunidades', compact('orden',
            'cotizacion', 'dia','mes', 'anio','proveedor','array_merged',
            'administrador', 'total'));
        //$customPaper = array(0,0,470.61,612.36);
        $customPaper = array(0,0,470.61,612.36);
        $pdf->setPaper($customPaper)->setWarnings(false);
        return $pdf->stream('Orden_Compra.pdf');
    }

    public function vistaAñoOrdenesComprasUnidadesAprobadas(){
        $anios = P_AnioPresupuesto::orderBy('id', 'ASC')->get();

        return view('backend.admin.presupuestounidad.ordenes.ordenesaprobadas.vistaanioordenesaprobadasunidades', compact('anios'));
    }


    // retorna vista con las ordenes de compras para unidades
    public function indexOrdenesComprasAprobadasUnidades($idanio){

        return view('backend.admin.presupuestounidad.ordenes.ordenesaprobadas.vistaordenesunidadcompraaprobada', compact('idanio'));
    }

    // retorna tabla con las ordenes de compras
    public function tablaOrdenesComprasAprobadasUnidades($idanio){

        $lista = DB::table('orden_unidad AS or')
            ->join('cotizacion_unidad AS cu', 'or.id_cotizacion', '=', 'cu.id')
            ->join('requisicion_unidad AS requ', 'cu.id_requisicion_unidad', '=', 'requ.id')
            ->join('p_presup_unidad AS presu', 'requ.id_presup_unidad', '=', 'presu.id')
            ->select('presu.id_anio', 'or.estado', 'or.fecha_orden', 'or.id_cotizacion', 'or.id')
            ->where('presu.id_anio', $idanio)
            ->where('or.estado', 0) // solo aprobadas
            ->orderBy('or.fecha_orden')
            ->get();

        foreach($lista as $val){
            $infoCotizacion = CotizacionUnidad::where('id', $val->id_cotizacion)->first();
            $infoRequisicion = RequisicionUnidad::where('id', $infoCotizacion->id_requisicion_unidad)->first();
            $infoProveedor = Proveedores::where('id', $infoCotizacion->id_proveedor)->first();
            $infoPresup = P_PresupUnidad::where('id', $infoRequisicion->id_presup_unidad)->first();
            $infoDepar = P_Departamento::where('id', $infoPresup->id_departamento)->first();

            $val->departamento = $infoDepar->nombre;

            if($infoacta = ActaUnidad::where('id_ordenunidad', $val->id)->first()){
                $val->actaid = $infoacta->id;
            }else{
                $val->actaid = 0;
            }

            $val->requidestino = $infoRequisicion->destino;
            $val->nomproveedor = $infoProveedor->nombre;
        }

        return view('backend.admin.presupuestounidad.ordenes.ordenesaprobadas.tablaordenesunidadcompraaprobada', compact('lista'));
    }

    // anular una orden de compra para unidades.
    public function anularCompraUnidades(Request $request){

        DB::beginTransaction();
        try {

            // SOLO ANULAR SINO TIENE ACTA GENERADA
            if(ActaUnidad::where('id_ordenunidad', $request->id)->first()){
                return ['success' => 1];
            }

            if($infoOrden = OrdenUnidad::where('id', $request->id)->first()){

                $infoCotizacion = CotizacionUnidad::where('id', $infoOrden->id_cotizacion)->first();
                $infoRequisicion = RequisicionUnidad::where('id', $infoCotizacion->id_requisicion_unidad)->first();
                $infoPresup = P_PresupUnidad::where('id', $infoRequisicion->id_presup_unidad)->first();
                $infoAnio = P_AnioPresupuesto::where('id', $infoPresup->id_anio)->first();

                if($infoAnio->permiso == 0){
                    // pausado
                    return ['success' => 3];
                }

                // pendiente de anulación
                if($infoOrden->estado == 0){

                    OrdenUnidad::where('id', $request->id)->update([
                        'estado' => 1,
                        'fecha_anulada' => Carbon::now('America/El_Salvador'),
                    ]);

                    $infoCotiDeta = CotizacionUnidadDetalle::where('id_cotizacion_unidad', $infoCotizacion->id)->get();

                    // para que vuelva el dinero de RESTANTE, SE DEBERA BORRAR LA FILA
                    CuentaUnidadRestante::where('id_ordenunidad', $infoOrden->id)->delete();

                    // setear por cada material cotizado
                    foreach ($infoCotiDeta as $dd){

                        // para que pueda ser cotizado nuevamente
                        RequisicionUnidadDetalle::where('id', $dd->id_requi_unidaddetalle)->update([
                            'estado' => 0,
                        ]);

                        $infoRequiDetalle = RequisicionUnidadDetalle::where('id', $dd->id_requi_unidaddetalle)->first();
                        $infoMaterial = P_Materiales::where('id', $infoRequiDetalle->id_material)->first();

                        $cuentaUnidad = CuentaUnidad::where('id_presup_unidad', $infoRequisicion->id_presup_unidad)
                            ->where('id_objespeci', $infoMaterial->id_objespecifico)
                            ->first();

                        // VOLVER A COLOCAR DINERO RETENIDO, porque la orden fue anulada, ya que el material
                        // estara listo para ser cotizado de nuevo.
                        if(!CuentaUnidadRetenido::where('id_requi_detalle', $infoRequiDetalle->id)
                            ->where('id_cuenta_unidad', $cuentaUnidad->id)->first()){

                            $retenido = new CuentaUnidadRetenido();
                            $retenido->id_requi_detalle = $infoRequiDetalle->id;
                            $retenido->id_cuenta_unidad = $cuentaUnidad->id;
                            $retenido->save();
                        }
                    }
                }

                DB::commit();
                return ['success' => 2];
            }else{
                return ['success' => 99];
            }

        }catch(\Throwable $e){
            Log::info('error: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    // seleccion de año para mostrar ordenes de compra denegadas para unidades
    public function vistaAñoOrdenesComprasUnidadesDenegadas(){
        $anios = P_AnioPresupuesto::orderBy('id', 'ASC')->get();

        return view('backend.admin.presupuestounidad.ordenes.ordenesdenegadas.vistaanioordenesdenegadasunidades', compact('anios'));
    }


    // retorna vista para las ordenes de compra anuladas para unidades
    public function indexOrdenesComprasUnidadesDenegadas($idanio){
        return view('backend.admin.presupuestounidad.ordenes.ordenesdenegadas.vistaordenesunidadcompradenegadas', compact('idanio'));
    }

    // retorna tabla para las ordenes de compra anuladas para unidades
    public function tablaOrdenesComprasUnidadesDenegadas($idanio){

        $lista = DB::table('orden_unidad AS or')
            ->join('cotizacion_unidad AS cu', 'or.id_cotizacion', '=', 'cu.id')
            ->join('requisicion_unidad AS requ', 'cu.id_requisicion_unidad', '=', 'requ.id')
            ->join('p_presup_unidad AS presu', 'requ.id_presup_unidad', '=', 'presu.id')
            ->select('presu.id_anio', 'or.estado', 'or.fecha_orden', 'or.id_cotizacion', 'or.id')
            ->where('presu.id_anio', $idanio)
            ->where('or.estado', 1) // solo denegadas
            ->orderBy('or.fecha_orden')
            ->get();

        foreach($lista as $val){
            $infoCotizacion = CotizacionUnidad::where('id', $val->id_cotizacion)->first();
            $infoRequisicion = RequisicionUnidad::where('id', $infoCotizacion->id_requisicion_unidad)->first();
            $infoProveedor = Proveedores::where('id', $infoCotizacion->id_proveedor)->first();
            $infoPresup = P_PresupUnidad::where('id', $infoRequisicion->id_presup_unidad)->first();
            $infoDepar = P_Departamento::where('id', $infoPresup->id_departamento)->first();

            $val->departamento = $infoDepar->nombre;

            if($infoacta = ActaUnidad::where('id_ordenunidad', $val->id)->first()){
                $val->actaid = $infoacta->id;
            }else{
                $val->actaid = 0;
            }

            $val->requidestino = $infoRequisicion->destino;
            $val->nomproveedor = $infoProveedor->nombre;
        }

        return view('backend.admin.presupuestounidad.ordenes.ordenesdenegadas.tablaordenesunidadcompradenegadas', compact('lista'));
    }

    // vista detalle de una cotización unidad, esto se mira desde las ordenes de compra
    public function vistaDetalleCotizacionUnidadOrden($idcoti){
        // id de cotizacion

        $infoCotizacion = CotizacionUnidad::where('id', $idcoti)->first();
        $infoRequisicion = RequisicionUnidad::where('id', $infoCotizacion->id_requisicion_unidad)->first();
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

            /*if($infoUnidad = P_UnidadMedida::where('id', $infoMaterial->id_unidadmedida)->first()){
                $de->nombrematerial = $infoRequiDetalle->material_descripcion . " - " . $infoUnidad->nombre;
            }else{

            }*/

            $infoUnidad = P_UnidadMedida::where('id', $infoMaterial->id_unidadmedida)->first();
            $de->nombrematerial = $infoRequiDetalle->material_descripcion;
            $de->unidadmedida = $infoUnidad->nombre;

            $infoObjeto = ObjEspecifico::where('id', $infoMaterial->id_objespecifico)->first();
            $de->objeto = $infoObjeto->codigo . " - " . $infoObjeto->nombre;

            $de->precio_u = number_format((float)$de->precio_u, 2, '.', ',');
            $de->total = number_format((float)$multi, 2, '.', ',');
        }

        $totalCantidad = number_format((float)$totalCantidad, 2, '.', ',');
        $totalPrecio = number_format((float)$totalPrecio, 2, '.', ',');
        $totalTotal = number_format((float)$totalTotal, 2, '.', ',');

        return view('backend.admin.presupuestounidad.ordenes.detalleorden.vistadetalleordenunidad', compact('idcoti', 'infoRequisicion',
            'proveedor', 'infoCotiDetalle', 'fecha', 'totalCantidad', 'totalPrecio', 'totalTotal'));
    }


    // generar acta de una orden de compra para unidad
    function generarActadeCompraUnidades(Request $request){

        $regla = array(
            'idorden' => 'required',
            'horaacta' => 'required',
            'fechaacta' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        $infoOrden = OrdenUnidad::where('id', $request->idorden)->first();
        $infoCotizacion = CotizacionUnidad::where('id', $infoOrden->id_cotizacion)->first();
        $infoRequisicion = RequisicionUnidad::where('id', $infoCotizacion->id_requisicion_unidad)->first();
        $infoPresuUnidad = P_PresupUnidad::where('id', $infoRequisicion->id_presup_unidad)->first();
        $infoAnio = P_AnioPresupuesto::where('id', $infoPresuUnidad->id_anio)->first();

        if($infoAnio->permiso == 0){
            return ['success' => 1];
        }

        // si ya existe, solo retornar como que fue guardado
        if($info = ActaUnidad::where('id_ordenunidad', $request->idorden)->first()){
            return ['success' => 2, 'actaid'=> $info->id];
        }else{
            $acta = new ActaUnidad();
            $acta->id_ordenunidad = $request->idorden;
            $acta->fecha_acta = $request->fechaacta;
            $acta->hora = $request->horaacta;
            $acta->estado = 1; // acta generada

            if($acta->save()) {
                return ['success' => 2, 'actaid'=> $acta->id];
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
        $requisicion = RequisicionUnidad::where('id',  $cotizacion->id_requisicion_unidad)->first();

        $fecha = strftime("%d-%B-%Y",strtotime($acta->fecha_acta));
        $hora = $acta->hora;

        $pdf = PDF::loadView('backend.admin.presupuestounidad.reportes.pdfactaordencompraunidades', compact('acta','fecha','proveedor','administrador','hora','orden'));
        $pdf->setPaper('letter', 'portrait')->setWarnings(false);
        return $pdf->stream('acta_orden_compra.pdf');
    }


}

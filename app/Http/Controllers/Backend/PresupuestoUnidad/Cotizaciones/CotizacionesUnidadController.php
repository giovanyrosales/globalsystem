<?php

namespace App\Http\Controllers\Backend\PresupuestoUnidad\Cotizaciones;

use App\Http\Controllers\Controller;
use App\Models\Administradores;
use App\Models\CotizacionUnidad;
use App\Models\CotizacionUnidadDetalle;
use App\Models\CuentaUnidad;
use App\Models\MoviCuentaUnidad;
use App\Models\ObjEspecifico;
use App\Models\OrdenUnidad;
use App\Models\P_AnioPresupuesto;
use App\Models\P_Departamento;
use App\Models\P_Materiales;
use App\Models\P_PresupUnidad;
use App\Models\P_UnidadMedida;
use App\Models\Proveedores;
use App\Models\Referencias;
use App\Models\RequisicionAgrupada;
use App\Models\RequisicionAgrupadaDetalle;
use App\Models\RequisicionUnidad;
use App\Models\RequisicionUnidadDetalle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CotizacionesUnidadController extends Controller{


    // COTIZACIONES PARA UNIDADES

    public function __construct(){
        $this->middleware('auth');
    }


    // SELECCIONA EL ANIO PARA BUSCAR UN AGRUPADO
    public function indexListaAgrupadoAnios(){

        $anios = P_AnioPresupuesto::orderBy('nombre', 'DESC')->get();

        return view('backend.admin.presupuestounidad.requerimientos.requerimientosunidad.revision.elegirfecha', compact('anios'));
    }


    // MUESTRA VISTA PARA CARGAR LA TABLA Y PASAR EL ANIO
    public function indexListarRequerimientosPendienteUnidad($idanio){

        $proveedores = Proveedores::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.presupuestounidad.requerimientos.requerimientosunidad.revision.vistarequerimientosunidadrevision', compact('proveedores', 'idanio'));
    }


    // AQUI SE MOSTRARA LISTA DE AGRUPADOS QUE TIENEN MATERIALES QUE NO HAN SIDO COTIZADOS AUN
    public function indexTablaListarRequerimientosPendienteUnidad($idanio){


        // DEL AÑO ELEGIDO

        // OBTENER LISTADO DE REQUISICION AGRUPADA DETALLE DONDE COTIZADO SEA 0 Y SEA DEL X ANIO ELEGIDO

        $arrayAgrupado = RequisicionAgrupada::where('id_anio', $idanio)->get();


        $pilaID = array();

        foreach ($arrayAgrupado as $dato){
            array_push($pilaID, $dato->id);
        }


        $arrayAgruDetalle = RequisicionAgrupadaDetalle::whereIn('id_requi_agrupada', $pilaID)
            ->where('cotizado', 0)
            ->get();


        $pilaPadre = array();

        foreach ($arrayAgruDetalle as $dato){

            $infoPadre = RequisicionAgrupada::where('id', $dato->id_requi_agrupada)->first();

            // NO ESTA DENEGADA POR UCP
            if($infoPadre->estado == 0){
                // SE DEBE INGRESAR EL PADRE ID
                array_push($pilaPadre, $dato->id_requi_agrupada);
            }
        }

        $listado = RequisicionAgrupada::whereIn('id', $pilaPadre)->get();


        return view('backend.admin.presupuestounidad.requerimientos.requerimientosunidad.revision.tablarequerimientosunidadrevision', compact('listado'));
    }




    // informacion del agrupado para hacer la cotizacion
    public function informacionRequerimientoCotizarInfo(Request $request){

        $regla = array(
            'id' => 'required', // id requisicion_agrupada
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        if($infoRequiAgrupada = RequisicionAgrupada::where('id', $request->id)->first()){

            // obtener todos sus materiales del agrupado

            $listado = DB::table('requisicion_agrupada_detalle AS rad')
                ->join('requisicion_unidad_detalle AS ru', 'rad.id_requi_unidad_detalle', '=', 'ru.id')
                ->join('p_materiales AS mate', 'ru.id_material', '=', 'mate.id')
                ->join('obj_especifico AS obj', 'mate.id_objespecifico', '=', 'obj.id')
                ->select('ru.id', 'obj.codigo', 'rad.id_requi_agrupada', 'mate.descripcion')
                ->where('rad.id_requi_agrupada', $infoRequiAgrupada->id)
                ->where('rad.cotizado', 0)
                ->orderBy('obj.codigo', 'ASC')
                ->get();

            foreach ($listado as $dato){

                $texto = "(" . $dato->codigo . ") " . $dato->descripcion;
                $dato->materialformat = $texto;
            }


            return ['success' => 1, 'info' => $infoRequiAgrupada, 'listado' => $listado];
        }else{
            return ['success' => 2];
        }
    }


    // ABRE MODAL PARA ESCRIBIR EL NUEVO PRECIO DEL MATERIAL Y SE COTIZARA
    public function verificarRequerimientoUnidadAcotizar(Request $request){

        $info = RequisicionUnidadDetalle::whereIn('id', $request->lista)
            ->orderBy('id', 'ASC')
            ->get();

        foreach ($info as $dd){
            $infoCatalogo = P_Materiales::where('id', $dd->id_material)->first();
            $infoUnidad = P_UnidadMedida::where('id', $infoCatalogo->id_unidadmedida)->first();
            $infoCodigo = ObjEspecifico::where('id', $infoCatalogo->id_objespecifico)->first();

            $dd->nombre = $infoCatalogo->descripcion;
            $dd->medida = $infoUnidad->nombre;
            $dd->codigo = $infoCodigo->codigo . " - " . $infoCodigo->nombre;
        }

        return ['success' => 1, 'lista' => $info];
    }



    // GUARDAR LA COTIZACION
    public function guardarNuevaCotizacionRequeriUnidad(Request $request){

        $regla = array(
            'fecha' => 'required',
            'proveedor' => 'required',
            'idagrupado' => 'required'
        );

        // ARRAY
        // unidades[]   precio unitario a cotizar
        // idfila[]
        // descripmate[]  la descripcion escrito por uaci




        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        DB::beginTransaction();

        try {

            $infoRequiAgrupado = RequisicionAgrupada::where('id', $request->idagrupado)->first();

            // CANCELADO POR UCP
            if($infoRequiAgrupado->estado== 1){
                return ['success' => 1];
            }

            // VERIFICAR QUE ESTOS MATERIAL A COTIZAR NO ESTEN COTIZADOS, NINGUNO

            // VERIFICAR QUE ALCANCE DINERO

            for ($i = 0; $i < count($request->idfila); $i++) {
                if($infoRequiUniDetalle = RequisicionUnidadDetalle::where('id', $request->idfila[$i])->first()){

                    // ESTE MATERIAL ESTA CANCELADO
                    if($infoRequiUniDetalle->cancelado == 1){
                        return ['success' => 2];
                    }

                    // AUNQUE EL MATERIAL SOLO PUEDE ESTAR EN 1 SOLO AGRUPADO, PERO POR PRECAUCION, SE
                    // UTILIZARA GET PARA BUSCAR SI ESTA COTIZADO
                    $arrayRequiAgrupadoDetalle = RequisicionAgrupadaDetalle::where('id_requi_unidad_detalle', $infoRequiUniDetalle->id)->get();

                    foreach ($arrayRequiAgrupadoDetalle as $dato) {
                        if ($dato->cotizado == 1) {
                            return ['success' => 3];
                        }
                    }


                    $infoRequisicion = RequisicionUnidad::where('id', $infoRequiUniDetalle->id_requisicion_unidad)->first();

                    $infoMaterial = P_Materiales::where('id', $infoRequiUniDetalle->id_material)->first();

                    $infoObjeto = ObjEspecifico::where('id', $infoMaterial->id_objespecifico)->first();

                    $infoCuentaUnidad = CuentaUnidad::where('id_presup_unidad', $infoRequisicion->id_presup_unidad)
                        ->where('id_objespeci', $infoMaterial->id_objespecifico)
                        ->first();




                    // VERIFICAR QUE ESTE MATERIAL SI ESTA COTIZADO YA, NO ESTE ESPERANDO UNA RESPUESTA,
                    // SOLO DEJARA PASAR SI NO ESTA DENEGADA LA COTIZACION COMPLETA

                    $arrayCotiDeta = CotizacionUnidadDetalle::where('id_requi_unidaddetalle', $infoRequiUniDetalle->id)->get();

                    foreach ($arrayCotiDeta as $dato){

                        $cotizacionPr = CotizacionUnidad::where('id', $dato->id_cotizacion_unidad)->first();

                        if($cotizacionPr->estado != 2){
                            // es decir, que esta en defecto, o esta aprobada por jefe uaci
                            DB::rollback();
                            return ['success' => 11];
                        }

                    }


                    // RECORDAR QUE EL MATERIAL YA TIENE UNA DESCONTADA DE $$ CUANTO SE HIZO LA REQUISICION,
                    // SE DEBE DE DEVOLVER ESE MISMO MONTO PARA REALIZAR EL CALCULO CORRECTO, A LA
                    // NUEVA RESTA QUE SE HARA. SE DEBERA RESETEAR AL SALDO INICIAL FIJO

                    $suma = $infoCuentaUnidad->saldo_inicial + ($infoRequiUniDetalle->dinero * $infoRequiUniDetalle->cantidad);

                    // DEBO MULTIPLICAR LA CANTIDAD QUE SE PIDE X EL PRECIO QUE VIENE DE LA COTIZACION
                    $multiplicar = $infoRequiUniDetalle->cantidad * $request->unidades[$i];

                    $resta = $suma - $multiplicar;

                    // DINERO NO ALCALZA PARA BAJARLE
                    if($this->redondear_dos_decimal($resta) < 0){

                        $disponibleFormat = number_format((float)$suma, 2, '.', ',');

                        DB::rollback();

                        return ['success' => 5, 'fila' => ($i + 1),
                            'material' => $infoMaterial->descripcion,
                            'objcodigo' => $infoObjeto->codigo,
                            'disponibleFormat' => $disponibleFormat, // este el saldo que tiene disponible ese codigo
                        ];
                    }else{

                        // IR DESCONTANDO EN CADA ENTRADA

                        CuentaUnidad::where('id', $infoCuentaUnidad->id)->update([
                            'saldo_inicial' => $resta,
                        ]);


                        RequisicionUnidadDetalle::where('id', $infoRequiUniDetalle->id)->update([
                            'dinero' => $request->unidades[$i],
                        ]);

                        RequisicionAgrupadaDetalle::where('id_requi_unidad_detalle', $infoRequiUniDetalle->id)->update([
                            'cotizado' => 1,
                        ]);
                    }

                }else{

                    DB::rollback();

                    // ID MATERIAL NO ENCONTRADO
                    return ['success' => 4];
                }
            }

            // PASO TODAS LAS VALIDACIONES


            // crear cotizacion
            $coti = new CotizacionUnidad();
            $coti->id_agrupado = $request->idagrupado;
            $coti->id_proveedor = $request->proveedor;
            $coti->fecha = $request->fecha;
            $coti->fecha_estado = null;
            $coti->estado = 0;
            $coti->save();


            for ($i = 0; $i < count($request->idfila); $i++) {
                $infoRequiUniDetalle = RequisicionUnidadDetalle::where('id', $request->idfila[$i])->first();

                $cotiDetalle = new CotizacionUnidadDetalle();
                $cotiDetalle->id_cotizacion_unidad = $coti->id;
                $cotiDetalle->id_requi_unidaddetalle = $request->idfila[$i];
                $cotiDetalle->cantidad = $infoRequiUniDetalle->cantidad;
                $cotiDetalle->precio_u = $request->unidades[$i];
                $cotiDetalle->descripcion = $request->descripmate[$i];
                $cotiDetalle->save();
            }


            DB::commit();
            return ['success' => 10];
        }catch(\Throwable $e){
            Log::info('error' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    function redondear_dos_decimal($valor) {
        $float_redondeado=round($valor * 100) / 100;
        return $float_redondeado;
    }


    // *** COTIZACIONES PARA UNIDADES ***

    public function indexAnioCotiUnidadPendiente(){

        $anios = P_AnioPresupuesto::orderBy('nombre', 'DESC')->get();

        return view('backend.admin.presupuestounidad.cotizaciones.pendientes.vistaaniocotiunidadpendiente', compact('anios'));
    }

    // retorna vista con las cotizaciones pendientes
    public function indexCotizacionesUnidadesPendiente($idanio){
        // viene id anio para buscar las pendiente
        return view('backend.admin.presupuestounidad.cotizaciones.pendientes.vistacotizacionpendienteunidad', compact('idanio'));
    }

    // retorna tabla con las cotizaciones pendientes
    public function indexCotizacionesUnidadesPendienteTabla($idanio){

        // SE BUSCA POR AÑO DE COTIZACION

        $infoAnio = P_AnioPresupuesto::where('id', $idanio)->first();

        $lista = DB::table('cotizacion_unidad AS cu')
            ->join('requisicion_agrupada AS ra', 'cu.id_agrupado', '=', 'ra.id')
            ->select('cu.id', 'ra.id_anio', 'ra.nombreodestino', 'ra.justificacion', 'cu.estado', 'cu.id_proveedor',  'cu.fecha')
            ->whereYear('cu.fecha', $infoAnio->nombre)
            ->where('cu.estado', 0)
            ->orderBy('cu.id', 'ASC')
            ->get();

        foreach ($lista as $dd){

            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));

            $infoProveedor = Proveedores::where('id', $dd->id_proveedor)->first();
            $dd->proveedor = $infoProveedor->nombre;
        }

        return view('backend.admin.presupuestounidad.cotizaciones.pendientes.tablacotizacionpendienteunidad', compact('lista'));
    }

    // ver detalle de una cotización para unidades
    public function indexCotizacionUnidadDetalle($id){ // id de cotizacion unidad


        // destino, necesidad, proveedor, fecha cotizacion
        $cotizacion = CotizacionUnidad::where('id', $id)->first();

        $infoAgrupado = RequisicionAgrupada::where('id', $cotizacion->id_agrupado)->first();

        $infoProveedor = Proveedores::where('id', $cotizacion->id_proveedor)->first();
        $proveedor = $infoProveedor->nombre;

        $infoCotiDetalle = CotizacionUnidadDetalle::where('id_cotizacion_unidad', $id)->get();
        $conteo = 0;
        $fecha = date("d-m-Y", strtotime($cotizacion->fecha));

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

            if($infoUnidad = P_UnidadMedida::where('id', $infoMaterial->id_unidadmedida)->first()){
                $de->nombrematerial = $infoRequiDetalle->material_descripcion . " - " . $infoUnidad->nombre;
            }else{
                $de->nombrematerial = $infoRequiDetalle->material_descripcion;
            }

            $infoObjeto = ObjEspecifico::where('id', $infoMaterial->id_objespecifico)->first();
            $de->objeto = $infoObjeto->codigo . " - " . $infoObjeto->nombre;

            $de->precio_u = number_format((float)$de->precio_u, 2, '.', ',');
            $de->total = number_format((float)$multi, 2, '.', ',');
        }

        $totalCantidad = number_format((float)$totalCantidad, 2, '.', ',');
        $totalPrecio = number_format((float)$totalPrecio, 2, '.', ',');
        $totalTotal = number_format((float)$totalTotal, 2, '.', ',');

        return view('backend.admin.presupuestounidad.cotizaciones.individual.vistacotizacionindividualunidad', compact('id', 'infoAgrupado',
            'proveedor', 'infoCotiDetalle', 'fecha', 'totalCantidad', 'totalPrecio', 'totalTotal', 'cotizacion'));
    }

    // autorizar cotización unidad jefe uaci
    public function autorizarCotizacionUnidad(Request $request){

        DB::beginTransaction();

        try {

            if(CotizacionUnidad::where('id', $request->id)->where('estado', 0)->first()){
                CotizacionUnidad::where('id', $request->id)->update([
                    'estado' => 1,
                    'fecha_estado' => Carbon::now('America/El_Salvador')
                ]);
            }

            DB::commit();
            return ['success' => 1];
        }catch(\Throwable $e){
            Log::info('ee ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    //**** AUTORIZADAS

    public function indexAnioCotiUnidadAutorizada(){
        $anios = P_AnioPresupuesto::orderBy('nombre', 'DESC')->get();
        return view('backend.admin.presupuestounidad.cotizaciones.procesada.vistaaniocotiunidadprocesada', compact('anios'));
    }


    // retorna vista con las cotizaciones autorizaciones para unidades
    public function indexCotizacionesUnidadesAutorizadas($idanio){

        $arrayReferencias = Referencias::orderBy('nombre')->get();

        return view('backend.admin.presupuestounidad.cotizaciones.procesada.vistacotizacionprocesadaunidad', compact( 'idanio', 'arrayReferencias'));
    }

    // retorna tabla con las cotizaciones autorizaciones para unidades
    public function tablaCotizacionesUnidadesAutorizadas($idanio){

        $infoAnio = P_AnioPresupuesto::where('id', $idanio)->first();

        // autorizadas
        $lista = DB::table('cotizacion_unidad AS cu')
            ->join('requisicion_agrupada AS ra', 'cu.id_agrupado', '=', 'ra.id')
            ->select('cu.id', 'cu.estado', 'ra.nombreodestino', 'ra.justificacion', 'cu.id_proveedor', 'cu.fecha', 'ra.id_anio')
            ->where('cu.estado', 1)
            ->whereYear('cu.fecha', $infoAnio->nombre)
            ->get();

        foreach ($lista as $info){

            $info->fecha = date("d-m-Y", strtotime($info->fecha));

            $infoProveedor = Proveedores::where('id', $info->id_proveedor)->first();
            $info->proveedor = $infoProveedor->nombre;

            if(OrdenUnidad::where('id_cotizacion', $info->id)->first()){
                $info->bloqueo = true;
            }else{
                $info->bloqueo = false;
            }
        }

        return view('backend.admin.presupuestounidad.cotizaciones.procesada.tablacotizacionprocesadaunidad', compact('lista'));
    }

    // denegar una cotización de unidad
    public function denegarCotizacionUnidad(Request $request){

        $regla = array(
            'id' => 'required', // id cotizacion
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();

        try {

            // COTIZACIÓN DENEGADA
            $infoCotizacion = CotizacionUnidad::where('id', $request->id)->first();
            $arrayAgrupadoDetalle = RequisicionAgrupadaDetalle::where('id_requi_agrupada', $infoCotizacion->id_agrupado)->get();

            if($infoCotizacion->estado == 1){

                // ESTA COTIZACION ESTA YA APROBADA
                return ['success' => 1];
            }

            if($infoCotizacion->estado != 2){

                CotizacionUnidad::where('id', $request->id)->update([
                    'estado' => 2,
                    'fecha_estado' => Carbon::now('America/El_Salvador')
                ]);

                foreach ($arrayAgrupadoDetalle as $info){
                    RequisicionAgrupadaDetalle::where('id', $info->id)->update([
                        'cotizado' => 0,
                    ]);
                }
            }


            DB::commit();
            return ['success' => 2];

        }catch(\Throwable $e){
            //Log::info('ee' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    //**** DENEGADAS

    public function indexAnioCotiUnidadDenegadas(){
        $anios = P_AnioPresupuesto::orderBy('nombre', 'DESC')->get();
        return view('backend.admin.presupuestounidad.cotizaciones.denegadas.vistaaniocotiunidaddenegadas', compact('anios'));
    }

    // retorna vista con las cotizaciones denegadas para unidades
    public function indexCotizacionesUnidadesDenegadas($idanio){
        $contrato = Administradores::orderBy('nombre')->get();
        return view('backend.admin.presupuestounidad.cotizaciones.denegadas.vistacotizaciondenegadaunidad', compact('contrato', 'idanio'));
    }

    // retorna tabla con las cotizaciones autorizaciones para unidades
    public function tablaCotizacionesUnidadesDenegadas($idanio){

        $infoAnio = P_AnioPresupuesto::where('id', $idanio)->first();

        // autorizadas
        $lista = DB::table('cotizacion_unidad AS cu')
            ->join('requisicion_agrupada AS ra', 'cu.id_agrupado', '=', 'ra.id')
            ->select('cu.id', 'cu.estado', 'cu.id_agrupado AS idagrupado', 'cu.id_proveedor', 'cu.fecha', 'ra.id_anio')
            ->where('cu.estado', 2) // DENEGADAS
            ->whereYear('cu.fecha', $infoAnio->nombre)
            ->get();

        foreach ($lista as $info){

            $info->fecha = date("d-m-Y", strtotime($info->fecha));

            $infoProveedor = Proveedores::where('id', $info->id_proveedor)->first();
            $infoAgrupado = RequisicionAgrupada::where('id', $info->idagrupado)->first();

            $info->proveedor = $infoProveedor->nombre;
            $info->nomdestino = $infoAgrupado->nombreodestino;
            $info->destino = $infoAgrupado->destino;
            $info->justificacion = $infoAgrupado->justificacion;
        }

        return view('backend.admin.presupuestounidad.cotizaciones.denegadas.tablacotizaciondenegadaunidad', compact('lista'));
    }


    public function descargarActaCotizacionDenegada($idagrupado){

        $url = RequisicionAgrupada::where('id', $idagrupado)->pluck('documento')->first();

        $pathToFile = "storage/archivos/".$url;

        $extension = pathinfo(($pathToFile), PATHINFO_EXTENSION);

        $nombre = "Doc." . $extension;

        return response()->download($pathToFile, $nombre);
    }

    public function vistaDetalleCotizacionUnidad($id){
        // id de cotizacion

        $cotizacion = CotizacionUnidad::where('id', $id)->first();
        $infoRequisicion = RequisicionUnidad::where('id', $cotizacion->id_requisicion_unidad)->first();
        $infoProveedor = Proveedores::where('id', $cotizacion->id_proveedor)->first();

        $infoAgrupado = RequisicionAgrupada::where('id', $cotizacion->id_agrupado)->first();

        $proveedor = $infoProveedor->nombre;

        $infoCotiDetalle = CotizacionUnidadDetalle::where('id_cotizacion_unidad', $id)->get();
        $conteo = 0;
        $fecha = date("d-m-Y", strtotime($cotizacion->fecha));

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

            $de->nombrematerial = $infoRequiDetalle->material_descripcion;

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

        return view('backend.admin.presupuestounidad.cotizaciones.individual.vistacotizaciondetalleunidad', compact('id', 'infoRequisicion',
            'proveedor', 'infoCotiDetalle', 'fecha', 'totalCantidad', 'infoAgrupado', 'totalPrecio', 'totalTotal'));
    }




    public function pdfRequerimientoUnidadMateriales($id){

        // ID REQUERIMIENTO_UNIDAD

        $infoReq = RequisicionUnidad::where('id', $id)->first();

        $infoPresuUni = P_PresupUnidad::where('id', $infoReq->id_presup_unidad)->first();
        $infoDepa = P_Departamento::where('id', $infoPresuUni->id_departamento)->first();

        $fecha = date("d-m-Y", strtotime($infoReq->fecha));

        $requiDetalle = RequisicionUnidadDetalle::where('id_requisicion_unidad', $id)->get();

        $cantidad = 7;
        $dataArray = array();
        $array_merged = array();
        $vuelta = 0;

        $costoporhoja = 0;

        foreach ($requiDetalle as $dd){
            $vuelta += 1;

            $infoMaterial = P_Materiales::where('id', $dd->id_material)->first();
            $infoMedida = P_UnidadMedida::where('id', $infoMaterial->id_unidadmedida)->first();
            $infoObj = ObjEspecifico::where('id', $infoMaterial->id_objespecifico)->first();

            $dd->unidadmedida = $infoMedida->nombre;
            $dd->material = $infoMaterial->descripcion;
            $dd->codigopres = $infoObj->codigo;

            $multiFila = $dd->cantidad * $dd->dinero_fijo;

           // Log::info('fila: ' . $multiFila);

            $costoporhoja = $costoporhoja + $multiFila;


            $multifila = number_format((float)$multiFila, 2, '.', ',');
            $dd->dinero_fijo = number_format((float)$dd->dinero_fijo, 2, '.', ',');
            $costodd = number_format((float)$costoporhoja, 2, '.', ',');


            $dataArray[] = [
                'cantidad' => $dd->cantidad,
                'medida' => $infoMedida->nombre,
                'descripcion' => $dd->material_descripcion,
                'precio_u' => $dd->dinero_fijo,
                'costofila' => $multifila,
                'codigopres' => $infoObj->codigo,
                'costoxhoja' => $costodd
            ];

            // CANTIDAD POR HOJA
            if($vuelta == $cantidad){

                $costoporhoja = 0;

                $array_merged[] = array_merge($dataArray);
                $dataArray = array();
                $vuelta = 0;
            }
        }

        if(!empty($dataArray)){
            $array_merged[] = array_merge($dataArray);
        }

        // INFORMACION DEL USUARIO AUTENTIFICADO
        $nombreUsuario = Auth::user()->nombre;

        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        $mpdf->SetTitle('Requerimiento');
        $stylesheet = file_get_contents('css/cssrequerimiento.css');

        // mostrar errores
        $mpdf->showImageErrors = false;
        $logoalcaldia = 'images/logo.png';

        foreach ($array_merged as $items => $items_value){

            $tabla = "<div class='content'>
            <img id='logoizq' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            REQUERIMIENTO <br>
            </p>
            <p style='text-align: right; margin-right: 13px;'>No.____________</p>
            </div>";

            $tabla .= "
            <table >
            <tbody>
            <tr>
                <th style='width: 20% ;font-weight: normal; text-align: right; font-size: 12px'>ACTA.___________</th>
                <th style='width: 25% ;font-weight: normal; text-align: right; padding-right: 20px; font-size: 12px'>ACUERDO._______</th>
                <th style='width: 30% ;font-weight: normal; text-align: right; font-size: 12px'>APROBACIÓN DE PROYECTO._______________&nbsp;&nbsp;</th>
            </tr>";

            $tabla .= "</tbody></table>";

            $tabla .= "
        <table id='tablaFor' style='width: 100%'>
        <tbody>
        <tr>
            <td colspan='6' style='text-align: left; font-size:10px;'>NOMBRE O DESTINO DEL PROYECTO: <strong> $infoReq->destino </strong></td>
        </tr>
         <tr>
            <td colspan='4' style='text-align: left; font-size:10px;'>DEPARTAMENTO: <strong> $infoDepa->nombre </strong></td>
             <td colspan='2' style='text-align: left; font-size:10px;'>FECHA: <strong> $fecha </strong></td>
        </tr>

        <tr>
            <td colspan='6' style='text-align: center; background: #b4b4b4; font-weight: bold; font-size:10px;'>DETALLE DE NECESIDAD</td>
        </tr>
          <tr>
            <td colspan='6' style='text-align: left; font-size:10px; font-weight: bold'>$infoReq->necesidad</td>
        </tr>
          <tr>
            <td colspan='6' style='text-align: center; background: #b4b4b4; font-weight: bold; font-size:10px;'>BIEN SOLICITADO</td>
          </tr>

          <tr>
            <td style='text-align: center; padding-top: 3px; font-size:11px;'>CANTIDAD</td>
            <td style='text-align: center; padding-top: 3px;font-size:11px;'>U. MEDIDA</td>
            <td style='text-align: center; padding-top: 3px;font-size:11px;'>DESCRIPCION/ESPECIFICACION TECNICA DETALLADA</td>
            <td style='text-align: center; padding-top: 3px;font-size:11px;'>PRECIO U.</td>
            <td style='text-align: center;padding-top: 3px; font-size:11px;'>COSTO TOTAL ESTIMADO</td>
            <td style='text-align: center; padding-top: 3px;font-size:11px;'>CODIGO PRES.</td>
          </tr>
          ";


            foreach ($items_value as $item => $item_value){

                $c_cantidad = $item_value['cantidad'];
                $c_medida = $item_value['medida'];
                $c_descripcion = $item_value['descripcion'];
                $c_preciou = $item_value['precio_u'];
                $c_costofila = $item_value['costofila'];
                $c_codigopres = $item_value['codigopres'];
                $c_costoxhoja = $item_value['costoxhoja'];

                if(!empty($c_cantidad)){
                    $tabla .=  "<tr>
                    <td style='text-align: center; font-size:12px;padding-top: 0px'>$c_cantidad</td>
                    <td style='text-align: center; font-size:12px;padding-top: 0px'>$c_medida</td>
                    <td style='text-align: center; font-size:12px;padding-top: 0px'>$c_descripcion</td>
                    <td style='text-align: center; font-size:12px;padding-top: 0px'>$$c_preciou</td>
                    <td style='text-align: center; font-size:12px;padding-top: 0px'>$$c_costofila</td>
                    <td style='text-align: center; font-size:12px;padding-top: 0px'>$c_codigopres</td>
                  </tr>";
                }

                // DIFERENCIANDO CUANDO ES ULTIMA VUELTA LOOP
                if(end($items_value) == $item_value) {
                    $tabla .=  "<tr>
                    <td colspan='4' style='text-align: center; font-size:12px;padding-top: 1px'><strong>TOTAL</strong></td>
                    <td style='text-align: center; font-size:12px;padding-top: 0px'><strong>$$c_costoxhoja </strong></td>
                    <td style='text-align: center; font-size:12px;padding-top: 0px'></td>
                  </tr>";
                }
            }

            $tabla .= "</tbody></table>";

            $tabla .= "
        <table style='width: 100%; padding-top: 15px; margin-right: 16px; margin-left: 16px'>
        <tbody>
        <tr>
            <td style='text-align: left; font-weight: bold; padding-top: 5px; font-size:11px;'>SOLICITA</td>
            <td style='text-align: right; font-weight: bold; padding-top: 5px; font-size:11px;'>AUTORIZA</td>
        </tr>

        <tr>
            <td style='text-align: left; font-size:11px; padding-top: 10px;'>FIRMA:___________________________________</td>
            <td style='text-align: right; font-size:11px; padding-top: 10px;'>FIRMA:_________________________________</td>
        </tr>

        <tr>
            <td style='text-align: left; font-size:11px; padding-top: 10px;'>NOMBRE: <strong>$nombreUsuario</strong></td>
            <td style='text-align: right; font-size:11px; padding-top: 10px;'>NOMBRE:_________________________________</td>
        </tr>

        <tr>
            <td style='text-align: left; font-size:11px; padding-top: 10px;'></td>
            <td style='text-align: right; font-size:11px; padding-top: 10px;'>JEFE:__________________________________</td>
        </tr>

        <tr>
            <td colspan='2' style='text-align: center; font-weight: bold; font-size:11px; padding-top: 10px;'>REVISADO</td>

        </tr>

        <tr>
            <td colspan='2' style='text-align: center; font-size:11px; padding-top: 10px;'>FIRMA:_____________________________</td>
        </tr>
         <tr>
            <td colspan='2' style='text-align: center; font-size:11px; padding-top: 10px;'>NOMBRE:___________________________</td>
        </tr>

        <tr>
            <td style='text-align: left; font-weight: bold; padding-top: 10px; font-size:11px;'>PRESUPUESTO</td>
            <td style='text-align: right; font-weight: bold; padding-top: 10px; font-size:11px;'>RECIBE UCP</td>
        </tr>

        <tr>
            <td style='text-align: left; font-size:11px; padding-top: 10px;'>FIRMA:___________________________</td>
            <td style='text-align: right; font-size:11px; padding-top: 10px;'>FIRMA:_______________________________</td>
        </tr>

        <tr>
            <td style='text-align: left; padding-top: 10px; font-size:11px; '>NOMBRE: <strong>Lic. Jesus Calderón</strong></td>
            <td style='text-align: right; padding-right: 20px; font-size:11px; padding-top: 10px;'>NOMBRE: <strong>Lic. Heidi Chinchilla</strong></td>
        </tr>

        <tr>
            <td style='text-align: left; font-size:11px; padding-top: 10px;'>FECHA:___________________________</td>
            <td style='text-align: right; font-size:11px; padding-top: 10px;'>FECHA:______________________________</td>
        </tr>

        <tr>
            <td style='text-align: left; font-size:11px; padding-top: 10px;'>HORA:_____________________________</td>
            <td style='text-align: right; font-size:11px; padding-top: 10px;'>HORA:_______________________________</td>
        </tr>

          ";


            $tabla .= "</tbody></table>";

            $mpdf->WriteHTML($stylesheet,1);
            $mpdf->AddPage();
            $mpdf->WriteHTML($tabla,2);
        }

        $mpdf->Output();
    }


    // vista para ver requerimientos denegados
    public function indexFechaRequerimientosDenegadosUnidades(){

        $anios = P_AnioPresupuesto::orderBy('id', 'DESC')->get();

        return view('backend.admin.presupuestounidad.requerimientos.requerimientosunidad.denegados.vistarequerimientosunidaddenegados', compact('anios'));
    }


    public function indexRequerimientosDenegadosUnidades($idanio){

        return view('backend.admin.presupuestounidad.requerimientos.requerimientosunidad.denegados.vistarequerimientos.vistarequerimientosdene', compact('idanio'));
    }

    public function tablaRequerimientosDenegadosUnidades($idanio){

        $arrayRequiAgrupada = RequisicionAgrupada::where('estado', 1)
            ->where('id_anio', $idanio)
            ->get();

        foreach ($arrayRequiAgrupada as $info){
            $info->fecha = date("d-m-Y", strtotime($info->fecha));
        }

        return view('backend.admin.presupuestounidad.requerimientos.requerimientosunidad.denegados.vistarequerimientos.tablarequerimientosdene', compact('arrayRequiAgrupada'));
    }


    public function indexRequeDeneUnidadesMateriales($idrequi){
        return view('backend.admin.presupuestounidad.requerimientos.requerimientosunidad.denegados.vistarequerimientosdetalle.vistarequerimientosdenemateriales', compact('idrequi'));
    }


    public function indexRequeDeneUnidadesMaterialesDetalle($idrequiAgrupa){

        $arrayRequiAgrupadaDeta = RequisicionAgrupadaDetalle::where('id_requi_agrupada', $idrequiAgrupa)->get();

        foreach ($arrayRequiAgrupadaDeta as $info){

            $infoRequiDetalle = RequisicionUnidadDetalle::where('id', $info->id_requi_unidad_detalle)->first();
            $infoMaterial = P_Materiales::where('id', $infoRequiDetalle->id_material)->first();

            $info->nommaterial = $infoMaterial->descripcion;
            $info->cantidad = $infoRequiDetalle->cantidad;
            $info->costo = '$' . number_format((float)$infoRequiDetalle->dinero_fijo, 2, '.', ',');
        }

        return view('backend.admin.presupuestounidad.requerimientos.requerimientosunidad.denegados.vistarequerimientosdetalle.tablarequerimientosdenemateriales', compact('arrayRequiAgrupadaDeta'));
    }




}

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
use App\Models\RequisicionUnidad;
use App\Models\RequisicionUnidadDetalle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CotizacionesUnidadController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    public function indexListarRequerimientosPendienteUnidad(){

        $proveedores = Proveedores::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.presupuestounidad.requerimientos.requerimientosunidad.revision.vistarequerimientosunidadrevision', compact('proveedores'));
    }

    public function indexTablaListarRequerimientosPendienteUnidad(){

        $data = DB::table('requisicion_unidad AS r')
            ->join('requisicion_unidad_detalle AS d', 'd.id_requisicion_unidad', '=', 'r.id')
            ->select('r.id')
            ->where('d.estado', 0)
            ->where('d.cancelado', 0)
            ->groupBy('r.id')
            ->get();

        $pilaIdRequisicion = array();

        foreach ($data as $dd){
            array_push($pilaIdRequisicion, $dd->id);
        }

        $listaRequisicion = RequisicionUnidad::whereIn('id', $pilaIdRequisicion)
            ->orderBy('fecha', 'DESC')
            ->get();

        foreach ($listaRequisicion as $ll){
            $ll->fecha = date("d-m-Y", strtotime($ll->fecha));

            $infoPresup = P_PresupUnidad::where('id', $ll->id_presup_unidad)->first();
            $infoDepar = P_Departamento::where('id', $infoPresup->id_departamento)->first();

            $ll->departamento = $infoDepar->nombre;

            // Necesito comprobar que no hay ningun material cotizado para poder cancelar
            $infoRequiDetalle = RequisicionUnidadDetalle::where('id_requisicion_unidad', $ll->id)
                ->where('estado', 1) // al menos 1 ya esta en proceso de cotizado
                ->where('cancelado', 0) // si ya esta cancelado, no hacer nada
                ->count();

            // Si se encuentra al menos 1, significa que un material ya tiene una cotización en proceso
            if($infoRequiDetalle > 0){
                $ll->puedecancelar = false;
            }else{
                $ll->puedecancelar = true;
            }
        }

        return view('backend.admin.presupuestounidad.requerimientos.requerimientosunidad.revision.tablarequerimientosunidadrevision', compact('listaRequisicion'));
    }

    // informacion de requisición para hacer la cotizacion
    public function informacionRequerimientoCotizarInfo(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($info = RequisicionUnidad::where('id', $request->id)->first()){

            $listado = RequisicionUnidadDetalle::where('id_requisicion_unidad', $request->id)
                ->where('estado', 0)
                ->where('cancelado', 0)
                ->get();

            foreach ($listado as $l){
                $data = P_Materiales::where('id', $l->id_material)->first();
                $data2 = P_UnidadMedida::where('id', $data->id_unidadmedida)->first();

                $l->nombre = $data->descripcion;
                $l->medida = $data2->nombre;
            }

            return ['success' => 1, 'info' => $info, 'listado' => $listado];
        }else{
            return ['success' => 2];
        }
    }


    // se envía los ID requi_detalle de proyectos para verificar y retornar información de lo que se cotizara
    public function verificarRequerimientoUnidadAcotizar(Request $request){

        // La lista de ID que llega son de requisicion_detalle

        // VERIFICAR QUE EXISTAN TODOS LOS MATERIALES A COTIZAR EN REQUISICIÓN DETALLE
        for ($i = 0; $i < count($request->lista); $i++) {

            // SI NO LA ENCUENTRA, EL ADMINISTRADOR BORRO EL MATERIAL A COTIZAR
            if(!RequisicionUnidadDetalle::where('id', $request->lista[$i])->first()){
                return ['success' => 1];
            }
        }

        $info = RequisicionUnidadDetalle::whereIn('id', $request->lista)
            ->orderBy('id', 'ASC')
            ->get();

        $totalCantidad = 0;
        $totalMulti = 0;

        foreach ($info as $dd){
            $infoCatalogo = P_Materiales::where('id', $dd->id_material)->first();
            $infoUnidad = P_UnidadMedida::where('id', $infoCatalogo->id_unidadmedida)->first();
            $infoCodigo = ObjEspecifico::where('id', $infoCatalogo->id_objespecifico)->first();

            $dd->nombre = $infoCatalogo->descripcion;
            $dd->medida = $infoUnidad->nombre;
            $dd->codigo = $infoCodigo->codigo . " - " . $infoCodigo->nombre;

        }

        $totalCantidad = number_format((float)$totalCantidad, 2, '.', ',');
        $totalMulti = number_format((float)$totalMulti, 2, '.', ',');

        return ['success' => 2, 'lista' => $info,
            'totalCantidad' => $totalCantidad,
            //'totalPrecio' => $totalPrecio,
            'totalMulti' => $totalMulti];
    }

    // guardar cotización para requerimiento de unidad
    public function guardarNuevaCotizacionRequeriUnidad(Request $request){


        DB::beginTransaction();

        try {

            $infoRequisicion = RequisicionUnidad::where('id', $request->idrequisicion)->first();
            $infoPresuUni = P_PresupUnidad::where('id', $infoRequisicion->id_presup_unidad)->first();
            $infoAnio = P_AnioPresupuesto::where('id', $infoPresuUni->id_anio)->first();

            if($infoAnio->permiso == 0){
                // sin permiso
                return ['success' => 6];
            }

            // VERIFICAR QUE EXISTAN TODOS LOS MATERIALES A COTIZAR EN REQUISICIÓN DETALLE
            for ($i = 0; $i < count($request->lista); $i++) {

                // SI NO LA ENCUENTRA, EL ADMINISTRADOR BORRO EL MATERIAL A COTIZAR
                if(!RequisicionUnidadDetalle::where('id', $request->lista[$i])->first()){
                    return ['success' => 1];
                }
            }

            // crear cotizacion
            $coti = new CotizacionUnidad();
            $coti->id_proveedor = $request->proveedor;
            $coti->id_requisicion_unidad = $request->idrequisicion;
            $coti->fecha = $request->fecha;
            $coti->fecha_estado = null;
            $coti->estado = 0;
            $coti->save();

            // ACTUALIZAR PRECIO DE CATALOGO DE MATERIALES
            for ($j = 0; $j < count($request->idfila); $j++) {

                if($inrequiuni = RequisicionUnidadDetalle::where('id', $request->idfila[$j])->first()) {

                    P_Materiales::where('id', $inrequiuni->id_material)->update([
                        'costo' => $request->unidades[$j], // obtenido del array,
                    ]);
                }
            }

            //foreach ($arrayRequiDetalle as $datainfo){
            for ($j = 0; $j < count($request->lista); $j++) {

                $infoRequiDetl = RequisicionUnidadDetalle::where('id', $request->lista[$j])->first();

                // MATERIAL A COTIZAR FUE CANCELADO
                if($infoRequiDetl->cancelado == 1){
                    return ['success' => 4];
                }

                if(CotizacionUnidadDetalle::where('id_requi_unidaddetalle', $infoRequiDetl->id)->first()){

                    // como ese material puede estar en multiples cotizaciones
                    $arrayCotiDetalle = CotizacionUnidadDetalle::where('id_requi_unidaddetalle', $infoRequiDetl->id)
                        ->select('id_cotizacion_unidad')
                        ->get();

                    // Por cada ID material de reui detalle, ya obtuve todos los ID
                    // de cotización. Para comprobar si está denegada o aprobada

                    // solo estado default
                    $arrayCotiConteo = CotizacionUnidad::whereIn('id', $arrayCotiDetalle)
                        ->whereIn('estado', [0]) // estado default
                        ->count();

                    // ESTE MATERIAL QUE VIENE YA ESTA EN MODO ESPERA, ES DECIR,
                    // YA FUE COTIZADO Y ESTA ESPERANDO UNA RESPUESTA DE APROBADA O DENEGADA
                    if($arrayCotiConteo > 0){
                        return ['success' => 2];
                    }

                    // solo estado default
                    $arrayCotiConteoAprobada = CotizacionUnidad::whereIn('id', $arrayCotiDetalle)
                        ->whereIn('estado', [1]) // estado aprobadas
                        ->get();

                    foreach ($arrayCotiConteoAprobada as $dd){
                        // Toda contenedor de la cotización
                        // conocer si la orden no está denegada para retornar
                        if(!OrdenUnidad::where('id_cotizacion', $dd->id)
                            ->where('estado', 1)->first()){
                            return ['success' => 2];
                        }
                    }
                }

                // **** VERIFICACIÓN DE SALDOS PORQUE LA COTIZACIÓN PUEDE CAMBIAR EL COSTO DEL MATERIAL
                // SE RESERVO X DINERO PERO AQUÍ PUEDE VALER MAS QUE LO RESERVADO

                $infoCatalogo = P_Materiales::where('id', $infoRequiDetl->id_material)->first();

                $infoObjeto = ObjEspecifico::where('id', $infoCatalogo->id_objespecifico)->first();
                $infoUnidad = P_UnidadMedida::where('id', $infoCatalogo->id_unidadmedida)->first();

                $txtObjeto = $infoObjeto->codigo . " - " . $infoObjeto->nombre;

                // como siempre busco material que estaban en el presupuesto, siempre encontrara
                // cuenta unidad y el ID de objeto específico
                $infoCuentaUnidad = CuentaUnidad::where('id_presup_unidad', $infoRequisicion->id_presup_unidad)
                    ->where('id_objespeci', $infoCatalogo->id_objespecifico)
                    ->first();


                // CÁLCULOS

                $totalRestante = 0;
                $totalRetenido = 0;

                // movimiento de cuentas (sube y baja)
                $infoMoviCuentaProySube = MoviCuentaUnidad::where('id_cuentaunidad_sube', $infoCuentaUnidad->id)
                    ->where('autorizado', 1) // autorizado por presupuesto
                    ->sum('dinero');

                $infoMoviCuentaProyBaja = MoviCuentaUnidad::where('id_cuentaunidad_baja', $infoCuentaUnidad->id)
                    ->where('autorizado', 1) // autorizado por presupuesto
                    ->sum('dinero');

                $totalMoviCuenta = $infoMoviCuentaProySube - $infoMoviCuentaProyBaja;

                // obtener lo guardado de ordenes de compra, para obtener su restante
                $arrayRestante = DB::table('cuentaunidad_restante AS pd')
                    ->join('requisicion_unidad_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                    ->select('rd.cantidad', 'rd.dinero')
                    ->where('pd.id_cuenta_unidad', $infoCuentaUnidad->id)
                    ->where('rd.cancelado', 0)
                    ->get();

                foreach ($arrayRestante as $dd){
                    $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
                }

                // información de saldos retenidos
                $infoSaldoRetenido = DB::table('cuentaunidad_retenido AS psr')
                    ->join('requisicion_unidad_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                    ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                    ->where('psr.id_cuenta_unidad', $infoCuentaUnidad->id)
                    ->where('rd.cancelado', 0)
                    ->get();

                foreach ($infoSaldoRetenido as $dd){
                    $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
                }


                // aquí se obtiene el Saldo Restante del código
                $totalRestanteSaldo = $totalMoviCuenta + ($infoCuentaUnidad->saldo_inicial - $totalRestante);

                // verificar cantidad * dinero del material nuevo
                $saldoMaterial = $infoRequiDetl->cantidad * $infoCatalogo->costo;

                // ************* NO SE RESTA EL SALDO RETENIDO. SOLO SE VERIFICA QUE HAYA SALDO RESTANTE.



                // verificar si alcanza el saldo para guardar la cotización
                if($this->redondear_dos_decimal($totalRestanteSaldo) < $this->redondear_dos_decimal($saldoMaterial)){
                    // retornar que no alcanza el saldo

                    // SALDO RESTANTE Y SALDO RETENIDO FORMATEADOS
                    $saldoRestanteFormat = number_format((float)$totalRestanteSaldo, 2, '.', ',');
                    $saldoRetenidoFormat = number_format((float)$totalRetenido, 2, '.', ',');

                    $saldoMaterial = number_format((float)$saldoMaterial, 2, '.', ',');

                    // disponible - retenido
                    // PASAR A NUMERO POSITIVO
                    $totalActualFormat = abs($totalRestanteSaldo - $totalRetenido);
                    $totalActualFormat = number_format((float)$totalActualFormat, 2, '.', ',');

                    return ['success' => 3, 'fila' => $i,
                        'obj' => $txtObjeto,
                        'disponibleFormat' => $saldoRestanteFormat, // esto va formateado
                        'retenidoFormat' => $saldoRetenidoFormat, // esto va formateado
                        'material' => $infoCatalogo,
                        'unidad' => $infoUnidad->medida,
                        'costo' => $saldoMaterial,
                        'totalactual' => $totalActualFormat
                    ];
                }else {

                    $detalle = new CotizacionUnidadDetalle();
                    $detalle->id_cotizacion_unidad = $coti->id;
                    $detalle->id_requi_unidaddetalle = $infoRequiDetl->id;
                    $detalle->cantidad = $infoRequiDetl->cantidad;
                    $detalle->precio_u = $infoCatalogo->costo;
                    $detalle->estado = 0;
                    $detalle->descripcion = $request->descripmate[$j];
                    $detalle->save();

                    // cambiar estado de requisiciones detalle porque ya fueron cotizadas
                    RequisicionUnidadDetalle::where('id', $infoRequiDetl->id)->update([
                        'estado' => 1,
                    ]);
                }
            } // end for

            DB::commit();
            return ['success' => 5];
        }catch(\Throwable $e){
            Log::info('ee' . $e);
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

        $lista = DB::table('cotizacion_unidad AS cu')
            ->join('requisicion_unidad AS ru', 'cu.id_requisicion_unidad', '=', 'ru.id')
            ->join('p_presup_unidad AS pu', 'ru.id_presup_unidad', '=', 'pu.id')
            ->select('cu.id', 'cu.estado', 'cu.id_proveedor', 'cu.id_requisicion_unidad', 'cu.fecha', 'pu.id_anio')
            ->where('cu.estado', 0)
            ->where('pu.id_anio', $idanio)
            ->orderBy('cu.id', 'ASC')
            ->get();

        foreach ($lista as $dd){

            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));

            $infoProveedor = Proveedores::where('id', $dd->id_proveedor)->first();
            $infoRequisicion = RequisicionUnidad::where('id', $dd->id_requisicion_unidad)->first();
            $infoPresupUni = P_PresupUnidad::where('id', $infoRequisicion->id_presup_unidad)->first();
            $infoDepar = P_Departamento::where('id', $infoPresupUni->id_departamento)->first();

            $dd->proveedor = $infoProveedor->nombre;
            $dd->necesidad = $infoRequisicion->necesidad;
            $dd->destino = $infoRequisicion->destino;
            $dd->departamento = $infoDepar->nombre;
        }

        return view('backend.admin.presupuestounidad.cotizaciones.pendientes.tablacotizacionpendienteunidad', compact('lista'));
    }

    // ver detalle de una cotización para unidades
    public function indexCotizacionUnidadDetalle($id){ // id de cotizacion unidad

        // destino, necesidad, proveedor, fecha cotizacion
        $cotizacion = CotizacionUnidad::where('id', $id)->first();
        $infoRequisicion = RequisicionUnidad::where('id', $cotizacion->id_requisicion_unidad)->first();
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

        return view('backend.admin.presupuestounidad.cotizaciones.individual.vistacotizacionindividualunidad', compact('id', 'infoRequisicion',
            'proveedor', 'infoCotiDetalle', 'fecha', 'totalCantidad', 'totalPrecio', 'totalTotal', 'cotizacion'));
    }

    // autorizar cotización unidad jefe uaci
    public function autorizarCotizacionUnidad(Request $request){

        DB::beginTransaction();

        try {

            $infoCotizacion = CotizacionUnidad::where('id', $request->id)->first();
            $infoRequisicion = RequisicionUnidad::where('id', $infoCotizacion->id_requisicion_unidad)->first();
            $infoPresupUni = P_PresupUnidad::where('id', $infoRequisicion->id_presup_unidad)->first();
            $infoAnio = P_AnioPresupuesto::where('id', $infoPresupUni->id_anio)->first();

            if($infoAnio->permiso == 0){
                // sin permiso
                return ['success' => 1];
            }

            // sacar año
            $infoAnio = DB::table('cotizacion_unidad AS cu')
                ->join('requisicion_unidad AS ru', 'cu.id_requisicion_unidad', '=', 'ru.id')
                ->join('p_presup_unidad AS pu', 'ru.id_presup_unidad', '=', 'pu.id')
                ->select('cu.id', 'pu.id_anio')
                ->where('cu.id', $request->id)
                ->first();

            if(CotizacionUnidad::where('id', $request->id)
                ->where('estado', 0)->first()){
                CotizacionUnidad::where('id', $request->id)->update([
                    'estado' => 1,
                    'fecha_estado' => Carbon::now('America/El_Salvador')
                ]);
            }

            DB::commit();
            return ['success' => 2, 'idanio' => $infoAnio->id_anio];
        }catch(\Throwable $e){
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
        $contrato = Administradores::orderBy('nombre')->get();
        return view('backend.admin.presupuestounidad.cotizaciones.procesada.vistacotizacionprocesadaunidad', compact('contrato', 'idanio'));
    }

    // retorna tabla con las cotizaciones autorizaciones para unidades
    public function tablaCotizacionesUnidadesAutorizadas($idanio){

        // autorizadas
        $lista = DB::table('cotizacion_unidad AS cu')
            ->join('requisicion_unidad AS ru', 'cu.id_requisicion_unidad', '=', 'ru.id')
            ->join('p_presup_unidad AS pu', 'ru.id_presup_unidad', '=', 'pu.id')
            ->select('cu.id', 'cu.estado', 'cu.id_proveedor', 'cu.id_requisicion_unidad', 'cu.fecha', 'pu.id_anio')
            ->where('cu.estado', 1)
            ->where('pu.id_anio', $idanio)
            ->orderBy('cu.fecha', 'DESC') // la ultima cotizacion quiero primero
            ->get();

        foreach ($lista as $dd){

            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));

            $infoProveedor = Proveedores::where('id', $dd->id_proveedor)->first();
            $infoRequisicion = RequisicionUnidad::where('id', $dd->id_requisicion_unidad)->first();
            $infoPresupUni = P_PresupUnidad::where('id', $infoRequisicion->id_presup_unidad)->first();
            $infoDepar = P_Departamento::where('id', $infoPresupUni->id_departamento)->first();

            $dd->proveedor = $infoProveedor->nombre;
            $dd->necesidad = $infoRequisicion->necesidad;
            $dd->destino = $infoRequisicion->destino;
            $dd->departamento = $infoDepar->nombre;

            if(OrdenUnidad::where('id_cotizacion', $dd->id)->first()){
                $dd->bloqueo = true;
            }else{
                $dd->bloqueo = false;
            }
        }

        return view('backend.admin.presupuestounidad.cotizaciones.procesada.tablacotizacionprocesadaunidad', compact('lista'));
    }

    // denegar una cotización de unidad
    public function denegarCotizacionUnidad(Request $request){

        DB::beginTransaction();

        try {

            // COTIZACIÓN DENEGADA
            $infoCotizacion = CotizacionUnidad::where('id', $request->id)->first();
            $infoRequisicion = RequisicionUnidad::where('id', $infoCotizacion->id_requisicion_unidad)->first();
            $infoPresupUni = P_PresupUnidad::where('id', $infoRequisicion->id_presup_unidad)->first();
            $infoAnio = P_AnioPresupuesto::where('id', $infoPresupUni->id_anio)->first();

            if($infoAnio->permiso == 0){
                // sin permiso
                return ['success' => 1];
            }

            CotizacionUnidad::where('id', $request->id)->update([
                'estado' => 2,
                'fecha_estado' => Carbon::now('America/El_Salvador')
            ]);

            // Hoy verificar cuales materiales fueron cotizados y volver a 0.
            // Para que puedan ser cotizados de nuevo.

            $listado = CotizacionUnidadDetalle::where('id_cotizacion_unidad', $request->id)->get();

            foreach ($listado as $ll){
                RequisicionUnidadDetalle::where('id', $ll->id_requi_unidaddetalle)->update([
                    'estado' => 0,
                ]);
            }

            DB::commit();
            return ['success' => 2, 'idanio' => $infoPresupUni->id_anio];

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

        // autorizadas
        $lista = DB::table('cotizacion_unidad AS cu')
            ->join('requisicion_unidad AS ru', 'cu.id_requisicion_unidad', '=', 'ru.id')
            ->join('p_presup_unidad AS pu', 'ru.id_presup_unidad', '=', 'pu.id')
            ->select('cu.id', 'cu.estado', 'cu.id_proveedor', 'cu.id_requisicion_unidad', 'cu.fecha', 'pu.id_anio')
            ->where('cu.estado', 2) // DENEGADAS
            ->where('pu.id_anio', $idanio)
            ->orderBy('cu.fecha', 'DESC') // la ultima cotizacion quiero primero
            ->get();

        foreach ($lista as $dd){

            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));

            $infoProveedor = Proveedores::where('id', $dd->id_proveedor)->first();
            $infoRequisicion = RequisicionUnidad::where('id', $dd->id_requisicion_unidad)->first();
            $infoPresupUni = P_PresupUnidad::where('id', $infoRequisicion->id_presup_unidad)->first();
            $infoDepar = P_Departamento::where('id', $infoPresupUni->id_departamento)->first();

            $dd->proveedor = $infoProveedor->nombre;
            $dd->necesidad = $infoRequisicion->necesidad;
            $dd->destino = $infoRequisicion->destino;
            $dd->departamento = $infoDepar->nombre;

            if(OrdenUnidad::where('id_cotizacion', $dd->id)->first()){
                $dd->bloqueo = true;
            }else{
                $dd->bloqueo = false;
            }
        }

        return view('backend.admin.presupuestounidad.cotizaciones.denegadas.tablacotizaciondenegadaunidad', compact('lista'));
    }

    public function vistaDetalleCotizacionUnidad($id){
        // id de cotizacion

        $cotizacion = CotizacionUnidad::where('id', $id)->first();
        $infoRequisicion = RequisicionUnidad::where('id', $cotizacion->id_requisicion_unidad)->first();
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

            /*if($infoUnidad = P_UnidadMedida::where('id', $infoMaterial->id_unidadmedida)->first()){
                $de->nombrematerial = $infoRequiDetalle->material_descripcion . " - " . $infoUnidad->nombre;
            }else{
                $de->nombrematerial = $infoRequiDetalle->material_descripcion;
            }*/
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
            'proveedor', 'infoCotiDetalle', 'fecha', 'totalCantidad', 'totalPrecio', 'totalTotal'));
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

            $costoporhoja += $multiFila;

            $multifila = number_format((float)$multiFila, 2, '.', ',');
            $dd->dinero_fijo = number_format((float)$dd->dinero_fijo, 2, '.', ',');
            $costoporhoja = number_format((float)$costoporhoja, 2, '.', ',');


            $dataArray[] = [
                'cantidad' => $dd->cantidad,
                'medida' => $infoMedida->nombre,
                'descripcion' => $dd->material_descripcion,
                'precio_u' => $dd->dinero_fijo,
                'costofila' => $multifila,
                'codigopres' => $infoObj->codigo,
                'costoxhoja' => $costoporhoja
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
            <td style='text-align: right; font-weight: bold; padding-top: 10px; font-size:11px;'>RECIBE UACI</td>
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

        $registro = DB::table('p_presup_unidad AS p')
            ->join('requisicion_unidad AS req', 'req.id_presup_unidad', '=', 'p.id')
            ->select('req.estado_denegado', 'req.texto_denegado', 'p.id_anio',
                'req.destino', 'req.fecha', 'req.necesidad', 'req.id AS idrequi', 'p.id_departamento')
            ->where('req.estado_denegado', 1) // solo denegados
            ->where('p.id_anio', $idanio)
            ->get();

        foreach ($registro as $dd){
            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));
            // obtener total de todos los materiales de la requisición
            $arrayDetalle = RequisicionUnidadDetalle::where('id_requisicion_unidad', $dd->idrequi)->get();

            $infoDepartamento = P_Departamento::where('id', $dd->id_departamento)->first();
            $dd->departamento = $infoDepartamento->nombre;

            $multi = 0;
            foreach ($arrayDetalle as $info){
                $multi += ($info->cantidad * $info->dinero_fijo);
            }

            $dd->multiplicado = '$' . number_format((float)$multi, 2, '.', ',');
        }

        return view('backend.admin.presupuestounidad.requerimientos.requerimientosunidad.denegados.vistarequerimientos.tablarequerimientosdene', compact('registro'));
    }


    public function indexRequeDeneUnidadesMateriales($idrequi){
        return view('backend.admin.presupuestounidad.requerimientos.requerimientosunidad.denegados.vistarequerimientosdetalle.vistarequerimientosdenemateriales', compact('idrequi'));
    }

    public function indexRequeDeneUnidadesMaterialesDetalle($idrequi){

        $registro = RequisicionUnidadDetalle::where('id', $idrequi)->get();

        foreach ($registro as $dd){
            $dd->dinero_fijo = '$' . number_format((float)$dd->dinero_fijo, 2, '.', ',');
        }

        return view('backend.admin.presupuestounidad.requerimientos.requerimientosunidad.denegados.vistarequerimientosdetalle.tablarequerimientosdenemateriales', compact('registro'));
    }

}

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
        $totalPrecio = 0;
        $totalMulti = 0;

        foreach ($info as $dd){
            $infoCatalogo = P_Materiales::where('id', $dd->id_material)->first();
            $infoUnidad = P_UnidadMedida::where('id', $infoCatalogo->id_unidadmedida)->first();
            $infoCodigo = ObjEspecifico::where('id', $infoCatalogo->id_objespecifico)->first();

            // ACTUALIZAR PRECIO
            RequisicionUnidadDetalle::where('id', $dd->id)->update([
                'dinero' => $infoCatalogo->costo
            ]);

            $dd->nombre = $infoCatalogo->descripcion;
            $dd->pu = $infoCatalogo->costo;
            $dd->medida = $infoUnidad->nombre;
            $dd->codigo = $infoCodigo->codigo . " - " . $infoCodigo->nombre;

            $multi = $dd->cantidad * $infoCatalogo->costo;
            $totalMulti = $totalMulti + $multi;

            $dd->multiTotal = number_format((float)$multi, 2, '.', ',');

            $totalCantidad = $totalCantidad + $dd->cantidad;
            $totalPrecio = $totalPrecio + $infoCatalogo->costo;
        }

        $totalCantidad = number_format((float)$totalCantidad, 2, '.', ',');
        $totalPrecio = number_format((float)$totalPrecio, 2, '.', ',');
        $totalMulti = number_format((float)$totalMulti, 2, '.', ',');

        return ['success' => 2, 'lista' => $info,
            'totalCantidad' => $totalCantidad,
            'totalPrecio' => $totalPrecio,
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

            // obtener todos los materiales de id requisiciín detalle
            $arrayRequiDetalle = RequisicionUnidadDetalle::whereIn('id', $request->lista)
                ->orderBy('id', 'ASC')
                ->get();

            foreach ($arrayRequiDetalle as $datainfo){

                // MATERIAL A COTIZAR FUE CANCELADO
                if($datainfo->cancelado == 1){
                    return ['success' => 4];
                }

                if(CotizacionUnidadDetalle::where('id_requi_unidaddetalle', $datainfo->id)->first()){

                    // como ese material puede estar en multiples cotizaciones
                    $arrayCotiDetalle = CotizacionUnidadDetalle::where('id_requi_unidaddetalle', $datainfo->id)
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

                $infoCatalogo = P_Materiales::where('id', $datainfo->id_material)->first();

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
                    ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                    ->select('rd.cantidad', 'rd.dinero')
                    ->where('pd.id_cuenta_unidad', $infoCuentaUnidad->id)
                    ->where('rd.cancelado', 0)
                    ->get();

                foreach ($arrayRestante as $dd){
                    $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
                }

                // información de saldos retenidos
                $infoSaldoRetenido = DB::table('cuentaunidad_retenido AS psr')
                    ->join('requisicion_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
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
                $saldoMaterial = $datainfo->cantidad * $infoCatalogo->costo;

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
                    $detalle->id_requi_unidaddetalle = $datainfo->id;
                    $detalle->cantidad = $datainfo->cantidad;
                    $detalle->precio_u = $infoCatalogo->costo;
                    $detalle->estado = 0;
                    $detalle->save();

                    // cambiar estado de requisiciones detalle porque ya fueron cotizadas
                    RequisicionUnidadDetalle::where('id', $datainfo->id)->update([
                        'estado' => 1,
                    ]);
                }
            } // end foreach

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

        $anios = P_AnioPresupuesto::orderBy('nombre', 'ASC')->get();

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
                $de->nombrematerial = $infoMaterial->descripcion . " - " . $infoUnidad->nombre;
            }else{
                $de->nombrematerial = $infoMaterial->descripcion;
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
        $anios = P_AnioPresupuesto::orderBy('nombre', 'ASC')->get();
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
        $anios = P_AnioPresupuesto::orderBy('nombre', 'ASC')->get();
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

            if($infoUnidad = P_UnidadMedida::where('id', $infoMaterial->id_unidadmedida)->first()){
                $de->nombrematerial = $infoMaterial->descripcion . " - " . $infoUnidad->nombre;
            }else{
                $de->nombrematerial = $infoMaterial->descripcion;
            }

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

}

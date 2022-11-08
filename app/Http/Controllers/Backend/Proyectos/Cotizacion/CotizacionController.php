<?php

namespace App\Http\Controllers\Backend\Proyectos\Cotizacion;

use App\Http\Controllers\Controller;
use App\Models\Administradores;
use App\Models\CatalogoMateriales;
use App\Models\Cotizacion;
use App\Models\CotizacionDetalle;
use App\Models\CuentaProy;
use App\Models\CuentaproyPartidaAdicional;
use App\Models\MoviCuentaProy;
use App\Models\ObjEspecifico;
use App\Models\Orden;
use App\Models\Proveedores;
use App\Models\Proyecto;
use App\Models\Requisicion;
use App\Models\RequisicionDetalle;
use App\Models\UnidadMedida;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class CotizacionController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    // retorna vista con las cotizaciones pendientes
    public function indexPendiente(){
        return view('backend.admin.proyectos.cotizaciones.pendiente.vistacotizacionpendienteing');
    }

    // retorna tabla con las cotizaciones pendientes
    public function indexPendienteTabla(){

        $lista = Cotizacion::where('estado', 0)->orderBy('id', 'ASC')->get();

        foreach ($lista as $dd){

            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));

            $infoProveedor = Proveedores::where('id', $dd->proveedor_id)->first();
            $infoRequisicion = Requisicion::where('id', $dd->requisicion_id)->first();
            $infoProyecto = Proyecto::where('id', $infoRequisicion->id_proyecto)->first();

            $dd->proveedor = $infoProveedor->nombre;
            $dd->necesidad = $infoRequisicion->necesidad;
            $dd->destino = $infoRequisicion->destino;
            $dd->codigoproyecto = $infoProyecto->codigo;
        }

        return view('backend.admin.proyectos.cotizaciones.pendiente.tablacotizacionpendienteing', compact('lista'));
    }

    // retorna vista de los detalle de la cotización, un uso es cuando uaci espera que sea aprobada la coti
    public function indexCotizacion($id){ // id de cotizacion

        // destino, necesidad, proveedor, fecha cotizacion
        $cotizacion = Cotizacion::where('id', $id)->first();
        $infoRequisicion = Requisicion::where('id', $cotizacion->requisicion_id)->first();
        $infoProveedor = Proveedores::where('id', $cotizacion->proveedor_id)->first();

        $proveedor = $infoProveedor->nombre;

        $infoCotiDetalle = CotizacionDetalle::where('cotizacion_id', $id)->get();
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

            $infoRequiDetalle = RequisicionDetalle::where('id', $de->id_requidetalle)->first();
            $infoMaterial = CatalogoMateriales::where('id', $infoRequiDetalle->material_id)->first();

            if($infoUnidad = UnidadMedida::where('id', $infoMaterial->id_unidadmedida)->first()){
                $de->nombrematerial = $infoMaterial->nombre . " - " . $infoUnidad->medida;
            }else{
                $de->nombrematerial = $infoMaterial->nombre;
            }

            $infoObjeto = ObjEspecifico::where('id', $infoMaterial->id_objespecifico)->first();
            $de->objeto = $infoObjeto->codigo . " - " . $infoObjeto->nombre;

            $de->precio_u = number_format((float)$de->precio_u, 2, '.', ',');
            $de->total = number_format((float)$multi, 2, '.', ',');
        }

        $totalCantidad = number_format((float)$totalCantidad, 2, '.', ',');
        $totalPrecio = number_format((float)$totalPrecio, 2, '.', ',');
        $totalTotal = number_format((float)$totalTotal, 2, '.', ',');

        return view('backend.admin.proyectos.cotizaciones.individual.vistacotizacionindividualing', compact('id', 'infoRequisicion',
            'proveedor', 'infoCotiDetalle', 'fecha', 'totalCantidad', 'totalPrecio', 'totalTotal', 'cotizacion'));
    }

    // autorizar la cotización
    public function autorizarCotizacion(Request $request){

        DB::beginTransaction();
        try {

            $infoCotizacion = Cotizacion::where('id', $request->id)->first();
            $infoRequisicion = Requisicion::where('id', $infoCotizacion->requisicion_id)->first();
            $infoProyecto = Proyecto::where('id', $infoRequisicion->id_proyecto)->first();

            if($infoProyecto->id_estado == 3){
                // pausado

                $texto = "El estado del proyecto es Pausado";
                return ['success' => 1, 'mensaje' => $texto];
            }

            if($infoProyecto->id_estado == 4){
                // finalizado

                $texto = "El estado del proyecto es Finalizado";
                return ['success' => 1, 'mensaje' => $texto];
            }


            if(Cotizacion::where('id', $request->id)
            ->where('estado', 0)->first()){
                Cotizacion::where('id', $request->id)->update([
                    'estado' => 1,
                    'fecha_estado' => Carbon::now('America/El_Salvador')
                ]);
            }

            DB::commit();
            return ['success' => 2];
        }catch(\Throwable $e){
            DB::rollback();
            return ['success' => 99];
        }
    }

    // denegar la cotización
    public function denegarCotizacion(Request $request){

        DB::beginTransaction();

        try {

            // COTIZACIÓN DENEGADA

            $infoCotizacion = Cotizacion::where('id', $request->id)->first();
            $infoRequisicion = Requisicion::where('id', $infoCotizacion->requisicion_id)->first();
            $infoProyecto = Proyecto::where('id', $infoRequisicion->id_proyecto)->first();

            if($infoProyecto->id_estado == 3){
                // pausado

                $texto = "El estado del proyecto es Pausado";
                return ['success' => 1, 'mensaje' => $texto];
            }

            if($infoProyecto->id_estado == 4){
                // finalizado

                $texto = "El estado del proyecto es Finalizado";
                return ['success' => 1, 'mensaje' => $texto];
            }


            Cotizacion::where('id', $request->id)->update([
                'estado' => 2,
                'fecha_estado' => Carbon::now('America/El_Salvador')
            ]);

            // hoy verificar cuales materiales fueron cotizados y volver a 0.
            // para que puedan ser cotizados de nuevo.

            $listado = CotizacionDetalle::where('cotizacion_id', $request->id)->get();

            foreach ($listado as $ll){
                RequisicionDetalle::where('id', $ll->id_requidetalle)->update([
                    'estado' => 0,
                ]);
            }

            DB::commit();
            return ['success' => 2];

        }catch(\Throwable $e){
            //Log::info('ee' . $e);
            DB::rollback();
            return ['success' => 99];
        }

    }

    // retorna vista de cotizaciones autorizadas
    public function indexAutorizadas(){
        $contrato = Administradores::orderBy('nombre')->get();

        return view('backend.admin.proyectos.cotizaciones.procesada.vistacotizacionprocesadaing', compact('contrato'));
    }

    // retorna tabla de cotizaciones autorizadas
    public function indexAutorizadasTabla(){

        // autorizadas
        $lista = Cotizacion::where('estado', 1)
           // ->whereNotIn('id', $pila) // no quiero las que ya se genero la orden de compra
            ->orderBy('fecha', 'DESC') // la ultima cotizacion quiero primero
            ->get();

        foreach ($lista as $dd){

            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));

            $infoProveedor = Proveedores::where('id', $dd->proveedor_id)->first();
            $infoRequisicion = Requisicion::where('id', $dd->requisicion_id)->first();
            $infoProyecto = Proyecto::where('id', $infoRequisicion->id_proyecto)->first();

            $dd->proveedor = $infoProveedor->nombre;
            $dd->necesidad = $infoRequisicion->necesidad;
            $dd->destino = $infoRequisicion->destino;
            $dd->codigoproyecto = $infoProyecto->codigo;

            if(Orden::where('cotizacion_id', $dd->id)->first()){
                $dd->bloqueo = true;
            }else{
                $dd->bloqueo = false;
            }
        }

        return view('backend.admin.proyectos.cotizaciones.procesada.tablacotizacionprocesadaing', compact('lista'));
    }

    // retorna vista de cotizaciones denegadas
    public function indexDenegadas(){

        return view('backend.admin.proyectos.cotizaciones.denegadas.vistacotizaciondenegadaing');
    }

    // retorna tabla de cotizaciones denegadas
    public function indexDenegadasTabla(){

        // denegadas
        $lista = Cotizacion::where('estado', 2)->orderBy('id', 'ASC')->get();

        foreach ($lista as $dd){

            $infoProveedor = Proveedores::where('id', $dd->proveedor_id)->first();
            $infoRequisicion = Requisicion::where('id', $dd->requisicion_id)->first();
            $infoProyecto = Proyecto::where('id', $infoRequisicion->id_proyecto)->first();

            $dd->proveedor = $infoProveedor->nombre;
            $dd->necesidad = $infoRequisicion->necesidad;
            $dd->destino = $infoRequisicion->destino;
            $dd->codigoproyecto = $infoProyecto->codigo;

            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));
        }

        return view('backend.admin.proyectos.cotizaciones.denegadas.tablacotizaciondenegadaing', compact('lista'));
    }

    // retorna vista con el proyecto, que tiene requerimientos pendientes de cotización
    public function indexListarRequerimientos(){
        return view('backend.admin.proyectos.requerimientos.vistarequerimientosing');
    }

    // retorna tabla con el proyecto, que tiene requerimientos pendientes de cotización
    public function indexTablaListarRequerimientos(){

        $data = DB::table('requisicion AS r')
            ->join('requisicion_detalle AS d', 'd.requisicion_id', '=', 'r.id')
            ->select('r.id_proyecto')
            ->where('d.estado', 0)
            ->where('d.cancelado', 0)
            ->groupBy('r.id_proyecto')
            ->get();

        $pila = array();

        foreach ($data as $dd){
            array_push($pila, $dd->id_proyecto);
        }

        $lista = Proyecto::whereIn('id', $pila)
            ->orderBy('fecha', 'DESC')
            ->get();

        foreach ($lista as $ll){
            $ll->fecha = date("d-m-Y", strtotime($ll->fecha));
        }

        return view('backend.admin.proyectos.requerimientos.tablarequerimientosing', compact('lista'));
    }

    // retorna vista de requisiciones pendientes de proyecto para ser cotizadas
    public function listadoRequerimientoPorProyecto($id){

        $proveedores = Proveedores::orderBy('nombre')->get();

        return view('backend.admin.proyectos.requerimientos.vistaindividualrequerimientoing', compact('id', 'proveedores'));
    }

    // retorna tabla de requisiciones pendientes de proyecto para ser cotizadas
    public function tablaRequerimientosIndividual($id){
        // se recibe ID de proyecto

        $data = DB::table('requisicion AS r')
            ->join('requisicion_detalle AS d', 'd.requisicion_id', '=', 'r.id')
            ->select('r.id')
            ->where('d.estado', 0)
            ->where('d.cancelado', 0)
            ->where('r.id_proyecto', $id)
            ->groupBy('r.id')
            ->get();

        $pila = array(); // array id de requerimientos

        foreach ($data as $dd){
            array_push($pila, $dd->id);
        }

        $lista = Requisicion::whereIn('id', $pila)->orderBy('fecha', 'ASC')->get();

        foreach ($lista as $dd){
            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));
        }

        return view('backend.admin.proyectos.requerimientos.tablaindividualrequerimientoing', compact('lista'));
    }

    // retorna información de requerimiento para ser cotizada
    public function informacionRequerimiento(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($info = Requisicion::where('id', $request->id)->first()){

            $listado = RequisicionDetalle::where('requisicion_id', $request->id)
                ->where('estado', 0)
                ->where('cancelado', 0)
                ->get();

            foreach ($listado as $l){
                $data = CatalogoMateriales::where('id', $l->material_id)->first();
                $data2 = UnidadMedida::where('id', $data->id_unidadmedida)->first();

                $l->nombre = $data->nombre;
                $l->medida = $data2->medida;
            }

            return ['success' => 1, 'info' => $info, 'listado' => $listado];
        }else{
            return ['success' => 2];
        }
    }

    // se envía los ID requi_detalle de proyectos para verificar y retornar información de lo que se cotizara
    public function verificarRequerimiento(Request $request){

        // La lista de ID que llega son de requisicion_detalle

        // VERIFICAR QUE EXISTAN TODOS LOS MATERIALES A COTIZAR EN REQUISICIÓN DETALLE
        for ($i = 0; $i < count($request->lista); $i++) {

            // SI NO LA ENCUENTRA, EL ADMINISTRADOR BORRO EL MATERIAL A COTIZAR
            if(!RequisicionDetalle::where('id', $request->lista[$i])->first()){
                return ['success' => 1];
            }
        }

        $info = RequisicionDetalle::whereIn('id', $request->lista)
            ->orderBy('id', 'ASC')
            ->get();

        $totalCantidad = 0;
        $totalPrecio = 0;
        $totalMulti = 0;

        foreach ($info as $dd){
            $infoCatalogo = CatalogoMateriales::where('id', $dd->material_id)->first();
            $infoUnidad = UnidadMedida::where('id', $infoCatalogo->id_unidadmedida)->first();
            $infoCodigo = ObjEspecifico::where('id', $infoCatalogo->id_objespecifico)->first();

            // ACTUALIZAR PRECIO
            RequisicionDetalle::where('id', $dd->id)->update([
                'dinero' => $infoCatalogo->pu
            ]);

            $dd->nombre = $infoCatalogo->nombre;
            $dd->pu = $infoCatalogo->pu;
            $dd->medida = $infoUnidad->medida;
            $dd->codigo = $infoCodigo->codigo . " - " . $infoCodigo->nombre;

            $multi = $dd->cantidad * $infoCatalogo->pu;
            $totalMulti = $totalMulti + $multi;

            $dd->multiTotal = number_format((float)$multi, 2, '.', ',');

            $totalCantidad = $totalCantidad + $dd->cantidad;
            $totalPrecio = $totalPrecio + $infoCatalogo->pu;
        }

        $totalCantidad = number_format((float)$totalCantidad, 2, '.', ',');
        $totalPrecio = number_format((float)$totalPrecio, 2, '.', ',');
        $totalMulti = number_format((float)$totalMulti, 2, '.', ',');

        return ['success' => 2, 'lista' => $info,
            'totalCantidad' => $totalCantidad,
            'totalPrecio' => $totalPrecio,
            'totalMulti' => $totalMulti];
    }

    // guarda una nueva cotización
    public function guardarNuevaCotizacion(Request $request){

        DB::beginTransaction();

        try {

            $infoRequisicion = Requisicion::where('id', $request->idrequisicion)->first();
            $infoProyecto = Proyecto::where('id', $infoRequisicion->id_proyecto)->first();

            if($infoProyecto->id_estado == 3){
                // pausado

                $texto = "El estado del proyecto es Pausado";
                return ['success' => 6, 'mensaje' => $texto];
            }

            if($infoProyecto->id_estado == 4){
                // finalizado

                $texto = "El estado del proyecto es Finalizado";
                return ['success' => 6, 'mensaje' => $texto];
            }

            // VERIFICAR QUE EXISTAN TODOS LOS MATERIALES A COTIZAR EN REQUISICIÓN DETALLE
            for ($i = 0; $i < count($request->lista); $i++) {

                // SI NO LA ENCUENTRA, EL ADMINISTRADOR BORRO EL MATERIAL A COTIZAR
                if(!RequisicionDetalle::where('id', $request->lista[$i])->first()){
                    return ['success' => 1];
                }
            }

            // crear cotizacion
            $coti = new Cotizacion();
            $coti->proveedor_id = $request->proveedor;
            $coti->requisicion_id = $request->idrequisicion;
            $coti->fecha = $request->fecha;
            $coti->fecha_estado = null;
            $coti->estado = 0;
            $coti->save();

            // obtener todos los materiales de id requisiciín detalle
            $arrayRequiDetalle = RequisicionDetalle::whereIn('id', $request->lista)
                ->orderBy('id', 'ASC')
                ->get();

            foreach ($arrayRequiDetalle as $datainfo){

                // MATERIAL A COTIZAR FUE CANCELADO
                if($datainfo->cancelado == 1){
                    return ['success' => 4];
                }

                if(CotizacionDetalle::where('id_requidetalle', $datainfo->id)->first()){

                    // como ese material puede estar en multiples cotizaciones
                    $arrayCotiDetalle = CotizacionDetalle::where('id_requidetalle', $datainfo->id)
                        ->select('cotizacion_id')
                        ->get();

                    // Por cada ID material de reui detalle, ya obtuve todos los ID
                    // de cotización. Para comprobar si está denegada o aprobada

                    // solo estado default
                    $arrayCotiConteo = Cotizacion::whereIn('id', $arrayCotiDetalle)
                        ->whereIn('estado', [0]) // estado default
                        ->count();

                    // ESTE MATERIAL QUE VIENE YA ESTA EN MODO ESPERA, ES DECIR,
                    // YA FUE COTIZADO Y ESTA ESPERANDO UNA RESPUESTA DE APROBADA O DENEGADA
                    if($arrayCotiConteo > 0){
                        return ['success' => 2];
                    }

                    // solo estado default
                    $arrayCotiConteoAprobada = Cotizacion::whereIn('id', $arrayCotiDetalle)
                        ->whereIn('estado', [1]) // estado aprobadas
                        ->get();

                    foreach ($arrayCotiConteoAprobada as $dd){
                        // Toda contenedor de la cotización
                        // conocer si la orden no está denegada para retornar
                        if(!Orden::where('cotizacion_id', $dd->id)
                        ->where('estado', 1)->first()){
                            return ['success' => 2];
                        }
                    }
                }

                // **** VERIFICACIÓN DE SALDOS PORQUE LA COTIZACIÓN PUEDE CAMBIAR EL COSTO DEL MATERIAL
                // SE RESERVO X DINERO PERO AQUÍ PUEDE VALER MAS QUE LO RESERVADO

                $infoCatalogo = CatalogoMateriales::where('id', $datainfo->material_id)->first();

                $infoObjeto = ObjEspecifico::where('id', $infoCatalogo->id_objespecifico)->first();
                $infoUnidad = UnidadMedida::where('id', $infoCatalogo->id_unidadmedida)->first();

                $txtObjeto = $infoObjeto->codigo . " - " . $infoObjeto->nombre;

                // como siempre busco material que estaban en el presupuesto, siempre encontrara
                // el proyecto ID y el ID de objeto específico
                $infoCuentaProy = CuentaProy::where('proyecto_id', $infoRequisicion->id_proyecto)
                    ->where('objespeci_id', $infoCatalogo->id_objespecifico)
                    ->first();


                // CÁLCULOS

                $totalRestante = 0;
                $totalRetenido = 0;

                // movimiento de cuentas (sube y baja)
                $infoMoviCuentaProySube = MoviCuentaProy::where('id_cuentaproy_sube', $infoCuentaProy->id)
                    ->where('autorizado', 1) // autorizado por presupuesto
                    ->sum('dinero');

                $infoMoviCuentaProyBaja = MoviCuentaProy::where('id_cuentaproy_baja', $infoCuentaProy->id)
                    ->where('autorizado', 1) // autorizado por presupuesto
                    ->sum('dinero');

                $totalMoviCuenta = $infoMoviCuentaProySube - $infoMoviCuentaProyBaja;

                // obtener lo guardado de ordenes de compra, para obtener su restante
                $arrayRestante = DB::table('cuentaproy_restante AS pd')
                    ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                    ->select('rd.cantidad', 'rd.dinero')
                    ->where('pd.id_cuentaproy', $infoCuentaProy->id)
                    ->where('rd.cancelado', 0)
                    ->get();

                foreach ($arrayRestante as $dd){
                    $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
                }

                $infoCuentaPartida = CuentaproyPartidaAdicional::where('id_proyecto', $infoRequisicion->id_proyecto)->get();

                $sumaPartidaAdicional = 0;
                foreach ($infoCuentaPartida as $dd){
                    if($infoCuentaProy->objespeci_id == $dd->objespeci_id){
                        $sumaPartidaAdicional += $dd->monto;
                    }
                }

                // información de saldos retenidos
                $infoSaldoRetenido = DB::table('cuentaproy_retenido AS psr')
                    ->join('requisicion_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                    ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                    ->where('psr.id_cuentaproy', $infoCuentaProy->id)
                    ->where('rd.cancelado', 0)
                    ->get();

                foreach ($infoSaldoRetenido as $dd){
                    $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
                }


                $sumaPartidaAdicional += $infoCuentaProy->saldo_inicial;

                // aquí se obtiene el Saldo Restante del código
                $totalRestanteSaldo = $totalMoviCuenta + ($sumaPartidaAdicional - $totalRestante);


                // verificar cantidad * dinero del material nuevo
                $saldoMaterial = $datainfo->cantidad * $infoCatalogo->pu;

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

                    $detalle = new CotizacionDetalle();
                    $detalle->cotizacion_id = $coti->id;
                    $detalle->id_requidetalle = $datainfo->id;
                    $detalle->cantidad = $datainfo->cantidad;
                    $detalle->precio_u = $infoCatalogo->pu;
                    $detalle->estado = 0;
                    $detalle->save();

                    // cambiar estado de requisiciones detalle porque ya fueron cotizadas
                    RequisicionDetalle::where('id', $datainfo->id)->update([
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

    // vista de cotización detalle para procesadas o denegadas
    public function vistaDetalleCotizacion($id){
        // id de cotizacion

        $cotizacion = Cotizacion::where('id', $id)->first();
        $infoRequisicion = Requisicion::where('id', $cotizacion->requisicion_id)->first();
        $infoProveedor = Proveedores::where('id', $cotizacion->proveedor_id)->first();

        $proveedor = $infoProveedor->nombre;

        $infoCotiDetalle = CotizacionDetalle::where('cotizacion_id', $id)->get();
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

            $infoRequiDetalle = RequisicionDetalle::where('id', $de->id_requidetalle)->first();
            $infoMaterial = CatalogoMateriales::where('id', $infoRequiDetalle->material_id)->first();

            if($infoUnidad = UnidadMedida::where('id', $infoMaterial->id_unidadmedida)->first()){
                $de->nombrematerial = $infoMaterial->nombre . " - " . $infoUnidad->medida;
            }else{
                $de->nombrematerial = $infoMaterial->nombre;
            }

            $infoObjeto = ObjEspecifico::where('id', $infoMaterial->id_objespecifico)->first();
            $de->objeto = $infoObjeto->codigo . " - " . $infoObjeto->nombre;

            $de->precio_u = number_format((float)$de->precio_u, 2, '.', ',');
            $de->total = number_format((float)$multi, 2, '.', ',');
        }

        $totalCantidad = number_format((float)$totalCantidad, 2, '.', ',');
        $totalPrecio = number_format((float)$totalPrecio, 2, '.', ',');
        $totalTotal = number_format((float)$totalTotal, 2, '.', ',');

        return view('backend.admin.proyectos.cotizaciones.individual.vistacotizaciondetalleing', compact('id', 'infoRequisicion',
            'proveedor', 'infoCotiDetalle', 'fecha', 'totalCantidad', 'totalPrecio', 'totalTotal'));
    }


}

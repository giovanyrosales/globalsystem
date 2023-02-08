<?php

namespace App\Http\Controllers\Backend\PresupuestoUnidad\Requerimientos;

use App\Http\Controllers\Controller;
use App\Models\CotizacionUnidad;
use App\Models\CotizacionUnidadDetalle;
use App\Models\Cuenta;
use App\Models\CuentaUnidad;
use App\Models\CuentaUnidadRetenido;
use App\Models\MoviCuentaUnidad;
use App\Models\ObjEspecifico;
use App\Models\OrdenUnidad;
use App\Models\P_AnioPresupuesto;
use App\Models\P_Departamento;
use App\Models\P_Materiales;
use App\Models\P_PresupUnidad;
use App\Models\P_PresupUnidadDetalle;
use App\Models\P_UnidadMedida;
use App\Models\P_UsuarioDepartamento;
use App\Models\RequisicionUnidad;
use App\Models\RequisicionUnidadDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class RequerimientosUnidadController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    // retornar vista para poder elegir año de presupuesto para solicitar requerimiento
    public function indexBuscarAñoPresupuesto(){
        $anios = P_AnioPresupuesto::orderBy('nombre', 'DESC')->get();
        return view('backend.admin.presupuestounidad.requerimientos.buscaraniopresupuesto.vistaaniorequerimiento', compact('anios'));
    }

    // verifica si puede hacer requerimientos segun año de presupuesto
    public function verificarEstadoPresupuesto(Request $request){

        $idusuario = Auth::id();

        // primero ver si está registrado en un departamento el usuario
        if(!P_UsuarioDepartamento::where('id_usuario', $idusuario)->first()){
           return ['success' => 1];
        }

        $infoUsuarioDepa = P_UsuarioDepartamento::where('id_usuario', $idusuario)->first();

        // verificar si presupuesto esta creado y aprobado
        if($infoPresuUnidad = P_PresupUnidad::where('id_anio', $request->anio)
            ->where('id_departamento', $infoUsuarioDepa->id_departamento)
            ->first()){

            // en modo desarrollo
            if($infoPresuUnidad->id_estado == 1){
                return ['success' => 2];
            }

            // en modo revisión
            if($infoPresuUnidad->id_estado == 2){
                return ['success' => 3];
            }

            // está aprobado, pero es de ver si ya crearon las cuentas unidades

            if(CuentaUnidad::where('id_presup_unidad', $infoPresuUnidad->id)->first()){
               // PASAR A PANTALLA REQUERIMIENTOS
                return ['success' => 4];
            }else{
               return ['success' => 5];
            }

        }else{
            // no está creado aun, asi que agregar a pendientes
            return ['success' => 6];
        }
    }

    // retorna vista donde están los requerimientos por año
    public function indexRequerimientosUnidades($idanio){

        // obtener usuario
        $user = Auth::user();

        // conseguir el departamento
        $infoDepartamento = P_UsuarioDepartamento::where('id_usuario', $user->id)->first();
        $infoAnio = P_AnioPresupuesto::where('id', $idanio)->first();

        $txtanio = $infoAnio->nombre;
        $bloqueo = $infoAnio->permiso;

        $infoPresuUnidad = P_PresupUnidad::where('id_anio', $idanio)
            ->where('id_departamento', $infoDepartamento->id_departamento)
            ->first();

        $monto = CuentaUnidad::where('id_presup_unidad', $infoPresuUnidad->id)->sum('saldo_inicial_fijo');

        $monto = '$' . number_format((float)$monto, 2, '.', ',');

        $conteo = RequisicionUnidad::where('id_presup_unidad', $infoPresuUnidad->id)->count();
        if($conteo == null){
            $conteo = 1;
        }else{
            $conteo += 1;
        }

        $idpresubunidad = $infoPresuUnidad->id;

        return view('backend.admin.presupuestounidad.requerimientos.requerimientosunidad.vistarequerimientosunidad', compact('monto'
        , 'txtanio', 'idanio', 'conteo', 'idpresubunidad', 'bloqueo'));
    }

    // retorna tabla donde están los requerimientos por año
    public function tablaRequerimientosUnidades($idpresubunidad){

        // listado de requisiciones
        $listaRequisicion = RequisicionUnidad::where('id_presup_unidad', $idpresubunidad)
            ->orderBy('fecha', 'ASC')
            ->get();

        $contador = 0;
        foreach ($listaRequisicion as $ll){
            $contador += 1;
            $ll->numero = $contador;
            $ll->fecha = date("d-m-Y", strtotime($ll->fecha));

            $infoEstado = "Editar";
            //------------------------------------------------------------------
            // SI HAY MATERIAL COTIZADO, NO PODRA BORRAR REQUISICIÓN YA
            $hayCotizacion = true;
            if(CotizacionUnidad::where('id_requisicion_unidad', $ll->id)->first()){
                $hayCotizacion = false;
                $infoEstado = "Información"; // no tomar si esta default, aprobado, denegado
            }

            $ll->haycotizacion = $hayCotizacion;
            $ll->estado = $infoEstado;

        } // end foreach

        return view('backend.admin.presupuestounidad.requerimientos.requerimientosunidad.tablarequerimientosunidad', compact('listaRequisicion'));
    }

    // visualizar MODAL DE SALDOS para unidades. se recibe id p_presup_unidad
    public function infoModalSaldoUnidad($idpresup){

        // presupuesto
        $presupuesto = DB::table('cuenta_unidad AS cu')
            ->join('obj_especifico AS obj', 'cu.id_objespeci', '=', 'obj.id')
            ->select('obj.nombre', 'obj.id AS idcodigo', 'obj.codigo', 'cu.id', 'cu.saldo_inicial')
            ->where('cu.id_presup_unidad', $idpresup)
            ->get();

        foreach ($presupuesto as $pp){

            // CÁLCULOS

            $totalRestante = 0;
            $totalRetenido = 0;

            // movimiento de cuentas SUBE
            $infoMoviCuentaUnidadSube = MoviCuentaUnidad::where('id_cuentaunidad_sube', $pp->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            // movimiento de cuentas BAJA
            $infoMoviCuentaUnidadBaja = MoviCuentaUnidad::where('id_cuentaunidad_baja', $pp->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $totalMoviCuenta = $infoMoviCuentaUnidadSube - $infoMoviCuentaUnidadBaja;

            // obtener todas las salidas de material
            $arrayRestante = DB::table('cuentaunidad_restante AS pd')
                ->join('requisicion_unidad_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero')
                ->where('pd.id_cuenta_unidad', $pp->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRestante as $dd){
                $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
            }

            // información de saldos retenidos
            $arrayRetenido = DB::table('cuentaunidad_retenido AS psr')
                ->join('requisicion_unidad_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('psr.id_cuenta_unidad', $pp->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRetenido as $dd){
                $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
            }

            // aquí se obtiene el Saldo Restante del código
            $totalRestanteSaldo = $totalMoviCuenta + ($pp->saldo_inicial - $totalRestante);

            $pp->saldo_inicial = number_format((float)$pp->saldo_inicial, 2, '.', ',');
            $pp->saldo_restante = number_format((float)$totalRestanteSaldo, 2, '.', ',');
            $pp->total_retenido = number_format((float)$totalRetenido, 2, '.', ',');

            $pp->saldoRestante = $this->redondear_dos_decimal($totalRestanteSaldo);
            $pp->totalRetenido = $this->redondear_dos_decimal($totalRetenido);
        }

        return view('backend.admin.presupuestounidad.requerimientos.modal.vistamodalsaldounidad', compact('presupuesto'));
    }

    function redondear_dos_decimal($valor){
        $float_redondeado = round($valor * 100) / 100;
        return $float_redondeado;
    }



    public function buscadorMaterialRequisicionUnidad(Request $request){

        if($request->get('query')){
            $query = $request->get('query');

            // idpresuunidad

            $arrayPresuDetalle = P_PresupUnidadDetalle::where('id_presup_unidad', $request->idpresuunidad)->get();

            $pilaIdMateriales = array();

            foreach ($arrayPresuDetalle as $dd){
                array_push($pilaIdMateriales, $dd->id_material);
            }

            // array de materiales materiales adicionales
            $arrayMateriales = P_Materiales::whereIn('id', $pilaIdMateriales)
            ->where('descripcion', 'LIKE', "%{$query}%")
                ->take(40)
                ->get();

            // BÚSQUEDA

            $output = '<ul class="dropdown-menu" style="display:block; position:relative;">';
            $tiene = true;
            foreach($arrayMateriales as $row){

                $infoUnidad = P_UnidadMedida::where('id', $row->id_unidadmedida)->first();
                $row->unido = $row->descripcion . ' - ' . $infoUnidad->nombre;

                // si solo hay 1 fila, No mostrara el hr, salto de linea
                if(count($arrayMateriales) == 1){
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValorRequisicion(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->unido.'</a></li>
                ';
                    }
                }

                else{
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValorRequisicion(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->unido.'</a></li>
                   <hr>
                ';
                    }
                }
            }

            $output .= '</ul>';
            if($tiene){
                $output = '';
            }
            echo $output;
        }
    }

    // registrar una nueva requisición para unidades
    public function nuevoRequisicionUnidades(Request $request){

        $rules = array(
            'fecha' => 'required',
            'idpresubuni' => 'required',
            'idanio' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            // VERIFICAR SI ES PERMITIDO REALIZAR REQUISICION
            if($infoAnioPre = P_AnioPresupuesto::where('id', $request->idanio)->first()){
                if($infoAnioPre->permiso == 0){
                    return ['success' => 3];
                }
            }

            // primero se crea, y después verificamos
            $r = new RequisicionUnidad();
            $r->id_presup_unidad = $request->idpresubuni;
            $r->destino = $request->destino;
            $r->fecha = $request->fecha;
            $r->necesidad = $request->necesidad;
            $r->req_revision = 0; // NECESITA SER REVISADO POR PRESUPUESTO
            $r->save();

            for ($i = 0; $i < count($request->cantidad); $i++) {

                $infoCatalogo = P_Materiales::where('id', $request->datainfo[$i])->first();

                // en esta parte ya debera tener objeto específico
                $infoObjeto = ObjEspecifico::where('id', $infoCatalogo->id_objespecifico)->first();
                $txtObjeto = $infoObjeto->codigo . " - " . $infoObjeto->nombre;

                // verificar el presupuesto detalle para el obj especifico de este material
                // obtener el saldo inicial - total de salidas y esto dara cuanto tengo en caja

                // como siempre busco material que estaban en el presupuesto, siempre encontrara
                // el proyecto ID y el ID de objeto específico
                $infoCuentaUnidad = CuentaUnidad::where('id_presup_unidad', $request->idpresubuni)
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

                // obtener todas las salidas de material
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
                $arrayRetenido = DB::table('cuentaunidad_retenido AS psr')
                    ->join('requisicion_unidad_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                    ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                    ->where('psr.id_cuenta_unidad', $infoCuentaUnidad->id)
                    ->where('rd.cancelado', 0)
                    ->get();

                foreach ($arrayRetenido as $dd){
                    $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
                }

                // aquí se obtiene el Saldo Restante del código
                $totalRestanteSaldo = $totalMoviCuenta + ($infoCuentaUnidad->saldo_inicial - $totalRestante);

                // Esto es lo que hay SALDO RESTANTE quitando el retenido
                $totalCalculado = $totalRestanteSaldo - $totalRetenido;


                // Verificar cantidad * dinero del material nuevo.
                // Este dinero se está solicitando para la fila.
                $saldoMaterial = $request->cantidad[$i] * $infoCatalogo->costo;


                if($this->redondear_dos_decimal($totalCalculado) < $this->redondear_dos_decimal($saldoMaterial)){

                    // retornar que no alcanza el saldo

                    // SALDO RESTANTE Y SALDO RETENIDO FORMATEADOS
                    $restanteFormat = number_format((float)$totalRestanteSaldo, 2, '.', ',');
                    $retenidoFormat = number_format((float)$totalRetenido, 2, '.', ',');

                    $saldoMaterial = number_format((float)$saldoMaterial, 2, '.', ',');

                    return ['success' => 1, 'fila' => $i,
                        'obj' => $txtObjeto,
                        'restanteFormat' => $restanteFormat,
                        'retenidoFormat' => $retenidoFormat,
                        'retenido' => $totalRetenido,
                        'solicita' => $saldoMaterial, // el usuario solicita este dinero
                    ];

                }else{

                    // si hay saldo para este material
                    // guardar detalle cotización e ingresar una salida de dinero

                    $rDetalle = new RequisicionUnidadDetalle();
                    $rDetalle->id_requisicion_unidad = $r->id;
                    $rDetalle->id_material = $request->datainfo[$i];
                    $rDetalle->cantidad = $request->cantidad[$i];
                    $rDetalle->material_descripcion = $request->materialDescriptivo[$i];
                    $rDetalle->estado = 0;
                    $rDetalle->dinero = $infoCatalogo->costo; // lo que vale el material en ese momento
                    $rDetalle->cancelado = 0;
                    $rDetalle->save();

                    // guardar el SALDO RETENIDO
                    $rRetenido = new CuentaUnidadRetenido();
                    $rRetenido->id_requi_detalle = $rDetalle->id;
                    $rRetenido->id_cuenta_unidad = $infoCuentaUnidad->id;

                    $rRetenido->save();
                }
            }

            $contador = RequisicionUnidadDetalle::where('id_requisicion_unidad', $r->id)->count();
            $contador += 1;

            DB::commit();
            return ['success' => 2, 'contador' => $contador];

        }catch(\Throwable $e){
            Log::info('ERROR ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // borrar requisición de unidades
    public function borrarRequisicionUnidades(Request $request){

        $regla = array(
            'id' => 'required', // id requisicion unidad
            'idanio' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        if(RequisicionUnidad::where('id', $request->id)->first()){

            $infoAnio = P_AnioPresupuesto::where('id', $request->idanio)->first();

            if($infoAnio->permiso == 0){
                // no hay permiso para modificar requisiciones
                return ['success' => 1];
            }

            // buscar si no hay ningún material ya cotizado
            if(CotizacionUnidad::where('id_requisicion_unidad', $request->id)->first()){
                // SE ENCONTRO UN MATERIAL COTIZADO, RETORNAR.
                return ['success' => 2];
            }

            // obtener todos los ID REQUISICION DETALLE CON EL ID REQUISICION
            $arrayID = RequisicionUnidadDetalle::where('id_requisicion_unidad', $request->id)
                ->select('id')
                ->get();

            // LIBERAR SALDO RETENIDO
            CuentaUnidadRetenido::whereIn('id_requi_detalle', $arrayID)->delete();

            // borrar listado detalle
            RequisicionUnidadDetalle::where('id_requisicion_unidad', $request->id)->delete();

            // borrar requisicion
            RequisicionUnidad::where('id', $request->id)->delete();

            return ['success' => 3];
        }else{
            return ['success' => 99];
        }
    }

    // informacion de una requisicion unidad
    function informacionRequisicionUnidad(Request $request){
        $rules = array(
            'id' => 'required', // id fila requisicion
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){
            return ['success' => 0];
        }

        if($info = RequisicionUnidad::where('id', $request->id)->first()){

            $detalle = RequisicionUnidadDetalle::where('id_requisicion_unidad', $request->id)
                ->orderBy('id', 'ASC')
                ->get();

            foreach ($detalle as $deta) {

                $multi = ($deta->cantidad * $deta->dinero);
                $multi = number_format((float)$multi, 2, '.', ',');
                $deta->multiplicado = $multi;

                $infoCatalogo = P_Materiales::where('id', $deta->id_material)->first();

                //-------------------------------------------

                // VERIFICAR QUE ESTE MATERIAL ESTE COTIZADO. PARA NO BORRARLO O PARA CANCELAR SI YA NO LO VA A QUERER

                $infoCotiDetalle = CotizacionUnidadDetalle::where('id_requi_unidaddetalle', $deta->id)->get();
                $pilaIdCotizacion = array();
                $haycoti = false;
                foreach ($infoCotiDetalle as $dd){
                    $haycoti = true;
                    array_push($pilaIdCotizacion, $dd->id_cotizacion_unidad);
                }

                // saber si ya fue aprobado alguna cotizacion o todas han sido denegadas
                $infoCoti = CotizacionUnidad::whereIn('id', $pilaIdCotizacion)
                    ->where('estado', 1) // aprobados
                    ->count();

                if($infoCoti > 0){
                    // COTI APROBADA, NO PUEDE BORRAR
                    $infoEstado = 1;

                    // verificar si la orden de compra con esa cotización fue denegada, para cancelar

                    // todas las cotizaciones donde puede estar este MATERIAL DE REQUI DETALLE
                    $arrayCoti = CotizacionUnidad::whereIn('id', $pilaIdCotizacion)->get();
                    $pilaCoti = array();
                    foreach ($arrayCoti as $dd){
                        array_push($pilaCoti, $dd->id);
                    }

                    // ver si existe al menos 1 orden
                    if(OrdenUnidad::whereIn('id_cotizacion', $pilaCoti)->first()){
                        $conteoOrden = OrdenUnidad::whereIn('id_cotizacion', $pilaCoti)
                            ->where('estado', 0) // APROBADA LA ORDEN
                            ->count();

                        if($conteoOrden > 0){
                            // material tiene una orden aprobada
                        }else{
                            // material tiene una orden denegada
                            $infoEstado = 2;
                        }
                    }
                }else{
                    // COTI DENEGADA, PUEDE CANCELAR MATERIAL
                    $infoEstado = 2;
                }

                $deta->cotizado = $infoEstado;
                $deta->haycoti = $haycoti;
                //-------------------------------------------------

                $infoObjeto = ObjEspecifico::where('id', $infoCatalogo->id_objespecifico)->first();
                $infoUnidad = P_UnidadMedida::where('id', $infoCatalogo->id_unidadmedida)->first();

                $unidoCodigo = $infoObjeto->codigo . " - " . $infoObjeto->nombre;
                $unidoNombre = $infoCatalogo->descripcion . " - " . $infoUnidad->nombre;

                $deta->descripcion = $unidoNombre;
                $deta->codigo = $unidoCodigo;


            }// end foreach

            // conocer si hay una cotizacion hecha, asi no puede editar detalles como fecha, destino, necesidad
            $btnEditar = false;
            if(CotizacionUnidad::where('id_requisicion_unidad', $info->id)->first()){
                $btnEditar = true;
            }

            return ['success' => 1, 'info' => $info, 'detalle' => $detalle, 'btneditar' => $btnEditar];
        }
        return ['success' => 2];
    }

    // modificar las requisiciones de unidad
    public function editarRequisicionUnidad(Request $request){

        $regla = array(
            'idrequisicion' => 'required', // id requisicion unidad
            'idanio' => 'required'
        );

        $validator = Validator::make($request->all(), $regla);

        if ($validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $infoAnio = P_AnioPresupuesto::where('id', $request->idanio)->first();

            if($infoAnio->permiso == 0){
                // no hay permiso para modificar requisiciones
                return ['success' => 3];
            }

            // ACTUALIZAR SOLAMENTE SI NO TIENE COTIZACIÓN
            if(!CotizacionUnidad::where('id_requisicion_unidad', $request->idrequisicion)->first()){
                RequisicionUnidad::where('id', $request->idrequisicion)->update([
                    'destino' => $request->destino,
                    'fecha' => $request->fecha,
                    'necesidad' => $request->necesidad,
                ]);
            }

            // agregar id a pila
            $pila = array();
            for ($i = 0; $i < count($request->idarray); $i++) {
                // Los id que sean 0, seran nuevos registros
                if($request->idarray[$i] != 0) {
                    array_push($pila, $request->idarray[$i]);
                }
            }

            // OBTENER LOS ID REQUISICION DETALLE QUE SE VAN A BORRAR,
            // Y SOLO VERIFICAR QUE NO ESTE COTIZADO
            $infoRequiDetalle = RequisicionUnidadDetalle::where('id_requisicion_unidad', $request->idrequisicion)
                ->whereNotIn('id', $pila)
                ->get();

            $pilaBorrar = array();

            // ya con los id a borrar. verificar que no esten cotizados
            foreach ($infoRequiDetalle as $dd){
                array_push($pilaBorrar, $dd->id);
                if($dd->estado == 1){
                    // MATERIAL COTIZADO, RETORNAR

                    $infoCatalogo = P_Materiales::where('id', $dd->id_material)->first();
                    return ['success' => 1, 'nombre' => $infoCatalogo->nombre];
                }
            }

            // YA SE PUEDE BORRAR SI HAY MATERIALES A BORRAR.
            // borrar de saldo retenido y de la requisicion detalle
            CuentaUnidadRetenido::whereIn('id_requi_detalle', $pilaBorrar)->delete();

            RequisicionUnidadDetalle::whereIn('id', $pilaBorrar)->delete();

            DB::commit();
            return ['success' => 2];

        }catch(\Throwable $e){
            Log::info('ee ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // cancelar material de requisicion unidad detalle
    public function cancelarMaterialRequisicionUnidad(Request $request){

        // ID REQUISICION DETALLE de unidad


        $infoRequiDetalle = RequisicionUnidadDetalle::where('id', $request->id)->first();
        $infoRequisicion = RequisicionUnidad::where('id', $infoRequiDetalle->requisicion_id)->first();

        // verificar que este material no este cotizado con una autorizada.

        // obtener todas las cotizaciones id donde esté cotizado
        $lista = CotizacionUnidadDetalle::where('id_requi_unidaddetalle', $request->id)->get();

        $pila = array();
        foreach ($lista as $dd){
            array_push($pila, $dd->id_cotizacion_unidad);
        }

        // saber si hay una cotización autorizada. CON ESTE MATERIAL
        // EL ESTADO 1, APROBADA, 2: DENEGADA
        // ES DECIR, MIENTRAS EL ESTADO DE LA COTIZACION ESTA EN DEFAULT Y APROBADO.
        // NO PODRA CANCELAR EL MATERIAL
        $conteo = CotizacionUnidad::whereIn('id', $pila)
            ->whereIn('estado', [0, 1])
            ->count();

        if($conteo > 0){

            // si hay cotización, hoy verificar si orden de compra fue anulada
            $arrayCoti = CotizacionUnidad::whereIn('id', $pila)->get();
            $pilaCoti = array();
            foreach ($arrayCoti as $dd){
                array_push($pilaCoti, $dd->id);
            }

            // ver si existe al menos 1 orden
            if(OrdenUnidad::whereIn('id_cotizacion_unidad', $pilaCoti)->first()){
                $conteoOrden = OrdenUnidad::whereIn('id_cotizacion_unidad', $pilaCoti)
                    ->where('estado', 0) // APROBADA LA ORDEN
                    ->count();

                if($conteoOrden > 0){
                    // material tiene una orden aprobada
                }else{
                    // material tiene una orden denegada

                    // SE PUEDE CANCELAR PORQUE TIENE UNA ORDEN DE COMPRA CANCELADA

                    RequisicionUnidadDetalle::where('id', $request->id)->update([
                        'cancelado' => 1,
                    ]);

                    return ['success' => 2];
                }
            }

            // MATERIAL FUE APROBADO O ---- ESPERANDO APROBACIÓN ----, NO SE PUEDE CANCELAR YA
            // solo para mostrar mensaje que la coti fue aprobada y no se puede borrar.
            $infoTipo = CotizacionUnidad::whereIn('id', $pila)->where('estado', 1)->count();
            return ['success' => 1, 'tipo' => $infoTipo];
        }

        // SE PUEDE CANCELAR, PORQUE NINGUNA COTI ESTA APROBADA
        RequisicionUnidadDetalle::where('id', $request->id)->update([
            'cancelado' => 1,
        ]);

        return ['success' => 2];
    }

    // borrar fila de requisicion unidad detalle
    public function borrarMaterialRequisicionFilaUnidad(Request $request){
        DB::beginTransaction();

        try {

            // verificar si hay una cotización con este material

            if(CotizacionUnidadDetalle::where('id_requi_unidaddetalle', $request->id)->first()){
                return ['success' => 1];
            }

            if(RequisicionUnidadDetalle::where('id', $request->id)->first()){
                CuentaUnidadRetenido::where('id_requi_detalle', $request->id)->delete();
                RequisicionUnidadDetalle::where('id', $request->id)->delete();
            }

            DB::commit();
            return ['success' => 2];
        }catch(\Throwable $e){
            Log::info('ee' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


}

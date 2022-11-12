<?php

namespace App\Http\Controllers\Backend\PresupuestoUnidad\Requerimientos;

use App\Http\Controllers\Controller;
use App\Models\CotizacionUnidad;
use App\Models\Cuenta;
use App\Models\CuentaUnidad;
use App\Models\MoviCuentaUnidad;
use App\Models\ObjEspecifico;
use App\Models\P_AnioPresupuesto;
use App\Models\P_Departamento;
use App\Models\P_PresupUnidad;
use App\Models\P_UsuarioDepartamento;
use App\Models\RequisicionUnidad;
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
        $anios = P_AnioPresupuesto::orderBy('nombre', 'ASC')->get();
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

        $monto = DB::table('cuenta_unidad AS cu')
            ->join('p_presup_unidad AS pu', 'cu.id_presup_unidad', '=', 'pu.id')
            ->select('cu.saldo_inicial')
            ->where('pu.id_anio', $idanio)
            ->where('pu.id_departamento', $infoDepartamento->id_departamento)
            ->sum('cu.saldo_inicial');

        $monto = '$' . number_format((float)$monto, 2, '.', ',');

        $infoPresuUnidad = P_PresupUnidad::where('id_anio', $idanio)
            ->where('id_departamento', $infoDepartamento->id_departamento)
            ->first();

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


    public function indexMovimientoCuentaUnidad($idpresup){

        // obtener usuario
        $user = Auth::user();

        // conseguir el departamento
        $infoDepartamento = P_UsuarioDepartamento::where('id_usuario', $user->id)->first();

        // permiso para realizar un movimiento de cuenta
        $permiso = $infoDepartamento->permiso_movi_unidad;

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.vistamovimientocuentaunidad', compact('idpresup', 'permiso'));
    }

    public function tablaMovimientoCuentaUnidad($idpresup){

        // obtener usuario
        $user = Auth::user();

        // conseguir el departamento
        $infoUsuario = P_UsuarioDepartamento::where('id_usuario', $user->id)->first();
        $infoDepartamento = P_Departamento::where('id', $infoUsuario->id_departamento)->first();

        // permiso para realizar un movimiento de cuenta
        $permiso = $infoDepartamento->permiso_movi_unidad;

        $presupuesto = DB::table('cuenta_unidad AS cu')
            ->join('obj_especifico AS obj', 'cu.id_objespeci', '=', 'obj.id')
            ->select('obj.nombre', 'obj.id AS idcodigo', 'cu.id_presup_unidad',
                'obj.codigo', 'cu.id', 'cu.saldo_inicial')
            ->where('cu.id_presup_unidad', $idpresup)
            ->get();

        foreach ($presupuesto as $pp) {

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
            $arrayRestante = DB::table('cuentaunidad_restante AS cr')
                ->join('requisicion_unidad_detalle AS rud', 'cr.id_requi_detalle', '=', 'rud.id')
                ->select('rud.cantidad', 'rud.dinero')
                ->where('cr.id_cuenta_unidad', $pp->id)
                ->where('rud.cancelado', 0)
                ->get();

            foreach ($arrayRestante as $dd) {
                $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
            }

            // información de saldos retenidos
            $arrayRetenido = DB::table('cuentaunidad_retenido AS cr')
                ->join('requisicion_unidad_detalle AS rud', 'cr.id_requi_detalle', '=', 'rud.id')
                ->select('rud.cantidad', 'rud.dinero', 'rud.cancelado')
                ->where('cr.id_cuenta_unidad', $pp->id)
                ->where('rud.cancelado', 0)
                ->get();

            foreach ($arrayRetenido as $dd) {
                $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
            }

            // aquí se obtiene el Saldo Restante del código
            $totalRestanteSaldo = $totalMoviCuenta + ($pp->saldo_inicial - $totalRestante);

            // usado para ver puedo hacer un movimiento de cuenta unidad
            $pp->permiso = $permiso;

            $pp->saldo_inicial = number_format((float)$pp->saldo_inicial, 2, '.', ',');
            $pp->saldo_restante = number_format((float)$totalRestanteSaldo, 2, '.', ',');
            $pp->total_retenido = number_format((float)$totalRetenido, 2, '.', ',');
        }

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.tablamovimientocuentaunidad', compact('presupuesto'));
    }


    public function informacionMoviCuentaUnidad(Request $request){

        $regla = array(
            'id' => 'required', // ID CUENTA UNIDAD
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if ($lista = CuentaUnidad::where('id', $request->id)->first()) {

            $infoObjeto = ObjEspecifico::where('id', $lista->id_objespeci)->first();
            $infoCuenta = Cuenta::where('id', $infoObjeto->id_cuenta)->first();
            $cuenta = $infoCuenta->nombre;

            // obtener CUENTA UNIDAD. menos la seleccionada
            $arrayCuentaUnidad = DB::table('cuenta_unidad AS cu')
                ->join('obj_especifico AS obj', 'cu.id_objespeci', '=', 'obj.id')
                ->select('obj.nombre', 'obj.codigo', 'cu.id',)
                ->where('cu.id', '!=', $lista->id)
                ->get();

            // CÁLCULOS

            $totalRestante = 0;
            $totalRetenido = 0;

            // movimiento de cuentas (sube y baja)
            $infoMoviCuentaUnidadSube = MoviCuentaUnidad::where('id_cuentaunidad_sube', $lista->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $infoMoviCuentaUnidadBaja = MoviCuentaUnidad::where('id_cuentaunidad_baja', $lista->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $totalMoviCuenta = $infoMoviCuentaUnidadSube - $infoMoviCuentaUnidadBaja;

            // obtener todas las salidas de material
            $arrayRestante = DB::table('cuentaunidad_restante AS cr')
                ->join('requisicion_detalle AS rd', 'cr.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero')
                ->where('cr.id_cuenta_unidad', $lista->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRestante as $dd){
                $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
            }

            // obtener todas las salidas de material
            $arrayRetenido = DB::table('cuentaunidad_retenido AS cr')
                ->join('requisicion_detalle AS rd', 'cr.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero')
                ->where('cr.id_cuenta_unidad', $lista->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRetenido as $dd) {
                $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
            }

            // aquí se obtiene el Saldo Restante del código
            $totalRestanteSaldo = $totalMoviCuenta + ($lista->saldo_inicial - $totalRestante);

            $totalRestanteSaldo = "$" . number_format((float)$totalRestanteSaldo, 2, '.', ',');

            return ['success' => 1, 'info' => $lista,
                'objeto' => $infoObjeto, 'cuenta' => $cuenta,
                'restante' => $totalRestanteSaldo, 'arraycuentaunidad' => $arrayCuentaUnidad];
        } else {
            return ['success' => 2];
        }
    }

    // al mover select de movimiento cuenta unidad, retorna saldo restante del obj seleccionado
    public function infoSaldoRestanteCuentaUnidad(Request $request){

        $regla = array(
            'id' => 'required', // ID CUENTA UNIDAD
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if ($lista = CuentaUnidad::where('id', $request->id)->first()) {


            // CÁLCULOS

            $totalRestante = 0;
            $totalRetenido = 0;

            $infoMoviCuentaUnidadSube = MoviCuentaUnidad::where('id_cuentaunidad_sube', $lista->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $infoMoviCuentaUnidadBaja = MoviCuentaUnidad::where('id_cuentaunidad_baja', $lista->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $totalMoviCuenta = $infoMoviCuentaUnidadSube - $infoMoviCuentaUnidadBaja;

            // obtener saldos restante
            $arrayRestante = DB::table('cuentaunidad_restante AS pd')
                ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero')
                ->where('pd.id_cuenta_unidad', $lista->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRestante as $dd) {
                $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
            }

            // información de saldos retenidos
            $arrayRetenido = DB::table('cuentaunidad_retenido AS psr')
                ->join('requisicion_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('psr.id_cuenta_unidad', $lista->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRetenido as $dd) {
                $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
            }

            // aquí se obtiene el Saldo Restante del código
            $totalRestanteSaldo = $totalMoviCuenta + ($lista->saldo_inicial - $totalRestante);

            // se debe quitar el retenido
            $totalCalculado = $totalRestanteSaldo - $totalRetenido;

            $totalCalculado = "$" . number_format((float)$totalCalculado, 2, '.', ',');

            return ['success' => 1, 'restante' => $totalCalculado];
        } else {
            return ['success' => 2];
        }
    }

    // registrar un nuevo movimiento de cuenta unidad por jefe de unidad
    public function nuevaMoviCuentaUnidad(Request $request){

        DB::beginTransaction();

        try {

            // VERIFICAR MIS SALDOS RESTANTE Y VERIFICAR QUE NO QUEDE MENOR A 0

            $infoCuentaProy = CuentaUnidad::where('id', $request->selectcuenta)->first(); // y este va a disminuir

            $infoObjetoEspe = ObjEspecifico::where('id', $infoCuentaProy->id_objespeci)->first();
            $txtObjetoEspec = $infoObjetoEspe->codigo . " - " . $infoObjetoEspe->nombre;

            // PROCESO DE CÁLCULOS

            $totalRestante = 0;
            $totalRetenido = 0;

            // movimiento de cuentas (sube y baja)
            $infoMoviCuentaUnidadSube = MoviCuentaUnidad::where('id_cuentaunidad_sube', $request->selectcuenta)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $infoMoviCuentaUnidadBaja = MoviCuentaUnidad::where('id_cuentaunidad_baja', $request->selectcuenta)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            // variable para guardar movimiento de cuenta calculada
            $totalMoviCuenta = $infoMoviCuentaUnidadSube - $infoMoviCuentaUnidadBaja;

            // obtener todas las salidas de material
            $arrayRestante = DB::table('cuentaunidad_restante AS pd')
                ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('pd.id_cuenta_unidad', $infoCuentaProy->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRestante as $dd) {
                $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
            }

            // obtener saldos retenidos
            $arrayRetenido = DB::table('cuentaunidad_retenido AS psr')
                ->join('requisicion_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('psr.id_cuenta_unidad', $infoCuentaProy->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRetenido as $dd) {
                $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
            }

            // aquí se obtiene el Saldo Restante del código
            $totalRestanteSaldo = $totalMoviCuenta + ($infoCuentaProy->saldo_inicial - $totalRestante);

            $totalCalculado = ($totalRestanteSaldo - $request->saldomodificar) - $totalRetenido;

            // al final no debe quedar menor a 0, para poder guardar el movimiento de cuenta.
            if ($this->redondear_dos_decimal($totalCalculado) < 0) {
                // saldo insuficiente para hacer este movimiento, ya que queda NEGATIVO

                // pasar a positivo
                $totalCalculado = abs($totalCalculado);
                $totalCalculado = "-$" . number_format((float)$totalCalculado, 2, '.', ',');

                $totalRestanteSaldo = number_format((float)$totalRestanteSaldo, 2, '.', ',');
                $totalRetenido = number_format((float)$totalRetenido, 2, '.', ',');
                $dinero = number_format((float)$request->saldomodificar, 2, '.', ',');

                return ['success' => 2, 'objeto' => $txtObjetoEspec, 'restante' => $totalRestanteSaldo,
                    'retenido' => $totalRetenido, 'dinero' => $dinero, 'calculado' => $totalCalculado];
            }

            // Guardar

            $co = new MoviCuentaUnidad();
            $co->id_cuentaunidad_sube = $request->idcuentaunidad;
            $co->dinero = $request->saldomodificar;
            $co->id_cuentaunidad_baja = $request->selectcuenta;
            $co->fecha = $request->fecha;
            $co->reforma = null;
            $co->autorizado = 0;
            $co->save();

            // setear para que no agregue más movimientos

            $user = Auth::user();

            // conseguir el departamento
            $infoUsuario = P_UsuarioDepartamento::where('id_usuario', $user->id)->first();
            P_Departamento::where('id',  $infoUsuario->id_departamento)->update([
                'permiso_movi_unidad' => 0,
            ]);

            DB::commit();
            return ['success' => 3];

        } catch (\Throwable $e) {
            Log::info('ee' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // retorna vista con los historicos movimientos cuenta unidad por ID PRESUP UNIDAD
    public function indexMoviCuentaUnidadHistorico($id){
        // ID: PRESUP UNIDAD
        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.historico.vistamovicuentaunidadhistorico', compact('id'));
    }

    // retorna tabla con los historicos movimientos cuenta unidad por ID PRESUP UNIDAD
    public function tablaMoviCuentaUnidadHistorico($id){
        // ID: PRESUP UNIDAD

        $pilaIdCuentaUnidad = array();
        $listado = CuentaUnidad::where('id_presup_unidad', $id)->get();

        foreach ($listado as $ll) {
            array_push($pilaIdCuentaUnidad, $ll->id);
        }

        $infoMovimiento = MoviCuentaUnidad::whereIn('id_cuentaunidad_sube', $pilaIdCuentaUnidad)
            ->orderBy('fecha', 'ASC')
            ->get();

        foreach ($infoMovimiento as $dd) {

            $infoCuentaUnidadAumenta = CuentaUnidad::where('id', $dd->id_cuentaunidad_sube)->first();
            $infoCuentaUnidadBaja = CuentaUnidad::where('id', $dd->id_cuentaunidad_baja)->first();

            $infoObjetoAumenta = ObjEspecifico::where('id', $infoCuentaUnidadAumenta->id_objespeci)->first();
            $infoObjetoBaja = ObjEspecifico::where('id', $infoCuentaUnidadBaja->id_objespeci)->first();

            $dd->cuentaaumenta = $infoObjetoAumenta->codigo . " - " . $infoObjetoAumenta->nombre;
            $dd->cuentabaja = $infoObjetoBaja->codigo . " - " . $infoObjetoBaja->nombre;

            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));
        }

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.historico.tablamovicuentaunidadhistorico', compact('infoMovimiento'));
    }


    // ver los movimientos historicos para que jefe presupuesto los apruebe
    public function indexMovimientoCuentaUnidadTodos(){
        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.historicorevision.vistamovicuentaunidadhistoricorevision');
    }

    // ver tabla de los movimientos historicos de cuenta unidad, jefatura presupuesto para aprobar
    public function tablaMovimientoCuentaUnidadTodos(){

        $pilaIdCuentaUnidad = array();
        $listado = CuentaUnidad::get();

        foreach ($listado as $ll) {
            array_push($pilaIdCuentaUnidad, $ll->id);
        }

        $infoMovimiento = MoviCuentaUnidad::whereIn('id_cuentaunidad_sube', $pilaIdCuentaUnidad)
            ->orderBy('fecha', 'ASC')
            ->where('autorizado', 0) // NO AUTORIZADO AUN
            ->get();

        foreach ($infoMovimiento as $dd) {

            $infoCuentaUnidadAumenta = CuentaUnidad::where('id', $dd->id_cuentaunidad_sube)->first();
            $infoCuentaUnidadBaja = CuentaUnidad::where('id', $dd->id_cuentaunidad_baja)->first();

            $infoObjetoAumenta = ObjEspecifico::where('id', $infoCuentaUnidadAumenta->id_objespeci)->first();
            $infoObjetoBaja = ObjEspecifico::where('id', $infoCuentaUnidadBaja->id_objespeci)->first();

            $dd->cuentaaumenta = $infoObjetoAumenta->codigo . " - " . $infoObjetoAumenta->nombre;
            $dd->cuentabaja = $infoObjetoBaja->codigo . " - " . $infoObjetoBaja->nombre;

            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));

            // obtener departamento
            $infoPresuUnidad = P_PresupUnidad::where('id', $infoCuentaUnidadAumenta->id_presup_unidad)->first();
            $infoDepartamento = P_Departamento::where('id', $infoPresuUnidad->id_departamento)->first();

            $dd->departamento = $infoDepartamento->nombre;
        }

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.historicorevision.tablamovicuentaunidadhistoricorevision', compact('infoMovimiento'));
    }

    // información para jefe de presupuesto para que revise un movimiento de cuenta unidad
    public function infoHistoricoMovimientoUnidadParaAutorizar(Request $request){

        $regla = array(
            'id' => 'required', // ID movicuentaunidad
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if ($infoMovi = MoviCuentaUnidad::where('id', $request->id)->first()) {

            // PUEDO TOMAR YA SEA EL SUBE O BAJA, YA QUE TODOS PERTENECEN AL MISMO PROYECTO

            $infoCuentaUnidadSube = CuentaUnidad::where('id', $infoMovi->id_cuentaunidad_sube)->first();
            $infoCuentaUnidadBaja = CuentaUnidad::where('id', $infoMovi->id_cuentaunidad_baja)->first();

            $infoObjetoSube = ObjEspecifico::where('id', $infoCuentaUnidadSube->id_objespeci)->first();
            $infoObjetoBaja = ObjEspecifico::where('id', $infoCuentaUnidadBaja->id_objespeci)->first();

            $infoCuentaSube = Cuenta::where('id', $infoObjetoSube->id_cuenta)->first();
            $infoCuentaBaja = Cuenta::where('id', $infoObjetoBaja->id_cuenta)->first();

            $objetoaumenta = $infoObjetoSube->codigo . " - " . $infoObjetoSube->nombre;
            $objetobaja = $infoObjetoBaja->codigo . " - " . $infoObjetoBaja->nombre;

            $cuentaaumenta = $infoCuentaSube->codigo . " - " . $infoCuentaSube->nombre;
            $cuentabaja = $infoCuentaBaja->codigo . " - " . $infoCuentaBaja->nombre;

            $fecha = date("d-m-Y", strtotime($infoMovi->fecha));

            // OBTENER SALDO RESTANTE, PARA EL OBJETO ESPECÍFICO QUE SE QUITARA DINERO

            // CÁLCULOS

            $infoCuentaUnidad = CuentaUnidad::where('id', $infoMovi->id_cuentaunidad_baja)->first();

            $totalRestante = 0;
            $totalRetenido = 0;

            // movimiento de cuentas (sube y baja)
            $infoMoviCuentaProySube = MoviCuentaUnidad::where('id_cuentaunidad_sube', $infoCuentaUnidadSube->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $infoMoviCuentaProyBaja = MoviCuentaUnidad::where('id_cuentaunidad_baja', $infoCuentaUnidadBaja->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $totalMoviCuenta = $infoMoviCuentaProySube - $infoMoviCuentaProyBaja;

            // obtener todas las salidas de material
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
            $arrayRetenido = DB::table('cuentaunidad_retenido AS psr')
                ->join('requisicion_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('psr.id_cuenta_unidad', $infoCuentaUnidad->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRetenido as $dd){
                $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
            }

            // aquí se obtiene el Saldo Restante del código
            $totalRestanteSaldo = $totalMoviCuenta + ($infoCuentaUnidad->saldo_inicial - $totalRestante);

            $totalRestanteSaldo = number_format((float)$totalRestanteSaldo, 2, '.', ',');

            return ['success' => 1, 'info' => $infoMovi, 'cuentaaumenta' => $cuentaaumenta,
                'cuentabaja' => $cuentabaja, 'objetosube' => $objetoaumenta, 'objetobaja' => $objetobaja,
                'fecha' => $fecha, 'restantecuentabaja' => $totalRestanteSaldo];
        } else {
            return ['success' => 2];
        }
    }

    // borrar movimiento de cuenta para unidades
    public function denegarBorrarMovimientoCuentaUnidad(Request $request){
        $regla = array(
            'id' => 'required', // ID movicuentaproy
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if ($infoMovi = MoviCuentaUnidad::where('id', $request->id)->first()) {

            // no borrar porque ya esta autorizado
            if ($infoMovi->autorizado == 1) {
                return ['success' => 1];
            }

            // borrar fila
            MoviCuentaUnidad::where('id', $request->id)->delete();

            return ['success' => 2];
        } else {
            return ['success' => 99];
        }
    }

    // autorizar movimiento de cuenta unidad
    public function autorizarMovimientoCuentaUnidad(Request $request){

        // ID movicuentaunidad

        DB::beginTransaction();

        try {

            if ($infoMovimiento = MoviCuentaUnidad::where('id', $request->id)->first()) {

                // movimiento ya estaba autorizado
                if ($infoMovimiento->autorizado == 1) {
                    return ['success' => 1];
                }

                // INFO DE LA CUENTA QUE VA A BAJAR
                $infoCuentaUnidad = CuentaUnidad::where('id', $infoMovimiento->id_cuentaunidad_baja)->first(); // y este va a disminuir

                $infoObjetoEspe = ObjEspecifico::where('id', $infoCuentaUnidad->id_objespeci)->first();
                $txtObjetoEspec = $infoObjetoEspe->codigo . " - " . $infoObjetoEspe->nombre;

                // CÁLCULOS

                $totalRestante = 0;
                $totalRetenido = 0;

                // se está haciendo cálculos únicamente con la cuenta que BAJARA, la que subirá no se hace ningún cálculo

                // SOLO CALCULOS CON LA CUENTA QUE BAJA
                $infoMoviCuentaUnidadSube = MoviCuentaUnidad::where('id_cuentaunidad_sube', $infoMovimiento->id_cuentaunidad_baja)
                    ->where('autorizado', 1) // autorizado por presupuesto
                    ->sum('dinero');

                $infoMoviCuentaUnidadBaja = MoviCuentaUnidad::where('id_cuentaunidad_baja', $infoMovimiento->id_cuentaunidad_baja)
                    ->where('autorizado', 1) // autorizado por presupuesto
                    ->sum('dinero');

                // variable para guardar movimiento de cuenta calculada
                $totalMoviCuenta = $infoMoviCuentaUnidadSube - $infoMoviCuentaUnidadBaja;

                // obtener saldo restante
                $arrayRestante = DB::table('cuentaunidad_restante AS pd')
                    ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                    ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                    ->where('pd.id_cuenta_unidad', $infoCuentaUnidad->id)
                    ->where('rd.cancelado', 0)
                    ->get();

                foreach ($arrayRestante as $dd) {
                    $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
                }

                // obtener saldos retenidos
                $arrayRetenido = DB::table('cuentaunidad_retenido AS psr')
                    ->join('requisicion_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                    ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                    ->where('psr.id_cuenta_unidad', $infoCuentaUnidad->id)
                    ->where('rd.cancelado', 0)
                    ->get();

                foreach ($arrayRetenido as $dd) {
                    $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
                }

                // aquí se obtiene el Saldo Restante del código
                $totalRestanteSaldo = $totalMoviCuenta + ($infoCuentaUnidad->saldo_inicial - $totalRestante);

                // HOY RESTAR LO QUE SE QUIERE QUITAR AL OBJETO ESPECÍFICO Y TAMBIEN QUE NO HAYA SALDO RETENIDO
                $totalCalculado = ($totalRestanteSaldo - $infoMovimiento->dinero) - $totalRetenido;

                if ($this->redondear_dos_decimal($totalCalculado) < 0) {
                    // saldo insuficiente para hacer este movimiento, ya que queda NEGATIVO

                    // pasar a positivo
                    $totalCalculado = abs($totalCalculado);
                    $totalCalculado = '-$' . number_format((float)$totalCalculado, 2, '.', ',');
                    $totalRestanteSaldo = number_format((float)$totalRestanteSaldo, 2, '.', ',');
                    $totalRetenido = number_format((float)$totalRetenido, 2, '.', ',');
                    $dinero = number_format((float)$infoMovimiento->dinero, 2, '.', ',');

                    return ['success' => 2, 'objeto' => $txtObjetoEspec, 'restante' => $totalRestanteSaldo,
                        'retenido' => $totalRetenido, 'dinero' => $dinero, 'calculado' => $totalCalculado];
                }

                // PASADO VALIDACIÓN, SE PUEDE GUARDAR

                if ($request->hasFile('documento')) {
                    $cadena = Str::random(15);
                    $tiempo = microtime();
                    $union = $cadena . $tiempo;
                    $nombre = str_replace(' ', '_', $union);

                    $extension = '.' . $request->documento->getClientOriginalExtension();
                    $nomDocumento = $nombre . strtolower($extension);
                    $avatar = $request->file('documento');
                    $estado = Storage::disk('archivos')->put($nomDocumento, \File::get($avatar));

                    if ($estado) {

                        // pasar estado autorizado y guardar documento

                        MoviCuentaUnidad::where('id', $request->id)->update([
                            'reforma' => $nomDocumento,
                            'autorizado' => 1
                        ]);

                        DB::commit();
                        return ['success' => 3];
                    } else {
                        return ['success' => 99];
                    }
                } else {

                    MoviCuentaUnidad::where('id', $request->id)->update([
                        'autorizado' => 1
                    ]);

                    DB::commit();
                    return ['success' => 3];
                }
            } else {
                return ['success' => 99];
            }

        } catch (\Throwable $e) {
            Log::info('ee' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // ver los movimientos de cuenta unidad aprobados
    public function indexMovimientoCuentaUnidadAprobados(){
        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.historicoaprobado.vistamovicuentaunidadhistoricoaprobado');
    }

    // ver tabla de los movimientos historicos de cuenta unidad aprobados
    public function tablaMovimientoCuentaUnidadAprobados(){

        $pilaIdCuentaUnidad = array();
        $listado = CuentaUnidad::get();

        foreach ($listado as $ll) {
            array_push($pilaIdCuentaUnidad, $ll->id);
        }

        $infoMovimiento = MoviCuentaUnidad::whereIn('id_cuentaunidad_sube', $pilaIdCuentaUnidad)
            ->orderBy('fecha', 'ASC')
            ->where('autorizado', 1) // AUTORIZADOS
            ->get();

        foreach ($infoMovimiento as $dd) {

            $infoCuentaUnidadAumenta = CuentaUnidad::where('id', $dd->id_cuentaunidad_sube)->first();
            $infoCuentaUnidadBaja = CuentaUnidad::where('id', $dd->id_cuentaunidad_baja)->first();

            $infoObjetoAumenta = ObjEspecifico::where('id', $infoCuentaUnidadAumenta->id_objespeci)->first();
            $infoObjetoBaja = ObjEspecifico::where('id', $infoCuentaUnidadBaja->id_objespeci)->first();

            $dd->cuentaaumenta = $infoObjetoAumenta->codigo . " - " . $infoObjetoAumenta->nombre;
            $dd->cuentabaja = $infoObjetoBaja->codigo . " - " . $infoObjetoBaja->nombre;

            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));

            // obtener departamento
            $infoPresuUnidad = P_PresupUnidad::where('id', $infoCuentaUnidadAumenta->id_presup_unidad)->first();
            $infoDepartamento = P_Departamento::where('id', $infoPresuUnidad->id_departamento)->first();

            $dd->departamento = $infoDepartamento->nombre;
        }

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.historicoaprobado.tablamovicuentaunidadhistoricoaprobado', compact('infoMovimiento'));
    }

    // descargar documento reforma de movimiento cuenta unidad
    public function descargarReformaMovimientoUnidades($id){

        $url = MoviCuentaUnidad::where('id', $id)->pluck('reforma')->first();
        $pathToFile = "storage/archivos/" . $url;
        $extension = pathinfo(($pathToFile), PATHINFO_EXTENSION);
        $nombre = "Documento." . $extension;
        return response()->download($pathToFile, $nombre);
    }


    public function guardarDocumentoReformaMoviUnidad(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {
            $infoMovimiento = MoviCuentaUnidad::where('id', $request->id)->first();

            if ($request->hasFile('documento')) {

                $cadena = Str::random(15);
                $tiempo = microtime();
                $union = $cadena . $tiempo;
                $nombre = str_replace(' ', '_', $union);

                $extension = '.' . $request->documento->getClientOriginalExtension();
                $nomDocumento = $nombre . strtolower($extension);
                $avatar = $request->file('documento');
                $archivo = Storage::disk('archivos')->put($nomDocumento, \File::get($avatar));

                if ($archivo) {

                    if (Storage::disk('archivos')->exists($infoMovimiento->reforma)) {
                        Storage::disk('archivos')->delete($infoMovimiento->reforma);
                    }

                    MoviCuentaUnidad::where('id', $request->id)->update([
                        'reforma' => $nomDocumento
                    ]);

                    DB::commit();
                    return ['success' => 1];
                } else {
                    return ['success' => 99];
                }
            } else {
                return ['success' => 99];
            }
        } catch (\Throwable $e) {

            DB::rollback();
            return ['success' => 99];
        }
    }

}

<?php

namespace App\Http\Controllers\Backend\PresupuestoUnidad\MovimientoUnidad;

use App\Http\Controllers\Controller;
use App\Models\CotizacionUnidad;
use App\Models\Cuenta;
use App\Models\CuentaUnidad;
use App\Models\MoviCuentaUnidad;
use App\Models\ObjEspecifico;
use App\Models\P_AnioPresupuesto;
use App\Models\P_Departamento;
use App\Models\P_Materiales;
use App\Models\P_PresupUnidad;
use App\Models\P_PresupUnidadDetalle;
use App\Models\P_SolicitudMaterial;
use App\Models\P_SolicitudMaterialDetalle;
use App\Models\P_UnidadMedida;
use App\Models\P_UsuarioDepartamento;
use App\Models\RequisicionUnidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class MovimientosUnidadControlles extends Controller
{
    public function __construct(){
        $this->middleware('auth');
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
                ->select('obj.nombre', 'obj.codigo', 'cu.id', 'cu.id_presup_unidad')
                ->where('cu.id', '!=', $lista->id)
                ->where('cu.id_presup_unidad', $lista->id_presup_unidad)
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
                ->join('requisicion_unidad_detalle AS rd', 'cr.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero')
                ->where('cr.id_cuenta_unidad', $lista->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRestante as $dd){
                $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
            }

            // obtener todas las salidas de material
            $arrayRetenido = DB::table('cuentaunidad_retenido AS cr')
                ->join('requisicion_unidad_detalle AS rd', 'cr.id_requi_detalle', '=', 'rd.id')
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
                ->join('requisicion_unidad_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero')
                ->where('pd.id_cuenta_unidad', $lista->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRestante as $dd) {
                $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
            }

            // información de saldos retenidos
            $arrayRetenido = DB::table('cuentaunidad_retenido AS psr')
                ->join('requisicion_unidad_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
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
                ->join('requisicion_unidad_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('pd.id_cuenta_unidad', $infoCuentaProy->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRestante as $dd) {
                $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
            }

            // obtener saldos retenidos
            $arrayRetenido = DB::table('cuentaunidad_retenido AS psr')
                ->join('requisicion_unidad_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
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
                    ->join('requisicion_unidad_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                    ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                    ->where('pd.id_cuenta_unidad', $infoCuentaUnidad->id)
                    ->where('rd.cancelado', 0)
                    ->get();

                foreach ($arrayRestante as $dd) {
                    $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
                }

                // obtener saldos retenidos
                $arrayRetenido = DB::table('cuentaunidad_retenido AS psr')
                    ->join('requisicion_unidad_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
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

    public function indexMovimientoCuentaUnidadAprobadosAnio(){
        $anios = P_AnioPresupuesto::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.historicoaprobado.vistaaniocuentahistoricounidad', compact('anios'));
    }

    // ver los movimientos de cuenta unidad aprobados
    public function indexMovimientoCuentaUnidadAprobados($idanio){

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.historicoaprobado.vistamovicuentaunidadhistoricoaprobado', compact('idanio'));
    }

    // ver tabla de los movimientos historicos de cuenta unidad aprobados
    public function tablaMovimientoCuentaUnidadAprobados($idanio){

        $pilaIdCuentaUnidad = array();

        $listado = DB::table('cuenta_unidad AS cu')
            ->join('p_presup_unidad AS pru', 'cu.id_presup_unidad', '=', 'pru.id')
            ->select('cu.id', 'cu.id_presup_unidad', 'pru.id_anio', 'cu.id_objespeci', 'cu.saldo_inicial')
            ->where('pru.id_anio', $idanio)
            ->get();

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


    //* *************************

    // retorna vista para ver materiales solicitados y se quita dinero de un código
    public function indexSolicitudMovimientoUnidadMaterial($idpresubunidad){
        // ID: PRESUP UNIDAD

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.solicitudmaterial.vistamovimientounidadsolicitudmaterial', compact('idpresubunidad'));
    }

    // retorna tabla para ver materiales solicitados y se quita dinero de un código
    public function tablaSolicitudMovimientoUnidadMaterial($idpresubunidad){

        // ID: PRESUP UNIDAD

        $lista = P_SolicitudMaterial::where('id_presup_unidad', $idpresubunidad)->get();

        foreach ($lista as $dd) {

            $infoMaterial = P_Materiales::where('id', $dd->id_material)->first();
            $infoCuenta = CuentaUnidad::where('id', $dd->id_cuentaunidad)->first();
            $infoObj = ObjEspecifico::where('id', $infoCuenta->id_objespeci)->first();

            $dd->material = $infoMaterial->descripcion;
            $dd->objnombre = $infoObj->codigo . ' - ' . $infoObj->nombre;

            $total = ($dd->cantidad * $infoMaterial->costo) * $dd->periodo;

            $total = "$" . number_format((float)$total, 2, '.', ',');

            $dd->total = $total;
        }

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.solicitudmaterial.tablamovimientounidadsolicitudmaterial', compact('lista'));
    }

    // buscador de material de solicitud
    public function buscadorMaterialSolicitudUnidad(Request $request){

        if($request->get('query')){
            $query = $request->get('query');

            // idpresuunidad

            $arrayPresuDetalle = P_PresupUnidadDetalle::where('id_presup_unidad', $request->idpresuunidad)->get();

            $pilaIdMateriales = array();

            foreach ($arrayPresuDetalle as $dd){
                array_push($pilaIdMateriales, $dd->id_material);
            }

            // array de materiales materiales adicionales
            $arrayMateriales = P_Materiales::whereNotIn('id', $pilaIdMateriales)
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
                 <li onclick="modificarValorSolicitud(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->unido.'</a></li>
                ';
                    }
                }

                else{
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValorSolicitud(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->unido.'</a></li>
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


    public function buscadorObjEspeciSolicitudMaterial(Request $request){

        $regla = array(
            'idmaterial' => 'required',
            'idpresup' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = P_Materiales::where('id', $request->idmaterial)->first()){

            $detalle = DB::table('p_presup_unidad_detalle AS ppu')
            ->join('p_materiales AS m', 'ppu.id_material', '=', 'm.id')
            ->select('m.id_objespecifico')
            ->where('ppu.id_presup_unidad', $request->idpresup)
            ->whereNotIn('m.id_objespecifico', [$lista->id_objespecifico])
            ->groupBy('m.id_objespecifico')
            ->get();

            $pilaArray = array();

            foreach ($detalle as $p){
                array_push($pilaArray, $p->id_objespecifico);
            }

            $arrayobj = ObjEspecifico::whereIn('id', $pilaArray)->get();

            $costoactual = "$" . number_format((float)$lista->costo, 2, '.', ',');

            return ['success' => 1, 'arrayobj' => $arrayobj, 'costoactual' => $costoactual];
        }else{
            return ['success' => 2];
        }
    }

    // obtener saldo restando MENOS el saldo retenido de un obj especifico
    public function infoSaldoRestanteSolicitudMaterial(Request $request){

        $regla = array(
            'idobj' => 'required', // ID objeto específico
            'idpresup' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if ($lista = CuentaUnidad::where('id_presup_unidad', $request->idpresup)
            ->where('id_objespeci', $request->idobj)
            ->first()) {

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
                ->join('requisicion_unidad_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero')
                ->where('pd.id_cuenta_unidad', $lista->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRestante as $dd) {
                $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
            }

            // información de saldos retenidos
            $arrayRetenido = DB::table('cuentaunidad_retenido AS psr')
                ->join('requisicion_unidad_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
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

    // guardar solicitud de materiales
    public function guardarSolicitudMaterialUnidad(Request $request){

        $regla = array(
            'idobj' => 'required', // ID objeto específico
            'idpresup' => 'required',
            'idmaterial' => 'required',
            'cantidad' => 'required',
            'periodo' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            if ($lista = CuentaUnidad::where('id_presup_unidad', $request->idpresup)
                ->where('id_objespeci', $request->idobj)
                ->first()) {

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
                    ->join('requisicion_unidad_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                    ->select('rd.cantidad', 'rd.dinero')
                    ->where('pd.id_cuenta_unidad', $lista->id)
                    ->where('rd.cancelado', 0)
                    ->get();

                foreach ($arrayRestante as $dd) {
                    $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
                }

                // información de saldos retenidos
                $arrayRetenido = DB::table('cuentaunidad_retenido AS psr')
                    ->join('requisicion_unidad_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
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

                // obtener dinero
                $infoMaterial = P_Materiales::where('id', $request->idmaterial)->first();

                // periodo siempre sera mínimo 1
                $totalMaterial = ($infoMaterial->costo * $request->cantidad) * $request->periodo;

                // ver que haya saldo disponible
                if($this->redondear_dos_decimal($totalCalculado) < $this->redondear_dos_decimal($totalMaterial)){

                    // saldo insuficiente.

                    $totalMaterial = number_format((float)$totalMaterial, 2, '.', ',');
                    $totalCalculado = number_format((float)$totalCalculado, 2, '.', ',');

                    return ['success' => 1, 'restante' => $totalCalculado, 'costo' => $totalMaterial];
                }else{

                    // guardar ya
                    $deta = new P_SolicitudMaterial();
                    $deta->id_presup_unidad = $request->idpresup;
                    $deta->id_material = $request->idmaterial;
                    $deta->id_cuentaunidad = $lista->id; // cuenta unidad que bajara
                    $deta->cantidad = $request->cantidad;
                    $deta->periodo = $request->periodo;
                    $deta->save();

                    DB::commit();
                    return ['success' => 2];
                }
            }else{
                // cuenta unidad no encontrada
                return ['success' => 3];
            }

        } catch (\Throwable $e) {
            Log::info('ee' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // retorna vista para ver materiales solicitados y se quita dinero de un código
    public function indexRevisionSolicitudMaterialUnidad(){
        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.solicitudmaterial.revision.vistarevisionsolicitudmaterial');
    }

    // revision por presupuesto de material solicitado por una unidad
    public function tablaRevisionSolicitudMaterialUnidad(){

        $lista = P_SolicitudMaterial::all();

        foreach ($lista as $dd) {

            $infoPresup = P_PresupUnidad::where('id', $dd->id_presup_unidad)->first();
            $infoDepar = P_Departamento::where('id', $infoPresup->id_departamento)->first();

            $dd->departamento = $infoDepar->nombre;

            $infoMaterial = P_Materiales::where('id', $dd->id_material)->first();
            $infoCuenta = CuentaUnidad::where('id', $dd->id_cuentaunidad)->first();
            $infoObj = ObjEspecifico::where('id', $infoCuenta->id_objespeci)->first();

            $dd->material = $infoMaterial->descripcion;
            $dd->objnombre = $infoObj->codigo . ' - ' . $infoObj->nombre;

            $total = ($dd->cantidad * $infoMaterial->costo) * $dd->periodo;

            $total = "$" . number_format((float)$total, 2, '.', ',');

            $dd->total = $total;

            $costoactual = "$" . number_format((float)$infoMaterial->costo, 2, '.', ',');

            $dd->costoactual = $costoactual;
        }

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.solicitudmaterial.revision.tablarevisionsolicitudmaterial', compact('lista'));
    }

    // revision por presupuesto de material solicitado por una unidad
    public function informacionSolicitudMaterialPresupuesto(Request $request){

        $regla = array(
            'idsolicitud' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if($infoSoli = P_SolicitudMaterial::where('id', $request->idsolicitud)->first()){

            $infoMaterial = P_Materiales::where('id', $infoSoli->id_material)->first();
            $infoCuenta = CuentaUnidad::where('id', $infoSoli->id_cuentaunidad)->first();
            $infoObjeto = ObjEspecifico::where('id', $infoCuenta->id_objespeci)->first();

            $nommaterial = $infoMaterial->descripcion;

            $objeto = $infoObjeto->codigo . ' - ' . $infoObjeto->nombre;

            $totalsolicitado = ($infoSoli->cantidad * $infoMaterial->costo) * $infoSoli->periodo;

            $totalsolicitado = "$" . number_format((float)$totalsolicitado, 2, '.', ',');

            $unitario = '$' . number_format((float)$infoMaterial->costo, 2, '.', ',');

            // *** BUSCAR RESTANTE

            $totalRestante = 0;
            $totalRetenido = 0;

            $infoMoviCuentaUnidadSube = MoviCuentaUnidad::where('id_cuentaunidad_sube', $infoCuenta->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $infoMoviCuentaUnidadBaja = MoviCuentaUnidad::where('id_cuentaunidad_baja', $infoCuenta->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $totalMoviCuenta = $infoMoviCuentaUnidadSube - $infoMoviCuentaUnidadBaja;

            // obtener saldos restante
            $arrayRestante = DB::table('cuentaunidad_restante AS pd')
                ->join('requisicion_unidad_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero')
                ->where('pd.id_cuenta_unidad', $infoCuenta->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRestante as $dd) {
                $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
            }

            // información de saldos retenidos
            $arrayRetenido = DB::table('cuentaunidad_retenido AS psr')
                ->join('requisicion_unidad_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('psr.id_cuenta_unidad', $infoCuenta->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRetenido as $dd) {
                $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
            }

            // aquí se obtiene el Saldo Restante del código
            $totalRestanteSaldo = $totalMoviCuenta + ($infoCuenta->saldo_inicial - $totalRestante);

            // se debe quitar el retenido
            $totalCalculado = $totalRestanteSaldo - $totalRetenido;

            $totalCalculado = '$' . number_format((float)$totalCalculado, 2, '.', ',');

            return ['success' => 1, 'nommaterial' => $nommaterial, 'info' => $infoSoli,
                'objeto' => $objeto, 'restante' => $totalCalculado, 'totalsolicitado' => $totalsolicitado,
                'unitario' => $unitario];
        }else{
            return ['success' => 2];
        }
    }

    // borrar solicitud material solicitado
    public function borrarSolicitudMaterialPresupuesto(Request $request){

        $regla = array(
            'idsolicitud' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if(P_SolicitudMaterial::where('id', $request->idsolicitud)->first()){

            P_SolicitudMaterial::where('id', $request->idsolicitud)->delete();
            return ['success' => 1];
        }
        else {
            return ['success' => 2];
        }
    }

    // aprobar solicitud de material solicitado y sumar a obj y descontar a otro obj
    function aprobarSolicitudMaterialPresupuesto(Request $request){

        $regla = array(
            'idsolicitud' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $infoSolicitud = P_SolicitudMaterial::where('id', $request->idsolicitud)->first();

            $infoMaterial = P_Materiales::where('id', $infoSolicitud->id_material)->first();
            $infoCuentaDescontar = CuentaUnidad::where('id', $infoSolicitud->id_cuentaunidad)->first();

            $totalsolicitado = ($infoSolicitud->cantidad * $infoMaterial->costo) * $infoSolicitud->periodo;


            // *** BUSCAR RESTANTE

            $totalRestante = 0;
            $totalRetenido = 0;

            $infoMoviCuentaUnidadSube = MoviCuentaUnidad::where('id_cuentaunidad_sube', $infoCuentaDescontar->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $infoMoviCuentaUnidadBaja = MoviCuentaUnidad::where('id_cuentaunidad_baja', $infoCuentaDescontar->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $totalMoviCuenta = $infoMoviCuentaUnidadSube - $infoMoviCuentaUnidadBaja;

            // obtener saldos restante
            $arrayRestante = DB::table('cuentaunidad_restante AS pd')
                ->join('requisicion_unidad_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero')
                ->where('pd.id_cuenta_unidad', $infoCuentaDescontar->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRestante as $dd) {
                $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
            }

            // información de saldos retenidos
            $arrayRetenido = DB::table('cuentaunidad_retenido AS psr')
                ->join('requisicion_unidad_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('psr.id_cuenta_unidad', $infoCuentaDescontar->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRetenido as $dd) {
                $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
            }

            // aquí se obtiene el Saldo Restante del código
            $totalRestanteSaldo = $totalMoviCuenta + ($infoCuentaDescontar->saldo_inicial - $totalRestante);

            // se debe quitar el retenido
            $totalCalculado = $totalRestanteSaldo - $totalRetenido;

            // ver que haya saldo disponible para PODER RESTARSERLO
            if($this->redondear_dos_decimal($totalCalculado) < $this->redondear_dos_decimal($totalsolicitado)){

                // saldo insuficiente.

                $totalsolicitado = number_format((float)$totalsolicitado, 2, '.', ',');
                $totalCalculado = number_format((float)$totalCalculado, 2, '.', ',');

                return ['success' => 1, 'restante' => $totalCalculado, 'costo' => $totalsolicitado];
            }else{
                // SI HAY SALDO AL QUE SE VA A DESCONTAR

                // la cuenta unidad con el obj específico ya existe. así que solo subir saldo inicial
                if($infoCC = CuentaUnidad::where('id_presup_unidad' , $infoSolicitud->id_presup_unidad)
                    ->where('id_objespeci', $infoMaterial->id_objespecifico)
                    ->first()){

                    // suma de dinero
                    $saldoInicialSubir = $infoCC->saldo_inicial + $totalsolicitado;

                    $totalQuedaraBajar = $infoCuentaDescontar->saldo_inicial - $totalsolicitado;


                    // guardar solicitud

                    $deta = new P_SolicitudMaterialDetalle();
                    $deta->id_material = $infoSolicitud->id_material;
                    $deta->id_presup_unidad = $infoSolicitud->id_presup_unidad;
                    $deta->id_cuentaunidad_sube = $infoCC->id;
                    $deta->id_cuentaunidad_baja = $infoSolicitud->id_cuentaunidad;
                    $deta->unidades = $infoSolicitud->cantidad;
                    $deta->periodo = $infoSolicitud->periodo;
                    $deta->copia_saldoini_antes_subir = $infoCC->saldo_inicial; // lo que había antes de modificarse
                    $deta->copia_saldoini_antes_bajar = $infoCuentaDescontar->saldo_inicial; // lo que habia en la cuenta inicial antes que bajara
                    $deta->dinero_solicitado = $totalsolicitado;
                    $deta->cuenta_creada = 0; // solo para ver si esta cuenta fue creada
                    $deta->save();

                    // actualizar subir saldo
                    CuentaUnidad::where('id', $infoCC->id)->update([
                        'saldo_inicial' => $saldoInicialSubir,
                    ]);

                    // BAJAR SALDO
                    CuentaUnidad::where('id', $infoCuentaDescontar->id)->update([
                        'saldo_inicial' => $totalQuedaraBajar,
                    ]);

                    // guardar material
                    $nuevoMate = new P_PresupUnidadDetalle();
                    $nuevoMate->id_presup_unidad = $infoSolicitud->id_presup_unidad;
                    $nuevoMate->id_material = $infoSolicitud->id_material;
                    $nuevoMate->cantidad = $infoSolicitud->cantidad;
                    $nuevoMate->precio = $infoMaterial->costo;
                    $nuevoMate->periodo = $infoSolicitud->periodo;
                    $nuevoMate->save();

                    // borrar solicitud
                    P_SolicitudMaterial::where('id', $request->idsolicitud)->delete();

                    DB::commit();
                    return ['success' => 2];
                }else{

                    // guardar nueva cuenta unidad, con el saldo solicitado
                    $nuevaCuenta = new CuentaUnidad();
                    $nuevaCuenta->id_presup_unidad = $infoSolicitud->id_presup_unidad;
                    $nuevaCuenta->id_objespeci = $infoMaterial->id_objespecifico;
                    $nuevaCuenta->saldo_inicial = $totalsolicitado;
                    $nuevaCuenta->save();

                    // SOLO OBTENER EL SALDO A BAJAR, YA QUE EL SALDO INICIAL SE COLOCO AL CREAR LA CUENTA UNIDAD
                    $totalQuedaraBajar = $infoCuentaDescontar->saldo_inicial - $totalsolicitado;

                    // guardar solicitud

                    $deta = new P_SolicitudMaterialDetalle();
                    $deta->id_material = $infoSolicitud->id_material;
                    $deta->id_presup_unidad = $infoSolicitud->id_presup_unidad;
                    $deta->id_cuentaunidad_sube = $nuevaCuenta->id;
                    $deta->id_cuentaunidad_baja = $infoSolicitud->id_cuentaunidad;
                    $deta->unidades = $infoSolicitud->cantidad;
                    $deta->periodo = $infoSolicitud->periodo;
                    $deta->copia_saldoini_antes_subir = $totalsolicitado; // lo que había antes de modificarse
                    $deta->copia_saldoini_antes_bajar = $infoCuentaDescontar->saldo_inicial; // lo que había en la cuenta inicial antes que bajara
                    $deta->dinero_solicitado = $totalsolicitado;
                    $deta->cuenta_creada = 1;
                    $deta->save();

                    // COMO SE CREO LA CUENTA UNIDAD, ESTA NO TIENE PORQUE SUBIR SU MONTO INICIAL

                    // BAJAR SALDO
                    CuentaUnidad::where('id', $infoCuentaDescontar->id)->update([
                        'saldo_inicial' => $totalQuedaraBajar,
                    ]);

                    // guardar material
                    $nuevoMate = new P_PresupUnidadDetalle();
                    $nuevoMate->id_presup_unidad = $infoSolicitud->id_presup_unidad;
                    $nuevoMate->id_material = $infoSolicitud->id_material;
                    $nuevoMate->cantidad = $infoSolicitud->cantidad;
                    $nuevoMate->precio = $infoMaterial->costo;
                    $nuevoMate->periodo = $infoSolicitud->periodo;
                    $nuevoMate->save();

                    // borrar solicitud
                    P_SolicitudMaterial::where('id', $request->idsolicitud)->delete();

                    DB::commit();
                    return ['success' => 2];
                }
            }

        } catch (\Throwable $e) {
            Log::info('ee' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function verCatalogoMaterialRequisicionUnidad($id){

        // id presupuesto unidad

        // presupuesto
        $presupuesto = DB::table('p_presup_unidad_detalle AS p')
            ->join('p_materiales AS m', 'p.id_material', '=', 'm.id')
            ->join('obj_especifico AS obj', 'm.id_objespecifico', '=', 'obj.id')
            ->select('m.descripcion', 'm.id AS idmaterial', 'obj.codigo', 'm.costo', 'p.id_presup_unidad', 'm.id_objespecifico', 'm.id_unidadmedida')
            ->where('p.id_presup_unidad', $id)
            ->orderBy('obj.codigo', 'ASC')
            ->get();

        foreach ($presupuesto as $pp){

            $infoMedida = P_UnidadMedida::where('id', $pp->id_unidadmedida)->first();
            $pp->medida = $infoMedida->nombre;

            $infoObjeto = ObjEspecifico::where('id', $pp->id_objespecifico)->first();

            $pp->objcodigo = $infoObjeto->codigo;
            $pp->objnombre = $infoObjeto->nombre;

            $pp->actual = '$' . number_format((float)$pp->costo, 2, '.', ',');
        }

        return view('backend.admin.presupuestounidad.requerimientos.modal.modalcatalogomaterial', compact('presupuesto'));
    }


    public function vistaAñoPresupuestoMaterialAprobados(){
        $anios = P_AnioPresupuesto::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.solicitudmaterial.aprobados.vistaaniosolicitudmaterialaprobados', compact('anios'));
    }

    public function indexRevisionSolicitudMaterialAprobada($idanio){

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.solicitudmaterial.aprobados.vistarevisionsolicitudmaterialaprobados', compact('idanio'));

    }


    public function tablaRevisionSolicitudMaterialUnidadAprobados($idanio){

        return "ttab";

        $lista = P_SolicitudMaterial::all();

        foreach ($lista as $dd) {

            $infoPresup = P_PresupUnidad::where('id', $dd->id_presup_unidad)->first();
            $infoDepar = P_Departamento::where('id', $infoPresup->id_departamento)->first();

            $dd->departamento = $infoDepar->nombre;

            $infoMaterial = P_Materiales::where('id', $dd->id_material)->first();
            $infoCuenta = CuentaUnidad::where('id', $dd->id_cuentaunidad)->first();
            $infoObj = ObjEspecifico::where('id', $infoCuenta->id_objespeci)->first();

            $dd->material = $infoMaterial->descripcion;
            $dd->objnombre = $infoObj->codigo . ' - ' . $infoObj->nombre;

            $total = ($dd->cantidad * $infoMaterial->costo) * $dd->periodo;

            $total = "$" . number_format((float)$total, 2, '.', ',');

            $dd->total = $total;

            $costoactual = "$" . number_format((float)$infoMaterial->costo, 2, '.', ',');

            $dd->costoactual = $costoactual;
        }

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.solicitudmaterial.revision.tablarevisionsolicitudmaterial', compact('lista'));
    }

}

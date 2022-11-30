<?php

namespace App\Http\Controllers\Backend\DescargosDirectos;

use App\Http\Controllers\Controller;
use App\Models\Acta;
use App\Models\Cotizacion;
use App\Models\CuentaProy;
use App\Models\CuentaproyPartidaAdicional;
use App\Models\CuentaUnidad;
use App\Models\DescargosDirectos;
use App\Models\FuenteFinanciamiento;
use App\Models\LineaTrabajo;
use App\Models\MoviCuentaProy;
use App\Models\MoviCuentaUnidad;
use App\Models\ObjEspecifico;
use App\Models\Orden;
use App\Models\P_AnioPresupuesto;
use App\Models\P_PresupUnidad;
use App\Models\Proveedores;
use App\Models\Proyecto;
use App\Models\Requisicion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DescargosDirectosController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    function redondear_dos_decimal($valor){
        $float_redondeado = round($valor * 100) / 100;
        return $float_redondeado;
    }

    // vista para realizar un descargo directo
    public function indexDescargosDirectos(){

        $arrayLineaTrabajo = LineaTrabajo::orderBy('id', 'ASC')->get();
        $arrayFuenteFinanciamiento = FuenteFinanciamiento::orderBy('id', 'ASC')->get();
        $proveedores = Proveedores::orderBy('nombre', 'ASC')->get();
        $anios = P_AnioPresupuesto::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.descargosdirectos.crear.vistacreardescargodirectos', compact('arrayLineaTrabajo',
        'arrayFuenteFinanciamiento', 'proveedores', 'anios'));
    }

    public function tipoDescargoDirectoInformacion(Request $request){

        // 1 proveedor
        // 2 proyecto
        // 3 contribución

        $regla = array(
            'idtipo' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        DB::beginTransaction();
        try {

            if($request->idtipo == 1){

                // POR EL MOMENTO NO DEVUELVE INFORMACION

                return ['success' => 1];
            }
            else if($request->idtipo == 2){

                // obtener lista de proyectos
                // NO ESTÁN FINALIZADOS
                // NO PAUSADOS
                // NO PRIORIZADO
                // SOLO LOS QUE ESTÁN INICIADOS

                $arrayProyectos = Proyecto::where('id_estado', 2)->orderBy('nombre', 'ASC')->get();

                return ['success' => 2, 'proyectos' => $arrayProyectos];
            }
            else if($request->idtipo == 3){

                // POR EL MOMENTO NO DEVUELVE INFORMACION

                return ['success' => 3];
            }
            else{
                return ['success' => 99];
            }

        }catch(\Throwable $e){
            Log::info('error: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // retorna los objeto especificos del proyecto seleccionado
    public function objEspecificosSegunProyecto(Request $request){

        $regla = array(
            'idproyecto' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        if(Proyecto::where('id', $request->idproyecto)->first()){

            $arrayCuentaProy = CuentaProy::where('proyecto_id', $request->idproyecto)->get();

            foreach ($arrayCuentaProy as $dd){
                $objeto = ObjEspecifico::where('id', $dd->objespeci_id)->first();
                $dd->objnombre = $objeto->codigo . ' - ' . $objeto->nombre;
            }

            return ['success' => 1, 'objetos' => $arrayCuentaProy];
        }else{
            return ['success' => 2];
        }
    }

    // retorna saldo restante (- el saldo retenido) de cuenta proy
    public function infoCuentaProySaldos(Request $request){

        $regla = array(
            'idcuenta' => 'required', // cuenta proy
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        if($infoCuenta = CuentaProy::where('id', $request->idcuenta)->first()){

                // CÁLCULOS

                $totalRestante = 0;
                $totalRetenido = 0;

                // movimiento de cuentas SUBE
                $infoMoviCuentaProySube = MoviCuentaProy::where('id_cuentaproy_sube', $infoCuenta->id)
                    ->where('autorizado', 1) // autorizado por presupuesto
                    ->sum('dinero');

                // movimiento de cuentas BAJA
                $infoMoviCuentaProyBaja = MoviCuentaProy::where('id_cuentaproy_baja', $infoCuenta->id)
                    ->where('autorizado', 1) // autorizado por presupuesto
                    ->sum('dinero');

                $totalMoviCuenta = $infoMoviCuentaProySube - $infoMoviCuentaProyBaja;

                // obtener todas las salidas de material
                $arrayRestante = DB::table('cuentaproy_restante AS pd')
                    ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                    ->select('rd.cantidad', 'rd.dinero')
                    ->where('pd.id_cuentaproy', $infoCuenta->id)
                    ->where('rd.cancelado', 0)
                    ->get();

                foreach ($arrayRestante as $dd){
                    $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
                }

                $infoCuentaPartida = CuentaproyPartidaAdicional::where('id_proyecto', $infoCuenta->proyecto_id)
                    ->where('objespeci_id', $infoCuenta->objespeci_id)
                    ->get();

            $sumaPartidaAdicional = 0;

            foreach ($infoCuentaPartida as $dd){
                $sumaPartidaAdicional += $dd->monto;
            }

            // información de saldos retenidos
            $arrayRetenido = DB::table('cuentaproy_retenido AS psr')
                ->join('requisicion_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('psr.id_cuentaproy', $infoCuenta->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRetenido as $dd){
                $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
            }

            // sumando partidas adicionales que coincidan con el obj específico + saldo inicial
            $sumaPartidaAdicional += $infoCuenta->saldo_inicial;

            // aquí se obtiene el Saldo Restante del código
            $totalRestanteSaldo = $totalMoviCuenta + ($sumaPartidaAdicional - $totalRestante);

            $totalCalculado = $totalRestanteSaldo - $totalRetenido;

            $montoRestante = "$" . number_format((float)$totalCalculado, 2, '.', ',');

            return ['success' => 1, 'montorestante' => $montoRestante];
        }else{
            return ['success' => 2];
        }
    }

    // guardar un descargo directo PARA PROYECTO
    public function guardarDescargoDirectoProyecto(Request $request){

        $regla = array(
            'idcuentaproy' => 'required', // CUENTA PROY
            'numacuerdo' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}


        // guardar un descargo directo PARA PROYECTO
        DB::beginTransaction();
        try {

            $infoCuentaProy = CuentaProy::where('id', $request->idcuentaproy)->first();

            // CÁLCULOS

            $totalRestante = 0;
            $totalRetenido = 0;

            // movimiento de cuentas SUBE
            $infoMoviCuentaProySube = MoviCuentaProy::where('id_cuentaproy_sube', $request->idcuentaproy)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            // movimiento de cuentas BAJA
            $infoMoviCuentaProyBaja = MoviCuentaProy::where('id_cuentaproy_baja', $request->idcuentaproy)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $totalMoviCuenta = $infoMoviCuentaProySube - $infoMoviCuentaProyBaja;

            // obtener todas las salidas de material
            $arrayRestante = DB::table('cuentaproy_restante AS pd')
                ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero')
                ->where('pd.id_cuentaproy', $request->idcuentaproy)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRestante as $dd){
                $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
            }

            $infoCuentaPartida = CuentaproyPartidaAdicional::where('id_proyecto', $infoCuentaProy->proyecto_id)
                ->where('objespeci_id', $infoCuentaProy->objespeci_id)
                ->get();

            $sumaPartidaAdicional = 0;

            foreach ($infoCuentaPartida as $dd){
                $sumaPartidaAdicional += $dd->monto;
            }

            // información de saldos retenidos
            $arrayRetenido = DB::table('cuentaproy_retenido AS psr')
                ->join('requisicion_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('psr.id_cuentaproy', $request->idcuentaproy)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRetenido as $dd){
                $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
            }

            // sumando partidas adicionales que coincidan con el obj específico + saldo inicial
            $sumaPartidaAdicional += $infoCuentaProy->saldo_inicial;

            // aquí se obtiene el Saldo Restante del código
            $totalRestanteSaldo = $totalMoviCuenta + ($sumaPartidaAdicional - $totalRestante);

            $totalCalculado = $totalRestanteSaldo - $totalRetenido;

            if($this->redondear_dos_decimal($totalCalculado) < $this->redondear_dos_decimal($request->montodescontar)){

                // EL SALDO NO ALCANZA

                $restante = number_format((float)$totalCalculado, 2, '.', ',');
                $solicitado = number_format((float)$request->montodescontar, 2, '.', ',');

                return ['success' => 1, 'restante' => $restante, 'solicitado' => $solicitado];
            }else{

                // DISMINUIR SALDO Y GUARDAR

                $dato = new DescargosDirectos();
                $dato->fecha = Carbon::now('America/El_Salvador');
                $dato->tipodescargo = 2; // PROYECTO
                $dato->cuentaproy_id = $request->idcuentaproy;
                $dato->saldo_cuentaproy_tenia = $infoCuentaProy->saldo_inicial;
                $dato->cuentaunidad_id = null;
                $dato->saldo_cuentaunidad_tenia = null;
                $dato->proveedores_id = null;
                $dato->numero_acuerdo = $request->numacuerdo;
                $dato->numero_orden = $request->numorden;
                $dato->lineatrabajo_id = $request->sellinea;
                $dato->fuentef_id = $request->selfuentef;
                $dato->concepto = $request->concepto;
                $dato->montodescontar = $request->montodescontar;
                $dato->beneficiario = null;
                $dato->save();

                $saldoactual = ($infoCuentaProy->saldo_inicial - $request->montodescontar);

                // MODIFICAR

                CuentaProy::where('id', $infoCuentaProy->id)->update([
                    'saldo_inicial' => $saldoactual
                ]);

                DB::commit();
                return ['success' => 2];
            }

        }catch(\Throwable $e){
            Log::info('error: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function buscarUnidadSegunAnio(Request $request){

        $regla = array(
            'idanio' => 'required',
            'idtipo' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}


        if($request->idtipo == 1){ // PROVEEDOR

            if($infoPresup = P_PresupUnidad::where('id_anio', $request->idanio)
                ->where('id_departamento', 1)
                ->where('id_estado', 3) // aprobado
                ->first()){
                // encontrada

                if(CuentaUnidad::where('id_presup_unidad', $infoPresup->id)
                    ->first()){
                    // por lo menos si hay una cuenta unidad

                    // CUENTA UNIDAD DEL DEPARTAMENTO CONCEJO
                    $arrayCuenta = CuentaUnidad::where('id_presup_unidad', $infoPresup->id)->get();
                    foreach ($arrayCuenta as $dd){
                        $infoObj = ObjEspecifico::where('id', $dd->id_objespeci)->first();
                        $dd->codigo = $infoObj->codigo . ' - ' . $infoObj->nombre;
                    }

                    return ['success' => 1, 'listado' => $arrayCuenta];

                }else{
                    // no se encuentra ninguna cuenta unidad
                    return ['success' => 2];
                }

            }else{
               // no encontrada
                return ['success' => 3];
            }
        }
        else{ // POR DEFECTO CONTRIBUCION

            if($infoPresup = P_PresupUnidad::where('id_anio', $request->idanio)
                ->where('id_departamento', 49)
                ->where('id_estado', 3) // aprobado
                ->first()){
                // encontrada

                if(CuentaUnidad::where('id_presup_unidad', $infoPresup->id)
                    ->first()){
                    // por lo menos si hay una cuenta unidad

                    // CUENTA UNIDAD DEL DEPARTAMENTO CONCEJO
                    $arrayCuenta = CuentaUnidad::where('id_presup_unidad', $infoPresup->id)->get();
                    foreach ($arrayCuenta as $dd){
                        $infoObj = ObjEspecifico::where('id', $dd->id_objespeci)->first();
                        $dd->codigo = $infoObj->codigo . ' - ' . $infoObj->nombre;
                    }

                    return ['success' => 4, 'listado' => $arrayCuenta];

                }else{
                    // no se encuentra ninguna cuenta unidad
                    return ['success' => 5];
                }

            }else{
                // no encontrada
                return ['success' => 6];
            }
        }
    }


    public function infoCuentaUnidadSaldos(Request $request){

        $regla = array(
            'idunidad' => 'required', // cuenta unidad
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        if($infoCuenta = CuentaUnidad::where('id', $request->idunidad)->first()){

            // CÁLCULOS

            $totalRestante = 0;
            $totalRetenido = 0;

            // movimiento de cuentas SUBE
            $infoMoviCuentaUnidadSube = MoviCuentaUnidad::where('id_cuentaunidad_sube', $infoCuenta->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            // movimiento de cuentas BAJA
            $infoMoviCuentaUnidadBaja = MoviCuentaUnidad::where('id_cuentaunidad_baja', $infoCuenta->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $totalMoviCuenta = $infoMoviCuentaUnidadSube - $infoMoviCuentaUnidadBaja;

            // obtener todas las salidas de material
            $arrayRestante = DB::table('cuentaunidad_restante AS pd')
                ->join('requisicion_unidad_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero')
                ->where('pd.id_cuenta_unidad', $infoCuenta->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRestante as $dd){
                $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
            }

            // información de saldos retenidos
            $arrayRetenido = DB::table('cuentaunidad_retenido AS psr')
                ->join('requisicion_unidad_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('psr.id_cuenta_unidad', $infoCuenta->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRetenido as $dd){
                $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
            }

            // aquí se obtiene el Saldo Restante del código
            $totalRestanteSaldo = $totalMoviCuenta + ($infoCuenta->saldo_inicial - $totalRestante);

            $totalCalculado = $totalRestanteSaldo - $totalRetenido;

            $montoRestante = "$" . number_format((float)$totalCalculado, 2, '.', ',');

            return ['success' => 1, 'montorestante' => $montoRestante];
        }else{
            return ['success' => 2];
        }
    }

    public function guardarDescargoDirectoProveedor(Request $request){

        $regla = array(
            'idcuentaunidad' => 'required', // CUENTA UNIDAD
            'numacuerdo' => 'required',
            'idproveedor' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}


        // guardar un descargo directo PARA PROYECTO
        DB::beginTransaction();
        try {

            if($infoCuentaUnidad = CuentaUnidad::where('id', $request->idcuentaunidad)->first()){

                // CÁLCULOS

                $totalRestante = 0;
                $totalRetenido = 0;

                // movimiento de cuentas SUBE
                $infoMoviCuentaUnidadSube = MoviCuentaUnidad::where('id_cuentaunidad_sube', $infoCuentaUnidad->id)
                    ->where('autorizado', 1) // autorizado por presupuesto
                    ->sum('dinero');

                // movimiento de cuentas BAJA
                $infoMoviCuentaUnidadBaja = MoviCuentaUnidad::where('id_cuentaunidad_baja', $infoCuentaUnidad->id)
                    ->where('autorizado', 1) // autorizado por presupuesto
                    ->sum('dinero');

                $totalMoviCuenta = $infoMoviCuentaUnidadSube - $infoMoviCuentaUnidadBaja;

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

                $totalCalculado = $totalRestanteSaldo - $totalRetenido;

                if($this->redondear_dos_decimal($totalCalculado) < $this->redondear_dos_decimal($request->montodescontar)){

                    // EL SALDO NO ALCANZA

                    $restante = number_format((float)$totalCalculado, 2, '.', ',');
                    $solicitado = number_format((float)$request->montodescontar, 2, '.', ',');

                    return ['success' => 1, 'restante' => $restante, 'solicitado' => $solicitado];
                }else{

                    // DISMINUIR SALDO Y GUARDAR

                    $dato = new DescargosDirectos();
                    $dato->fecha = Carbon::now('America/El_Salvador');
                    $dato->tipodescargo = 1; // PROVEEDOR
                    $dato->cuentaproy_id = null;
                    $dato->saldo_cuentaproy_tenia = null;
                    $dato->cuentaunidad_id = $infoCuentaUnidad->id;
                    $dato->saldo_cuentaunidad_tenia = $infoCuentaUnidad->saldo_inicial;
                    $dato->proveedores_id = $request->idproveedor;
                    $dato->numero_acuerdo = $request->numacuerdo;
                    $dato->numero_orden = $request->numorden;
                    $dato->lineatrabajo_id = $request->sellinea;
                    $dato->fuentef_id = $request->selfuentef;
                    $dato->concepto = $request->concepto;
                    $dato->montodescontar = $request->montodescontar;
                    $dato->beneficiario = null;
                    $dato->save();

                    $saldoactual = ($infoCuentaUnidad->saldo_inicial - $request->montodescontar);

                    // MODIFICAR

                    CuentaUnidad::where('id', $infoCuentaUnidad->id)->update([
                        'saldo_inicial' => $saldoactual
                    ]);

                    DB::commit();
                    return ['success' => 2];
                }
            }
            else{
                // siempre deberia encontrar la cuenta unidad
                return ['success' => 99];
            }

        }catch(\Throwable $e){
            Log::info('error: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function guardarDescargoDirectoContribucion(Request $request){

        $regla = array(
            'idcuentaunidad' => 'required', // CUENTA UNIDAD
            'numacuerdo' => 'required',
            'beneficiario' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        // guardar un descargo directo PARA PROYECTO
        DB::beginTransaction();
        try {

            if($infoCuentaUnidad = CuentaUnidad::where('id', $request->idcuentaunidad)->first()){

                // CÁLCULOS

                $totalRestante = 0;
                $totalRetenido = 0;

                // movimiento de cuentas SUBE
                $infoMoviCuentaUnidadSube = MoviCuentaUnidad::where('id_cuentaunidad_sube', $infoCuentaUnidad->id)
                    ->where('autorizado', 1) // autorizado por presupuesto
                    ->sum('dinero');

                // movimiento de cuentas BAJA
                $infoMoviCuentaUnidadBaja = MoviCuentaUnidad::where('id_cuentaunidad_baja', $infoCuentaUnidad->id)
                    ->where('autorizado', 1) // autorizado por presupuesto
                    ->sum('dinero');

                $totalMoviCuenta = $infoMoviCuentaUnidadSube - $infoMoviCuentaUnidadBaja;

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

                $totalCalculado = $totalRestanteSaldo - $totalRetenido;

                if($this->redondear_dos_decimal($totalCalculado) < $this->redondear_dos_decimal($request->montodescontar)){

                    // EL SALDO NO ALCANZA

                    $restante = number_format((float)$totalCalculado, 2, '.', ',');
                    $solicitado = number_format((float)$request->montodescontar, 2, '.', ',');

                    return ['success' => 1, 'restante' => $restante, 'solicitado' => $solicitado];
                }else{

                    // DISMINUIR SALDO Y GUARDAR

                    $dato = new DescargosDirectos();
                    $dato->fecha = Carbon::now('America/El_Salvador');
                    $dato->tipodescargo = 1; // PROVEEDOR
                    $dato->cuentaproy_id = null;
                    $dato->saldo_cuentaproy_tenia = null;
                    $dato->cuentaunidad_id = $infoCuentaUnidad->id;
                    $dato->saldo_cuentaunidad_tenia = $infoCuentaUnidad->saldo_inicial;
                    $dato->proveedores_id = $request->idproveedor;
                    $dato->numero_acuerdo = $request->numacuerdo;
                    $dato->numero_orden = $request->numorden;
                    $dato->lineatrabajo_id = $request->sellinea;
                    $dato->fuentef_id = $request->selfuentef;
                    $dato->concepto = $request->concepto;
                    $dato->montodescontar = $request->montodescontar;
                    $dato->beneficiario = $request->beneficiario;
                    $dato->save();

                    $saldoactual = ($infoCuentaUnidad->saldo_inicial - $request->montodescontar);

                    // MODIFICAR

                    CuentaUnidad::where('id', $infoCuentaUnidad->id)->update([
                        'saldo_inicial' => $saldoactual
                    ]);

                    DB::commit();
                    return ['success' => 2];
                }
            }
            else{
                // siempre deberia encontrar la cuenta unidad
                return ['success' => 99];
            }

        }catch(\Throwable $e){
            Log::info('error: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


}

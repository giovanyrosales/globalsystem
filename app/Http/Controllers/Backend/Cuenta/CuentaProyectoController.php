<?php

namespace App\Http\Controllers\Backend\Cuenta;

use App\Http\Controllers\Controller;
use App\Models\Cuenta;
use App\Models\CuentaProy;
use App\Models\MoviCuentaProy;
use App\Models\ObjEspecifico;
use App\Models\PartidaAdicional;
use App\Models\PartidaAdicionalContenedor;
use App\Models\PartidaAdicionalDetalle;
use App\Models\Planilla;
use App\Models\Proyecto;
use App\Models\TipoPartida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CuentaProyectoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // retorna vista con los movimientos de cuenta para un proyecto ID
    public function indexMoviCuentaProy($id)
    {
        // ID: PROYECTO

        $infoProyecto = Proyecto::where('id', $id)->first();
        $permiso = $infoProyecto->permiso;

        return view('backend.admin.proyectos.cuentaproyecto.movimiento.vistamovicuentaproy', compact('id', 'permiso'));
    }

    // retorna vista con los historicos movimientos por proyecto ID
    public function indexMoviCuentaProyHistorico($id)
    {
        // ID: PROYECTO
        return view('backend.admin.proyectos.cuentaproyecto.historico.vistamovicuentahistorico', compact('id'));
    }

    // retorna tabla con los historicos movimientos por proyecto ID
    public function tablaMoviCuentaProyHistorico($id)
    {

        // ID PROYECTO
        $pila = array();
        $listado = CuentaProy::where('proyecto_id', $id)->get();

        foreach ($listado as $ll) {
            array_push($pila, $ll->id);
        }

        $infoMovimiento = MoviCuentaProy::whereIn('id_cuentaproy_sube', $pila)
            ->orderBy('fecha', 'ASC')
            ->get();

        foreach ($infoMovimiento as $dd) {

            $infoCuentaProyAumenta = CuentaProy::where('id', $dd->id_cuentaproy_sube)->first();
            $infoCuentaProyBaja = CuentaProy::where('id', $dd->id_cuentaproy_baja)->first();

            $infoObjetoAumenta = ObjEspecifico::where('id', $infoCuentaProyAumenta->objespeci_id)->first();
            $infoObjetoBaja = ObjEspecifico::where('id', $infoCuentaProyBaja->objespeci_id)->first();

            $dd->cuentaaumenta = $infoObjetoAumenta->codigo . " - " . $infoObjetoAumenta->nombre;
            $dd->cuentabaja = $infoObjetoBaja->codigo . " - " . $infoObjetoBaja->nombre;

            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));
        }

        return view('backend.admin.proyectos.cuentaproyecto.historico.tablamovicuentahistorico', compact('infoMovimiento'));
    }

    // retorna tabla con los movimientos de cuenta para un proyecto ID
    public function indexTablaMoviCuentaProy($id)
    {

        // ID PROYECTO

        $presupuesto = DB::table('cuentaproy AS p')
            ->join('obj_especifico AS obj', 'p.objespeci_id', '=', 'obj.id')
            ->select('obj.nombre', 'obj.id AS idcodigo', 'p.proyecto_id',
                'obj.codigo', 'p.id', 'p.saldo_inicial')
            ->where('p.proyecto_id', $id)
            ->get();

        foreach ($presupuesto as $pp) {

            // CÁLCULOS

            $totalRestante = 0;
            $totalRetenido = 0;

            // movimiento de cuentas (sube y baja)
            $infoMoviCuentaProySube = MoviCuentaProy::where('id_cuentaproy_sube', $pp->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $infoMoviCuentaProyBaja = MoviCuentaProy::where('id_cuentaproy_baja', $pp->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $totalMoviCuenta = $infoMoviCuentaProySube - $infoMoviCuentaProyBaja;

            // obtener todas las salidas de material
            $arrayRestante = DB::table('cuentaproy_restante AS pd')
                ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero')
                ->where('pd.id_cuentaproy', $pp->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRestante as $dd) {
                $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
            }

            // información de saldos retenidos
            $arrayRetenido = DB::table('cuentaproy_retenido AS psr')
                ->join('requisicion_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('psr.id_cuentaproy', $pp->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRetenido as $dd) {
                $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
            }

            // aquí se obtiene el Saldo Restante del código
            $totalRestanteSaldo = $totalMoviCuenta + $pp->saldo_inicial - $totalRestante;

            //$totalCalculado = $totalRestanteSaldo - $totalRetenido;

            // usado para ver puedo hacer un movimiento de cuenta
            $infoProyecto = Proyecto::where('id', $pp->proyecto_id)->first();
            $pp->permiso = $infoProyecto->permiso;


            $pp->saldo_inicial = number_format((float)$pp->saldo_inicial, 2, '.', ',');
            $pp->saldo_restante = number_format((float)$totalRestanteSaldo, 2, '.', ',');
            $pp->total_retenido = number_format((float)$totalRetenido, 2, '.', ',');
        }

        return view('backend.admin.proyectos.cuentaproyecto.movimiento.tablamovicuentaproy', compact('presupuesto'));
    }

    // registra una nuevo movimiento de cuenta
    public function nuevaMoviCuentaProy(Request $request)
    {

        DB::beginTransaction();

        try {

            // VERIFICAR MIS SALDOS RESTANTE Y VERIFICAR QUE NO QUEDE MENOR A 0

            $infoCuentaProy = CuentaProy::where('id', $request->selectcuenta)->first(); // y este va a disminuir
            $infoProyecto = Proyecto::where('id', $infoCuentaProy->proyecto_id)->first();

            // no hay permiso para realizar el movimiento de cuenta
            if ($infoProyecto->permiso == 0) {
                return ['success' => 1];
            }

            $infoObjetoEspe = ObjEspecifico::where('id', $infoCuentaProy->objespeci_id)->first();
            $txtObjetoEspec = $infoObjetoEspe->codigo . " - " . $infoObjetoEspe->nombre;

            // PROCESO DE CÁLCULOS

            $totalRestante = 0;
            $totalRetenido = 0;

            // movimiento de cuentas (sube y baja)
            $infoMoviCuentaProySube = MoviCuentaProy::where('id_cuentaproy_sube', $request->selectcuenta)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $infoMoviCuentaProyBaja = MoviCuentaProy::where('id_cuentaproy_baja', $request->selectcuenta)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            // variable para guardar movimiento de cuenta calculada
            $totalMoviCuenta = $infoMoviCuentaProySube - $infoMoviCuentaProyBaja;

            // obtener todas las salidas de material
            $arrayRestante = DB::table('cuentaproy_restante AS pd')
                ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('pd.id_cuentaproy', $infoCuentaProy->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRestante as $dd) {
                $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
            }

            // obtener saldos retenidos
            $arrayRetenido = DB::table('cuentaproy_retenido AS psr')
                ->join('requisicion_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('psr.id_cuentaproy', $infoCuentaProy->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRetenido as $dd) {
                $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
            }

            // aquí se obtiene el Saldo Restante del código
            $totalRestanteSaldo = $totalMoviCuenta + $infoCuentaProy->saldo_inicial - $totalRestante;

            // al saldo restante le vamos a restar el dinero que se quitara al código
            // al otro código que se sumara, no sé verífica ya que no afecta que suba su dinero
            // hoy se obtendrá lo restante - retenido, ya
            $totalCalculado = ($totalRestanteSaldo - $request->saldomodificar) - $totalRetenido;

            // al final no debe quedar menor a 0, para poder guardar el movimiento de cuenta.
            if ($totalCalculado < 0) {
                // saldo insuficiente para hacer este movimiento, ya que queda NEGATIVO

                $totalCalculado = number_format((float)$totalCalculado, 2, '.', ',');
                $totalRestanteSaldo = number_format((float)$totalRestanteSaldo, 2, '.', ',');
                $totalRetenido = number_format((float)$totalRetenido, 2, '.', ',');
                $dinero = number_format((float)$request->saldomodificar, 2, '.', ',');

                return ['success' => 2, 'objeto' => $txtObjetoEspec, 'restante' => $totalRestanteSaldo,
                    'retenido' => $totalRetenido, 'dinero' => $dinero, 'calculado' => $totalCalculado];
            }

            // Guardar

            $co = new MoviCuentaProy();
            $co->id_cuentaproy_sube = $request->idcuentaproy;
            $co->dinero = $request->saldomodificar;
            $co->id_cuentaproy_baja = $request->selectcuenta;
            $co->fecha = $request->fecha;
            $co->reforma = null;
            $co->autorizado = 0;
            $co->save();

            // setear para que no agregue más movimientos
            Proyecto::where('id', $infoProyecto->id)->update([
                'permiso' => 0,
            ]);

            DB::commit();
            return ['success' => 3];

        } catch (\Throwable $e) {
            Log::info('ee' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    function redondear_dos_decimal($valor)
    {
        $float_redondeado = round($valor * 100) / 100;
        return $float_redondeado;
    }

    // descargar un documento Reforma de movimiento de cuenta
    public function descargarReforma($id)
    {

        $url = MoviCuentaProy::where('id', $id)->pluck('reforma')->first();
        $pathToFile = "storage/archivos/" . $url;
        $extension = pathinfo(($pathToFile), PATHINFO_EXTENSION);
        $nombre = "Documento." . $extension;
        return response()->download($pathToFile, $nombre);
    }

    // guardar un documento Reforma para movimiento de cuenta
    public function guardarDocumentoReforma(Request $request)
    {

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

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

                    $info = MoviCuentaProy::where('id', $request->id)->first();

                    if (Storage::disk('archivos')->exists($info->reforma)) {
                        Storage::disk('archivos')->delete($info->reforma);
                    }

                    MoviCuentaProy::where('id', $request->id)->update([
                        'reforma' => $nomDocumento
                    ]);

                    DB::commit();
                    return ['success' => 1];
                } else {
                    return ['success' => 2];
                }
            } else {
                return ['success' => 2];
            }
        } catch (\Throwable $e) {

            DB::rollback();
            return ['success' => 2];
        }
    }

    // información de un movimiento de cuenta
    public function informacionMoviCuentaProy(Request $request)
    {

        $regla = array(
            'id' => 'required', // ID CUENTA PROY
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if ($lista = CuentaProy::where('id', $request->id)->first()) {

            $infoObjeto = ObjEspecifico::where('id', $lista->objespeci_id)->first();
            $infoCuenta = Cuenta::where('id', $infoObjeto->id_cuenta)->first();
            $cuenta = $infoCuenta->nombre;

            // obtener CUENTA PROY. menos la seleccionada
            $arrayCuentaProy = DB::table('cuentaproy AS cp')
                ->join('obj_especifico AS obj', 'cp.objespeci_id', '=', 'obj.id')
                ->select('obj.nombre', 'obj.codigo', 'cp.id',)
                ->where('cp.id', '!=', $lista->id)
                ->get();

            // CÁLCULOS

            $totalSalida = 0;
            $totalEntrada = 0;
            $totalRetenido = 0;

            // movimiento de cuentas (sube y baja)
            $infoMoviCuentaProySube = MoviCuentaProy::where('id_cuentaproy_sube', $lista->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $infoMoviCuentaProyBaja = MoviCuentaProy::where('id_cuentaproy_baja', $lista->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $totalMoviCuenta = $infoMoviCuentaProySube - $infoMoviCuentaProyBaja;

            // obtener todas las salidas de material
            $infoSalidaDetalle = DB::table('cuentaproy_retenido AS pd')
                ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero')
                ->where('pd.id_cuentaproy', $lista->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($infoSalidaDetalle as $dd) {
                $totalSalida = $totalSalida + ($dd->cantidad * $dd->dinero);
            }

            $infoEntradaDetalle = DB::table('cuentaproy_retenido AS pd')
                ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('pd.id_cuentaproy', $lista->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($infoEntradaDetalle as $dd) {
                $totalEntrada = $totalEntrada + ($dd->cantidad * $dd->dinero);
            }

            // información de saldos retenidos
            $infoSaldoRetenido = DB::table('cuentaproy_retenido AS psr')
                ->join('requisicion_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('psr.id_cuentaproy', $lista->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($infoSaldoRetenido as $dd) {
                $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
            }

            // total de los cambios de detalle que se han hecho.
            $totalCuentaDetalle = $totalEntrada - $totalSalida;

            // aquí se obtiene el Saldo Restante del código
            $totalRestanteSaldo = $totalMoviCuenta + $lista->saldo_inicial - $totalCuentaDetalle;

            //$totalCalculado = $totalRestanteSaldo - $totalRetenido;

            $totalRestanteSaldo = "$" . number_format((float)$totalRestanteSaldo, 2, '.', ',');

            return ['success' => 1, 'info' => $lista,
                'objeto' => $infoObjeto, 'cuenta' => $cuenta,
                'restante' => $totalRestanteSaldo, 'arraycuentaproy' => $arrayCuentaProy];
        } else {
            return ['success' => 2];
        }
    }

    // al mover el select de movimiento cuenta a modificar, quiero ver el saldo restante
    public function infoSaldoRestanteCuenta(Request $request)
    {

        $regla = array(
            'id' => 'required', // ID CUENTA PROY
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if ($lista = CuentaProy::where('id', $request->id)->first()) {


            // CÁLCULOS

            $totalRestante = 0;
            $totalRetenido = 0;

            // movimiento de cuentas (sube y baja)
            $infoMoviCuentaProySube = MoviCuentaProy::where('id_cuentaproy_sube', $lista->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $infoMoviCuentaProyBaja = MoviCuentaProy::where('id_cuentaproy_baja', $lista->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $totalMoviCuenta = $infoMoviCuentaProySube - $infoMoviCuentaProyBaja;

            // obtener saldos restante
            $arrayRestante = DB::table('cuentaproy_restante AS pd')
                ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero')
                ->where('pd.id_cuentaproy', $lista->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRestante as $dd) {
                $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
            }

            // información de saldos retenidos
            $arrayRetenido = DB::table('cuentaproy_retenido AS psr')
                ->join('requisicion_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('psr.id_cuentaproy', $lista->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRetenido as $dd) {
                $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
            }

            // aquí se obtiene el Saldo Restante del código
            $totalRestanteSaldo = $totalMoviCuenta + $lista->saldo_inicial - $totalRestante;

            //$totalCalculado = $totalRestanteSaldo - $totalRetenido;

            $totalRestanteSaldo = "$" . number_format((float)$totalRestanteSaldo, 2, '.', ',');

            return ['success' => 1, 'restante' => $totalRestanteSaldo];
        } else {
            return ['success' => 2];
        }
    }

    // retorna vista para agregar planilla a proyecto
    public function indexPlanilla($id)
    {

        $info = Proyecto::where('id', $id)->first();
        if ($info->codigo != null) {
            $datos = $info->codigo . " - " . $info->nombre;
        } else {
            $datos = $info->nombre;
        }

        return view('backend.admin.proyectos.planilla.vistaplanilla', compact('id', 'datos'));
    }

    // retorna tabla para agregar planilla a proyecto
    public function tablaPlanilla($id)
    {

        $lista = Planilla::where('proyecto_id', $id)->orderBy('fecha_de')->get();

        foreach ($lista as $ll) {

            // periodo de pago
            $ll->periodopago = date("d/m/Y", strtotime($ll->fecha_de)) . " - " . date("d/m/Y", strtotime($ll->fecha_hasta));

            // total devengado: salario extra + horas extras
            $suma = $ll->salario_total + $ll->horas_extra;

            $ll->salario_total = number_format((float)$ll->salario_total, 2, '.', ',');
            $ll->horas_extra = number_format((float)$ll->horas_extra, 2, '.', ',');

            $ll->totaldevengado = number_format((float)$suma, 2, '.', ',');
            $ll->insaforp = number_format((float)$ll->insaforp, 2, '.', ',');
        }

        return view('backend.admin.proyectos.planilla.tablaplanilla', compact('lista'));
    }

    // agrega una nueva planilla a proyecto
    public function nuevaPlanilla(Request $request)
    {

        DB::beginTransaction();

        try {
            $dato = new Planilla();
            $dato->proyecto_id = $request->id;
            $dato->fecha_de = $request->fechade;
            $dato->fecha_hasta = $request->fechahasta;
            $dato->salario_total = $request->salariototal;
            $dato->horas_extra = $request->horasextra;
            $dato->isss_laboral = $request->issslaboral;
            $dato->isss_patronal = $request->issspatronal;
            $dato->afpconfia_laboral = $request->confialaboral;
            $dato->afpconfia_patronal = $request->confiapatronal;
            $dato->afpcrecer_laboral = $request->crecerlaboral;
            $dato->afpcrecer_patronal = $request->crecerpatronal;
            $dato->insaforp = $request->insaforp;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('ee ' . $e);
            DB::rollback();
            return ['success' => 2];
        }
    }

    // obtener información de planilla
    public function informacionPlanilla(Request $request)
    {
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if ($lista = Planilla::where('id', $request->id)->first()) {

            return ['success' => 1, 'planilla' => $lista];
        } else {
            return ['success' => 2];
        }
    }

    // edita la información de una planilla
    public function editarPlanilla(Request $request)
    {

        if (Planilla::where('id', $request->id)->first()) {

            Planilla::where('id', $request->id)->update([
                'fecha_de' => $request->fechade,
                'fecha_hasta' => $request->fechahasta,
                'salario_total' => $request->salariototal,
                'horas_extra' => $request->horasextra,
                'isss_laboral' => $request->issslaboral,
                'isss_patronal' => $request->issspatronal,
                'afpconfia_laboral' => $request->confialaboral,
                'afpconfia_patronal' => $request->confiapatronal,
                'afpcrecer_laboral' => $request->crecerlaboral,
                'afpcrecer_patronal' => $request->crecerpatronal,
                'insaforp' => $request->insaforp,
            ]);

            return ['success' => 1];
        } else {
            return ['success' => 2];
        }
    }


    // petición para que jefe presupuesto autorice un movimiento de cuenta
    public function autorizarMovimientoDeCuenta(Request $request)
    {

        if (Proyecto::where('id', $request->id)->first()) {

            Proyecto::where('id', $request->id)->update([
                'permiso' => 1
            ]);

            return ['success' => 1];
        } else {
            return ['success' => 2];
        }

    }

    // petición para que jefe presupuesto deniegue un movimiento de cuenta
    public function denegarMovimientoDeCuenta(Request $request)
    {

        if (Proyecto::where('id', $request->id)->first()) {

            Proyecto::where('id', $request->id)->update([
                'permiso' => 0
            ]);

            return ['success' => 1];
        } else {
            return ['success' => 2];
        }
    }

    // ver información del movimiento de cuenta para que jefe presupuesto Apruebe o Denegar
    public function informacionHistoricoParaAutorizar(Request $request)
    {

        $regla = array(
            'id' => 'required', // ID movicuentaproy
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if ($infoMovi = MoviCuentaProy::where('id', $request->id)->first()) {

            $infoCuentaProySube = CuentaProy::where('id', $infoMovi->id_cuentaproy_sube)->first();
            $infoCuentaProyBaja = CuentaProy::where('id', $infoMovi->id_cuentaproy_baja)->first();

            $infoObjetoSube = ObjEspecifico::where('id', $infoCuentaProySube->objespeci_id)->first();
            $infoObjetoBaja = ObjEspecifico::where('id', $infoCuentaProyBaja->objespeci_id)->first();

            $infoCuentaSube = Cuenta::where('id', $infoObjetoSube->id_cuenta)->first();
            $infoCuentaBaja = Cuenta::where('id', $infoObjetoBaja->id_cuenta)->first();

            $objetoaumenta = $infoObjetoSube->codigo . " - " . $infoObjetoSube->nombre;
            $objetobaja = $infoObjetoBaja->codigo . " - " . $infoObjetoBaja->nombre;

            $cuentaaumenta = $infoCuentaSube->codigo . " - " . $infoCuentaSube->nombre;
            $cuentabaja = $infoCuentaBaja->codigo . " - " . $infoCuentaBaja->nombre;

            $fecha = date("d-m-Y", strtotime($infoMovi->fecha));


            // OBTENER SALDO RESTANTE, PARA EL OBJETO ESPECÍFICO QUE SE QUITARA DINERO

            // CÁLCULOS

            $totalSalida = 0;
            $totalEntrada = 0;
            $totalRetenido = 0;

            // movimiento de cuentas (sube y baja)
            $infoMoviCuentaProySube = MoviCuentaProy::where('id_cuentaproy_sube', $infoCuentaProyBaja->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $infoMoviCuentaProyBaja = MoviCuentaProy::where('id_cuentaproy_baja', $infoCuentaProyBaja->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $totalMoviCuenta = $infoMoviCuentaProySube - $infoMoviCuentaProyBaja;

            // obtener todas las salidas de material
            $infoSalidaDetalle = DB::table('cuentaproy_retenido AS pd')
                ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero')
                ->where('pd.id_cuentaproy', $infoCuentaProyBaja->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($infoSalidaDetalle as $dd) {
                $totalSalida = $totalSalida + ($dd->cantidad * $dd->dinero);
            }

            $infoEntradaDetalle = DB::table('cuentaproy_retenido AS pd')
                ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('pd.id_cuentaproy', $infoCuentaProyBaja->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($infoEntradaDetalle as $dd) {
                $totalEntrada = $totalEntrada + ($dd->cantidad * $dd->dinero);
            }

            // información de saldos retenidos
            $infoSaldoRetenido = DB::table('cuentaproy_retenido AS psr')
                ->join('requisicion_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('psr.id_cuentaproy', $infoCuentaProyBaja->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($infoSaldoRetenido as $dd) {
                $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
            }

            // total de los cambios de detalle que se han hecho.
            $totalCuentaDetalle = $totalEntrada - $totalSalida;

            // aquí se obtiene el Saldo Restante del código
            $totalRestanteSaldo = $totalMoviCuenta + $infoCuentaProyBaja->saldo_inicial - $totalCuentaDetalle;

            //$totalCalculado = $totalRestanteSaldo - $totalRetenido;

            $totalRestanteSaldo = number_format((float)$totalRestanteSaldo, 2, '.', ',');

            return ['success' => 1, 'info' => $infoMovi, 'cuentaaumenta' => $cuentaaumenta,
                'cuentabaja' => $cuentabaja, 'objetosube' => $objetoaumenta, 'objetobaja' => $objetobaja,
                'fecha' => $fecha, 'restantecuentabaja' => $totalRestanteSaldo];
        } else {
            return ['success' => 2];
        }
    }


    public function denegarBorrarMovimientoCuenta(Request $request)
    {
        $regla = array(
            'id' => 'required', // ID movicuentaproy
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if ($infoMovi = MoviCuentaProy::where('id', $request->id)->first()) {

            // no borrar porque ya esta autorizado
            if ($infoMovi->autorizado == 1) {
                return ['success' => 1];
            }

            // borrar fila
            MoviCuentaProy::where('id', $request->id)->delete();

            return ['success' => 2];
        } else {
            return ['success' => 3];
        }
    }


    public function autorizarMovimientoCuenta(Request $request)
    {

        // ID movicuentaproy

        DB::beginTransaction();

        try {

            if ($infoMovimiento = MoviCuentaProy::where('id', $request->id)->first()) {

                // movimiento ya estaba autorizado
                if ($infoMovimiento->autorizado == 1) {
                    return ['success' => 1];
                }

                $infoCuentaProy = CuentaProy::where('id', $infoMovimiento->id_cuentaproy_baja)->first(); // y este va a disminuir

                $infoObjetoEspe = ObjEspecifico::where('id', $infoCuentaProy->objespeci_id)->first();
                $txtObjetoEspec = $infoObjetoEspe->codigo . " - " . $infoObjetoEspe->nombre;

                // CÁLCULOS

                $totalRestante = 0;
                $totalRetenido = 0;

                // se está haciendo cálculos únicamente con la cuenta que BAJARA, la que subirá no se hace ningún cálculo

                // movimiento de cuentas (sube y baja)
                $infoMoviCuentaProySube = MoviCuentaProy::where('id_cuentaproy_sube', $infoMovimiento->id_cuentaproy_baja)
                    ->where('autorizado', 1) // autorizado por presupuesto
                    ->sum('dinero');

                $infoMoviCuentaProyBaja = MoviCuentaProy::where('id_cuentaproy_baja', $infoMovimiento->id_cuentaproy_baja)
                    ->where('autorizado', 1) // autorizado por presupuesto
                    ->sum('dinero');

                // variable para guardar movimiento de cuenta calculada
                $totalMoviCuenta = $infoMoviCuentaProySube - $infoMoviCuentaProyBaja;

                // obtener saldo restante
                $arrayRestante = DB::table('cuentaproy_restante AS pd')
                    ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                    ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                    ->where('pd.id_cuentaproy', $infoCuentaProy->id)
                    ->where('rd.cancelado', 0)
                    ->get();

                foreach ($arrayRestante as $dd) {
                    $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
                }

                // obtener saldos retenidos
                $arrayRetenido = DB::table('cuentaproy_retenido AS psr')
                    ->join('requisicion_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                    ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                    ->where('psr.id_cuentaproy', $infoCuentaProy->id)
                    ->where('rd.cancelado', 0)
                    ->get();

                foreach ($arrayRetenido as $dd) {
                    $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
                }

                // aquí se obtiene el Saldo Restante del código
                $totalRestanteSaldo = $totalMoviCuenta + $infoCuentaProy->saldo_inicial - $totalRestante;

                // al saldo restante le vamos a restar el dinero que se quitara al código
                // al otro código que se sumara, no sé verífica ya que no afecta que suba su dinero
                // hoy se obtendrá lo restante - retenido, ya
                $totalCalculado = ($totalRestanteSaldo - $infoMovimiento->dinero) - $totalRetenido;

                if ($this->redondear_dos_decimal($totalCalculado) < 0) {
                    // saldo insuficiente para hacer este movimiento, ya que queda NEGATIVO

                    $totalCalculado = number_format((float)$totalCalculado, 2, '.', ',');
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

                        MoviCuentaProy::where('id', $request->id)->update([
                            'reforma' => $nomDocumento,
                            'autorizado' => 1
                        ]);

                        DB::commit();
                        return ['success' => 3];
                    } else {
                        return ['success' => 99];
                    }
                } else {

                    MoviCuentaProy::where('id', $request->id)->update([
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


    //***************  PARTIDAS ADICIONALES  *********************


    public function indexPartidaAdicionalContenedor($id)
    {
        // id PROYECTO

        $infoPro = Proyecto::where('id', $id)->first();

        return view('backend.admin.proyectos.partidaadicional.contenedor.vistacontenedorpartidaadicional', compact('id', 'infoPro'));
    }


    public function tablaPartidaAdicionalContenedor($id)
    {
        // id PROYECTO

        $lista = PartidaAdicionalContenedor::where('id_proyecto', $id)
            ->orderBy('fecha', 'ASC')
            ->get();

        foreach ($lista as $dd) {
            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));
        }

        return view('backend.admin.proyectos.partidaadicional.contenedor.tablacontenedorpartidaadicional', compact('lista'));
    }

    // autorizar que se pueda crear partidas adicionales
    public function autorizarPartidaAdicionalPermiso(Request $request)
    {
        if (Proyecto::where('id', $request->id)->first()) {

            Proyecto::where('id', $request->id)->update([
                'permiso_partida_adic' => 1
            ]);

            return ['success' => 1];
        } else {
            return ['success' => 2];
        }
    }

    // denegar que se pueda crear partidas adicionales
    public function denegarPartidaAdicionalPermiso(Request $request)
    {
        if (Proyecto::where('id', $request->id)->first()) {

            Proyecto::where('id', $request->id)->update([
                'permiso_partida_adic' => 0
            ]);

            return ['success' => 1];
        } else {
            return ['success' => 2];
        }
    }

    // crear solicitud de partida
    public function crearSolicitudPartidaAdicional(Request $request)
    {

        $regla = array(
            'idproyecto' => 'required',
            'fecha' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $co = new PartidaAdicionalContenedor();
            $co->id_proyecto = $request->idproyecto;
            $co->fecha = $request->fecha;
            $co->documento = null;
            $co->estado = 0; // 0: en desarrollo, 1: listo para revisión, 2: aprobado
            $co->monto = 0;
            $co->save();

            DB::commit();
            return ['success' => 1];

        } catch (\Throwable $e) {
            DB::rollback();
            return ['success' => 99];
        }
    }

    // vista donde se crean ya las partidas adicionales
    public function indexCreacionPartidasAdicionales($id)
    {
        // ID CONTENEDOR PARTIDA

        $infoContenedor = PartidaAdicionalContenedor::where('id', $id)->first();

        $infoProyecto = Proyecto::where('id', $infoContenedor->id_proyecto)->first();
        $nombreProyecto = $infoProyecto->nombre;

        $fecha = date("d-m-Y", strtotime($infoContenedor->fecha));

        return view('backend.admin.proyectos.partidaadicional.partidas.vistapartidaadicional', compact('id', 'fecha', 'infoContenedor', 'nombreProyecto'));
    }


    // tabla donde se crean ya las partidas adicionales
    public function tablaCreacionPartidasAdicionales($id)
    {
        // ID CONTENEDOR PARTIDA

        $infoContenedor = PartidaAdicionalContenedor::where('id', $id)->first();

        $lista = PartidaAdicional::where('id_partidaadic_conte', $id)->get();

        foreach ($lista as $dd){

            $infoTipoPartida = TipoPartida::where('id', $dd->id_tipopartida)->first();

            $dd->tipopartida = $infoTipoPartida->nombre;
        }

        return view('backend.admin.proyectos.partidaadicional.partidas.tablapartidaadicional', compact('infoContenedor', 'lista'));
    }

    // borrar contenedor de partidas adicionales
    public function borrarContenedorPartidaAdicional(Request $request){

        $regla = array(
            'id' => 'required' // id contenedor
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $infoContenedor = PartidaAdicionalContenedor::where('id', $request->id)->first();

            // ya esta en modo revisión
            if($infoContenedor->estado == 1){
                return ['success' => 1];
            }

            // ya esta aprobado
            if($infoContenedor->estado == 2){
                return ['success' => 2];
            }

            // obtener dependencias
            $arrayPartida = PartidaAdicional::where('id_partidaadic_conte', $request->id)->get();

            $pilaPartidas = array();

            foreach ($arrayPartida as $p){
                array_push($pilaPartidas, $p->id);
            }

            // borrar detalles
            PartidaAdicionalDetalle::whereIn('id_partida_adicional', $pilaPartidas)->delete();
            // borrar partida adicional
            PartidaAdicional::whereIn('id', $pilaPartidas)->delete();
            // borrar contenedor
            PartidaAdicionalContenedor::where('id', $request->id)->delete();

            DB::commit();
            return ['success' => 3];

        } catch (\Throwable $e) {
            DB::rollback();
            return ['success' => 99];
        }
    }

    // descargar documento de obra adicional
    public function documentoObraAdicional($id){

        $url = PartidaAdicionalContenedor::where('id', $id)->pluck('documento')->first();

        $pathToFile = "storage/archivos/".$url;

        $extension = pathinfo(($pathToFile), PATHINFO_EXTENSION);

        $nombre = "Doc." . $extension;

        return response()->download($pathToFile, $nombre);
    }


}

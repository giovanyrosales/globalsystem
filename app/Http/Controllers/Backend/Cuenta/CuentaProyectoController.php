<?php

namespace App\Http\Controllers\Backend\Cuenta;

use App\Http\Controllers\Controller;
use App\Models\Bolson;
use App\Models\CatalogoMateriales;
use App\Models\Cuenta;
use App\Models\CuentaProy;
use App\Models\CuentaproyPartidaAdicional;
use App\Models\FuenteRecursos;
use App\Models\InformacionGeneral;
use App\Models\MoviCuentaProy;
use App\Models\ObjEspecifico;
use App\Models\Partida;
use App\Models\PartidaAdicional;
use App\Models\PartidaAdicionalContenedor;
use App\Models\PartidaAdicionalDetalle;
use App\Models\Planilla;
use App\Models\Proyecto;
use App\Models\TipoPartida;
use App\Models\UnidadMedida;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CuentaProyectoController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    // retorna vista con los movimientos de cuenta para un proyecto ID
    public function indexMoviCuentaProy($id){
        // ID: PROYECTO

        $infoProyecto = Proyecto::where('id', $id)->first();
        $permiso = $infoProyecto->permiso;

        return view('backend.admin.proyectos.cuentaproyecto.movimiento.vistamovicuentaproy', compact('id', 'permiso'));
    }

    // retorna vista con los historicos movimientos por proyecto ID
    public function indexMoviCuentaProyHistorico($id){
        // ID: PROYECTO
        return view('backend.admin.proyectos.cuentaproyecto.historico.vistamovicuentahistorico', compact('id'));
    }

    // retorna tabla con los historicos movimientos por proyecto ID
    public function tablaMoviCuentaProyHistorico($id){

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

    // retorna vista con las partidas adicionales de un x proyecto
    public function indexPartidaAdicionalContenedor($id)
    {
        // id PROYECTO

        $infoPro = Proyecto::where('id', $id)->first();

        return view('backend.admin.proyectos.partidaadicional.contenedor.vistacontenedorpartidaadicional', compact('id', 'infoPro'));
    }

    // retorna tabla con las partidas adicionales de un x proyecto
    public function tablaPartidaAdicionalContenedor($id)
    {
        // id PROYECTO

        $lista = PartidaAdicionalContenedor::where('id_proyecto', $id)
            ->orderBy('fecha', 'ASC')
            ->get();

        foreach ($lista as $dd) {
            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));

            if($dd->estado == 2){
                // aprobado
                $dd->montopartida = '$' . number_format((float)$dd->monto_aprobado, 2, '.', ',');
            }else{
                $dd->montopartida = "Pendiente de Aprobación";
            }
        }

        return view('backend.admin.proyectos.partidaadicional.contenedor.tablacontenedorpartidaadicional', compact('lista'));
    }

    // vista de partidas adicionales, otras opciones solo para jefatura
    public function indexPartidaAdicionalConteJefatura($id){
        // id PROYECTO

        $infoPro = Proyecto::where('id', $id)->first();

        return view('backend.admin.proyectos.partidaadicional.contenedor.vistacontepartidaadicionaljefatura', compact('id', 'infoPro'));
    }

    // retorna tabla con las partidas adicionales de un x proyecto para jefatura
    public function tablaPartidaAdicionalConteJefatura($id){

        // id PROYECTO

        $lista = PartidaAdicionalContenedor::where('id_proyecto', $id)
            ->orderBy('fecha', 'ASC')
            ->get();

        foreach ($lista as $dd) {
            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));

            $montoPartidaActual = 0;

            $arrayPartidaAdicional = PartidaAdicional::where('id_partidaadic_conte', $dd->id)->get();

            foreach ($arrayPartidaAdicional as $datainfo){

                $arrayPartidaDeta = PartidaAdicionalDetalle::where('id_partida_adicional', $datainfo->id)->get();

                foreach ($arrayPartidaDeta as $infoDD){

                    $infoMaterial = CatalogoMateriales::where('id', $infoDD->id_material)->first();

                    if($infoDD->duplicado > 0){
                        $multi = ($infoDD->cantidad * $infoMaterial->pu) * $infoDD->duplicado;
                    }else{
                        $multi = ($infoDD->cantidad * $infoMaterial->pu);
                    }

                    $montoPartidaActual += $multi;
                }
            }

            $dd->montopartidas = '$' . number_format((float)$montoPartidaActual, 2, '.', ',');
        }

        return view('backend.admin.proyectos.partidaadicional.contenedor.tablacontepartiadicjefatura', compact('lista'));
    }

    // información de porcentaje de obra adicional
    public function informacionPorcentajeObra(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if($info = Proyecto::where('id', $request->id)->first()){

            return ['success' => 1, 'porcentaje' => $info->porcentaje_obra];
        }else{
            return ['success' => 2];
        }
    }

    // actualizar porcentaje de obra adicional
    public function actualizarPorcentajeObra(Request $request){

        $regla = array(
            'id' => 'required',
            'porcentaje' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if (Proyecto::where('id', $request->id)->first()) {

            Proyecto::where('id', $request->id)->update([
                'porcentaje_obra' => $request->porcentaje
            ]);

            return ['success' => 1];
        } else {
            return ['success' => 2];
        }
    }

    // autorizar que se pueda crear partidas adicionales
    public function autorizarPartidaAdicionalPermiso(Request $request){
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
    public function crearSolicitudPartidaAdicional(Request $request){

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

            $infoGeneral = InformacionGeneral::where('id', 1)->first();

            $infoProyecto = Proyecto::where('id', $request->idproyecto)->first();

            $co = new PartidaAdicionalContenedor();
            $co->id_proyecto = $request->idproyecto;
            $co->fecha = $request->fecha;
            $co->documento = null;
            $co->estado = 0; // 0: en desarrollo, 1: listo para revisión, 2: aprobado
            $co->monto_aprobado = 0;
            $co->imprevisto = 0; // se modifica hasta que sea aprobado
            $co->imprevisto_herramienta = 0; // se modifica hasta que sea aprobado
            $co->fecha_aprobado = null;
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

        $tipospartida = TipoPartida::orderBy('nombre')->get();

        $conteoPartida = PartidaAdicional::where('id_partidaadic_conte', $id)->count();
        if($conteoPartida == 0){
            $conteoPartida = 1;
        }else{
            $conteoPartida += 1;
        }

        return view('backend.admin.proyectos.partidaadicional.partidas.vistapartidaadicional', compact('id', 'fecha',
            'infoContenedor', 'nombreProyecto', 'tipospartida', 'conteoPartida'));
    }


    // tabla donde se crean ya las partidas adicionales
    public function tablaCreacionPartidasAdicionales($id)
    {
        // ID CONTENEDOR PARTIDA

        $infoContenedor = PartidaAdicionalContenedor::where('id', $id)->first();


        $lista = PartidaAdicional::where('id_partidaadic_conte', $id)->get();
        $item = 0;

        foreach ($lista as $dd){

            $infoTipoPartida = TipoPartida::where('id', $dd->id_tipopartida)->first();
            $dd->tipopartida = $infoTipoPartida->nombre;
            $item += 1;
            $dd->item = $item;

            $montoPartidaActual = 0;

            $arrayPartidaDeta = PartidaAdicionalDetalle::where('id_partida_adicional', $dd->id)->get();

            foreach ($arrayPartidaDeta as $infoDD){

                $infoMaterial = CatalogoMateriales::where('id', $infoDD->id_material)->first();

                if($infoDD->duplicado > 0){
                    $multi = ($infoDD->cantidad * $infoMaterial->pu) * $infoDD->duplicado;
                }else{
                    $multi = ($infoDD->cantidad * $infoMaterial->pu);
                }

                $montoPartidaActual += $multi;
            }

            $dd->montopartidas = '$' . number_format((float)$montoPartidaActual, 2, '.', ',');
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

            // ya está aprobado
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

    // registrar partida adicional con su detalle, validando que no sobrepase el 20%
    // en este caso no sé válida que haya fondos en bolsón, sino cuando se aprueba todas las partidas adicionales
    public function registrarPartidaAdicional(Request $request){

        $rules = array(
            'nombrepartida' => 'required',
            'tipopartida' => 'required',
            'idcontenedor' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);
        if ( $validator->fails()){
            return ['success' => 0];
        }

        if($infop = PartidaAdicionalContenedor::where('id', $request->idcontenedor)->first()){
            //0: presupuesto en desarrollo
            //1: listo para revision
            //2: aprobado

            if ($infop->estado == 1){
                return ['success' => 1];
            }

            if ($infop->estado == 2){
                return ['success' => 2];
            }
        }

        DB::beginTransaction();

        try {

            $r = new PartidaAdicional();
            $r->id_partidaadic_conte = $request->idcontenedor;
            $r->nombre = $request->nombrepartida;
            $r->cantidadp = $request->cantidadpartida;
            $r->id_tipopartida = $request->tipopartida;
            $r->save();

            $conteoPartida = PartidaAdicional::where('id_partidaadic_conte', $request->idcontenedor)->count();
            if($conteoPartida == 0){
                $conteoPartida = 1;
            }else{
                $conteoPartida += 1;
            }

            // siempre habrá registros
            if($request->cantidad != null) {
                for ($i = 0; $i < count($request->cantidad); $i++) {
                    $rDetalle = new PartidaAdicionalDetalle();
                    $rDetalle->id_partida_adicional = $r->id;
                    $rDetalle->id_material = $request->datainfo[$i];
                    $rDetalle->cantidad = $request->cantidad[$i];
                    $rDetalle->estado = 0; // sin uso ahorita
                    $rDetalle->duplicado = $request->duplicado[$i];
                    $rDetalle->save();
                }
            }

            DB::commit();
            return ['success' => 3, 'contador' => $conteoPartida];

        }catch(\Throwable $e){
            Log::info('ee' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // borrar una partida adicional
    function borrarPartidaAdicional(Request $request){

        // ID partida_adicional
        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ( $validator->fails()){
            return ['success' => 0];
        }

        if($infoPartida = PartidaAdicional::where('id', $request->id)->first()){

            $infoContenedor = PartidaAdicionalContenedor::where('id', $infoPartida->id_partidaadic_conte)->first();

            // modo revision
            if($infoContenedor->estado == 1){
                return ['success' => 1];
            }

            // modo aprobado
            if($infoContenedor->estado == 2){
                return ['success' => 2];
            }

            PartidaAdicionalDetalle::where('id_partida_adicional', $infoPartida->id)->delete();
            PartidaAdicional::where('id', $request->id)->delete();

            return ['success' => 3];
        }else{
            return ['success' => 0];
        }
    }

    // obtiene información de la partida adicional de un proyecto
    function informacionPartidaAdicional(Request $request){
        // Id PARTIDA ADICIONAL

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){
            return ['success' => 0];
        }

        if($infoPartida = PartidaAdicional::where('id', $request->id)->first()){

            $infoContenedor = PartidaAdicionalContenedor::where('id', $infoPartida->id_partidaadic_conte)->first();
            $presuaprobado = $infoContenedor->estado;

            $detalle = PartidaAdicionalDetalle::where('id_partida_adicional', $request->id)
                ->orderBy('id', 'ASC')
                ->get();

            foreach ($detalle as $dd){

                $infoMaterial = CatalogoMateriales::where('id', $dd->id_material)->first();

                if($infoUnidad = UnidadMedida::where('id', $infoMaterial->id_unidadmedida)->first()){
                    $dd->descripcion = $infoMaterial->nombre . " - " . $infoUnidad->medida;
                }else{
                    $dd->descripcion = $infoMaterial->nombre;
                }
            }

            return ['success' => 1, 'info' => $infoPartida, 'detalle' => $detalle, 'estado' => $presuaprobado];
        }
        return ['success' => 2];
    }

    // editar la información de una partida adicional
    public function editarPresupuesto(Request $request){

        DB::beginTransaction();

        try {

            // idpartida   ID PARTIDA ADICIONAL


            if($infoPartida = PartidaAdicional::where('id', $request->idpartida)->first()) {

                $infoContenedor = PartidaAdicionalContenedor::where('id', $infoPartida->id_partidaadic_conte)->first();

                // Modo revision
                if ($infoContenedor->estado == 1) {
                    return ['success' => 1];
                }

                // presupuesto aprobado
                if ($infoContenedor->estado == 2) {
                    return ['success' => 2];
                }
            }

            // actualizar registros requisicion
            PartidaAdicional::where('id', $request->idpartida)->update([
                'cantidadp' => $request->cantidadpartida,
                'nombre' => $request->nombrepartida,
                'id_tipopartida' => $request->tipopartida
            ]);

            // agregar id a pila
            $pila = array();
            for ($i = 0; $i < count($request->idarray); $i++) {
                // Los id que sean 0, seran nuevos registros
                if($request->idarray[$i] != 0) {
                    array_push($pila, $request->idarray[$i]);
                }
            }

            // borrar todos los registros
            // primero obtener solo la lista de requisición obtenido de la fila
            // y no quiero que borre los que si vamos a actualizar con los ID
            PartidaAdicionalDetalle::where('id_partida_adicional', $request->idpartida)
                ->whereNotIn('id', $pila)
                ->delete();

            // actualizar registros
            for ($i = 0; $i < count($request->cantidad); $i++) {
                if($request->idarray[$i] != 0){
                    PartidaAdicionalDetalle::where('id', $request->idarray[$i])->update([
                        'cantidad' => $request->cantidad[$i],
                        'duplicado' => $request->duplicado[$i],
                    ]);
                }
            }

            // hoy registrar los nuevos registros
            for ($i = 0; $i < count($request->cantidad); $i++) {
                if($request->idarray[$i] == 0){
                    $rDetalle = new PartidaAdicionalDetalle();
                    $rDetalle->id_partida_adicional = $request->idpartida;
                    $rDetalle->cantidad = $request->cantidad[$i];
                    $rDetalle->id_material = $request->datainfo[$i];
                    $rDetalle->estado = 0;
                    $rDetalle->duplicado = $request->duplicado[$i];
                    $rDetalle->save();
                }
            }

            DB::commit();
            return ['success' => 3];

        }catch(\Throwable $e){
            DB::rollback();
            return ['success' => 99];
        }
    }

    // información de contenedor partida adicional
    public function informacionEstadoContenedorPartidaAdic(Request $request){
        // ID CONTENEDOR partida adicional
        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){
            return ['success' => 0];
        }

        if($infoPartida = PartidaAdicionalContenedor::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $infoPartida];
        }else{
            return ['success' => 2];
        }
    }

    // actualizar estado de contenedor partida adicional
    public function actualizarEstadoContenedorPartidaAdic(Request $request){

        // ID CONTENEDOR partida adicional
        $rules = array(
            'id' => 'required',
            'estado' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){
            return ['success' => 0];
        }

        if($infoPartida = PartidaAdicionalContenedor::where('id', $request->id)->first()){

            // ya esta aprobada
            if($infoPartida->estado == 2){
                return ['success' => 1];
            }

            $conteo = PartidaAdicional::where('id_partidaadic_conte', $infoPartida->id)->count();

            if($conteo <= 0){
                // es decir, no tiene partidas el contenedor, así que no se puede actualizar
                return ['success' => 2];
            }

            PartidaAdicionalContenedor::where('id', $infoPartida->id)->update([
                'estado' => $request->estado,
            ]);

            return ['success' => 3];
        }else{
            return ['success' => 99];
        }
    }

    // información del contenedor para jefatura
    public function infoContenedorJefatura(Request $request){
        // ID CONTENEDOR partida adicional
        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            if($infoPartidaConte = PartidaAdicionalContenedor::where('id', $request->id)->first()){

                if($infoPartidaConte->estado == 0){
                    // en modo desarrollo
                    return ['success' => 1];
                }

                if($infoPartidaConte->estado == 2){
                    // ya fue aprobada
                    return ['success' => 2];
                }

                $conteo = PartidaAdicional::where('id_partidaadic_conte', $infoPartidaConte->id)->count();

                if($conteo <= 0){
                    // es decir, no tiene partidas el contenedor, así que no se puede actualizar
                    return ['success' => 3];
                }

                $infoProyecto = Proyecto::where('id', $infoPartidaConte->id_proyecto)->first();

                if($infoProyecto->id_bolson == null){
                    return ['success' => 4];
                }

                $infoBolson = Bolson::where('id', $infoProyecto->id_bolson)->first();

                // obtener monto de partidas adicionales
                $montoFinalPartidaAdicional = $this->montoFinalPartidaAdicional($infoPartidaConte->id);


                // buscar restante saldo bolsón

                // proyectoMontoBolson: es el monto de las partidas aprobadas de todos los proyectos a bolson
                // partidaAdicionalMonto: es el monto de las partidas adicionales aprobadas
                // proyectoFinalizadoMonto: es el monto sobrante de un proyecto cuando se finaliza

                $proyectoMontoBolson = Proyecto::where('id_bolson', $infoBolson->id)->sum('monto');

                $partidaAdicionalMonto = PartidaAdicionalContenedor::where('id_proyecto', $infoProyecto->id)
                    ->where('estado', 2) // partidas adicionales aprobadas
                    ->sum('monto_aprobado');

                $proyectoFinalizadoMonto = Proyecto::where('id_bolson', $infoBolson->id)
                    ->where('id_estado', 4)
                    ->sum('monto_finalizado');

                $montoBolsonInicial = Bolson::where('id', $infoBolson->id)->sum('monto_inicial');

                $montoBolsonActual = $montoBolsonInicial - ($proyectoMontoBolson + $partidaAdicionalMonto + $proyectoFinalizadoMonto);

                $montoBolsonActual = "$" . number_format((float)$montoBolsonActual, 2, '.', ',');

                return ['success' => 5, 'info' => $infoPartidaConte, 'montopartida' => $montoFinalPartidaAdicional,
                    'nombolson' => $infoBolson->nombre, 'bolsonrestante' => $montoBolsonActual];

            }else{
                return ['success' => 99];
            }

         } catch (\Throwable $e) {
            Log::info('ee' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    // UTILIZADO SOLO PARA OBTENER TOTAL DE UN PRESUPUESTO DE  PARTIDA ADICIONAL.
    function montoFinalPartidaAdicional($id){

        // id es CONTENEDOR

        // 1- Materiales
        // 2- Mano de obra (Por Administración)
        // 3- Alquiler de Maquinaria
        // 4- Transporte de Concreto Fresco

        $partida1 = PartidaAdicional::where('id_partidaadic_conte', $id)
            ->whereIn('id_tipopartida', [1, 3, 4])
            ->orderBy('id', 'ASC')
            ->get();

        $sumaMateriales = 0;

        foreach ($partida1 as $secciones) {

            $detalle1 = PartidaAdicionalDetalle::where('id_partida_adicional', $secciones->id)->get();

            $total = 0;

            foreach ($detalle1 as $lista) {

                $infomaterial = CatalogoMateriales::where('id', $lista->id_material)->first();

                if ($lista->duplicado > 0) {
                    $multi = ($lista->cantidad * $infomaterial->pu) * $lista->duplicado;
                    $lista->material = $infomaterial->nombre . " (" . $lista->duplicado . ")";
                } else {
                    $multi = $lista->cantidad * $infomaterial->pu;
                    $lista->material = $infomaterial->nombre;
                }

                // se sumara solo materiales
                if($secciones->id_tipopartida == 1){
                    $sumaMateriales = $sumaMateriales + $multi;
                }

                $total = $total + $multi;
            }
        }

        // 2- MANO DE OBRA POR ADMINISTRACION

        $manoobra = PartidaAdicional::where('id_partidaadic_conte', $id)
            ->where('id_tipopartida', 2)
            ->orderBy('id', 'ASC')
            ->get();

        $totalManoObra = 0;

        foreach ($manoobra as $secciones3) {

            $detalle3 = PartidaAdicionalDetalle::where('id_partida_adicional', $secciones3->id)->get();

            $total3 = 0;

            foreach ($detalle3 as $lista) {

                $infomaterial = CatalogoMateriales::where('id', $lista->id_material)->first();

                if ($lista->duplicado != 0) {
                    $lista->material = $infomaterial->nombre . " (" . $lista->duplicado . ")";
                } else {
                    $lista->material = $infomaterial->nombre;
                }

                $multi = $lista->cantidad * $infomaterial->pu;
                if ($lista->duplicado > 0) {
                    $multi = $multi * $lista->duplicado;
                }

                $totalManoObra = $totalManoObra + $multi;
                $total3 = $total3 + $multi;
            }
        }


        // 3- ALQUILER DE MAQUINARIA

        $alquilerMaquinaria = PartidaAdicional::where('id_partidaadic_conte', $id)
            ->where('id_tipopartida', 3)
            ->get();

        $totalAlquilerMaquinaria = 0;

        foreach ($alquilerMaquinaria as $secciones3) {

            $detalle4 = PartidaAdicionalDetalle::where('id_partida_adicional', $secciones3->id)->get();

            foreach ($detalle4 as $lista) {

                $infomaterial = CatalogoMateriales::where('id', $lista->id_material)->first();
                $lista->material = $infomaterial->nombre;
                $multi = $lista->cantidad * $infomaterial->pu;
                if ($lista->duplicado > 0) {
                    $multi = $multi * $lista->duplicado;
                }

                $totalAlquilerMaquinaria += $multi;
            }
        }

        // 4- TRANSPORTE CONCRETO FRESCO

        $trasportePesado = PartidaAdicional::where('id_partidaadic_conte', $id)
            ->where('id_tipopartida', 4)
            ->get();

        $totalTransportePesado = 0;

        foreach ($trasportePesado as $secciones3) {

            $detalle4 = PartidaAdicionalDetalle::where('id_partida_adicional', $secciones3->id)->get();

            foreach ($detalle4 as $lista) {

                $infomaterial = CatalogoMateriales::where('id', $lista->id_material)->first();
                $lista->material = $infomaterial->nombre;
                $multi = $lista->cantidad * $infomaterial->pu;
                if ($lista->duplicado > 0) {
                    $multi = $multi * $lista->duplicado;
                }

                $totalTransportePesado += $multi;
            }
        }

        $afp = ($totalManoObra * 7.75) / 100;
        $isss = ($totalManoObra * 7.5) / 100;
        $insaforp = ($totalManoObra * 1) / 100;

        $informacionGeneral = InformacionGeneral::where('id', 1)->first();

        // obtener porcentaje actual. Ya que aun no esta aprobada paritda adicional, se obtiene de
        // los modificables porcentajes

        $totalDescuento = ($afp + $isss + $insaforp);
        $herramientaXPorciento = ($sumaMateriales * $informacionGeneral->porcentaje_herramienta) / 100;

        // subtotal del presupuesto partida
        $subtotalPartida = ($sumaMateriales + $herramientaXPorciento + $totalManoObra + $totalDescuento
            + $totalAlquilerMaquinaria + $totalTransportePesado);

        // imprevisto obtenido del proyecto, se obtiene de los modificables porcentajes
        $imprevisto = ($subtotalPartida * $informacionGeneral->imprevisto_modificable) / 100;

        // total de la partida final
        $totalPartidaFinal = $subtotalPartida + $imprevisto;

        // total de la partida final
        return ($this->redondear_dos_decimal($totalPartidaFinal));
    }

    // aprobar una partida adicional
    public function aprobarPartidaAdicional(Request $request){

        // se debe verificar el porcentaje maximo de obra adicional
        // que haya dinero en bolsón
        // asignar dinero a cuenta proy y diferenciar cual es de obra adicional

        // $request->idcontenedor

        DB::beginTransaction();

        try {

            $infoContenedor = PartidaAdicionalContenedor::where('id', $request->idcontenedor)->first();
            $infoProyecto = Proyecto::where('id', $infoContenedor->id_proyecto)->first();

            // verificar que proyecto no este finalizado
            if($infoProyecto->id_estado == 4){
                return ['success' => 1];
            }

            //************
            // ESTE ES EL DINERO DE LA PARTIDA ADICIONAL QUE QUIERO APROBAR

            // obtener monto de partidas adicionales
            $montoFinalPartidaAdicional = $this->montoFinalPartidaAdicional($request->idcontenedor);

            //************

            // // OBTENER DINERO DE TODAS LAS PARTIDAS ADICIONALES APROBADAS PARA NO SUPERAR EL X PORCIENTO DE OBRA

            $totalAprobadasConte = PartidaAdicionalContenedor::where('id_proyecto', $infoProyecto->id)
                ->where('estado', 2) // solo aprobadados
                ->sum('monto_aprobado');

            // ESTO ES EL MONTO MÁXIMO PORCENTAJE OBRA
            $montoMaximoObra = ($infoProyecto->monto * $infoProyecto->porcentaje_obra) / 100;

            // COMPROBACIÓN

            // se suma la partida adicionales actual a aprobar + todas las partidas aprobadas
            $sumatoria = $montoFinalPartidaAdicional + $totalAprobadasConte;

            if($this->redondear_dos_decimal($sumatoria) <= $montoMaximoObra){

                // PASA, PERO VERIFICAR QUE HAYA DINERO EN BOLSÓN

                $montoBolsonInicial = Bolson::where('id', $infoProyecto->id_bolson)->sum('monto_inicial');

                $proyectoMontoBolson = Proyecto::where('id_bolson', $request->idbolson)->sum('monto');

                $partidaAdicionalMonto = PartidaAdicionalContenedor::where('id_proyecto', $request->id)
                    ->where('estado', 2) // partidas aprobadas
                    ->sum('monto_aprobado');

                $proyectoFinalizadoMonto = Proyecto::where('id_bolson', $request->idbolson)
                    ->where('id_estado', 4)
                    ->sum('monto_finalizado');

                // restar a monto inicial y después sumarle
                $restaBolsonInicial = $montoBolsonInicial - ($proyectoMontoBolson + $partidaAdicionalMonto);

                // BOLSÓN ACTUAL LO QUE HAY $
                $restaBolsonInicial += $proyectoFinalizadoMonto;

                // SI BOLSÓN ES MENOR A LO QUE SE SOLICITA DE LA PARTIDA ADICIONAL
                if($this->redondear_dos_decimal($restaBolsonInicial) < $this->redondear_dos_decimal($montoFinalPartidaAdicional)){

                    $restaBolsonInicial = "$" . number_format((float)$restaBolsonInicial, 2, '.', ',');
                    $montoFinalPartidaAdicional = "$" . number_format((float)$montoFinalPartidaAdicional, 2, '.', ',');

                    return ['success' => 2, 'quedabolson' => $restaBolsonInicial, 'solicita' => $montoFinalPartidaAdicional];
                }


                //******* PUEDE GUARDARSE

                $informacionGeneral = InformacionGeneral::where('id', 1)->first();


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








                        //DB::commit();
                        return ['success' => 3];
                    }else{
                        return ['success' => 99];
                    }
                }else {
                    // actualizar estado
                    PartidaAdicionalContenedor::where('id', $request->idcontenedor)->update([
                        'fecha_aprobado' => Carbon::now('America/El_Salvador'),
                        'estado' => 2, // aprobado
                        'imprevisto' => $informacionGeneral->imprevisto_modificable,
                        'imprevisto_herramienta' => $informacionGeneral->porcentaje_herramienta,
                        'monto_aprobado' => $montoFinalPartidaAdicional
                    ]);

                    //DB::commit();
                    return ['success' => 3];
                }
            }else{
                // no puede guardarse, porque monto supera al máximo de porcentaje

                $porcentajeObra = $infoProyecto->porcentaje_obra;
                $montoMaximoObra = number_format((float)$montoMaximoObra, 2, '.', ',');

                $resta = $montoMaximoObra - $sumatoria; // se excede por esto

                $resta = number_format((float)$resta, 2, '.', ',');

                return ['success' => 4, 'porcentaje' => $porcentajeObra, 'restado' => $resta,
                    'montomaximo' => $montoMaximoObra];
            }

        } catch (\Throwable $e) {
            Log::info('ee' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // comprobar que haya partidas adicionales
    public function verificarSiHayPartidas(Request $request){

        // id PARTIDA CONTENEDOR

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){
            return ['success' => 0];
        }

        $conteo = PartidaAdicional::where('id_partidaadic_conte', $request->id)->count();

        if($conteo > 0){

            // si hay partidas adicionales, asi que generar pdf
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // generar un documento PDF para partida adicional
    public function generarPdfPartidaAdicional($id){
        // id contenedor

        // 1- Materiales
        // 2- Mano de obra (Por Administración)
        // 3- Alquiler de Maquinaria
        // 4- Transporte de Concreto Fresco

        $infoContenedor = PartidaAdicionalContenedor::where('id', $id)->first();

        $partida1 = PartidaAdicional::where('id_partidaadic_conte', $id)
            ->whereIn('id_tipopartida', [1, 3, 4])
            ->orderBy('id', 'ASC')
            ->get();

        $infoPro = Proyecto::where('id', $infoContenedor->id_proyecto)->first();

        if ($infoFuenteR = FuenteRecursos::where('id', $infoPro->id_fuenter)->first()) {
            $fuenter = $infoFuenteR->nombre;
        } else {
            $fuenter = "";
        }

        $resultsBloque = array();
        $index = 0;
        $resultsBloque3 = array();
        $index3 = 0;

        // Fechas
        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $fecha = Carbon::parse(Carbon::now());
        $anio = Carbon::now()->format('Y');
        $mes = $meses[($fecha->format('n')) - 1] . " del " . $anio;

        $item = 0;
        $sumaMateriales = 0;

        foreach ($partida1 as $secciones) {
            array_push($resultsBloque, $secciones);
            $item = $item + 1;
            $secciones->item = $item;

            $detalle1 = PartidaAdicionalDetalle::where('id_partida_adicional', $secciones->id)->get();

            $total = 0;

            foreach ($detalle1 as $lista) {

                $infomaterial = CatalogoMateriales::where('id', $lista->id_material)->first();

                $lista->objespecifico = $infomaterial->id_objespecifico;

                $medida = '';
                if($infomedida = UnidadMedida::where('id', $infomaterial->id_unidadmedida)->first()){
                    $medida = $infomedida->medida;
                }

                $lista->medida = $medida;

                if ($lista->duplicado > 0) {
                    $multi = ($lista->cantidad * $infomaterial->pu) * $lista->duplicado;
                    $lista->material = $infomaterial->nombre . " (" . $lista->duplicado . ")";
                } else {
                    $multi = $lista->cantidad * $infomaterial->pu;
                    $lista->material = $infomaterial->nombre;
                }

                $lista->cantidad = number_format((float)$lista->cantidad, 2, '.', ',');
                $lista->pu = "$" . number_format((float)$infomaterial->pu, 2, '.', ',');
                $lista->subtotal = "$" . number_format((float)$multi, 2, '.', ',');

                // se sumara solo materiales
                if($secciones->id_tipopartida == 1){
                    $sumaMateriales = $sumaMateriales + $multi;
                }

                $total = $total + $multi;
            }

            $secciones->total = "$" . number_format((float)$total, 2, '.', ',');

            $resultsBloque[$index]->bloque1 = $detalle1;
            $index++;
        }

        // 2- MANO DE OBRA POR ADMINISTRACION

        $manoobra = PartidaAdicional::where('id_partidaadic_conte', $id)
            ->where('id_tipopartida', 2)
            ->orderBy('id', 'ASC')
            ->get();

        $totalManoObra = 0;

        foreach ($manoobra as $secciones3) {
            array_push($resultsBloque3, $secciones3);
            $item = $item + 1;
            $secciones3->item = $item;

            $detalle3 = PartidaAdicionalDetalle::where('id_partida_adicional', $secciones3->id)->get();

            $total3 = 0;

            foreach ($detalle3 as $lista) {

                $infomaterial = CatalogoMateriales::where('id', $lista->id_material)->first();

                $medida = '';
                if($infomedida = UnidadMedida::where('id', $infomaterial->id_unidadmedida)->first()){
                    $medida = $infomedida->medida;
                }

                $lista->medida = $medida;

                if ($lista->duplicado != 0) {
                    $lista->material = $infomaterial->nombre . " (" . $lista->duplicado . ")";
                } else {
                    $lista->material = $infomaterial->nombre;
                }

                $multi = $lista->cantidad * $infomaterial->pu;
                if ($lista->duplicado > 0) {
                    $multi = $multi * $lista->duplicado;
                }

                $lista->cantidad = number_format((float)$lista->cantidad, 2, '.', ',');
                $lista->pu = "$" . number_format((float)$infomaterial->pu, 2, '.', ',');
                $lista->subtotal = "$" . number_format((float)$multi, 2, '.', ',');

                $totalManoObra = $totalManoObra + $multi;
                $total3 = $total3 + $multi;
            }

            $secciones3->total = "$" . number_format((float)$total3, 2, '.', ',');

            $resultsBloque3[$index3]->bloque3 = $detalle3;
            $index3++;
        }

        // 3- ALQUILER DE MAQUINARIA

        $alquilerMaquinaria = PartidaAdicional::where('id_partidaadic_conte', $id)
            ->where('id_tipopartida', 3)
            ->get();

        $totalAlquilerMaquinaria = 0;

        foreach ($alquilerMaquinaria as $secciones3) {

            $detalle4 = PartidaAdicionalDetalle::where('id_partida_adicional', $secciones3->id)->get();

            foreach ($detalle4 as $lista) {

                $infomaterial = CatalogoMateriales::where('id', $lista->id_material)->first();
                $lista->material = $infomaterial->nombre;

                $multi = $lista->cantidad * $infomaterial->pu;
                if ($lista->duplicado > 0) {
                    $multi = $multi * $lista->duplicado;
                }

                $totalAlquilerMaquinaria += $multi;
            }
        }

        // 4- TRANSPORTE CONCRETO FRESCO

        $trasportePesado = PartidaAdicional::where('id_partidaadic_conte', $id)
            ->where('id_tipopartida', 4)
            ->get();

        $totalTransportePesado = 0;

        foreach ($trasportePesado as $secciones3) {

            $detalle4 = PartidaAdicionalDetalle::where('id_partida_adicional', $secciones3->id)->get();

            foreach ($detalle4 as $lista) {

                $infomaterial = CatalogoMateriales::where('id', $lista->id_material)->first();
                $lista->material = $infomaterial->nombre;

                $multi = $lista->cantidad * $infomaterial->pu;
                if ($lista->duplicado > 0) {
                    $multi = $multi * $lista->duplicado;
                }

                $totalTransportePesado += $multi;
            }
        }

        $afp = ($totalManoObra * 7.75) / 100;
        $isss = ($totalManoObra * 7.5) / 100;
        $insaforp = ($totalManoObra * 1) / 100;

        $informacionGeneral = InformacionGeneral::where('id', 1)->first();

        // obtener porcentaje actual
        if($infoContenedor->estado == 2){
            $porcientoHerramienta = $infoContenedor->imprevisto_herramienta;
        }else{
            $porcientoHerramienta = $informacionGeneral->porcentaje_herramienta;
        }

        $totalDescuento = ($afp + $isss + $insaforp);
        $herramientaXPorciento = ($sumaMateriales * $porcientoHerramienta) / 100;

        // subtotal del presupuesto partida
        $subtotalPartida = ($sumaMateriales + $herramientaXPorciento + $totalManoObra + $totalDescuento
            + $totalAlquilerMaquinaria + $totalTransportePesado);

        // obtener el imprevisto actual
        if($infoContenedor->estado == 2){
            $imprevistoActual = $infoContenedor->imprevisto;
        }else{
            $imprevistoActual = $informacionGeneral->imprevisto_modificable;
        }


        // imprevisto obtenido del proyecto
        $imprevisto = ($subtotalPartida * $imprevistoActual) / 100;

        // total de la partida final
        $totalPartidaFinal = $subtotalPartida + $imprevisto;

        $totalDescuento = "$" . number_format((float)$totalDescuento, 2, '.', ',');
        $afp = "$" . number_format((float)$afp, 2, '.', ',');
        $isss = "$" . number_format((float)$isss, 2, '.', ',');
        $insaforp = "$" . number_format((float)$insaforp, 2, '.', ',');
        $sumaMateriales = "$" . number_format((float)$sumaMateriales, 2, '.', ',');
        $herramientaXPorciento = "$" . number_format((float)$herramientaXPorciento, 2, '.', ',');
        $totalManoObra = "$" . number_format((float)$totalManoObra, 2, '.', ',');

        $totalAlquilerMaquinaria = "$" . number_format((float)$totalAlquilerMaquinaria, 2, '.', ',');
        $totalTransportePesado = "$" . number_format((float)$totalTransportePesado, 2, '.', ',');
        $subtotalPartida = "$" . number_format((float)$subtotalPartida, 2, '.', ',');
        $imprevisto = "$" . number_format((float)$imprevisto, 2, '.', ',');
        $totalPartidaFinal = "$" . number_format((float)$totalPartidaFinal, 2, '.', ',');

        $mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf->SetTitle('Presupuesto -' . $mes);

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/logo2.png';

        $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            Fondo: $fuenter <br>
            Hoja de presupuesto Partida Adicional<br>
            Fecha: $mes <br></p>
            </div>";

        $tabla .= "<table width='100%' id='tablaFor'>
            <tbody>";

        foreach ($partida1 as $dd) {

            if ($partida1->last() == $dd) {
                $tabla .= "<tr>
                    <td width='100%' colspan='6'></td>
                    </tr>>";
            }

            $tabla .= "<tr>
                    <td colspan='1' width='10%' style='font-weight: bold'>Item</td>
                    <td colspan='3' width='30%' style='font-weight: bold'>Partida</td>
                    <td colspan='2' width='20%' style='font-weight: bold'>Cantidad P.</td>
                </tr>

                <tr>
                    <td colspan='1' width='10%'>$dd->item</td>
                    <td colspan='3' width='30%'>$dd->nombre</td>
                    <td colspan='2' width='20%'>$dd->cantidadp</td>
                </tr>

                <tr>
                    <td width='25%' style='font-weight: bold'>Material</td>
                    <td width='11%' style='font-weight: bold'>U/M</td>
                    <td width='12%' style='font-weight: bold'>Cantidad</td>
                    <td width='10%' style='font-weight: bold'>P.U</td>
                    <td width='12%' style='font-weight: bold'>Sub Total</td>
                    <td width='20%' style='font-weight: bold'>Total</td>
                </tr>
                ";

            foreach ($dd->bloque1 as $gg) {

                $tabla .= "<tr>
                    <td width='25%'>$gg->material</td>
                    <td width='10%'>$gg->medida</td>
                    <td width='10%'>$gg->cantidad</td>
                    <td width='10%'>$gg->pu</td>
                    <td width='12%'>$gg->subtotal</td>
                    <td width='20%'></td>
                </tr>";

                if ($dd->bloque1->last() == $gg) {
                    $tabla .= "
                        <tr>
                            <td width='25%'></td>
                            <td width='10%'></td>
                            <td width='10%'></td>
                            <td width='10%'></td>
                            <td width='10%'></td>
                            <td width='20%' style='font-weight: bold; size: 15px'>$dd->total</td>
                        </tr>";
                }
            }
        }

        $tabla .= "</tbody></table>";


        $tabla .= "<table id='tablaFor' style='width: 100%'><tbody>";

        $vuelta = false;

        foreach ($manoobra as $dd) {

            if ($vuelta) {
                $tabla .= "<tr>
                    <td width = '100%' colspan='6'></td>
                </tr>";
            }

            $vuelta = true;

            $tabla .= "<tr>
                <td colspan='6' style='font-weight: bold'>MANO DE OBRA POR ADMINISTRACIÓN</td>
            </tr>

            <tr>
                <td colspan='1' width='10%'>Item</td>
                <td colspan='3' width='30%'>Partida</td>
                <td colspan='2' width='20%'>Cantidad P.</td>
            </tr>

            <tr>
                <td colspan='1' width='10%'>$dd->item</td>
                <td colspan='3' width='30%'>$dd->nombre</td>
                <td colspan='2' width='20%'>$dd->cantidadp</td>
            </tr>

            <tr>
                <td width='25%'>Material</td>
                <td width='11'>U/M</td>
                <td width='12%'>Cantidad</td>
                <td width='10%'>P.U</td>
                <td width='12%'>Sub Total</td>
                <td width='20%'>Total</td>
            </tr>
            ";

            foreach ($dd->bloque3 as $gg) {

                $tabla .= "
                <tr>
                    <td width='25%'>$gg->material</td>
                    <td width='10%'>$gg->medida</td>
                    <td width='10%'>$gg->cantidad</td>
                    <td width='10%'>$gg->pu</td>
                    <td width='12%'>$gg->subtotal</td>
                    <td width='20%'></td>
                </tr>
                ";

                if ($dd->bloque3->last() == $gg) {

                    $tabla .= "<tr>
                        <td width='25%'></td>
                        <td width='10%'></td>
                        <td width='10%'></td>
                        <td width='10%'></td>
                        <td width='10%'></td>
                        <td width='20%' style='font-weight: bold'>$dd->total</td>
                    </tr>";
                }
            }
        }

        $tabla .="</tbody>
            </table>
            <br>
            <br>";

        $tabla .= "<table id='tablaFor' style='width: 100%'><tbody>";

        $tabla .= "
        <tr>
            <td colspan='3' style='font-weight: bold'>APORTE PATRONAL</td>
        </tr>

        <tr>
            <td width='20%' style='font-weight: bold'>Descripción</td>
            <td width='12%' style='font-weight: bold'>Sub Total</td>
            <td width='20%' style='font-weight: bold'>Total</td>
        </tr>

        <tr>
            <td width='20%'>ISSS (7.5% mano de obra)</td>
            <td width='12%'>$isss</td>
            <td width='20%'></td>
        </tr>
        <tr>
            <td width='20%'>AFP (7.75% mano de obra)</td>
            <td width='12%'>$afp</td>
            <td width='20%'></td>
        </tr>
        <tr>
            <td width='20%'>INSAFOR (1.0% mano de obra)</td>
            <td width='12%'>$insaforp</td>
            <td width='20%'></td>
        </tr>

        <tr>
            <td width='20%'></td>
            <td width='12%'></td>
            <td width='20%'><strong>$totalDescuento</strong></td>
        </tr>
    </tbody>
</table>";


        $tabla2 = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            Fondo: " . $fuenter . " <br>
            Hoja de presupuesto Partida Adicional<br>
            Fecha: " . $mes . " <br></p>
            </div>";

        $tabla2 .= "<table style='width: 75%; margin: 0 auto' id='tablaFor'>
            <tbody>

             <tr>
        <td colspan='2'>RESUMEN DE PARTIDA</td>
    </tr>

    <tr>
        <td width='20%'>MATERIALES</td>
        <td width='12%'>$sumaMateriales</td>
    </tr>

    <tr>
        <td width='20%'>HERRAMIENTA ($porcientoHerramienta% DE MAT.)</td>
        <td width='12%'>$herramientaXPorciento</td>
    </tr>

    <tr>
        <td width='20%'>ALQUILER DE MAQUINARIA</td>
        <td width='12%'>$totalAlquilerMaquinaria</td>
    </tr>

    <tr>
        <td width='20%'>MANO DE OBRA (POR ADMINISTRACIÓN)</td>
        <td width='12%'>$totalManoObra</td>
    </tr>

     <tr>
        <td width='20%'>APORTE MANO DE OBRA (PATRONAL)</td>
        <td width='12%'>$totalDescuento</td>
    </tr>

     <tr>
        <td width='20%'>TRANSPORTE DE CONCRETO FRESCO</td>
        <td width='12%'>$totalTransportePesado</td>
    </tr>

    <tr>
        <td width='20%' style='font-weight: bold'>SUB TOTAL</td>
        <td width='12%' style='font-weight: bold'>$subtotalPartida</td>
    </tr>

    <tr>
        <td width='20%' style='font-weight: bold'>IMPREVISTOS ($imprevistoActual% de sub total)</td>
        <td width='12%' style='font-weight: bold'>$imprevisto</td>
    </tr>

    <tr>
        <td width='20%' style='font-weight: bold'>TOTAL</td>
        <td width='12%' style='font-weight: bold'>$totalPartidaFinal</td>
    </tr>
    </tbody>
</table> ";


        $stylesheet = file_get_contents('css/csspresupuesto.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla,2);
        $mpdf->AddPage();
        $mpdf->WriteHTML($tabla2,2);

        $mpdf->Output();

    }



}

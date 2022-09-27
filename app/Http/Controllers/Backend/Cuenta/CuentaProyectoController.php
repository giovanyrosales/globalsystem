<?php

namespace App\Http\Controllers\Backend\Cuenta;

use App\Http\Controllers\Controller;
use App\Models\Cuenta;
use App\Models\CuentaProy;
use App\Models\MoviCuentaProy;
use App\Models\ObjEspecifico;
use App\Models\Planilla;
use App\Models\Proyecto;
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

    public function indexCuenta($id){
        $infoProyecto = Proyecto::where('id', $id)->first();
        $nombre = $infoProyecto->codigo . " - " . $infoProyecto->nombre;

        return view('Backend.Admin.CuentaProyecto.vistaCuentaProyecto', compact('id', 'nombre'));
    }

    public function tablaCuenta($id){

        $cuenta = CuentaProy::where('proyecto_id', $id)->orderBy('id', 'DESC')->get();

        foreach ($cuenta as $dd){

            $infoCuenta = Cuenta::where('id', $dd->cuenta_id)->first();
            $dd->cuenta = $infoCuenta->nombre . " - " . $infoCuenta->codigo;

            $dd->montoini = number_format((float)$dd->montoini, 2, '.', ',');
            $dd->saldo = number_format((float)$dd->saldo, 2, '.', ',');
        }

        return view('Backend.Admin.CuentaProyecto.tablaCuentaProyecto', compact('cuenta'));
    }

    public function nuevaCuentaProy(Request $request){

        $rules = array(
            'proyecto' => 'required',
            'cuenta' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        $or = new CuentaProy();
        $or->proyecto_id = $request->proyecto;
        $or->cuenta_id = $request->cuenta;
        $or->montoini = $request->monto;
        $or->saldo = $request->saldo;
        $or->estado = 1;

        if($or->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


    public function informacionCuentaProy(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = CuentaProy::where('id', $request->id)->first()){

            $infoProyecto = Proyecto::orderBy('nombre')->get();
            $infocuenta = Cuenta::orderBy('nombre')->get();

            return ['success' => 1, 'info' => $lista, 'proyecto' => $infoProyecto,
                'idproyecto' => $lista->proyecto_id, 'cuenta' => $infocuenta,
                'idcuenta' => $lista->cuenta_id];
        }else{
            return ['success' => 2];
        }
    }

    public function editarCuentaProy(Request $request){

        $rules = array(
            'id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return ['success' => 0];
        }

        if(CuentaProy::where('id', $request->id)->first()){

            CuentaProy::where('id', $request->id)->update([
                'proyecto_id' => $request->proyecto,
                'cuenta_id' => $request->cuenta,
                'montoini' => $request->montoini,
                'saldo' => $request->saldo,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


    public function indexMoviCuentaProy($id){
        // ID: PROYECTO
        return view('backend.admin.proyectos.cuentaproyecto.movimiento.vistamovicuentaproy', compact('id'));
    }

    // VER HISTORICOS
    public function indexMoviCuentaProyHistorico($id){
        // ID: PROYECTO
        return view('backend.admin.proyectos.cuentaproyecto.historico.vistamovicuentahistorico', compact('id'));
    }

    public function tablaMoviCuentaProyHistorico($id){

        // ID PROYECTO
        $pila = array();
        $listado = CuentaProy::where('proyecto_id', $id)->get();

        foreach ($listado as $ll){
            array_push($pila, $ll->id);
        }

        $infoMovimiento = MoviCuentaProy::whereIn('id_cuentaproy', $pila)
            ->orderBy('fecha', 'ASC')
            ->get();

        foreach ($infoMovimiento as $dd){

            $infoCuentaProy = CuentaProy::where('id', $dd->id_cuentaproy)->first();
            $infoObjeto = ObjEspecifico::where('id', $infoCuentaProy->objespeci_id)->first();

            $dd->codigo = $infoObjeto->codigo;
            $dd->cuenta = $infoObjeto->nombre;

            $dd->aumento = number_format((float)$dd->aumento, 2, '.', ',');
            $dd->disminuye = number_format((float)$dd->disminuye, 2, '.', ',');

            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));
        }

        return view('backend.admin.proyectos.cuentaproyecto.historico.tablamovicuentahistorico', compact('infoMovimiento'));
    }



    public function indexTablaMoviCuentaProy($id){

        // ID PROYECTO

        $presupuesto = DB::table('cuentaproy AS p')
            ->join('obj_especifico AS obj', 'p.objespeci_id', '=', 'obj.id')
            ->select('obj.nombre', 'obj.id AS idcodigo', 'obj.codigo', 'p.id', 'p.saldo_inicial')
            ->where('p.proyecto_id', $id)
            ->get();

        foreach ($presupuesto as $pp){

            $totalSalida = 0;
            $totalEntrada = 0;
            $totalRetenido = 0;

            // SUMA Y RESTA DE MOVIMIENTO DE CÓDIGOS
            // suma de saldo
            $moviSumaSaldo = MoviCuentaProy::where('id_cuentaproy', $pp->id)
                ->sum('aumento');

            $moviRestaSaldo = MoviCuentaProy::where('id_cuentaproy', $pp->id)
                ->sum('disminuye');

            $totalMoviCuenta = $moviSumaSaldo - $moviRestaSaldo;

            // POR AQUI SE VALIDARA SI NO FUE CANSELADO LA ORDEN DE COMPRA, YA QUE AHI SE CREA EL PRESU_DETALLE.
            // Y SI ES CANCELADO SE CAMBIA UN ESTADO Y DEJAR DE SER VALIDO PARA VERIFICAR
            $infoSalidaDetalle = DB::table('cuentaproy_detalle AS pd')
                ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('pd.id_cuentaproy', $pp->id)
                ->where('pd.tipo', 0) // salidas. y la orden es valido
                ->where('rd.cancelado', 0)
                //->where('pd.estado', 0)// ES VALIDO, Y NO ESTA CANCELADO LA ORDEN DE COMPRA
                ->get();

            foreach ($infoSalidaDetalle as $dd){
                $totalSalida = $totalSalida + ($dd->cantidad * $dd->dinero);
            }

            $infoEntradaDetalle = DB::table('cuentaproy_detalle AS pd')
                ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('pd.id_cuentaproy', $pp->id)
                ->where('pd.tipo', 1) // entradas. // la orden fue cancelada
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($infoEntradaDetalle as $dd){
                $totalEntrada = $totalEntrada + ($dd->cantidad * $dd->dinero);
            }

            // SALDOS RETENIDOS

            $infoSaldoRetenido = DB::table('cuentaproy_retenido AS psr')
                ->join('requisicion_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('psr.id_cuentaproy', $pp->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($infoSaldoRetenido as $dd){
                $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
            }

            // SUMAR LOS MOVIMIENTOS DE CUENTA
            $totalRestante = $totalMoviCuenta;
            $totalRestante += $pp->saldo_inicial - ($totalSalida - $totalEntrada);

            $pp->saldo_inicial = number_format((float)$pp->saldo_inicial, 2, '.', ',');
            $pp->saldo_restante = number_format((float)$totalRestante, 2, '.', ',');
            $pp->total_retenido = number_format((float)$totalRetenido, 2, '.', ',');
        }

        return view('backend.admin.proyectos.cuentaproyecto.movimiento.tablamovicuentaproy', compact('presupuesto'));
    }

    public function nuevaMoviCuentaProy(Request $request){

        DB::beginTransaction();

        try {

            // VERIFICAR MIS SALDOS RESTANTE Y VERIFICAR QUE NO QUEDE MENOR A 0

            $infoSaldo2 = CuentaProy::where('id', $request->selectcuenta)->first(); // y este va a disminuir
            $infoObjeto2 = ObjEspecifico::where('id', $infoSaldo2->objespeci_id)->first();
            $unidoBloque2 = $infoObjeto2->codigo . " - " . $infoObjeto2->nombre;

            $totalSalida2 = 0;
            $totalEntrada2 = 0;
            $totalRetenido2 = 0;

            $moviSumaSaldo2 = MoviCuentaProy::where('id_cuentaproy', $infoSaldo2->id)
                ->sum('aumento');

            $moviRestaSaldo2 = MoviCuentaProy::where('id_cuentaproy', $infoSaldo2->id)
                ->sum('disminuye');

            $totalMoviCuenta2 = $moviSumaSaldo2 - $moviRestaSaldo2;

            // POR AQUI SE VALIDARA SI NO FUE CANCELADO LA ORDEN DE COMPRA, YA QUE AHI SE CREA EL PRESU_DETALLE.
            // Y SI ES CANCELADO SE CAMBIA UN ESTADO Y DEJAR DE SER VALIDO PARA VERIFICAR

            $infoSalidaDetalle2 = DB::table('cuentaproy_detalle AS pd')
                ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('pd.id_cuentaproy', $infoSaldo2->id)
                ->where('pd.tipo', 0) // salidas. la orden es valida
                ->where('rd.cancelado', 0)
                //->where('pd.estado', 0)// ES VALIDO, Y NO ESTA CANCELADO LA ORDEN DE COMPRA
                ->get();

            foreach ($infoSalidaDetalle2 as $dd){
                $totalSalida2 = $totalSalida2 + ($dd->cantidad * $dd->dinero);
            }

            $infoEntradaDetalle2 = DB::table('cuentaproy_detalle AS pd')
                ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('pd.id_cuentaproy', $infoSaldo2->id)
                ->where('pd.tipo', 1) // entradas. la orden fue cancelada
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($infoEntradaDetalle2 as $dd){
                $totalEntrada2 = $totalEntrada2 + ($dd->cantidad * $dd->dinero);
            }

            // SALDOS RETENIDOS

            $infoSaldoRetenido2 = DB::table('cuentaproy_retenido AS psr')
                ->join('requisicion_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('psr.id_cuentaproy', $infoSaldo2->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($infoSaldoRetenido2 as $dd){
                $totalRetenido2 = $totalRetenido2 + ($dd->cantidad * $dd->dinero);
            }

            // total de los cambios movimientos que se han hecho.
            $totalRestanteSaldo2 = $totalMoviCuenta2; // 0

            // saldo restante
            $totalRestanteSaldo2 += $infoSaldo2->saldo_inicial - ($totalSalida2 - $totalEntrada2);

            // VALIDACIONES.
            // EL BLOQUE 1 SIEMPRE SERA UNA SUMA. ASI QUE NO LLEVA VALIDACIÓN.
            // SOLO VALIDAR BLOQUE 2, QUE SERA LA CUENTA A DISMINUR SIEMPRE

            $totalRestanteSaldo2 = ($totalRestanteSaldo2 - $request->saldomodi);

            // aqui tenemos saldo restante - el saldo retenido.
            $totalBloque2 = $totalRestanteSaldo2 - $totalRetenido2;

            // comprobaciones.
            // VERIFICAR SOLO BLOQUE 2 PORQUE ES UNA RESTA.
            // saldo restante se RESTA TAMBIEN EL SALDO RETENIDO Y EL SALDO A QUITARLE.
            // NO DEBE QUEDAR MENOR A 0
            if($totalBloque2 < 0){
                // saldo insuficiente para hacer este movimiento, ya que queda NEGATIVO

                $totalBloque2 = number_format((float)$totalBloque2, 2, '.', ',');
                return ['success' => 1, 'saldo' => $totalBloque2, 'unido' => $unidoBloque2];
            }

            // PASADO VALIDACIÓN, SE PUEDE GUARDAR

            if($request->hasFile('documento')){
                $cadena = Str::random(15);
                $tiempo = microtime();
                $union = $cadena.$tiempo;
                $nombre = str_replace(' ', '_', $union);

                $extension = '.'.$request->documento->getClientOriginalExtension();
                $nomDocumento = $nombre.strtolower($extension);
                $avatar = $request->file('documento');
                $estado = Storage::disk('archivos')->put($nomDocumento, \File::get($avatar));

                if($estado){

                    // Guardar SUMA

                    $co = new MoviCuentaProy();
                    $co->id_cuentaproy = $request->id;
                    $co->aumento = $request->saldomodi;
                    $co->disminuye = 0;
                    $co->fecha = $request->fecha;
                    $co->reforma = $nomDocumento;
                    $co->save();

                    // Guardar RESTA

                    $co = new MoviCuentaProy();
                    $co->id_cuentaproy = $request->selectcuenta;
                    $co->aumento = 0;
                    $co->disminuye = $request->saldomodi;
                    $co->fecha = $request->fecha;
                    $co->reforma = $nomDocumento;
                    $co->save();

                    DB::commit();
                    return ['success' => 2];

                }else{
                    return ['success' => 99];
                }

            }else{
                // Guardar SUMA

                $co = new MoviCuentaProy();
                $co->id_cuentaproy = $request->id;
                $co->aumento = $request->saldomodi;
                $co->disminuye = 0;
                $co->fecha = $request->fecha;
                $co->reforma = null;
                $co->save();

                // Guardar RESTA

                $co = new MoviCuentaProy();
                $co->id_cuentaproy = $request->selectcuenta;
                $co->aumento = 0;
                $co->disminuye = $request->saldomodi;
                $co->fecha = $request->fecha;
                $co->reforma = null;
                $co->save();

                DB::commit();
                return ['success' => 2];
            }
        }catch(\Throwable $e){
            //Log::info('ee' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    function redondear_dos_decimal($valor) {
        $float_redondeado=round($valor * 100) / 100;
        return $float_redondeado;
    }

    public function descargarReforma($id){

        $url = MoviCuentaProy::where('id', $id)->pluck('reforma')->first();
        $pathToFile = "storage/archivos/".$url;
        $extension = pathinfo(($pathToFile), PATHINFO_EXTENSION);
        $nombre = "Documento." . $extension;
        return response()->download($pathToFile, $nombre);
    }

    public function guardarDocumentoReforma(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ( $validator->fails()){
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

                if($archivo){

                    $info = MoviCuentaProy::where('id', $request->id)->first();

                    if(Storage::disk('archivos')->exists($info->reforma)){
                        Storage::disk('archivos')->delete($info->reforma);
                    }

                    MoviCuentaProy::where('id', $request->id)->update([
                        'reforma' => $nomDocumento
                    ]);

                    DB::commit();
                    return ['success' => 1];
                }else{
                    return ['success' => 2];
                }
            }
            else{
                return ['success' => 2];
            }
        }catch(\Throwable $e){

            DB::rollback();
            return ['success' => 2];
        }
    }


    public function informacionMoviCuentaProy(Request $request){

        $regla = array(
            'id' => 'required', // ID CUENTA PROY
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = CuentaProy::where('id', $request->id)->first()){

            $infoObjeto = ObjEspecifico::where('id', $lista->objespeci_id)->first();
            $infoCuenta = Cuenta::where('id', $infoObjeto->id_cuenta)->first();
            $cuenta = $infoCuenta->nombre;

            // obtener CUENTA PROY. menos la seleccionada
            $arrayCuentaProy =DB::table('cuentaproy AS cp')
                ->join('obj_especifico AS obj', 'cp.objespeci_id', '=', 'obj.id')
                ->select('obj.nombre', 'obj.codigo', 'cp.id',)
                ->where('cp.id', '!=', $lista->id)
                ->get();

            // OBTENER SALDO RESTANTE

            $totalSalida = 0;
            $totalEntrada = 0;
            $totalRetenido = 0;

            // SUMA Y RESTA DE MOVIMIENTO DE CÓDIGOS
            // suma de saldo
            $moviSumaSaldo = MoviCuentaProy::where('id_cuentaproy', $lista->id)
                ->sum('aumento');

            $moviRestaSaldo = MoviCuentaProy::where('id_cuentaproy', $lista->id)
                ->sum('disminuye');

            $totalMoviCuenta = $moviSumaSaldo - $moviRestaSaldo;

            // POR AQUI SE VALIDARA SI NO FUE CANSELADO LA ORDEN DE COMPRA, YA QUE AHI SE CREA EL PRESU_DETALLE.
            // Y SI ES CANCELADO SE CAMBIA UN ESTADO Y DEJAR DE SER VALIDO PARA VERIFICAR
            $infoSalidaDetalle = DB::table('cuentaproy_detalle AS pd')
                ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero')
                ->where('pd.id_cuentaproy', $lista->id)
                ->where('pd.tipo', 0) // salidas. la orden es valida
                //->where('pd.estado', 0)// ES VALIDO, Y NO ESTA CANCELADO LA ORDEN DE COMPRA
                ->get();

            foreach ($infoSalidaDetalle as $dd){
                $totalSalida = $totalSalida + ($dd->cantidad * $dd->dinero);
            }

            $infoEntradaDetalle = DB::table('cuentaproy_detalle AS pd')
                ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero')
                ->where('pd.id_cuentaproy', $lista->id)
                ->where('pd.tipo', 1) // entradas
                ->get();

            foreach ($infoEntradaDetalle as $dd){
                $totalEntrada = $totalEntrada + ($dd->cantidad * $dd->dinero);
            }

            // SALDOS RETENIDOS

            $infoSaldoRetenido = DB::table('cuentaproy_retenido AS psr')
                ->join('requisicion_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero')
                ->where('psr.id_cuentaproy', $lista->id)
                ->get();

            foreach ($infoSaldoRetenido as $dd){
                $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
            }

            // SUMAR LOS MOVIMIENTOS DE CUENTA
            $totalRestante =  $totalMoviCuenta;
            $totalRestante += $lista->saldo_inicial - ($totalSalida - $totalEntrada);

            $totalRestante = "$". number_format((float)$totalRestante, 2, '.', ',');

            return ['success' => 1, 'info' => $lista,
                'objeto' => $infoObjeto, 'cuenta' => $cuenta,
                'restante' => $totalRestante, 'arraycuentaproy' => $arrayCuentaProy];
        }else{
            return ['success' => 2];
        }
    }

    public function infoSaldoRestanteCuenta(Request $request){

        $regla = array(
            'id' => 'required', // ID CUENTA PROY
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = CuentaProy::where('id', $request->id)->first()){

            // OBTENER SALDO RESTANTE

            $totalSalida = 0;
            $totalEntrada = 0;
            $totalRetenido = 0;

            // SUMA Y RESTA DE MOVIMIENTO DE CÓDIGOS
            // suma de saldo
            $moviSumaSaldo = MoviCuentaProy::where('id_cuentaproy', $lista->id)
                ->sum('aumento');

            $moviRestaSaldo = MoviCuentaProy::where('id_cuentaproy', $lista->id)
                ->sum('disminuye');

            $totalMoviCuenta = $moviSumaSaldo - $moviRestaSaldo;

            // POR AQUI SE VALIDARA SI NO FUE CANSELADO LA ORDEN DE COMPRA, YA QUE AHI SE CREA EL PRESU_DETALLE.
            // Y SI ES CANCELADO SE CAMBIA UN ESTADO Y DEJAR DE SER VALIDO PARA VERIFICAR
            $infoSalidaDetalle = DB::table('cuentaproy_detalle AS pd')
                ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero')
                ->where('pd.id_cuentaproy', $lista->id)
                ->where('pd.tipo', 0) // salidas. la orden es valida
                //->where('pd.estado', 0)// ES VALIDO, Y NO ESTA CANCELADO LA ORDEN DE COMPRA
                ->get();

            foreach ($infoSalidaDetalle as $dd){
                $totalSalida = $totalSalida + ($dd->cantidad * $dd->dinero);
            }

            $infoEntradaDetalle = DB::table('cuentaproy_detalle AS pd')
                ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero')
                ->where('pd.id_cuentaproy', $lista->id)
                ->where('pd.tipo', 1) // entradas
                ->get();

            foreach ($infoEntradaDetalle as $dd){
                $totalEntrada = $totalEntrada + ($dd->cantidad * $dd->dinero);
            }

            // SALDOS RETENIDOS

            $infoSaldoRetenido = DB::table('cuentaproy_retenido AS psr')
                ->join('requisicion_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero')
                ->where('psr.id_cuentaproy', $lista->id)
                ->get();

            foreach ($infoSaldoRetenido as $dd){
                $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
            }

            // SUMAR LOS MOVIMIENTOS DE CUENTA
            $totalRestante =  $totalMoviCuenta;
            $totalRestante += $lista->saldo_inicial - ($totalSalida - $totalEntrada);

            $totalRestante = "$". number_format((float)$totalRestante, 2, '.', ',');

            return ['success' => 1, 'restante' => $totalRestante];
        }else{
            return ['success' => 2];
        }

    }

    public function editarMoviCuentaProy(Request $request){
        $rules = array(
            'id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return ['success' => 0];
        }

        if($info = MoviCuentaProy::where('id', $request->id)->first()){


            if($request->hasFile('documento')){
                $cadena = Str::random(15);
                $tiempo = microtime();
                $union = $cadena.$tiempo;
                $nombre = str_replace(' ', '_', $union);

                $extension = '.'.$request->documento->getClientOriginalExtension();
                $nomDocumento = $nombre.strtolower($extension);
                $avatar = $request->file('documento');
                $estado = Storage::disk('archivos')->put($nomDocumento, \File::get($avatar));

                if($estado){

                    $documentoOld = $info->reforma;

                    MoviCuentaProy::where('id', $request->id)->update([
                        'proyecto_id' => $request->proyecto,
                        'cuentaproy_id' => $request->cuenta,
                        'aumenta' => $request->aumenta,
                        'disminuye' => $request->disminuye,
                        'fecha' => $request->fecha,
                        'reforma' => $nomDocumento]);

                    // borrar archivo anterior
                    if(Storage::disk('archivos')->exists($documentoOld)){
                        Storage::disk('archivos')->delete($documentoOld);
                    }

                    return ['success' => 1];
                }else{
                    return ['success' => 2];
                }

            }else{

                MoviCuentaProy::where('id', $request->id)->update([
                    'proyecto_id' => $request->proyecto,
                    'cuentaproy_id' => $request->cuenta,
                    'aumenta' => $request->aumenta,
                    'disminuye' => $request->disminuye,
                    'fecha' => $request->fecha]);

                return ['success' => 1];
            }

        }else{
            return ['success' => 2];
        }
    }


    //--------------- PLANILLA ---------------------------

    public function indexPlanilla($id){

        $info = Proyecto::where('id', $id)->first();
        if($info->codigo != null) {
            $datos = $info->codigo . " - " . $info->nombre;
        }else{
            $datos = $info->nombre;
        }

        return view('backend.admin.proyectos.planilla.vistaplanilla', compact('id','datos'));
    }

    public function tablaPlanilla($id){

        $lista = Planilla::where('proyecto_id', $id)->orderBy('fecha_de')->get();

        foreach ($lista as $ll){

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

    public function nuevaPlanilla(Request $request){

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
        }catch(\Throwable $e){

            DB::rollback();
            return ['success' => 2];
        }
    }

    public function informacionPlanilla(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Planilla::where('id', $request->id)->first()){

            return ['success' => 1, 'planilla' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarPlanilla(Request $request){

        if(Planilla::where('id', $request->id)->first()){

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
        }else{
            return ['success' => 2];
        }
    }

}

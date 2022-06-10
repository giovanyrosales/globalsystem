<?php

namespace App\Http\Controllers\Backend\Cuenta;

use App\Http\Controllers\Controller;
use App\Models\Cuenta;
use App\Models\CuentaProy;
use App\Models\MoviCuentaProy;
use App\Models\Planilla;
use App\Models\Proyecto;
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


    public function indexMoviCuentaProy(){
        $proyecto = Proyecto::orderBy('nombre')->get();
        return view('Backend.Admin.CuentaProyecto.Movimiento.vistaMoviCuentaProy', compact('proyecto'));
    }

    public function indexTablaMoviCuentaProy(){

        $cuenta = MoviCuentaProy::orderBy('id')->get();

        foreach ($cuenta as $dd){

            $infoProyecto = Proyecto::where('id', $dd->proyecto_id)->first();
            $infoCuentaProy = CuentaProy::where('id', $dd->cuentaproy_id)->first();
            $infoCuenta = Cuenta::where('id', $infoCuentaProy->cuenta_id)->first();

            $dd->proyecto = $infoProyecto->nombre . " - " . $infoProyecto->codigo;
            $dd->cuenta = $infoCuenta->nombre . " - " . $infoCuenta->codigo;

            $dd->aumenta = number_format((float)$dd->aumenta, 2, '.', ',');
            $dd->disminuye = number_format((float)$dd->disminuye, 2, '.', ',');
            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));
        }

        return view('Backend.Admin.CuentaProyecto.Movimiento.tablaMoviCuentaProy', compact('cuenta'));
    }

    public function buscadorCuentaProy(Request $request){
            // id proyecto
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $cuentaproy = CuentaProy::where('proyecto_id', $request->id)->get();

        foreach ($cuentaproy as $cp){

            $infoCuenta = Cuenta::where('id', $cp->cuenta_id)->first();

            $cp->nomcuenta = $infoCuenta->nombre . ' - ' . $infoCuenta->codigo;
        }

        return ['success' => 1, 'cuentaproy' => $cuentaproy];
    }

    public function nuevaMoviCuentaProy(Request $request){

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
                $co = new MoviCuentaProy();
                $co->cuentaproy_id = $request->cuenta;
                $co->proyecto_id = $request->proyecto;
                $co->aumenta = $request->aumenta;
                $co->disminuye = $request->disminuye;
                $co->fecha = $request->fecha;
                $co->reforma = $nomDocumento;
                if($co->save()){

                    return ['success' => 1];
                }else{return ['success' => 2];}
            }else{
                return ['success' => 2];
            }

        }else{
            $co = new MoviCuentaProy();
            $co->cuentaproy_id = $request->cuenta;
            $co->proyecto_id = $request->proyecto;
            $co->aumenta = $request->aumenta;
            $co->disminuye = $request->disminuye;
            $co->fecha = $request->fecha;
            if($co->save()){

                return ['success' => 1];
            }else{return ['success' => 2];}
        }
    }

    public function descargarReforma($id){

        $url = MoviCuentaProy::where('id', $id)->pluck('reforma')->first();
        $pathToFile = "storage/archivos/".$url;
        $extension = pathinfo(($pathToFile), PATHINFO_EXTENSION);
        $nombre = "Documento." . $extension;
        return response()->download($pathToFile, $nombre);
    }


    public function informacionMoviCuentaProy(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = MoviCuentaProy::where('id', $request->id)->first()){

            $proyecto = Proyecto::orderBy('nombre')->get();

            foreach ($proyecto as $pp){
                $pp->nombrecod = $pp->nombre . " - " . $pp->codigo;
            }

            $infoCuentaProy = CuentaProy::orderBy('id')->get();
            foreach ($infoCuentaProy as $ic){
                $info = Cuenta::where('id', $ic->cuenta_id)->first();
                $ic->nombrecod = $info->nombre . " - " . $info->codigo;
            }

            return ['success' => 1, 'info' => $lista, 'proyecto' => $proyecto,
                'idproyecto' => $proyecto, 'cuentaproy' => $infoCuentaProy,
                'idcuentaproy' => $lista->cuentaproy_id];
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
        $nombre = $info->codigo . " - " . $info->nombre;

        return view('Backend.Admin.Planilla.vistaPlanilla', compact('id','nombre'));
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

        return view('Backend.Admin.Planilla.tablaPlanilla', compact('lista'));
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
            Log::info('ee' . $e);
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

<?php

namespace App\Http\Controllers\Backend\Cuenta;

use App\Http\Controllers\Controller;
use App\Models\Cuenta;
use App\Models\CuentaProy;
use App\Models\MoviCuentaProy;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CuentaProyectoController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function indexCuenta(){

        $proyecto = Proyecto::orderBy('nombre')->get();
        $cuenta = Cuenta::orderBy('nombre')->get();

        return view('backend.admin.cuentaproyecto.vistacuentaproyecto', compact('proyecto', 'cuenta'));
    }

    public function tablaCuenta(){

        $cuenta = CuentaProy::orderBy('id')->get();

        foreach ($cuenta as $dd){

            $infoProyecto = Proyecto::where('id', $dd->proyecto_id)->first();
            $infoCuenta = Cuenta::where('id', $dd->cuenta_id)->first();

            $dd->proyecto = $infoProyecto->nombre . " - " . $infoProyecto->codigo;
            $dd->cuenta = $infoCuenta->nombre . " - " . $infoCuenta->codigo;

            $dd->montoini = number_format((float)$dd->montoini, 2, '.', '');
            $dd->saldo = number_format((float)$dd->saldo, 2, '.', '');
        }

        return view('backend.admin.cuentaproyecto.tablacuentaproyecto', compact('cuenta'));
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
        return view('backend.admin.cuentaproyecto.movimiento.vistamovicuentaproy', compact('proyecto'));
    }

    public function indexTablaMoviCuentaProy(){

        $cuenta = MoviCuentaProy::orderBy('id')->get();

        foreach ($cuenta as $dd){

            $infoProyecto = Proyecto::where('id', $dd->proyecto_id)->first();
            $infoCuentaProy = CuentaProy::where('id', $dd->cuentaproy_id)->first();
            $infoCuenta = Cuenta::where('id', $infoCuentaProy->cuenta_id)->first();

            $dd->proyecto = $infoProyecto->nombre . " - " . $infoProyecto->codigo;
            $dd->cuenta = $infoCuenta->nombre . " - " . $infoCuenta->codigo;

            $dd->aumenta = number_format((float)$dd->aumenta, 2, '.', '');
            $dd->disminuye = number_format((float)$dd->disminuye, 2, '.', '');
            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));
        }

        return view('backend.admin.cuentaproyecto.movimiento.tablamovicuentaproy', compact('cuenta'));
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

}

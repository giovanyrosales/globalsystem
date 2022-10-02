<?php

namespace App\Http\Controllers\Backend\Configuraciones;

use App\Http\Controllers\Controller;
use App\Models\Administradores;
use App\Models\Cuenta;
use App\Models\ObjEspecifico;
use App\Models\Rubro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CodigoEspecifController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    // retorna vista de cuenta
    public function indexCuenta(){

        $rubro = Rubro::orderBy('codigo', 'ASC')->get();

        return view('backend.admin.configuraciones.cuenta.vistacuenta', compact('rubro'));
    }

    // retorna tabla de cuenta
    public function tablaCuenta(){
        $lista = Cuenta::orderBy('codigo', 'ASC')->get();

        foreach ($lista as $l){
            $infoRubro = Rubro::where('id', $l->id_rubro)->first();

            $l->rubro = $infoRubro->codigo . " - " . $infoRubro->nombre;
        }

        return view('backend.admin.configuraciones.cuenta.tablacuenta', compact('lista'));
    }

    // registrar una nueva cuenta
    public function nuevaCuenta(Request $request){

        $regla = array(
            'nombre' => 'required',
            'numero' => 'required',
            'rubro' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new Cuenta();
        $dato->codigo = $request->numero;
        $dato->nombre = $request->nombre;
        $dato->id_rubro = $request->rubro;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // obtener información de una cuenta
    public function informacionCuenta(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Cuenta::where('id', $request->id)->first()){

            $rubro = Rubro::orderBy('nombre')->get();

            return ['success' => 1, 'cuenta' => $lista, 'idrr' => $lista->id_rubro, 'rr' => $rubro];
        }else{
            return ['success' => 2];
        }
    }

    // editar una cuenta
    public function editarCuenta(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'numero' => 'required',
            'rubro' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Cuenta::where('id', $request->id)->first()){

            Cuenta::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'codigo' => $request->numero,
                'id_rubro' => $request->rubro
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }



    // ******** OBJETO ESPECIFICO **************

    // retorna vista de objeto específico
    public function indexObjEspecifico(){

        $cuenta = Cuenta::orderBy('codigo', 'ASC')->get();

        return view('backend.admin.configuraciones.objespecifico.vistaobjespecifico', compact('cuenta'));
    }

    // retorna tabla de objeto específico
    public function tablaObjEspecifico(){

        $lista = ObjEspecifico::orderBy('nombre')->get();

        foreach ($lista as $l){
            $infoCuenta = Cuenta::where('id', $l->id_cuenta)->first();
            $l->cuenta = $infoCuenta->codigo . " - " . $infoCuenta->nombre;
        }

        return view('backend.admin.configuraciones.objespecifico.tablaobjespecifico', compact('lista'));
    }

    // registrar un objeto específico
    public function nuevaObjEspecifico(Request $request){

        $regla = array(
            'nombre' => 'required',
            'numero' => 'required',
            'cuenta' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new ObjEspecifico();
        $dato->codigo = $request->numero;
        $dato->nombre = $request->nombre;
        $dato->id_cuenta = $request->cuenta;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // obtener información de un objeto específico
    public function informacionObjEspecifico(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = ObjEspecifico::where('id', $request->id)->first()){

            $cuenta = Cuenta::orderBy('nombre')->get();

            return ['success' => 1, 'objespecifico' => $lista,
                'idcuenta' => $lista->id_cuenta, 'cuenta' => $cuenta];
        }else{
            return ['success' => 2];
        }
    }

    // editar un objeto específico
    public function editarObjEspecifico(Request $request){
        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'numero' => 'required',
            'cuenta' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(ObjEspecifico::where('id', $request->id)->first()){

            ObjEspecifico::where('id', $request->id)->update([
                'nombre' => $request -> nombre,
                'codigo' => $request -> numero,
                'id_cuenta' => $request -> cuenta
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


}

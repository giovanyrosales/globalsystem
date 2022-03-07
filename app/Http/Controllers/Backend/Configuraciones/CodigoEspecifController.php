<?php

namespace App\Http\Controllers\Backend\Configuraciones;

use App\Http\Controllers\Controller;
use App\Models\Cuenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CodigoEspecifController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        return view('backend.admin.configuraciones.codigoespecifico.vistacodigoespecifico');
    }

    public function tabla(){
        $lista = Cuenta::orderBy('codigo', 'ASC')->get();

        return view('backend.admin.configuraciones.codigoespecifico.tablacodigoespecifico', compact('lista'));
    }

    public function nuevaCuenta(Request $request){

        $regla = array(
            'nombre' => 'required',
            'codigo' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        $dato = new Cuenta();
        $dato->codigo = $request->codigo;
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // informacion
    public function informacionCuenta(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Cuenta::where('id', $request->id)->first()){

            return ['success' => 1, 'cuenta' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar
    public function editarCuenta(Request $request){

        $regla = array(
            'id' => 'required',
            'codigo' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Cuenta::where('id', $request->id)->first()){

            Cuenta::where('id', $request->id)->update([
                'codigo' => $request->codigo,
                'nombre' => $request->nombre
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

}

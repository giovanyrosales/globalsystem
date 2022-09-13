<?php

namespace App\Http\Controllers\Backend\Configuraciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Asociaciones;
use Illuminate\Support\Facades\Validator;


class AsociacionesController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){

        return view('Backend.Admin.Configuraciones.asociaciones.vistaAsociaciones');
    }

    public function tabla(){
        $lista = Asociaciones::orderBy('nombre', 'ASC')->get();

        return view('Backend.Admin.Configuraciones.asociaciones.tablaAsociaciones', compact('lista'));
    }

    public function nuevoAsociacion(Request $request){

        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new Asociaciones();
        $dato->nombre = $request->nombre;
        $dato->direccion = $request->direccion;
        $dato->tel = $request->tel;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // informacion
    public function informacionAsociacion(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Asociaciones::where('id', $request->id)->first()){

            return ['success' => 1, 'lista' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar
    public function editarAsociacion(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Asociaciones::where('id', $request->id)->first()){

            Asociaciones::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'direccion' => $request->direccion,
                'tel' => $request->tel
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

}

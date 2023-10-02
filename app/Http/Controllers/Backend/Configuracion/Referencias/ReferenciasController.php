<?php

namespace App\Http\Controllers\Backend\Configuracion\Referencias;

use App\Http\Controllers\Controller;
use App\Models\Referencias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReferenciasController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }



    public function indexReferencia(){

        return view('backend.admin.configuraciones.referencias.vistareferencias');
    }


    public function tablaReferencia(){

        $lista = Referencias::orderBy('nombre')->get();

        return view('backend.admin.configuraciones.referencias.tablareferencias', compact('lista'));
    }


    public function nuevaReferencia(Request $request){

        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new Referencias();
        $dato->nombre = $request->nombre;
        $dato->save();

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }

    }


    public function informacionReferencia(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Referencias::where('id', $request->id)->first()){


            return ['success' => 1, 'lista' => $lista];
        }else{
            return ['success' => 2];
        }
    }


    public function editarReferencia(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        Referencias::where('id', $request->id)->update([
            'nombre' => $request->nombre,
        ]);


        return ['success' => 1];
    }


}

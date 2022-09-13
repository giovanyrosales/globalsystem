<?php

namespace App\Http\Controllers\Backend\Configuraciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipos;
use Illuminate\Support\Facades\Validator;


class EquiposController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){

        return view('Backend.Admin.Configuraciones.equipos.vistaEquipos');
    }

    public function tabla(){
        $lista = Equipos::orderBy('codigo', 'ASC')->get();

        return view('Backend.Admin.Configuraciones.equipos.tablaEquipos', compact('lista'));
    }

    public function nuevoEquipo(Request $request){

        $regla = array(
            'codigo' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new Equipos();
        $dato->codigo = $request->codigo;
        $dato->descripcion = $request->descripcion;
        $dato->placa = $request->placa;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // informacion
    public function informacionEquipo(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Equipos::where('id', $request->id)->first()){

            return ['success' => 1, 'lista' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar
    public function editarEquipo(Request $request){

        $regla = array(
            'id' => 'required',
            'codigo' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Equipos::where('id', $request->id)->first()){

            Equipos::where('id', $request->id)->update([
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'placa' => $request->placa
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

}

<?php

namespace App\Http\Controllers\Backend\Configuraciones;

use App\Http\Controllers\Controller;
use App\Models\Clasificaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClasificacionesController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        return view('Backend.Admin.Configuraciones.Clasificaciones.vistaClasificaciones');
    }

    public function tabla(){
        $lista = Clasificaciones::orderBy('nombre', 'ASC')->get();
        return view('Backend.Admin.Configuraciones.Clasificaciones.tablaClasificaciones', compact('lista'));
    }

    public function nuevaClasificacion(Request $request){

        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new Clasificaciones();
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // informacion
    public function informacionClasificacion(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Clasificaciones::where('id', $request->id)->first()){

            return ['success' => 1, 'clasificacion' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar
    public function editarClasificacion(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Clasificaciones::where('id', $request->id)->first()){

            Clasificaciones::where('id', $request->id)->update([
                'nombre' => $request->nombre,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }
}

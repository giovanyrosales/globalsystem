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

    // retorna vista con las clasificaciones de material
    public function indexClasificaciones(){
        return view('backend.admin.proyectos.configuraciones.clasificaciones.vistaclasificaciones');
    }

    // retorna tabla con las clasificaciones de material
    public function tablaClasificaciones(){
        $lista = Clasificaciones::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.proyectos.configuraciones.clasificaciones.tablaclasificaciones', compact('lista'));
    }

    // registra nueva clasificación
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

    // obtener información de una clasificación
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

    // editar clasificación
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

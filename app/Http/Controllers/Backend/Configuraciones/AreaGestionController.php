<?php

namespace App\Http\Controllers\Backend\Configuraciones;

use App\Http\Controllers\Controller;
use App\Models\AreaGestion;
use App\Models\LineaTrabajo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AreaGestionController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    // retorna vista con las áreas de gestión
    public function indexAreaGestion(){

        return view('backend.admin.proyectos.configuraciones.areagestion.vistaareagestion');
    }

    // retorna tabla con las áreas de gestión
    public function tablaAreaGestion(){
        $lista = AreaGestion::orderBy('codigo', 'ASC')->get();

        return view('backend.admin.proyectos.configuraciones.areagestion.tablaareagestion', compact('lista'));
    }

    // registrar nueva área de gestión
    public function nuevaAreaGestion(Request $request){

        $regla = array(
            'codigo' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        $dato = new AreaGestion();
        $dato->codigo = $request->codigo;
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // obtener información de un área de gestión
    public function informacionAreaGestion(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = AreaGestion::where('id', $request->id)->first()){

            return ['success' => 1, 'fuente' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar área de gestión
    public function editarAreaGestion(Request $request){

        $regla = array(
            'id' => 'required',
            'codigo' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(AreaGestion::where('id', $request->id)->first()){

            AreaGestion::where('id', $request->id)->update([
                'codigo' => $request->codigo,
                'nombre' => $request->nombre,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }
}

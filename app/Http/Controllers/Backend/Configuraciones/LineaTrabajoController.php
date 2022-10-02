<?php

namespace App\Http\Controllers\Backend\Configuraciones;

use App\Http\Controllers\Controller;
use App\Models\LineaTrabajo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LineaTrabajoController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    // retorna vista con las líneas de trabajo
    public function indexLineaTrabajo(){
        return view('backend.admin.proyectos.configuraciones.lineatrabajo.vistalineadetrabajo');
    }

    // retorna tabla con las líneas de trabajo
    public function tablaLineaTrabajo(){
        $lista = LineaTrabajo::orderBy('codigo', 'ASC')->get();
        return view('backend.admin.proyectos.configuraciones.lineatrabajo.tablalineadetrabajo', compact('lista'));
    }

    // registrar nueva línea de trabajo
    public function nuevaLineaTrabajo(Request $request){

        $regla = array(
            'codigo' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        $dato = new LineaTrabajo();
        $dato->codigo = $request->codigo;
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // obtener información de línea de trabajo
    public function informacionLineaTrabajo(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = LineaTrabajo::where('id', $request->id)->first()){

            return ['success' => 1, 'linea' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar línea de trabajo
    public function editarLineaTrabajo(Request $request){

        $regla = array(
            'id' => 'required',
            'codigo' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(LineaTrabajo::where('id', $request->id)->first()){

            LineaTrabajo::where('id', $request->id)->update([
                'codigo' => $request->codigo,
                'nombre' => $request->nombre
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

}

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

    public function index(){
        return view('backend.admin.configuraciones.lineatrabajo.vistalineadetrabajo');
    }

    public function tabla(){
        $lista = LineaTrabajo::orderBy('codigo', 'ASC')->get();
        return view('backend.admin.configuraciones.lineatrabajo.tablalineadetrabajo', compact('lista'));
    }

    public function nuevaLinea(Request $request){

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

    // informacion
    public function informacionLinea(Request $request){
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

    // editar
    public function editarLinea(Request $request){

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

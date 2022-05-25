<?php

namespace App\Http\Controllers\Backend\Configuraciones;

use App\Http\Controllers\Controller;
use App\Models\FuenteFinanciamiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FuenteFinanciamientoController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        return view('Backend.Admin.Configuraciones.FuenteFinanciamiento.vistaFuenteFinanciamiento');
    }

    public function tabla(){
        $lista = FuenteFinanciamiento::orderBy('codigo', 'ASC')->get();
        return view('Backend.Admin.Configuraciones.FuenteFinanciamiento.tablaFuenteFinanciamiento', compact('lista'));
    }

    public function nuevaFuente(Request $request){

        $regla = array(
            'codigo' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        $dato = new FuenteFinanciamiento();
        $dato->codigo = $request->codigo;
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // informacion
    public function informacionFuente(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = FuenteFinanciamiento::where('id', $request->id)->first()){

            return ['success' => 1, 'fuente' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar
    public function editarFuente(Request $request){

        $regla = array(
            'id' => 'required',
            'codigo' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(FuenteFinanciamiento::where('id', $request->id)->first()){

            FuenteFinanciamiento::where('id', $request->id)->update([
                'codigo' => $request->codigo,
                'nombre' => $request->nombre
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }
}

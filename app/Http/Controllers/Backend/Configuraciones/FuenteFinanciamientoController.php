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

    // retorna vista con las fuentes de financiamiento
    public function indexFuenteFinanciamiento(){
        return view('backend.admin.proyectos.configuraciones.fuentefinanciamiento.vistafuentefinanciamiento');
    }

    // retorna tabla con las fuentes de financiamiento
    public function tablaFuenteFinanciamiento(){
        $lista = FuenteFinanciamiento::orderBy('codigo', 'ASC')->get();
        return view('backend.admin.proyectos.configuraciones.fuentefinanciamiento.tablafuentefinanciamiento', compact('lista'));
    }

    // registrar nueva fuente de financiamiento
    public function nuevaFuenteFinanciamiento(Request $request){

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

    // obtener información de una fuente de financiamiento
    public function informacionFuenteFinanciamiento(Request $request){
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

    // editar fuente de financiamiento
    public function editarFuenteFinanciamiento(Request $request){

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

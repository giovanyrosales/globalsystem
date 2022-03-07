<?php

namespace App\Http\Controllers\Backend\Configuraciones;

use App\Http\Controllers\Controller;
use App\Models\FuenteFinanciamiento;
use App\Models\FuenteRecursos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FuenteRecursosController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){

        $fuentef = FuenteFinanciamiento::orderBy('codigo', 'ASC')->get();

        foreach ($fuentef as $ll){
            if($ll->nombre == null){
                $ll->nombre = '';
            }
        }

        return view('backend.admin.configuraciones.fuenterecursos.vistafuenterecursos', compact('fuentef'));
    }

    public function tabla(){
        $lista = FuenteRecursos::orderBy('codigo', 'ASC')->get();

        foreach ($lista as $ll){

            $info = FuenteFinanciamiento::where('id', $ll->id_fuentef)->first();

            $recurso = $info->codigo . " " . $info->nombre;
            $ll->recurso = $recurso;
        }

        return view('backend.admin.configuraciones.fuenterecursos.tablafuenterecursos', compact('lista'));
    }

    public function nuevaFuente(Request $request){

        $regla = array(
            'codigo' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new FuenteRecursos();
        $dato->codigo = $request->codigo;
        $dato->nombre = $request->nombre;
        $dato->id_fuentef = $request->fuente;

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

        if($lista = FuenteRecursos::where('id', $request->id)->first()){

            $arrayFuente = FuenteFinanciamiento::orderBy('codigo', 'ASC')->get();

            foreach ($arrayFuente as $ll){
                if($ll->nombre == null){
                    $ll->nombre = '';
                }
            }

            return ['success' => 1, 'fuente' => $lista, 'idfuente' => $lista->id_fuentef, 'arrayfuente' => $arrayFuente];
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

        if(FuenteRecursos::where('id', $request->id)->first()){

            FuenteRecursos::where('id', $request->id)->update([
                'codigo' => $request->codigo,
                'nombre' => $request->nombre,
                'id_fuentef' => $request->fuente
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }
}

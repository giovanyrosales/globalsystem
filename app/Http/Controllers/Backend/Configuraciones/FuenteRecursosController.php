<?php

namespace App\Http\Controllers\Backend\Configuraciones;

use App\Http\Controllers\Controller;
use App\Models\FuenteFinanciamiento;
use App\Models\FuenteRecursos;
use App\Models\P_AnioPresupuesto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FuenteRecursosController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    // retorna vista con las fuentes de recursos
    public function indexFuenteRecursos(){

        $fuentef = FuenteFinanciamiento::orderBy('codigo', 'ASC')->get();

        foreach ($fuentef as $ll){
            if($ll->nombre == null){
                $ll->nombre = '';
            }
        }

        $anios = P_AnioPresupuesto::orderBy('nombre')->get();

        return view('backend.admin.proyectos.configuraciones.fuenterecursos.vistafuenterecursos', compact('fuentef', 'anios'));
    }

    // retorna tabla con las fuentes de recursos
    public function tablaFuenteRecursos(){
        $lista = FuenteRecursos::orderBy('codigo', 'ASC')->get();

        foreach ($lista as $ll){

            $info = FuenteFinanciamiento::where('id', $ll->id_fuentef)->first();

            $recurso = $info->codigo . " " . $info->nombre;
            $ll->recurso = $recurso;

            $infoAnio = P_AnioPresupuesto::where('id', $ll->id_p_anio)->first();
            $ll->fecha = $infoAnio->nombre;
        }

        return view('backend.admin.proyectos.configuraciones.fuenterecursos.tablafuenterecursos', compact('lista'));
    }

    public function nuevaFuenteRecursos(Request $request){

        $regla = array(
            'codigo' => 'required',
            'idanio' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new FuenteRecursos();
        $dato->codigo = $request->codigo;
        $dato->nombre = $request->nombre;
        $dato->id_fuentef = $request->fuente;
        $dato->id_p_anio = $request->idanio;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // informacion
    public function informacionFuenteRecursos(Request $request){
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

            $arrayAnios = P_AnioPresupuesto::orderBy('nombre')->get();

            return ['success' => 1, 'fuente' => $lista, 'idfuente' =>
                $lista->id_fuentef, 'arrayfuente' => $arrayFuente, 'arrayanios' => $arrayAnios];
        }else{
            return ['success' => 2];
        }
    }

    // editar
    public function editarFuenteRecursos(Request $request){

        $regla = array(
            'id' => 'required',
            'codigo' => 'required',
            'idanio' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(FuenteRecursos::where('id', $request->id)->first()){

            FuenteRecursos::where('id', $request->id)->update([
                'codigo' => $request->codigo,
                'nombre' => $request->nombre,
                'id_fuentef' => $request->fuente,
                'id_p_anio' => $request->idanio
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }
}

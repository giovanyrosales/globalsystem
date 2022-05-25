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

    public function index(){

        $linea = LineaTrabajo::orderBy('codigo', 'ASC')->get();

        foreach ($linea as $ll){
            if($ll->nombre == null){
                $ll->nombre = '';
            }
        }

        return view('Backend.Admin.Configuraciones.AreaGestion.vistaAreaGestion', compact('linea'));
    }

    public function tabla(){
        $lista = AreaGestion::orderBy('codigo', 'ASC')->get();

        foreach ($lista as $ll){

            $info = LineaTrabajo::where('id', $ll->id_linea)->first();

            $linea = $info->codigo . " " . $info->nombre;
            $ll->linea = $linea;
        }

        return view('Backend.Admin.Configuraciones.AreaGestion.tablaAreaGestion', compact('lista'));
    }

    public function nuevaAreaGestion(Request $request){

        $regla = array(
            'codigo' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        $dato = new AreaGestion();
        $dato->codigo = $request->codigo;
        $dato->nombre = $request->nombre;
        $dato->id_linea = $request->fuente;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // informacion
    public function informacionArea(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = AreaGestion::where('id', $request->id)->first()){

            $arrayFuente = LineaTrabajo::orderBy('codigo', 'ASC')->get();

            foreach ($arrayFuente as $ll){
                if($ll->nombre == null){
                    $ll->nombre = '';
                }
            }

            return ['success' => 1, 'fuente' => $lista, 'idfuente' => $lista->id_linea, 'arrayfuente' => $arrayFuente];
        }else{
            return ['success' => 2];
        }
    }

    // editar
    public function editarArea(Request $request){

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
                'id_linea' => $request->fuente
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }
}

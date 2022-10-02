<?php

namespace App\Http\Controllers\Backend\Configuraciones;

use App\Models\UnidadMedida;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UnidadMedidaController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    // retorna vista de las unidades de medida para Proyecto
    public function indexUnidadMedidaProyecto(){
        return view('backend.admin.proyectos.configuraciones.unidadmedida.vistaunidadmedida');
    }

    // retorna tabla de las unidades de medida para Proyecto
    public function tablaUnidadMedidaProyecto(){
        $lista = UnidadMedida::orderBy('medida', 'ASC')->get();
        return view('backend.admin.proyectos.configuraciones.unidadmedida.tablaunidadmedida', compact('lista'));
    }

    // registrar una nueva unidad de medida
    public function nuevaUnidadMedida(Request $request){

        $regla = array(
            'medida' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(UnidadMedida::where('medida', $request->medida)->first()){
            return ['success' => 3];
        }

        $dato = new UnidadMedida();
        $dato->medida = $request->medida;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // obtener información de unidad de medida
    public function informacionUnidadMedida(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = UnidadMedida::where('id', $request->id)->first()){

            return ['success' => 1, 'medida' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar una unidad de medida
    public function editarUnidadMedida(Request $request){

        $regla = array(
            'id' => 'required',
            'medida' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(UnidadMedida::where('id', $request->id)->first()){

            if(UnidadMedida::where('id', '!=', $request->id)
                ->where('medida', $request->medida)->first()){
                return ['success' => 3];
            }

            UnidadMedida::where('id', $request->id)->update([
                'medida' => $request->medida
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

}

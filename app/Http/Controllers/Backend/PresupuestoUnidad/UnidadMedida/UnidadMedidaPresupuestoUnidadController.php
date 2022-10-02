<?php

namespace App\Http\Controllers\Backend\PresupuestoUnidad\UnidadMedida;

use App\Http\Controllers\Controller;
use App\Models\P_UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnidadMedidaPresupuestoUnidadController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    // ************** CREAR UNIDAD DE MEDIDA PARA PRESUPUESTO DE UNIDAD **************

    // retorna vista con unidades de medida para presupuesto unidades
    public function indexUnidadMedida(){
        return view('backend.admin.presupuestounidad.configuracion.unidadmedida.vistaunidadmedida');
    }

    // retorna tabla con unidades de medida para presupuesto unidades
    public function tablaUnidadMedida(){
        $lista = P_UnidadMedida::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.presupuestounidad.configuracion.unidadmedida.tablaunidadmedida', compact('lista'));
    }

    // registra una nueva unidad de medida
    public function nuevoUnidadMedida(Request $request){
        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new P_UnidadMedida();
        $dato->nombre = $request->nombre;

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

        if($lista = P_UnidadMedida::where('id', $request->id)->first()){

            return ['success' => 1, 'lista' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // edita una unidad de medida
    public function editarUnidadMedida(Request $request){
        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(P_UnidadMedida::where('id', $request->id)->first()){

            P_UnidadMedida::where('id', $request->id)->update([
                'nombre' => $request->nombre,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

}

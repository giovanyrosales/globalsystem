<?php

namespace App\Http\Controllers\Backend\PresupuestoUnidad\Anio;

use App\Http\Controllers\Controller;
use App\Models\P_AnioPresupuesto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnioPresupuestoUnidadController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    // ************** CREAR AÑOS PARA PRESUPUESTO DE UNIDAD **************

    public function indexAnioPresupuesto(){
        return view('backend.admin.presupuestounidad.configuracion.aniopresupuesto.vistaaniopresupuesto');
    }

    public function tablaAnioPresupuesto(){
        $lista = P_AnioPresupuesto::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.presupuestounidad.configuracion.aniopresupuesto.tablaaniopresupuesto', compact('lista'));
    }

    public function nuevoAnioPresupuesto(Request $request){
        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        // año repetido
        if(P_AnioPresupuesto::where('nombre', $request->nombre)->first()){
            return ['success' => 1];
        }

        $dato = new P_AnioPresupuesto();
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 2];
        }else{
            return ['success' => 99];
        }

    }

    public function informacionAnioPresupuesto(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = P_AnioPresupuesto::where('id', $request->id)->first()){

            return ['success' => 1, 'lista' => $lista];
        }else{
            return ['success' => 2];
        }

    }

    public function editarAnioPresupuesto(Request $request){
        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(P_AnioPresupuesto::where('id', $request->id)->first()){

            if(P_AnioPresupuesto::where('id', '!=', $request->id)
                ->where('nombre', $request->nombre)
                ->first()){
                return ['success' => 1];
            }

            P_AnioPresupuesto::where('id', $request->id)->update([
                'nombre' => $request->nombre,
            ]);

            return ['success' => 2];
        }else{
            return ['success' => 99];
        }
    }

    // *******************************************************************************************

}

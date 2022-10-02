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

    // retorna vista de años para presupuesto
    public function indexAnioPresupuesto(){
        return view('backend.admin.presupuestounidad.configuracion.aniopresupuesto.vistaaniopresupuesto');
    }

    // retorna tabla de años para presupuesto
    public function tablaAnioPresupuesto(){
        $lista = P_AnioPresupuesto::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.presupuestounidad.configuracion.aniopresupuesto.tablaaniopresupuesto', compact('lista'));
    }

    // registra nuevo año
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

    // obtener información de año
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

    // editar un año de presupuesto
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

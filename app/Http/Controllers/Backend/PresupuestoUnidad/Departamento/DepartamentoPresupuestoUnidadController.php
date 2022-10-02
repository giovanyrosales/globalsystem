<?php

namespace App\Http\Controllers\Backend\PresupuestoUnidad\Departamento;

use App\Http\Controllers\Controller;
use App\Models\P_Departamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartamentoPresupuestoUnidadController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    // ************** CREAR DEPARTAMENTOS PARA PRESUPUESTO DE UNIDAD **************

    // retorna vista con los departamentos
    public function indexDepartamentos(){
        return view('backend.admin.presupuestounidad.configuracion.departamentos.vistadepartamentopresupuesto');
    }

    // retorna tabla con los departamentos
    public function tablaDepartamentos(){
        $lista = P_Departamento::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.presupuestounidad.configuracion.departamentos.tabladepartamentopresupuesto', compact('lista'));
    }

    // registrar un nuevo departamento
    public function nuevoDepartamentos(Request $request){
        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new P_Departamento();
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // obtener información de un departamento
    public function informacionDepartamentos(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = P_Departamento::where('id', $request->id)->first()){

            return ['success' => 1, 'lista' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar un departamento
    public function editarDepartamentos(Request $request){
        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(P_Departamento::where('id', $request->id)->first()){

            P_Departamento::where('id', $request->id)->update([
                'nombre' => $request->nombre,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


    //*************************************************************************************

}

<?php

namespace App\Http\Controllers\Backend\PresupuestoUnidad\Requerimientos;

use App\Http\Controllers\Controller;
use App\Models\P_AnioPresupuesto;
use App\Models\P_Departamento;
use App\Models\P_PresupUnidad;
use App\Models\P_UsuarioDepartamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RequerimientosUnidadController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    // retornar vista para poder elegir año de presupuesto para solicitar requerimiento
    public function indexBuscarAñoPresupuesto(){
        $anios = P_AnioPresupuesto::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.presupuestounidad.requerimientos.vistaaniorequerimiento', compact('anios'));
    }

    // verifica si puede hacer requerimientos segun año de presupuesto
    public function verificarEstadoPresupuesto(Request $request){

        $idusuario = Auth::id();

        // si este id de usuario no esta registrado con departamento. mostrar alerta
        if(!P_UsuarioDepartamento::where('id_usuario', $idusuario)->first()){
           return ['success' => 1];
        }

        $infoUsuario = P_UsuarioDepartamento::where('id_usuario', $idusuario)->first();

        // verificar que presupuesto este aprobado

        if($info = P_PresupUnidad::where('id_anio', $request->anio)
            ->where('id_departamento', $infoUsuario->id_departamento)
            ->first()){

            // Estados
            //* 1- En Desarrollo
            //* 2- Listo para Revisión
            //* 3- Aprobado

            if($info->id_estado == 1){
                return ['success' => 2];
            }

            if($info->id_estado == 2){
                return ['success' => 3];
            }

        }else{
            // no está creado aun, asi que agregar a pendientes
            return ['success' => 4];
        }

        // procede
        return ['success' => 5];
    }

}

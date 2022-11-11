<?php

namespace App\Http\Controllers\Backend\PresupuestoUnidad\Requerimientos;

use App\Http\Controllers\Controller;
use App\Models\CotizacionUnidad;
use App\Models\CuentaUnidad;
use App\Models\P_AnioPresupuesto;
use App\Models\P_Departamento;
use App\Models\P_PresupUnidad;
use App\Models\P_UsuarioDepartamento;
use App\Models\RequisicionUnidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RequerimientosUnidadController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    // retornar vista para poder elegir año de presupuesto para solicitar requerimiento
    public function indexBuscarAñoPresupuesto(){
        $anios = P_AnioPresupuesto::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.presupuestounidad.requerimientos.buscaraniopresupuesto.vistaaniorequerimiento', compact('anios'));
    }

    // verifica si puede hacer requerimientos segun año de presupuesto
    public function verificarEstadoPresupuesto(Request $request){

        $idusuario = Auth::id();

        // primero ver si está registrado en un departamento el usuario
        if(!P_UsuarioDepartamento::where('id_usuario', $idusuario)->first()){
           return ['success' => 1];
        }

        $infoUsuarioDepa = P_UsuarioDepartamento::where('id_usuario', $idusuario)->first();

        // verificar si presupuesto esta creado y aprobado
        if($infoPresuUnidad = P_PresupUnidad::where('id_anio', $request->anio)
            ->where('id_departamento', $infoUsuarioDepa->id_departamento)
            ->first()){

            // en modo desarrollo
            if($infoPresuUnidad->id_estado == 1){
                return ['success' => 2];
            }

            // en modo revisión
            if($infoPresuUnidad->id_estado == 2){
                return ['success' => 3];
            }

            // está aprobado, pero es de ver si ya crearon las cuentas unidades
            if(CuentaUnidad::where('id_presup_unidad', $infoPresuUnidad->first())){
               // PASAR A PANTALLA REQUERIMIENTOS
               return ['success' => 4];
            }else{
               return ['success' => 5];
            }

        }else{
            // no está creado aun, asi que agregar a pendientes
            return ['success' => 6];
        }
    }

    // retorna vista donde están los requerimientos por año
    public function indexRequerimientosUnidades($idanio){

        // obtener usuario
        $user = Auth::user();

        // conseguir el departamento
        $infoDepartamento = P_UsuarioDepartamento::where('id_usuario', $user->id)->first();
        $infoAnio = P_AnioPresupuesto::where('id', $idanio)->first();

        $txtanio = $infoAnio->nombre;

        $monto = DB::table('cuenta_unidad AS cu')
            ->join('p_presup_unidad AS pu', 'cu.id_presup_unidad', '=', 'pu.id')
            ->select('cu.saldo_inicial')
            ->where('pu.id_anio', $idanio)
            ->where('pu.id_departamento', $infoDepartamento->id_departamento)
            ->sum('cu.saldo_inicial');

        $monto = '$' . number_format((float)$monto, 2, '.', ',');

        $infoPresuUnidad = P_PresupUnidad::where('id_anio', $idanio)
            ->where('id_departamento', $infoDepartamento->id)
            ->first();

        $conteo = RequisicionUnidad::where('id_presup_unidad', $infoPresuUnidad->id)->count();
        if($conteo == null){
            $conteo = 1;
        }else{
            $conteo += 1;
        }

        $idpresubunidad = $infoPresuUnidad->id;

        return view('backend.admin.presupuestounidad.requerimientos.requerimientosunidad.vistarequerimientosunidad', compact('monto'
        , 'txtanio', 'idanio', 'conteo', 'idpresubunidad'));
    }

    // retorna tabla donde están los requerimientos por año
    public function tablaRequerimientosUnidades($idpresubunidad){

        // listado de requisiciones
        $listaRequisicion = RequisicionUnidad::where('id_presup_unidad', $idpresubunidad)
            ->orderBy('fecha', 'ASC')
            ->get();

        $contador = 0;
        foreach ($listaRequisicion as $ll){
            $contador += 1;
            $ll->numero = $contador;
            $ll->fecha = date("d-m-Y", strtotime($ll->fecha));

            $infoEstado = "Editar";
            //------------------------------------------------------------------
            // SI HAY MATERIAL COTIZADO, NO PODRA BORRAR REQUISICIÓN YA
            $hayCotizacion = true;
            if(CotizacionUnidad::where('id_requisicion_unidad', $ll->id)->first()){
                $hayCotizacion = false;
                $infoEstado = "Información"; // no tomar si esta default, aprobado, denegado
            }

            $ll->haycotizacion = $hayCotizacion;
            $ll->estado = $infoEstado;

        } // end foreach

        return view('backend.admin.presupuestounidad.requerimientos.requerimientosunidad.tablarequerimientosunidad', compact('listaRequisicion'));
    }


}

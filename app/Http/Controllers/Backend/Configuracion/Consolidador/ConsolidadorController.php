<?php

namespace App\Http\Controllers\Backend\Configuracion\Consolidador;

use App\Http\Controllers\Controller;
use App\Models\ConsolidadoresUnidades;
use App\Models\ObjEspecifico;
use App\Models\P_AnioPresupuesto;
use App\Models\P_Departamento;
use App\Models\P_Materiales;
use App\Models\P_PresupUnidad;
use App\Models\Requisicion;
use App\Models\RequisicionAgrupadaDetalle;
use App\Models\RequisicionUnidad;
use App\Models\RequisicionUnidadDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConsolidadorController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }


    public function indexRequerimientosPendientes(){

        // años para buscar los requerimientos de esos años
        $anios = P_AnioPresupuesto::orderBy('id', 'DESC')->get();

        return view('backend.admin.consolidador.requerimientos.vistabuscarporanio', compact('anios'));
    }


    public function vistaRequerimientosPendientes($idanio){

        // CARGAR REQUERIMIENTOS DEL CONSOLIDADOR, TODOS AQUELLOS QUE NO HAN SIDO AGRUPADOS



        return view('backend.admin.consolidador.requerimientos.vistarequerimientos', compact('idanio'));
    }


    public function tablaRequerimientosPendientes($idanio){

        // REGLAS

        // MOSTRAR REQUERIMIENTOS DE MIS UNIDADES ASIGNADAS,
        // AQUELLAS REQUERIMIENTOS QUE TENGAN UN (1) MATERIAL SIN CONSOLIDAR
        // AQUEL MATERIAL QUE NO ESTE CANCELADO

        // DEBERA APARECER SU UNIDAD

        $user = Auth::user();
        $miID = $user->id;


        $arrayUnidades = ConsolidadoresUnidades::where('id_usuario', $miID)->get();

        $pilaIdDepartamentos = array();

        foreach ($arrayUnidades as $dd){
            array_push($pilaIdDepartamentos, $dd->id_departamento);
        }

        $lista = DB::table('requisicion_unidad AS ru')
            ->join('p_presup_unidad AS pu', 'ru.id_presup_unidad', '=', 'pu.id')
            ->join('p_departamento AS dep', 'pu.id_departamento', '=', 'dep.id')
            ->select('pu.id_departamento', 'pu.id_anio', 'dep.nombre', 'ru.id')
            ->whereIn('pu.id_departamento', $pilaIdDepartamentos) // solo de mis unidades asignadas
            ->where('pu.id_anio', $idanio) // solo del año que quiero
            ->get();


        // HOY NECESITO FILTRAR QUE SOLO MUESTRE LOS NO AGRUPADOS Y NO CANCELADOS

        $pilaIdPendiente = array();

        foreach ($lista as $dato){

            $arrayRequiDetalle = RequisicionUnidadDetalle::where('id_requisicion_unidad', $dato->id)->get();

            foreach ($arrayRequiDetalle as $info){

                if($info->agrupado == 0 || $info->cancelado == 0){

                    // METER A LA LISTA, NO IMPORTA QUE SE REPITA EL ID
                    array_push($pilaIdPendiente, $dato->id);
                }
            }
        }


        // HOY YA FILTRADO, SOLO MOSTRAR LA FILA DE REQUISICION

        $arrayRequisicionPendiente = RequisicionUnidad::whereIn('id', $pilaIdPendiente)->get();


        foreach ($arrayRequisicionPendiente as $dd){

            $infoPresupuesto = P_PresupUnidad::where('id', $dd->id_presup_unidad)->first();
            $infoDepartamento = P_Departamento::where('id', $infoPresupuesto->id_departamento)->first();

            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));
            $dd->departamento = $infoDepartamento->nombre;
        }


        return view('backend.admin.consolidador.requerimientos.tablarequerimientos', compact('arrayRequisicionPendiente'));
    }


    public function listadoAgrupadosParaSelect(Request $request){

        // REQUEST
        // anio

        // REGRESA LOS ITEM AGRUPADOS

        // obtener listado de id de requisicion_detalle ya agrupadas para evitar mostrarlos
        // en el select

        $arrayIdAgrupadaDetalle = RequisicionAgrupadaDetalle::select('id_requi_unidad_detalle')->get();

        $arrayDetalles = DB::table('requisicion_unidad_detalle AS rud')
            ->join('requisicion_unidad AS ru', 'rud.id_requisicion_unidad', '=', 'ru.id')
            ->join('p_presup_unidad AS pp', 'ru.id_presup_unidad', '=', 'pp.id')
            ->select('rud.agrupado', 'rud.id_material')
            ->where('rud.agrupado', 0)
            ->where('pp.id_anio', $request->anio)
            ->get();

        foreach ($arrayDetalles as $info){

            $infoMaterial = P_Materiales::where('id', $info->id_material)->first();
            $info->nombrematerial = $infoMaterial->nombre;
        }


        return ['success' => 1, 'detalle' => $arrayDetalles];
    }



    public function informacionDetalleRequisicion($idrequi){

        $arrayDetalle = RequisicionUnidadDetalle::where('id_requisicion_unidad', $idrequi)
            ->where('agrupado', 0)
            ->where('cancelado', 0)
            ->get();

        foreach ($arrayDetalle as $info){

            $infoMaterial = P_Materiales::where('id', $info->id_material)->first();
            $infoObj = ObjEspecifico::where('id', $infoMaterial->id_objespecifico)->first();

            $info->codigo = $infoObj->codigo;
            $info->nombreobj = $infoObj->nombre;

            $info->nombrematerial = $infoMaterial->descripcion;
        }

        return view('backend.admin.consolidador.requerimientos.detalle.tabladetalle', compact('arrayDetalle'));
    }



}

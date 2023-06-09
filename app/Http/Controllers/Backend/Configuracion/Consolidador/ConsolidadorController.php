<?php

namespace App\Http\Controllers\Backend\Configuracion\Consolidador;

use App\Http\Controllers\Controller;
use App\Models\Administradores;
use App\Models\ConsolidadoresUnidades;
use App\Models\CotizacionUnidad;
use App\Models\ObjEspecifico;
use App\Models\P_AnioPresupuesto;
use App\Models\P_Departamento;
use App\Models\P_Materiales;
use App\Models\P_PresupUnidad;
use App\Models\Requisicion;
use App\Models\RequisicionAgrupada;
use App\Models\RequisicionAgrupadaDetalle;
use App\Models\RequisicionUnidad;
use App\Models\RequisicionUnidadDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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

        $adminContrato = Administradores::orderBy('nombre')->get();

        return view('backend.admin.consolidador.requerimientos.vistarequerimientos', compact('idanio',
        'adminContrato'));
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

                 // SOLO MOSTRAR SI NO ESTA AGRUPADO Y NO ESTA CANCELADO
                if($info->agrupado == 0 && $info->cancelado == 0){

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
            ->where('pu.id_anio', $request->anio) // solo del año que quiero
            ->get();


        // HOY NECESITO FILTRAR QUE SOLO MUESTRE LOS NO AGRUPADOS Y NO CANCELADOS

        $pilaIdPendiente = array();

        foreach ($lista as $dato){

            $arrayRequiDetalle = RequisicionUnidadDetalle::where('id_requisicion_unidad', $dato->id)->get();

            foreach ($arrayRequiDetalle as $info){

                if($info->agrupado == 0 || $info->cancelado == 0){

                    // METER A LA LISTA, TODOS LOS ID DE REQUISICION UNIDAD DETALLE
                    array_push($pilaIdPendiente, $info->id);
                }
            }
        }

        // ORDENAR POR CODIGO


        $arrayRequisicionPendiente = DB::table('requisicion_unidad_detalle AS ru')
            ->join('p_materiales AS pm', 'ru.id_material', '=', 'pm.id')
            ->join('obj_especifico AS obj', 'pm.id_objespecifico', '=', 'obj.id')
            ->select('ru.id', 'obj.codigo', 'obj.nombre', 'pm.descripcion')
            ->whereIn('ru.id', $pilaIdPendiente)
            ->orderBy('obj.codigo', 'ASC')
            ->get();


        foreach ($arrayRequisicionPendiente as $info){

            $texto = "(" . $info->codigo . ") " . $info->descripcion;
            $info->texto = $texto;
        }

        return ['success' => 1, 'detalle' => $arrayRequisicionPendiente];
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


    public function registrarAgrupados(Request $request){

        // REQUEST

        // lista
        // descripcion

        $regla = array(
            'fecha' => 'required',
            'administrador' => 'required',
            'evaluador' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        DB::beginTransaction();

        try {


            // REGISTRAR AGRUPADO

            $dato = new RequisicionAgrupada();
            $dato->id_anio = $request->anio;
            $dato->fecha = $request->fecha;
            $dato->id_contrato = $request->administrador;
            $dato->id_evaluador = $request->evaluador;
            $dato->estado = 0;
            $dato->nota_cancelado = null;
            $dato->documento = null;
            $dato->save();

            $infoRequiDetalle = RequisicionUnidadDetalle::whereIn('id', $request->lista)
                ->orderBy('id', 'ASC')
                ->get();

            foreach ($infoRequiDetalle as $info){


                // NO DEBE ESTAR AGRUPADO O CANCELADO
                if($info->agrupado == 1 || $info->cancelado == 1){
                    DB::rollback();
                    return ['success' => 1];
                }

                // *****

                $ingreso = new RequisicionAgrupadaDetalle();
                $ingreso->id_requi_agrupada = $dato->id;
                $ingreso->id_requi_unidad_detalle = $info->id;
                $ingreso->cotizado = 0;
                $ingreso->save();

                RequisicionUnidadDetalle::where('id', $info->id)->update([
                    'agrupado' => 1,
                ]);
            }


            DB::commit();
            return ['success' => 2];

        } catch (\Throwable $e) {
            Log::info('ee ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }




    public function indexListaAgrupados(){


        return view('backend.admin.consolidador.agrupados.vistaagrupados');
    }


    public function tablaListaAgrupados(){


        $listado = RequisicionAgrupada::orderBy('fecha', 'DESC')->get();

        foreach ($listado as $info) {

            $infoAdministrador = Administradores::where('id', $info->id_contrato)->first();
            $infoEvaluador = Administradores::where('id', $info->id_evaluador)->first();
            $info->fecha = date("d-m-Y", strtotime($info->fecha));

            $info->nomadmin = $infoAdministrador->nombre;
            $info->nomevaluador = $infoEvaluador->nombre;

            // PARA MOSTRAR BOTON DE BORRAR SINO HAY NINGUNA COTIZACION

            $btnBorrar = 1;
            $textoestado = "";

            if (CotizacionUnidad::where('id_agrupado', $info->id)->first()) {
                $btnBorrar = 0;

                // MOSTRAR ESTADOS, SOLO 2.
                // - CANCELADO
                // - COTIZACIONES INICIADAS
                $textoestado = "Cotización Iniciadas";
            }


            $info->btnborrar = $btnBorrar;
            $info->textoestado = $textoestado;
        }


        return view('backend.admin.consolidador.agrupados.tablaagrupados', compact('listado'));
    }



    public function generarPdfAgrupado($idagrupado){


        $infoRequiAgrupado = RequisicionAgrupada::where('id', $idagrupado)->first();

        $fecha = date("d-m-Y", strtotime($infoRequiAgrupado->fecha));

        //$mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf->SetTitle('Agrupado');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/logo2.png';

        $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            Fecha: $fecha <br></p>
            </div>";


        $stylesheet = file_get_contents('css/csspresupuesto.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla,2);


        $mpdf->Output();
    }



    public function borrarAgrupado(Request $request){

        $regla = array(
            'id' => 'required', // id requisicion_agrupado

        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        DB::beginTransaction();

        try {

            if($infoAgrupado = RequisicionAgrupada::where('id', $request->id)->first()){

                // SI ESTE AGRUPADO SE ENCUENTRA COTIZADO, YA NO SE PUEDE BORRAR
                if(CotizacionUnidad::where('id_agrupado', $infoAgrupado->id)->first()){

                    return ['success' => 1]; // no se puede borrar
                }

                // VOLVER A SETEAR DE AGRUPADO LOS MATERIALES
                $arrayRequi = RequisicionAgrupadaDetalle::where('id_requi_agrupada', $infoAgrupado->id)->get();

                foreach ($arrayRequi as $dato){

                    RequisicionUnidadDetalle::where('id', $dato->id_requi_unidad_detalle)->update([
                        'agrupado' => 0
                    ]);
                }


                // BORRAR
                RequisicionAgrupadaDetalle::where('id_requi_agrupada', $infoAgrupado->id)->delete();
                RequisicionAgrupada::where('id', $infoAgrupado->id)->delete();

                DB::commit();
                return ['success' => 2];

            }else{
                return ['success' => 99];
            }

        } catch (\Throwable $e) {
            Log::info('ee ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }



}

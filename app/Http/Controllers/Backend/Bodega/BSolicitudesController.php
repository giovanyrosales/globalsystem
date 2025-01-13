<?php

namespace App\Http\Controllers\Backend\Bodega;

use App\Http\Controllers\Controller;
use App\Models\BodegaMateriales;
use App\Models\BodegaSolicitud;
use App\Models\BodegaSolicitudDetalle;
use App\Models\BodegaUsuarioObjEspecifico;
use App\Models\ObjEspecifico;
use App\Models\P_Departamento;
use App\Models\P_UnidadMedida;
use App\Models\P_UsuarioDepartamento;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BSolicitudesController extends Controller
{
    public function indexNuevaSolicitud()
    {
        $arrayMedida = P_UnidadMedida::orderBy('nombre', 'asc')->get();
        $arrayCodigo = ObjEspecifico::whereIn('id', [24,33,34,81,78])
            ->orderBy('nombre', 'asc')
            ->get();

        return view('backend.admin.bodega.solicitudesunidad.vistanuevasolicitud',
            compact('arrayMedida', 'arrayCodigo'));
    }


    public function registrarSolicitudUnidad(Request $request)
    {
        $regla = array(
            'idObjEspeci' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();

        try {

            // usuario que hace la solicitud
            $usuario = auth()->user();
            $fecha = Carbon::now('America/El_Salvador');



            // Obtiene los datos enviados desde el formulario como una cadena JSON y luego decódificala
            $datosContenedor = json_decode($request->contenedorArray, true); // El segundo argumento convierte el resultado en un arreglo
            //

            $nuevoReg = new BodegaSolicitud();
            $nuevoReg->id_usuario = $usuario->id;
            $nuevoReg->fecha = $fecha;
            $nuevoReg->id_objespecifico = $request->idObjEspeci;
            $nuevoReg->estado = 0;
            $nuevoReg->save();

            // infoProducto, infoIdUnidad, infoIdPrioridad, infoCantidad

            foreach ($datosContenedor as $filaArray) {

                $detalle = new BodegaSolicitudDetalle();
                $detalle->id_bodesolicitud = $nuevoReg->id;
                $detalle->id_unidad = $filaArray['infoIdUnidad'];
                $detalle->nombre = $filaArray['infoProducto'];
                $detalle->cantidad = $filaArray['infoCantidad'];
                $detalle->prioridad = $filaArray['infoIdPrioridad'];
                $detalle->estado = 1; // 1- pendiente 2- entregado 3- entragado/parcial 4- denegado
                $detalle->cantidad_entregada = 0;
                $detalle->id_referencia = null;
                $detalle->save();
            }

            DB::commit();
            return ['success' => 1];

        }catch(\Throwable $e){
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function indexMisSolicitudUnidad()
    {
        return view('backend.admin.bodega.solicitudesunidad.missolicitudes.vistamisolicitudesunidad');
    }


    public function tablaMisSolicitudUnidad()
    {
        $usuario = auth()->user();

        $listado = BodegaSolicitud::where('id_usuario', $usuario->id)
        ->orderBy('fecha', 'desc')
            ->get();

        foreach ($listado as $fila) {
            $fila->fecha = date("d-m-Y", strtotime($fila->fecha));

            $objetoEspe = ObjEspecifico::where('id', $fila->id_objespecifico)->first();
            $fila->objetoEspecifico = $objetoEspe->nombre;
        }

        return view('backend.admin.bodega.solicitudesunidad.missolicitudes.tablamisolicitudesunidad', compact('listado'));
    }


    public function indexDetalleMisSolicitudUnidad($idsolicitud)
    {
        return view('backend.admin.bodega.solicitudesunidad.missolicitudes.detalle.detallevistamissolicitudes', compact('idsolicitud'));
    }


    public function tablaDetalleMisSolicitudUnidad($idsolicitud)
    {
        $listado = BodegaSolicitudDetalle::where('id_bodesolicitud', $idsolicitud)
            ->orderBy('nombre', 'asc')
            ->get();

        foreach ($listado as $fila) {

            $infoMedida = P_UnidadMedida::where('id', $fila->id_unidad)->first();
            $fila->unimedida = $infoMedida->nombre;

            // 1- baja 2- media 3- alta
            if($fila->prioridad == 1){
                $nombrePrioridad = "Baja";
            }else if($fila->prioridad == 2){
                $nombrePrioridad = "Media";
            }else{
                $nombrePrioridad = "Alta";
            }
            $fila->nombrePrioridad = $nombrePrioridad;

            if($fila->estado == 1){
                $estado = "Pendiente";
            }
            else if($fila->estado == 2){
                $estado = "Entregado";
            }
            else{
                $estado = "Denegado";
            }
            $fila->nombreEstado = $estado;

        }

        return view('backend.admin.bodega.solicitudesunidad.missolicitudes.detalle.tabladetallemissolicitudes', compact('listado'));
    }


    //********************** SOLICITUDES PENDIENTE *****************************


    public function indexSolicitudesPendientes()
    {
        return view('backend.admin.bodega.solicitudespendientes.pendientes.vistasolicitudpendiente');
    }


    public function tablaSolicitudesPendientes()
    {
        $pilaObjEspeci = array();
        $infoAuth = auth()->user();
        $arrayCodigo = BodegaUsuarioObjEspecifico::where('id_usuario', $infoAuth->id)->get();

        foreach ($arrayCodigo as $fila){
            array_push($pilaObjEspeci, $fila->id_objespecifico);
        }

        $listado = BodegaSolicitud::whereIn('id_objespecifico', $pilaObjEspeci)
            ->where('estado', 0) // pendientes
            ->orderBy('fecha', 'asc')->get();

        foreach ($listado as $fila){
            $fila->fecha = date("d-m-Y", strtotime($fila->fecha));

            $infoUsuario = Usuario::where('id', $fila->id_usuario)->first();
            $fila->nombreUsuario = $infoUsuario->nombre;

            $departamento = "";
            // usuario que hizo la solicitud
            if($infoUDepa = P_UsuarioDepartamento::where('id_usuario', $fila->id_usuario)->first()){
                $infoDepa = P_Departamento::where('id', $infoUDepa->id_departamento)->first();
                $departamento = $infoDepa->nombre;
            }

            $fila->nombreDepartamento = $departamento;
        }

        return view('backend.admin.bodega.solicitudespendientes.pendientes.tablasolicitudpendiente', compact('listado'));
    }


    public function cambiarEstadoAFinalizar(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        if(BodegaSolicitud::where('id', $request->id)->first()){

            // verificar que cada item tenga cualquier estado menos pendiente
            $listado = BodegaSolicitudDetalle::where('id_bodesolicitud', $request->id)->get();
            $faltaEstado = false;
            foreach ($listado as $fila){
                if($fila->estado == 1){
                    $faltaEstado = true; // hay un item que tiene estado pendiente
                    break;
                }
            }

            if($faltaEstado){
                return ['success' => 1];
            }

            BodegaSolicitud::where('id', $request->id)->update([
                'estado' => 1, // finalizado
            ]);

            return ['success' => 2];

        }else{
            return ['success' => 99];
        }
    }

    public function indexDetalleSolicitudesPendientes($idsolicitud)
    {

        $arrayReferencia = BodegaSolicitudDetalle::where('id_bodesolicitud', $idsolicitud)
            ->orderBy('nombre', 'asc')
            ->get();

        foreach ($arrayReferencia as $fila){
            $infoUnidad = P_UnidadMedida::where('id', $fila->id_unidad)->first();
            $fila->unidadMedida = $infoUnidad->nombre;
        }

        $pilaObjEspeci = array();
        $infoAuth = auth()->user();
        $arrayCodigo = BodegaUsuarioObjEspecifico::where('id_usuario', $infoAuth->id)->get();

        foreach ($arrayCodigo as $fila){
            array_push($pilaObjEspeci, $fila->id_objespecifico);
        }

        $arrayMateriales = BodegaMateriales::where('id_objespecifico', $pilaObjEspeci)
            ->orderBy('nombre', 'asc')
            ->get();

        return view('backend.admin.bodega.solicitudespendientes.pendientes.detalle.vistadetallesolicitudpendiente',
        compact('idsolicitud', 'arrayReferencia', 'arrayMateriales'));
    }

    public function tablaDetalleSolicitudesPendientes($idsolicitud)
    {


        return "dfgreg";

        return view('backend.admin.bodega.solicitudespendientes.pendientes.detalle.tabladetallesolicitudpendiente');
    }



    public function infoBodegaSolitudDetalleFila(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $info = BodegaSolicitudDetalle::where('id', $request->id)->first();
        $infoUnidad = P_UnidadMedida::where('id', $info->id_unidad)->first();
        $nombreUnidad = $infoUnidad->nombre;


        return ['success' => 1, 'info' => $info, 'nombreUnidad' => $nombreUnidad];
    }





}

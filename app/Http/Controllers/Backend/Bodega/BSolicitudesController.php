<?php

namespace App\Http\Controllers\Backend\Bodega;

use App\Http\Controllers\Controller;
use App\Models\BodegaEntradas;
use App\Models\BodegaEntradasDetalle;
use App\Models\BodegaMateriales;
use App\Models\BodegaSalida;
use App\Models\BodegaSalidaDetalle;
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
            $nuevoReg->numero_solicitud = null;
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
            else if($fila->estado == 3){
                $estado = "Entregado/Parcial";
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
        return view('backend.admin.bodega.solicitudes.pendientes.vistasolicitudpendiente');
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
            ->orderBy('fecha', 'asc')
            ->get();

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

            $puedeBorrar = 0;
            // SINO TIENE NINGUN REGISTRO PUEDE BORRAR
            if(BodegaSalida::where('id_solicitud', $fila->id)->first()){
                $puedeBorrar = 1;
            }

            $fila->puedeBorrar = $puedeBorrar;
        }

        return view('backend.admin.bodega.solicitudes.pendientes.tablasolicitudpendiente', compact('listado'));
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

    public function cambiarEstadoAPendiente(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        if(BodegaSolicitud::where('id', $request->id)->first()){

            BodegaSolicitud::where('id', $request->id)->update([
                'estado' => 0, // pendiente
            ]);

            return ['success' => 1];

        }else{
            return ['success' => 99];
        }
    }


    public function eliminarCompletamenteSolicitud(Request $request)
    {
        $regla = array(
            'id' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(BodegaSolicitud::where('id', $request->id)->first()){

            // SE ENCONTRARON SALIDAS
            if(BodegaSalida::where('id_solicitud', $request->id)->first()){
                return ['success' => 1];
            }

            DB::beginTransaction();
            try {

                BodegaSolicitudDetalle::where('id_bodesolicitud', $request->id)->delete();
                BodegaSolicitud::where('id', $request->id)->delete();

                DB::commit();
                return ['success' => 2];

            } catch (\Throwable $e) {
                Log::info('ee' . $e);
                DB::rollback();
                return ['success' => 99];
            }
        }else{
            return ['success' => 99];
        }
    }


    public function indexDetalleSolicitudesPendientes($idsolicitud)
    {
        $arraySinReferencia = BodegaSolicitudDetalle::where('id_bodesolicitud', $idsolicitud)
            ->where('id_referencia', null)
            ->orderBy('nombre', 'asc')
            ->get();

        foreach ($arraySinReferencia as $fila){
            $infoUnidad = P_UnidadMedida::where('id', $fila->id_unidad)->first();
            $fila->unidadMedida = $infoUnidad->nombre;


            if($fila->estado == 1){
                $nombreEstado = "Pendiente";
            }else{
                $nombreEstado = "Denegado";
            }

            $fila->nombreEstado = $nombreEstado;



            if($fila->prioridad == 1){
                $nombrePrioridad = "BAJA";
            }else if ($fila->prioridad == 2){
                $nombrePrioridad = "MEDIA";
            }else{
                $nombrePrioridad = "ALTA";
            }

            $fila->nombrePrioridad = $nombrePrioridad;
        }

        $pilaObjEspeci = array();
        $infoAuth = auth()->user();
        $arrayCodigo = BodegaUsuarioObjEspecifico::where('id_usuario', $infoAuth->id)->get();

        foreach ($arrayCodigo as $fila){
            array_push($pilaObjEspeci, $fila->id_objespecifico);
        }

        $arrayMateriales = BodegaMateriales::whereIn('id_objespecifico', $pilaObjEspeci)
            ->orderBy('nombre', 'asc')
            ->get();

        foreach ($arrayMateriales as $fila){

            $infoUnidad = P_UnidadMedida::where('id', $fila->id_unidadmedida)->first();
            $nombreCompleto = $fila->nombre . " (" . $infoUnidad->nombre . ")";
            $fila->nombre = $nombreCompleto;
        }



        // LISTADO QUE YA TIENEN UNA REFERENCIA
        $arrayReferencia = BodegaSolicitudDetalle::where('id_bodesolicitud', $idsolicitud)
            ->where('id_referencia', '!=', null)
            ->orderBy('nombre', 'asc')
            ->get();

        $contadorFila = 1;
        foreach ($arrayReferencia as $fila){
            $fila->numeralFila = $contadorFila;
            $contadorFila++;

            // siempre tendra referencia
            $infoMaterial = BodegaMateriales::where('id', $fila->id_referencia)->first();
            $fila->nombreReferencia = $infoMaterial->nombre;


            $infoUnidad = P_UnidadMedida::where('id', $infoMaterial->id_unidadmedida)->first();
            $fila->unidadMedida = $infoUnidad->nombre;

            if($fila->estado == 1){
                $nombreEstado = "Pendiente";
            }
            else if($fila->estado == 2){
                $nombreEstado = "Entregado";
            }
            else if($fila->estado == 3){
                $nombreEstado = "Entregado/Parcial";
            }else{
                $nombreEstado = "Denegado";
            }
            $fila->nombreEstado = $nombreEstado;



            // VERIFICAR QUE SI TIENE ALGUNA SALIDA ESTA SOLICITUD_DETALLE_MATERIAL
            // PARA OCULTAR EL BOTON DE CAMBIAR REFERENCIA PARA PRODUCTO
            $existeSalida = 0;
            if(BodegaSalidaDetalle::where('id_solidetalle', $fila->id)->first()){
                $existeSalida = 1;
            }
            $fila->existeSalida = $existeSalida;
        }

        return view('backend.admin.bodega.solicitudes.pendientes.detalle.vistadetallesolicitudpendiente',
        compact('idsolicitud', 'arraySinReferencia', 'arrayMateriales', 'arrayReferencia'));
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




        $pilaObjEspeci = array();
        $infoAuth = auth()->user();
        $arrayCodigo = BodegaUsuarioObjEspecifico::where('id_usuario', $infoAuth->id)->get();

        foreach ($arrayCodigo as $fila){
            array_push($pilaObjEspeci, $fila->id_objespecifico);
        }
        // array de materiales
        $arrayMateriales = BodegaMateriales::where('id_objespecifico', $pilaObjEspeci)
        ->orderBy('nombre', 'asc')->get();

        return ['success' => 1, 'info' => $info,
            'nombreUnidad' => $nombreUnidad,
            'nombreMaterial' => $info->nombre,
            'arrayMateriales' => $arrayMateriales,
        ];
    }

    public function asignarReferenciaMaterialSolicitado(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'idmaterial' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        // verificar que no tenga ya referencia
        if($info = BodegaSolicitudDetalle::where('id', $request->id)->first()){
            if ($info->id_referencia == null) {

                BodegaSolicitudDetalle::where('id', $request->id)->update([
                    'id_referencia' => $request->idmaterial
                ]);

                return ['success' => 2];
            }else{
                return ['success' => 1];// ya tiene referencia, asi que error
            }
        }else{
            return ['success' => 99];
        }
    }


    public function noReferenciaEstadoDenegado(Request $request)
    {
        $regla = array(
            'id' => 'required',
        );

        // nota

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(BodegaSolicitudDetalle::where('id', $request->id)->first()){

            BodegaSolicitudDetalle::where('id', $request->id)->update([
                'estado' => 4, // denegado
                'nota' => $request->nota
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 99];
        }
    }

    public function noReferenciaEstadoPendiente(Request $request)
    {
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(BodegaSolicitudDetalle::where('id', $request->id)->first()){

            BodegaSolicitudDetalle::where('id', $request->id)->update([
                'estado' => 1, // pendiente
                'nota' => ""
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 99];
        }
    }


    public function cambiarNuevaReferenciaProducto(Request $request)
    {

        $regla = array(
            'id' => 'required', //  bodega_solicitud_detalle
            'idReferencia' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(BodegaSolicitudDetalle::where('id', $request->id)->first()){

            // NO SE PUEDE CAMBIAR LA REFERENCIA DE PRODUCTO PORQUE YA HIZO UNA SALIDA
            if(BodegaSalidaDetalle::where('id_solidetalle', $request->id)->first()){
                return ['success' => 1];
            }

            BodegaSolicitudDetalle::where('id', $request->id)->update([
                'id_referencia' => $request->idReferencia,
            ]);

            return ['success' => 2];
        }else{
            return ['success' => 99];
        }
    }






    public function modificarEstadoFilaSolicitud(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'idestado' => 'required'
        );

        // nota

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(BodegaSolicitudDetalle::where('id', $request->id)->first()){

            BodegaSolicitudDetalle::where('id', $request->id)->update([
                'estado' => $request->idestado,
                'nota' => $request->nota
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 99];
        }
    }


    public function infoBodegaMaterialLoteDetalleFila(Request $request)
    {
        $regla = array(
            'id' => 'required' // BodegaSolicitudDetalle
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $info = BodegaSolicitudDetalle::where('id', $request->id)->first();
        $infoUnidad = P_UnidadMedida::where('id', $info->id_unidad)->first();
        $nombreUnidad = $infoUnidad->nombre;
        $infoMaterial = BodegaMateriales::where('id', $info->id_referencia)->first();
        $nombreMaterial = $infoMaterial->nombre;

        // listado del mismo material pero de diferentes lotes para retirar

        $listado = BodegaEntradasDetalle::where('id_material', $info->id_referencia)
            ->whereColumn('cantidad_entregada', '<', 'cantidad')
            ->get();

        foreach ($listado as $fila){
            $infoPadre = BodegaEntradas::where('id', $fila->id_entrada)->first();

            $fila->lote = $infoPadre->lote;

            // cantidad actual que hay
            $resta = $fila->cantidad - $fila->cantidad_entregada;
            $fila->cantidadActual = $resta;

            $fecha = date("d-m-Y", strtotime($infoPadre->fecha));
            $fila->fechaIngreso = $fecha;

            $codigoPro = "";
            if($fila->codigo_producto != null){
                $codigoPro = $fila->codigo_producto;
            }

            $fila->codigoPro = $codigoPro;
        }

        return ['success' => 1, 'info' => $info,
            'nombreUnidad' => $nombreUnidad,
            'nombreMaterial' => $nombreMaterial,
            'arrayIngreso' => $listado];
    }


    public function registrarSalidaBodegaSolicitud(Request $request)
    {
        $regla = array(
            'fecha' => 'required',
            'idbodesolidetalle' => 'required' // bodega_solicitud_detalle
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();

        try {

            $datosContenedor = json_decode($request->contenedorArray, true);

            // EVITAR QUE VENGA VACIO
            if($datosContenedor == null){
                return ['success' => 1];
            }

            $infoBodeSoliDetalle = BodegaSolicitudDetalle::where('id', $request->idbodesolidetalle)->first();

            $usuario = auth()->user();

            $reg = new BodegaSalida();
            $reg->fecha = $request->fecha;
            $reg->id_usuario = $usuario->id;
            $reg->id_solicitud = $infoBodeSoliDetalle->id_bodesolicitud;
            $reg->save();

            // infoIdEntradaDetalle, filaCantidadSalida

            foreach ($datosContenedor as $filaArray) {

                // verificar cantidad que hay en la entrada_detalla
                $infoFilaEntradaDetalle = BodegaEntradasDetalle::where('id', $filaArray['infoIdEntradaDetalle'])->first();


                // VERIFICACION:NO SUPERAR LA CANTIDAD_ENTREGADA TOTAL DE ESE MATERIAL-LOTE SEA MAYOR A LA CANTIDAD INGRESADA POR EL BODEGUERO DE ESE MATERIAL-LOTE
                $suma1 = $infoFilaEntradaDetalle->cantidad_entregada + $filaArray['filaCantidadSalida'];
                if($suma1 > $infoFilaEntradaDetalle->cantidad){
                    return ['success' => 2];
                }

                // info de la fila, porque como usamos update, debemos obtener de nuevo la cantidad
                $infoBodeSoliDetalleUpdate = BodegaSolicitudDetalle::where('id', $request->idbodesolidetalle)->first();

                // VERIFICACION: No superar la cantidad que solicito la UNIDAD
                $suma2 = $infoBodeSoliDetalleUpdate->cantidad_entregada + $filaArray['filaCantidadSalida'];
                if($suma2 > $infoBodeSoliDetalleUpdate->cantidad){
                    return ['success' => 3];
                }

                // Pasa validaciones

                // GUARDAR SALIDA DETALLE
                $detalle = new BodegaSalidaDetalle();
                $detalle->id_salida = $reg->id;
                $detalle->id_solidetalle = $request->idbodesolidetalle;
                $detalle->cantidad_salida = $filaArray['filaCantidadSalida'];
                $detalle->id_entradadetalle = $infoFilaEntradaDetalle->id;
                $detalle->save();

                // ACTUALIZAR CANTIDADES DE SALIDA
                BodegaEntradasDetalle::where('id', $filaArray['infoIdEntradaDetalle'])->update([
                    'cantidad_entregada' => ($filaArray['filaCantidadSalida'] + $infoFilaEntradaDetalle->cantidad_entregada)
                ]);

                BodegaSolicitudDetalle::where('id', $request->idbodesolidetalle)->update([
                    'cantidad_entregada' => ($filaArray['filaCantidadSalida'] + $infoBodeSoliDetalleUpdate->cantidad_entregada)
                ]);
            }

            // modificar el estado si es (igual a la cantidad solicitada y entregada)
            $filaDeta = BodegaSolicitudDetalle::where('id', $request->idbodesolidetalle)->first();
            if($filaDeta->cantidad == $filaDeta->cantidad_entregada){
                BodegaSolicitudDetalle::where('id', $request->idbodesolidetalle)->update([
                    'estado' => 2 // entregado
                ]);
            }

            DB::commit();
            return ['success' => 10];
        }catch(\Throwable $e){
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }




    public function infoCuantasSalidasTieneSoliDetalle(Request $request)
    {

        $regla = array(
            'id' => 'required', // bodega_solicitud_detalle
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        if(BodegaSolicitudDetalle::where('id', $request->id)->first()){

            // OBTENER ARRAY DE TODAS LAS SALIDAS
            $arraySalidaDeta = BodegaSalidaDetalle::where('id_solidetalle', $request->id)->get();

            foreach ($arraySalidaDeta as $fila) {
                $infoSalida = BodegaSalida::where('id', $fila->id_salida)->first();
                $fila->fechaFormat = date("d-m-Y", strtotime($infoSalida->fecha));

                $fechaFormat = date("d-m-Y", strtotime($infoSalida->fecha));

                $fila->nombreCompleto = $fechaFormat . " (Entregado: " . $fila->cantidad_salida . ")";
            }

            $arraySalidaDetaSort = $arraySalidaDeta->sortBy('fechaFormat');

            return ['success' => 1, 'arraySalidas' => $arraySalidaDetaSort];
        }else{
            return ['success' => 2];
        }
    }








    //*** SOLICITUDES FINALIZADAS

    public function indexSolicitudesFinalizadas()
    {
        return view('backend.admin.bodega.solicitudes.finalizadas.vistasolicitudfinalizada');
    }

    public function tablaSolicitudesFinalizadas()
    {
        $pilaObjEspeci = array();
        $infoAuth = auth()->user();
        $arrayCodigo = BodegaUsuarioObjEspecifico::where('id_usuario', $infoAuth->id)->get();

        foreach ($arrayCodigo as $fila){
            array_push($pilaObjEspeci, $fila->id_objespecifico);
        }

        $listado = BodegaSolicitud::whereIn('id_objespecifico', $pilaObjEspeci)
            ->where('estado', 1) // finalizadas
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

        return view('backend.admin.bodega.solicitudes.finalizadas.tablasolicitudfinalizada', compact('listado'));
    }


    public function indexDetalleSolicitudesFinalizadas($idsolicitud)
    {

        // AQUI TODOS LAS SOLICITUDES DETALLE, PUEDEN O NO TENER REFERENCIA A MATERIAL
        $arrayReferencia = BodegaSolicitudDetalle::where('id_bodesolicitud', $idsolicitud)
            ->orderBy('nombre', 'asc')
            ->get();

        $contadorFila = 1;
        foreach ($arrayReferencia as $fila){
            $fila->numeralFila = $contadorFila;
            $contadorFila++;

            // PUEDEN NO TENER REFERENCIA, YA QUE FUE DENEGADO ESE PRODUCTO SOLICITADO
            $nombreReferencia = "";
            $unidadMedida = "";
            if($infoMaterial = BodegaMateriales::where('id', $fila->id_referencia)->first()){
                $nombreReferencia = $infoMaterial->nombre;
                $infoUnidad = P_UnidadMedida::where('id', $infoMaterial->id_unidadmedida)->first();
                $unidadMedida = $infoUnidad->nombre;
            }
            $fila->nombreReferencia = $nombreReferencia;
            $fila->unidadMedida = $unidadMedida;


            if($fila->estado == 1){
                $nombreEstado = "Pendiente";
            }
            else if($fila->estado == 2){
                $nombreEstado = "Entregado";
            }
            else if($fila->estado == 3){
                $nombreEstado = "Entregado/Parcial";
            }else{
                $nombreEstado = "Denegado";
            }
            $fila->nombreEstado = $nombreEstado;
        }


        return view('backend.admin.bodega.solicitudes.finalizadas.detalle.vistadetallesolicitudfinalizada',
            compact('idsolicitud',  'arrayReferencia'));
    }



    public function informacionSolicitud(Request $request)
    {
        $regla = array(
            'id' => 'required', // bodega_solicitud
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        if($info = BodegaSolicitud::where('id', $request->id)->first()){


            return ['success' => 1, 'info' => $info];
        }else{
            return ['success' => 2];
        }
    }


    public function editarSolicitudNombre(Request $request)
    {
        $regla = array(
            'id' => 'required', // bodega_solicitud
        );

        // nombre

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        BodegaSolicitud::where('id', $request->id)->update([
            'numero_solicitud' => $request->nombre
        ]);

        return ['success' => 1];
    }


}

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
use App\Models\P_Departamento;
use App\Models\P_UnidadMedida;
use App\Models\P_UsuarioDepartamento;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ObjEspecifico;
use App\Models\UnidadMedida;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BHistorialController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    public function indexHistorialEntradas()
    {
        return view('backend.admin.bodega.historial.entradas.vistaentradabodega');
    }

    public function tablaHistorialEntradas()
    {
        $usuario = auth()->user();
        $listado = BodegaEntradas::where('id_usuario', $usuario->id)
            ->orderBy('fecha', 'desc')
            ->get();

        foreach ($listado as $fila) {
            $fila->fecha = date("d-m-Y", strtotime($fila->fecha));
        }

        return view('backend.admin.bodega.historial.entradas.tablaentradabodega', compact('listado'));
    }


    function informacionDatosEntrada(Request $request)
    {
        $regla = array(
            'id' => 'required', //tabla: bodega_entradas
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        if($info = BodegaEntradas::where('id', $request->id)->first()){
            return ['success' => 1, 'info' => $info];
        }else{
            return ['success' => 2];
        }
    }


    function guardarDatosEntrada(Request $request)
    {
        $regla = array(
            'id' => 'required', //tabla: bodega_entradas
            'fecha' => 'required',
        );

        // observacion, lote

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}


        BodegaEntradas::where('id', $request->id)->update([
            'fecha' => $request->fecha,
            'lote' => $request->lote,
            'observacion' => $request->observacion
        ]);


        return ['success' => 1];
    }





















    public function historialEntradaBorrarLote(Request $request)
    {
        $regla = array(
            'id' => 'required', //tabla: bodega_entradas
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        // VERIFICAR QUE EXISTA LA ENTRADA
        if(BodegaEntradas::where('id', $request->id)->first()){

            DB::beginTransaction();

            try {
                // OBTENER TODOS LOS DETALLES DE ESA ENTRADA
                $arrayEntradaDetalle = BodegaEntradasDetalle::where('id_entrada', $request->id)->get();
                $pilaIdSolicitud = array();
                $pilaIdEntradaDetalle = array();

                foreach ($arrayEntradaDetalle as $fila) {
                    // GUARDAR ID DE CADA ENTRADA DETALLE
                    array_push($pilaIdEntradaDetalle, $fila->id);

                    // TODAS LAS SALIDAS QUE TUVO ESE MATERIAL, YA QUE PUDO HABER SALIDO EN VARIAS SOLICITUDES
                    // DE VARIAS UNIDADES
                    $arraySalidaDeta = BodegaSalidaDetalle::where('id_entradadetalle', $fila->id)->get();

                    foreach ($arraySalidaDeta as $filaSalidaDeta) {

                        // POR CADA FILA SE DEBE OBTENER LA CANTIDAD ENTREGADA A LA UNIDAD
                        // RESTANDOLO CON LA CANTIDAD ENTREGADA EN SALIDA

                        if($filaSalidaDeta->id_solidetalle != null){
                            $infoSoliDeta = BodegaSolicitudDetalle::where('id', $filaSalidaDeta->id_solidetalle)->first();

                            $restaSoliDeta = $infoSoliDeta->cantidad_entregada - $filaSalidaDeta->cantidad_salida;

                            BodegaSolicitudDetalle::where('id', $filaSalidaDeta->id_solidetalle)->update([
                                'cantidad_entregada' => $restaSoliDeta,
                                'estado' => 1 // pendiente
                            ]);

                            array_push($pilaIdSolicitud, $infoSoliDeta->id_bodesolicitud);
                        }else{
                            // COMO ES SALIDA MANUAL SOLO REGRESAR CANTIDAD A BODEGA
                            $infoEntradaDetalle = BodegaEntradasDetalle::where('id', $filaSalidaDeta->id_entradadetalle)->first();
                            $resta = $infoEntradaDetalle->cantidad_entregada - $filaSalidaDeta->cantidad_salida;

                            BodegaEntradasDetalle::where('id', $infoEntradaDetalle->id)->update([
                                'cantidad_entregada' => $resta,
                            ]);
                        }
                    }
                }

                // CAMBIAR ESTADO A PENDIENTE
                BodegaSolicitud::whereIn('id', $pilaIdSolicitud)->update([
                    'estado' => 0, // pasara a pendiente
                ]);




                // BORRAR SALIDAS DETALLE
                BodegaSalidaDetalle::whereIn('id_entradadetalle', $pilaIdEntradaDetalle)->delete();
                // BORRAR SALIDAS
                BodegaSalida::whereNotIn('id', BodegaSalidaDetalle::pluck('id_salida'))->delete();

                // BORRAR ENTRADAS FINALMENTE
                BodegaEntradasDetalle::where('id_entrada', $request->id)->delete();
                BodegaEntradas::where('id', $request->id)->delete();

                DB::commit();
                return ['success' => 1];

            } catch (\Throwable $e) {
                Log::info('ee ' . $e);
                DB::rollback();
                return ['success' => 99];
            }
        }else{
            return ['success' => 99];
        }
    }

    public function historialEntradaDetalleBorrarItem(Request $request)
    {
        $regla = array(
            'id' => 'required', //tabla: bodega_entradas_detalle
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        if($infoEntradaDeta = BodegaEntradasDetalle::where('id', $request->id)->first()){

            DB::beginTransaction();

            try {
                // OBTENER TODOS LOS DETALLES DE ESA ENTRADA
                $pilaIdSolicitud = array();

                // TODAS LAS SALIDAS QUE TUVO ESE MATERIAL, YA QUE PUDO HABER SALIDO EN VARIAS SOLICITUDES
                // DE VARIAS UNIDADES
                $arraySalidaDeta = BodegaSalidaDetalle::where('id_entradadetalle', $infoEntradaDeta->id)->get();

                foreach ($arraySalidaDeta as $filaSalidaDeta) {

                    // POR CADA FILA SE DEBE OBTENER LA CANTIDAD ENTREGADA A LA UNIDAD
                    // RESTANDOLO CON LA CANTIDAD ENTREGADA EN SALIDA
                    // ** ESTO ES SI HAY SALIDAS POR SOLICITUD
                    if($infoSoliDeta = BodegaSolicitudDetalle::where('id', $filaSalidaDeta->id_solidetalle)->first()){
                        $restaSoliDeta = $infoSoliDeta->cantidad_entregada - $filaSalidaDeta->cantidad_salida;

                        BodegaSolicitudDetalle::where('id', $filaSalidaDeta->id_solidetalle)->update([
                            'cantidad_entregada' => $restaSoliDeta,
                            'estado' => 1 // pendiente
                        ]);

                        array_push($pilaIdSolicitud, $infoSoliDeta->id_bodesolicitud);
                    }
                }


                // CAMBIAR ESTADO A PENDIENTE
                BodegaSolicitud::whereIn('id', $pilaIdSolicitud)->update([
                    'estado' => 0, // pasara a pendiente
                ]);




                // BORRAR SALIDAS DETALLE
                BodegaSalidaDetalle::where('id_entradadetalle', $infoEntradaDeta->id)->delete();
                // BORRAR SALIDAS
                BodegaSalida::whereNotIn('id', BodegaSalidaDetalle::pluck('id_salida'))->delete();

                // BORRAR ENTRADAS FINALMENTE
                BodegaEntradasDetalle::where('id', $infoEntradaDeta->id)->delete();

                // SI YA NO HAY ENTRADAS SE DEBERA BORRAR
                BodegaEntradas::whereNotIn('id', BodegaEntradasDetalle::pluck('id_entrada'))->delete();


                DB::commit();
                return ['success' => 1];

            } catch (\Throwable $e) {
                Log::info('ee ' . $e);
                DB::rollback();
                return ['success' => 99];
            }

        }else{
            return ['success' => 99];
        }
    }


    public function informacionItemEntradaDetalle(Request $request)
    {
        $regla = array(
            'id' => 'required', //tabla: bodega_entradas
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        if($info = BodegaEntradasDetalle::where('id', $request->id)->first()){
            return ['success' => 1, 'info' => $info];
        }else{
            return ['success' => 2];
        }
    }


    public function editarItemEntradaDetalle(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'precio' => 'required',
        );

        // codigo

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        BodegaEntradasDetalle::where('id', $request->id)->update([
            'precio' => $request->precio,
            'codigo_producto' => $request->codigo,
        ]);

        return ['success' => 1];
    }
















    public function indexHistorialEntradasDetalle($id)
    {
        $info = BodegaEntradas::where('id', $id)->first();

        return view('backend.admin.bodega.historial.entradas.detalle.vistaentradadetallebodega', compact('id', 'info'));
    }

    public function tablaHistorialEntradasDetalle($id){

        $listado = DB::table('bodega_entradas_detalle AS bo')
            ->join('bodega_materiales AS bm', 'bo.id_material', '=', 'bm.id')
            ->select('bo.id', 'bo.cantidad', 'bo.precio', 'bm.nombre', 'bo.codigo_producto')
            ->where('bo.id_entrada', $id)
            ->get();

        return view('backend.admin.bodega.historial.entradas.detalle.tablaentradadetallebodega', compact('listado'));
    }

    public function indexNuevoIngresoEntradaDetalle($id)
    {
        // id: es de bodega_entrada
        $info = BodegaEntradas::where('id', $id)->first();

        return view('backend.admin.bodega.historial.entradas.detalle.vistaingresoextra', compact('id', 'info'));
    }






    //**************** HISTORIAL DE SALIDAS ************************


    public function indexHistorialSalidas()
    {
        return view('backend.admin.bodega.historial.salidas.vistasalidabodega');
    }

    public function tablaHistorialSalidas()
    {
        $usuario = auth()->user();
        $listado = BodegaSalida::where('id_usuario', $usuario->id)
            ->where('id_solicitud', '!=', null)
            ->orderBy('fecha', 'desc')
            ->get();

        foreach ($listado as $fila) {
            $fila->fecha = date("d-m-Y", strtotime($fila->fecha));

            $infoSoli = BodegaSolicitud::where('id', $fila->id_solicitud)->first();
            $infoObj = ObjEspecifico::where('id', $infoSoli->id_objespecifico)->first();
            $infoUserSolicito = Usuario::where('id', $infoSoli->id_usuario)->first();
            $infoUsuarioDepar = P_UsuarioDepartamento::where('id_usuario', $infoSoli->id_usuario)->first();
            $infoDepa = P_Departamento::where('id', $infoUsuarioDepar->id_departamento)->first();

            $fila->nombreObj = "(" . $infoObj->codigo . ")" . $infoObj->nombre;
            $fila->nombreUser = $infoUserSolicito->nombre;
            $fila->nombreUnidad = $infoDepa->nombre;
            $fila->numeroSolicitud = $infoSoli->numero_solicitud;
        }

        return view('backend.admin.bodega.historial.salidas.tablasalidabodega', compact('listado'));
    }



    public function indexHistorialSalidasDetalle($id)
    {
        return view('backend.admin.bodega.historial.salidas.detalle.vistasalidadetallebodega', compact('id'));
    }

    public function tablaHistorialSalidasDetalle($id){

        $listado = BodegaSalidaDetalle::where('id_salida', $id)->get();

        foreach ($listado as $fila) {

            $infoSoliDetalle = BodegaSolicitudDetalle::where('id', $fila->id_solidetalle)->first();
            $infoProducto = BodegaMateriales::where('id', $infoSoliDetalle->id_referencia)->first();
            $infoObj = ObjEspecifico::where('id', $infoProducto->id_objespecifico)->first();

            $infoEntradaDetalle = BodegaEntradasDetalle::where('id', $fila->id_entradadetalle)->first();
            $fila->codigoProducto = $infoEntradaDetalle->codigo_producto;

            $fila->nombreProducto = $infoProducto->nombre;
            $fila->nombreObj = "(" . $infoObj->codigo . ") " . $infoObj->nombre;
        }

        return view('backend.admin.bodega.historial.salidas.detalle.tablasalidadetallebodega', compact('listado'));
    }


    public function salidaDetalleBorrarItem(Request $request)
    {
        $regla = array(
            'id' => 'required', //tabla: bodega_salidas_detalle
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        if($infoSalidaDeta = BodegaSalidaDetalle::where('id', $request->id)->first()){

            DB::beginTransaction();

            try {

                $infoBodegaEntraDeta = BodegaEntradasDetalle::where('id', $infoSalidaDeta->id_entradadetalle)->first();

                $resta = $infoBodegaEntraDeta->cantidad_entregada - $infoSalidaDeta->cantidad_salida;

                // RESTAR CANTIDAD ENTREGADA
                BodegaEntradasDetalle::where('id', $infoBodegaEntraDeta->id)->update([
                    'cantidad_entregada' => $resta
                ]);

                // RESTAR CANTIDAD EN SOLICITUD DETALLE
                $infoSolicitudDeta = BodegaSolicitudDetalle::where('id', $infoSalidaDeta->id_solidetalle)->first();
                $restaSoli = $infoSolicitudDeta->cantidad_entregada - $infoSalidaDeta->cantidad_salida;



                // CAMBIAR ESTADO A PENDIENTE
                BodegaSolicitud::where('id', $infoSolicitudDeta->id_bodesolicitud)->update([
                    'estado' => 0, // pasara a pendiente
                ]);

                 BodegaSolicitudDetalle::where('id', $infoSolicitudDeta->id)->update([
                     'estado' => 1, // pasara a pendiente
                     'cantidad_entregada' => $restaSoli
                 ]);


                // BORRAR SALIDAS DETALLE
                BodegaSalidaDetalle::where('id', $request->id)->delete();
                // BORRAR SALIDAS (ESTO VERIFICA QUE SINO TIENE DETALLE, ELIMINA EL bodega_salidas)
                BodegaSalida::whereNotIn('id', BodegaSalidaDetalle::pluck('id_salida'))->delete();

                DB::commit();
                return ['success' => 1];

            } catch (\Throwable $e) {
                Log::info('ee ' . $e);
                DB::rollback();
                return ['success' => 99];
            }

        }else{
            return ['success' => 99];
        }
    }




    // ****************** HISTORIAL SALIDAS MANUAL ******************************

    public function indexHistorialSalidasManual()
    {
        return view('backend.admin.bodega.historial.salidamanual.vistasalidamanual');
    }

    public function tablaHistorialSalidasManual()
    {
        $usuario = auth()->user();
        $listado = BodegaSalida::where('id_usuario', $usuario->id)
            ->where('id_solicitud', null)// 0: son salidas por solicitud
            ->orderBy('fecha', 'desc')
            ->get();

        foreach ($listado as $fila) {
            $fila->fecha = date("d-m-Y", strtotime($fila->fecha));
            if($fila->estado_salida == 1){
                $tipoEstado = "SALIDA MANUAL";
            }else if($fila->estado_salida == 2){
                $tipoEstado = "DESPERFECTO";
            }else{
                $tipoEstado = "";
            }

            $fila->tipoEstado = $tipoEstado;
        }

        return view('backend.admin.bodega.historial.salidamanual.tablasalidamanual', compact('listado'));
    }


    public function indexHistorialSalidasManualDetalle($id)
    {
        return view('backend.admin.bodega.historial.salidamanual.detalle.vistasalidadetallemanual', compact('id'));
    }

    public function tablaHistorialSalidasManualDetalle($id){

        $listado = BodegaSalidaDetalle::where('id_salida', $id)->get();

        foreach ($listado as $fila){

            $infoEntraDetalle = BodegaEntradasDetalle::where('id', $fila->id_entradadetalle)->first();
            $infoProducto = BodegaMateriales::where('id', $infoEntraDetalle->id_material)->first();

            $infoEntrada = BodegaEntradas::where('id', $infoEntraDetalle->id_entrada)->first();

            $fila->nombreProducto = $infoProducto->nombre;
            $fila->lote = $infoEntrada->lote;
            $fila->precioProducto = $infoEntraDetalle->precio;
            $fila->codigoProducto = $infoEntraDetalle->codigo_producto;
        }

        return view('backend.admin.bodega.historial.salidamanual.detalle.tablasalidamanualdetalle',
            compact('listado'));
    }






}

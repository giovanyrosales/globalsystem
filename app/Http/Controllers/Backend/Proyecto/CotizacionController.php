<?php

namespace App\Http\Controllers\Backend\Proyecto;

use App\Http\Controllers\Controller;
use App\Models\Administradores;
use App\Models\CatalogoMateriales;
use App\Models\Cotizacion;
use App\Models\CotizacionDetalle;
use App\Models\Orden;
use App\Models\Proveedores;
use App\Models\Proyecto;
use App\Models\Requisicion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class CotizacionController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    public function indexPendiente(){

        return view('Backend.Admin.Cotizaciones.Pendiente.vistaCotizacionPendiente');
    }

    public function indexPendienteTabla(){

        $lista = Cotizacion::where('estado', 0)->orderBy('id', 'ASC')->get();

        foreach ($lista as $dd){

            $infoProveedor = Proveedores::where('id', $dd->proveedor_id)->first();
            $infoRequisicion = Requisicion::where('id', $dd->requisicion_id)->first();
            $infoProyecto = Proyecto::where('id', $infoRequisicion->id_proyecto)->first();

            $dd->proveedor = $infoProveedor->nombre;
            $dd->necesidad = $infoRequisicion->necesidad;
            $dd->destino = $infoRequisicion->destino;
            $dd->codigoproyecto = $infoProyecto->codigo;
        }

        return view('Backend.Admin.Cotizaciones.Pendiente.tablaCotizacionPendiente', compact('lista'));
    }

    public function indexCotizacion($id){ // id de cotizacion

        // destino, necesidad, proveedor, fecha cotizacion
        $cotizacion = Cotizacion::where('id', $id)->first();
        $info = Requisicion::where('id', $cotizacion->requisicion_id)->first();
        $proveedor = Proveedores::where('id', $cotizacion->proveedor_id)->first();

        $detalle = CotizacionDetalle::where('cotizacion_id', $id)->orderBy('cod_presup', 'ASC')->get();
        $conteo = 0;
        foreach ($detalle as $de){
            $conteo += 1;
            $de->conteo = $conteo;

            $infoDescripcion = CatalogoMateriales::where('id', $de->material_id)->first();
            $de->descripcion = $infoDescripcion->nombre;

            $de->precio_u = number_format((float)$de->precio_u, 2, '.', '');
        }

        $estado = $cotizacion->estado;

        return view('Backend.Admin.Cotizaciones.Individual.vistaCotizacionIndividual', compact('id', 'info',
            'proveedor', 'estado', 'detalle'));
    }


    public function actualizarCotizacion(Request $request){

        DB::beginTransaction();

        try {

            if($request->hayregistro == 1){

                // agregar id a pila
                $pila = array();
                for ($i = 0; $i < count($request->idarray); $i++) {
                    // Los id que sean 0, seran nuevos registros
                    if($request->idarray[$i] != 0) {
                        array_push($pila, $request->idarray[$i]);
                    }
                }

                // borrar todos los registros
                // primero obtener solo la lista de cotizacion obtenido de la fila
                // y no quiero que borre los que si vamos a actualizar con los ID
                CotizacionDetalle::where('cotizacion_id', $request->idcotizacion)
                    ->whereNotIn('id', $pila)
                    ->delete();

                // actualizar registros
                for ($i = 0; $i < count($request->cantidadarray); $i++) {
                    if($request->idarray[$i] != 0){
                        CotizacionDetalle::where('id', $request->idarray[$i])->update([
                            'cantidad' => $request->cantidadarray[$i],
                            'precio_u' => $request->preciounitarioarray[$i],
                            'cod_presup' => $request->codpresuparray[$i],
                        ]);
                    }
                }

                DB::commit();
                return ['success' => 1];
            }else{
                // borrar registros
                // solo si viene vacio el array
                if($request->cantidad == null){
                    CotizacionDetalle::where('cotizacion_id', $request->idcotizacion)->delete();
                    Cotizacion::where('id', $request->idcotizacion)->delete();
                }

                DB::commit();
                return ['success' => 2];
            }
        }catch(\Throwable $e){
            Log::info('ee' . $e);
            DB::rollback();
            return ['success' => 2];
        }

    }

    public function borrarCotizacion(Request $request){

        $regla = array(
            'id' => 'required', // id cotizacion
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($info = Cotizacion::where('id', $request->id)->first()){

            if($info->estado == 0){

                CotizacionDetalle::where('cotizacion_id', $request->id)->delete();
                Cotizacion::where('id', $request->id)->delete();
                return ['success' => 1];
            }else{
                return ['success' => 2];
            }
        }else{
            return ['success' => 3];
        }
    }

    public function autorizarCotizacion(Request $request){
        Cotizacion::where('id', $request->id)->update([
            'estado' => 1
        ]);

        return ['success' => 1];
    }

    public function denegarCotizacion(Request $request){
        Cotizacion::where('id', $request->id)->update([
            'estado' => 2
        ]);

        return ['success' => 1];
    }


    public function indexAutorizadas(){

        $contrato = Administradores::orderBy('nombre')->get();

        return view('Backend.Admin.Cotizaciones.Procesada.vistaCotizacionProcesada', compact('contrato'));
    }

    public function indexAutorizadasTabla(){

        // todas las ordenes de cotizacion, para no mostrarlas
        $orden = Orden::all();
        $pila = array();

        foreach ($orden as $dd){
            array_push($pila, $dd->id);
        }

        // autorizadas
        $lista = Cotizacion::where('estado', 1)
            ->whereNotIn('id', $pila)
            ->orderBy('id', 'ASC')
            ->get();

        foreach ($lista as $dd){

            $infoProveedor = Proveedores::where('id', $dd->proveedor_id)->first();
            $infoRequisicion = Requisicion::where('id', $dd->requisicion_id)->first();
            $infoProyecto = Proyecto::where('id', $infoRequisicion->id_proyecto)->first();

            $dd->proveedor = $infoProveedor->nombre;
            $dd->necesidad = $infoRequisicion->necesidad;
            $dd->destino = $infoRequisicion->destino;
            $dd->codigoproyecto = $infoProyecto->codigo;
        }

        return view('Backend.Admin.Cotizaciones.Procesada.tablaCotizacionProcesada', compact('lista'));
    }

    public function indexDenegadas(){
        return view('Backend.Admin.Cotizaciones.Denegadas.vistaCotizacionDenegada');
    }

    public function indexDenegadasTabla(){

        // denegadas
        $lista = Cotizacion::where('estado', 2)->orderBy('id', 'ASC')->get();

        foreach ($lista as $dd){

            $infoProveedor = Proveedores::where('id', $dd->proveedor_id)->first();
            $infoRequisicion = Requisicion::where('id', $dd->requisicion_id)->first();
            $infoProyecto = Proyecto::where('id', $infoRequisicion->id_proyecto)->first();

            $dd->proveedor = $infoProveedor->nombre;
            $dd->necesidad = $infoRequisicion->necesidad;
            $dd->destino = $infoRequisicion->destino;
            $dd->codigoproyecto = $infoProyecto->codigo;
        }

        return view('Backend.Admin.Cotizaciones.Denegadas.tablaCotizacionDenegada', compact('lista'));
    }


}

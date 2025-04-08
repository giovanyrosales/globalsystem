<?php

namespace App\Http\Controllers\Backend\Bodega;

use App\Http\Controllers\Controller;
use App\Models\BodegaEntradas;
use App\Models\BodegaEntradasDetalle;
use App\Models\BodegaMateriales;
use App\Models\BodegaSalida;
use App\Models\BodegaSalidaDetalle;
use App\Models\BodegaUsuarioObjEspecifico;
use App\Models\P_UnidadMedida;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ObjEspecifico;
use App\Models\UnidadMedida;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BMaterialesController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    // retorna vista de materiales de la seccion de bodega
    public function indexBodegaMateriales()
    {

        $pilaObjEspeci = array();
        $infoAuth = auth()->user();
        $arrayCodigo = BodegaUsuarioObjEspecifico::where('id_usuario', $infoAuth->id)->get();

        foreach ($arrayCodigo as $fila) {
            array_push($pilaObjEspeci, $fila->id_objespecifico);
        }

        $unidadmedida = UnidadMedida::orderBy('medida', 'ASC')->get();
        $objespecifico = ObjEspecifico::whereIn('id', $pilaObjEspeci)
            ->orderBy('codigo', 'ASC')
            ->get();

        return view('backend.admin.bodega.materiales.vistamateriales', compact('unidadmedida', 'objespecifico'));
    }

    // retorna tabla de materiales registrados
    public function tablaMateriales()
    {

        // solo puedo ver los codigos de mi usuario asignado
        $pilaObjEspeci = array();
        $infoAuth = auth()->user();
        $arrayCodigo = BodegaUsuarioObjEspecifico::where('id_usuario', $infoAuth->id)->get();

        foreach ($arrayCodigo as $fila) {
            array_push($pilaObjEspeci, $fila->id_objespecifico);
        }

        $lista = BodegaMateriales::whereIn('id_objespecifico', $pilaObjEspeci)
            ->orderBy('nombre', 'ASC')
            ->get();

        foreach ($lista as $fila) {
            $infoUnidadMedida = UnidadMedida::where('id', $fila->id_unidadmedida)->first();
            $fila->id_unidadmedida = $infoUnidadMedida->medida;
            $infoObjEspecifico = ObjEspecifico::where('id', $fila->id_objespecifico)->first();
            $fila->id_objespecifico = $infoObjEspecifico->codigo . " - " . $infoObjEspecifico->nombre;

            // CANTIDAD GLOBAL QUE TENGO DE ESE PRODUCTO
            $totalCantidadMate = BodegaEntradasDetalle::where('id_material', $fila->id)->sum('cantidad');
            $totalCantidadEntregada = BodegaEntradasDetalle::where('id_material', $fila->id)->sum('cantidad_entregada');

            $fila->cantidadGlobal = ($totalCantidadMate - $totalCantidadEntregada);
        }

        return view('backend.admin.bodega.materiales.tablamateriales', compact('lista'));
    }

    // registrar un nuevo material // No incluye cantidad ni precio porque esta no se si ira aqui aun
    public function nuevoMaterial(Request $request)
    {

        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $dato = new BodegaMateriales();
        $dato->nombre = $request->nombre;
        $dato->id_unidadmedida = $request->id_unidadmedida;
        $dato->id_objespecifico = $request->id_objespecifico;

        if ($dato->save()) {
            return ['success' => 1];
        } else {
            return ['success' => 2];
        }
    }

    // obtener información de un material en especifico
    public function informacionMaterial(Request $request)
    {
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if ($lista = BodegaMateriales::where('id', $request->id)->first()) {

            $pilaObjEspeci = array();
            $infoAuth = auth()->user();
            $arrayCodigo = BodegaUsuarioObjEspecifico::where('id_usuario', $infoAuth->id)->get();

            foreach ($arrayCodigo as $fila) {
                array_push($pilaObjEspeci, $fila->id_objespecifico);
            }


            $objespecifico = ObjEspecifico::whereIn('id', $pilaObjEspeci)->orderBy('codigo')->get();
            $unidadmedida = UnidadMedida::orderBy('id')->get();

            return ['success' => 1, 'lista' => $lista, 'obj' => $objespecifico, 'um' => $unidadmedida];
        } else {
            return ['success' => 2];
        }
    }

    // editar un material
    public function editarMaterial(Request $request)
    {

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'id_unidadmedida' => 'required',
            'id_objespecifico' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if (BodegaMateriales::where('id', $request->id)->first()) {

            BodegaMateriales::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'id_unidadmedida' => $request->id_unidadmedida,
                'id_objespecifico' => $request->id_objespecifico
            ]);

            return ['success' => 1];
        } else {
            return ['success' => 2];
        }
    }


    //********* DETALLE PARA VER CANTIDAD DE CADA MATERIAL EN CADA LOTE ****************

    public function indexDetalleMaterialCantidad($idmaterial)
    {
        return view('backend.admin.bodega.materiales.detalle.vistadetallematerial', compact('idmaterial'));
    }


    // listado de mateirales que tienen cantidad aun disponible de cada lote
    public function tablaDetalleMaterialCantidad($idmaterial)
    {

        $listado = BodegaEntradasDetalle::where('id_material', $idmaterial)
            ->whereColumn('cantidad_entregada', '<', 'cantidad')
            ->get();

        foreach ($listado as $fila) {
            $infoEntrada = BodegaEntradas::where('id', $fila->id_entrada)->first();
            $fila->fecha = date("d-m-Y", strtotime($infoEntrada->fecha));
            $fila->lote = $infoEntrada->lote;
            $fila->precio = "$" . $fila->precio;

            $fila->cantidadDisponible = ($fila->cantidad - $fila->cantidad_entregada);
        }

        return view('backend.admin.bodega.materiales.detalle.tabladetallematerial', compact('listado'));
    }


    //******************* REGISTRO DE ENTRADAS **********************

    public function indexEntradasRegistro(Request $request)
    {

        return view('backend.admin.bodega.entradasregistro.vistaentradasregistro');
    }

    public function buscarProducto(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');

            $pilaObjEspeci = array();
            $infoAuth = auth()->user();
            $arrayCodigo = BodegaUsuarioObjEspecifico::where('id_usuario', $infoAuth->id)->get();

            foreach ($arrayCodigo as $fila) {
                array_push($pilaObjEspeci, $fila->id_objespecifico);
            }


            $data = BodegaMateriales::where('nombre', 'LIKE', "%{$query}%")
                ->whereIn('id_objespecifico', $pilaObjEspeci)
                ->get();

            $output = '<ul class="dropdown-menu" style="display:block; position:relative; overflow: auto; max-height: 300px; width: 550px">';
            $tiene = true;
            foreach ($data as $row) {
                $infoUnidad = P_UnidadMedida::where('id', $row->id_unidadmedida)->first();
                $nombreCompleto = $row->nombre . " (" . $infoUnidad->nombre . ")";

                // si solo hay 1 fila, No mostrara el hr, salto de linea
                if (count($data) == 1) {
                    if (!empty($row)) {
                        $tiene = false;
                        $output .= '
                 <li class="cursor-pointer" onclick="modificarValor(this)" id="' . $row->id . '">' . $nombreCompleto . '</li>
                ';
                    }
                } else {
                    if (!empty($row)) {
                        $tiene = false;
                        $output .= '
                 <li class="cursor-pointer" onclick="modificarValor(this)" id="' . $row->id . '">' . $nombreCompleto . '</li>
                   <hr>
                ';
                    }
                }
            }
            $output .= '</ul>';
            if ($tiene) {
                $output = '';
            }
            echo $output;
        }
    }


    public function registrarProductos(Request $request)
    {
        $regla = array(
            'fecha' => 'required',
        );

        // lote, observacion

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            // Obtiene los datos enviados desde el formulario como una cadena JSON y luego decódificala
            $datosContenedor = json_decode($request->contenedorArray, true); // El segundo argumento convierte el resultado en un arreglo

            $usuario = auth()->user();

            $nuevoReg = new BodegaEntradas();
            $nuevoReg->id_usuario = $usuario->id;
            $nuevoReg->fecha = $request->fecha;
            $nuevoReg->lote = $request->lote;
            $nuevoReg->observacion = $request->observacion;
            $nuevoReg->save();

            // infoIdProducto, infoCantidad, infoPrecio, infoCodigoProducto

            foreach ($datosContenedor as $filaArray) {

                $infoProducto = BodegaMateriales::where('id', $filaArray['infoIdProducto'])->first();

                $detalle = new BodegaEntradasDetalle();
                $detalle->id_entrada = $nuevoReg->id;
                $detalle->id_material = $filaArray['infoIdProducto'];
                $detalle->cantidad = $filaArray['infoCantidad'];
                $detalle->precio = $filaArray['infoPrecio'];
                $detalle->codigo_producto = $filaArray['infoCodigoProducto'];
                $detalle->nombre_copia = $infoProducto->nombre;
                $detalle->cantidad_entregada = 0;
                $detalle->save();
            }

            DB::commit();
            return ['success' => 1];

        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function registrarProductosExtras(Request $request)
    {
        $regla = array(
            'identrada' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            // Obtiene los datos enviados desde el formulario como una cadena JSON y luego decódificala
            $datosContenedor = json_decode($request->contenedorArray, true); // El segundo argumento convierte el resultado en un arreglo

            foreach ($datosContenedor as $filaArray) {

                $infoProducto = BodegaMateriales::where('id', $filaArray['infoIdProducto'])->first();

                $detalle = new BodegaEntradasDetalle();
                $detalle->id_entrada = $request->identrada;
                $detalle->id_material = $filaArray['infoIdProducto'];
                $detalle->cantidad = $filaArray['infoCantidad'];
                $detalle->precio = $filaArray['infoPrecio'];
                $detalle->codigo_producto = $filaArray['infoCodigo'];
                $detalle->nombre_copia = $infoProducto->nombre;
                $detalle->cantidad_entregada = 0;
                $detalle->save();
            }

            DB::commit();
            return ['success' => 1];

        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    //********* SALIDAS MANUAL ************

    public function indexSalidasManual()
    {
        // LISTADO DE PRODUCTOS QUE TIENEN CANTIDAD DISPONIBLE DE SALIDA
        $pilaObjEspeci = array();
        $infoAuth = auth()->user();
        $arrayCodigo = BodegaUsuarioObjEspecifico::where('id_usuario', $infoAuth->id)->get();

        foreach ($arrayCodigo as $fila){
            array_push($pilaObjEspeci, $fila->id_objespecifico);
        }

        $arrayMateriales = BodegaMateriales::whereIn('id_objespecifico', $pilaObjEspeci)->get();
        $pilaIdMaterial = array();
        foreach ($arrayMateriales as $fila){
            array_push($pilaIdMaterial, $fila->id);
        }

        $arrayEntraDeta = BodegaEntradasDetalle::whereIn('id_material', $pilaIdMaterial)
            ->whereColumn('cantidad_entregada', '<', 'cantidad')
            ->get();

        foreach ($arrayEntraDeta as $fila){

            $infoMaterial = BodegaMateriales::where('id', $fila->id_material)->first();
            $infoEntrada = BodegaEntradas::where('id', $fila->id_entrada)->first();
            $fila->nombreMaterial = "LOTE: " . $infoEntrada->lote . " (" . $infoMaterial->nombre . ")";

            // CANTIDAD RESTANTE QUE QUEDA DISPONIBLE
            $resta = $fila->cantidad - $fila->cantidad_entregada;
            $fila->cantidadRestante = $resta;
        }

        return view('backend.admin.bodega.salidamanual.vistasalidamanual', compact('arrayEntraDeta'));
    }


    public function registrarSalidaManual(Request $request)
    {
        $regla = array(
            'fecha' => 'required',
            'tiposalida' => 'required',
        );

        // observacion

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            // Obtiene los datos enviados desde el formulario como una cadena JSON y luego decódificala
            $datosContenedor = json_decode($request->contenedorArray, true); // El segundo argumento convierte el resultado en un arreglo

            $usuario = auth()->user();

            $nuevoReg = new BodegaSalida();
            $nuevoReg->fecha = $request->fecha;
            $nuevoReg->id_usuario = $usuario->id;
            $nuevoReg->id_solicitud = null; // NO LLEVARA
            $nuevoReg->observacion = $request->observacion;
            $nuevoReg->estado_salida = $request->tiposalida;
            $nuevoReg->save();

            // infoIdProducto, infoCantidad, infoPrecio
            $contaFila = 0;
            foreach ($datosContenedor as $filaArray) {
                $contaFila++;
                // VERIFICAR QUE NO SUPERE CANTIDAD
                $infoBodeEnDeta = BodegaEntradasDetalle::where('id', $filaArray['infoIdProducto'])->first();
                $suma = $filaArray['infoCantidad'] + $infoBodeEnDeta->cantidad_entregada;

                if($suma > $infoBodeEnDeta->cantidad){

                    // SUPERA LA CANTIDAD
                    return ['success' => 1, 'fila' => $contaFila];
                }

                $detalle = new BodegaSalidaDetalle();
                $detalle->id_salida = $nuevoReg->id;
                $detalle->id_solidetalle = null;
                $detalle->id_entradadetalle = $filaArray['infoIdProducto'];
                $detalle->cantidad_salida = $filaArray['infoCantidad'];
                $detalle->save();

                // ACTUALIZAR CANTIDAD
                Log::info("suma: " . $suma);

                BodegaEntradasDetalle::where('id', $infoBodeEnDeta->id)->update([
                    'cantidad_entregada' => $suma,
                ]);
            }

            DB::commit();
            return ['success' => 2];

        } catch (\Throwable $e) {
            Log::info('error ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }



    public function salidaManualDetalleBorrarItem(Request $request)
    {
        $regla = array(
            'id' => 'required', //tabla: bodega_salidamanual_detalle
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        if($infoSalidaDeta = BodegaSalidaDetalle::where('id', $request->id)->first()){

            DB::beginTransaction();

            try {

                $infoBodegaEntraDeta = BodegaEntradasDetalle::where('id', $infoSalidaDeta->id_entradadetalle)->first();

                $resta = $infoBodegaEntraDeta->cantidad_entregada - $infoSalidaDeta->cantidad;

                // RESTAR CANTIDAD ENTREGADA
                BodegaEntradasDetalle::where('id', $infoBodegaEntraDeta->id)->update([
                    'cantidad_entregada' => $resta
                ]);

                // BORRAR SALIDA MANUAL DETALLE
                BodegaSalidaDetalle::where('id', $request->id)->delete();
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


}

<?php

namespace App\Http\Controllers\Backend\Bodega;

use App\Http\Controllers\Controller;
use App\Models\BodegaEntradas;
use App\Models\BodegaEntradasDetalle;
use App\Models\BodegaMateriales;
use App\Models\P_UnidadMedida;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ObjEspecifico;
use App\Models\UnidadMedida;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BHistorialController extends Controller
{

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


    public function historialEntradaBorrarLote(Request $request)
    {
        $regla = array(
            'id' => 'required', //tabla: bodega_entradas
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        if(BodegaEntradas::where('id', $request->id)->first()){

            // verificar que no haya salidas de ningun item para borrar el lote completo
            // y restar la cantidades

            return ['success' => 1];
        }else{
            return ['success' => 99];
        }
    }

    public function historialEntradaDetalleBorrarItem(Request $request)
    {
        $regla = array(
            'id' => 'required', //tabla: bodega_entradas
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        if(BodegaEntradasDetalle::where('id', $request->id)->first()){

            // verificar que no haya salidas del item para borrar
            // y restar la cantidades



            return ['success' => 1];
        }else{
            return ['success' => 99];
        }
    }


    public function indexHistorialEntradasDetalle($id)
    {

        return view('backend.admin.bodega.historial.entradas..detalle.vistaentradadetallebodega', compact('id'));
    }

    public function tablaHistorialEntradasDetalle($id){

        $listado = DB::table('bodega_entradas_detalle AS bo')
            ->join('bodega_materiales AS bm', 'bo.id_material', '=', 'bm.id')
            ->select('bo.id', 'bo.cantidad', 'bo.precio', 'bm.nombre')
            ->where('bo.id_entrada', $id)
            ->get();

        return view('backend.admin.bodega.historial.entradas.detalle.tablaentradadetallebodega', compact('listado'));
    }





}

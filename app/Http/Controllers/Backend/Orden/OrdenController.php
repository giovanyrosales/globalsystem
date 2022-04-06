<?php

namespace App\Http\Controllers\Backend\Orden;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use App\Models\Orden;
use App\Models\Proyecto;
use App\Models\Requisicion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class OrdenController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function generarOrden(Request $request){

        $regla = array(
            'fecha' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        $or = new Orden();
        $or->admin_contrato_id = $request->admin;
        $or->cotizacion_id = $request->idcoti;
        $or->fecha_orden = $request->fecha;
        $or->lugar = $request->lugar;
        $or->estado = 0;

        if($or->save()){
            return ['success' => 1, 'id' => $or->id];
        }else{
            return ['success' => 2];
        }
    }

    public function vistaPdfOrden($id){ // id de la orden

        $orden = Orden::where('id', $id)->first();
        $cotizacion = Cotizacion::where('id', $orden->cotizacion_id)->first();

        $requisicion = Requisicion::where('id',  $cotizacion->requisicion_id)->first();
        //$cotizacion = DB::table('cotizacion')->where('id',  $orden->cotizacion_id)->first();
        $proyecto =  Proyecto::where('id',  $requisicion->id_proyecto)->first();


        $proveedor =  DB::table('proveedores')->where('id',  $cotizacion->proveedor_id)->first();
        $det_cotizacion = DB::table('det_cotizacion')->where('cotizacion_id',  $orden->cotizacion_id)->get();
        $administrador = DB::table('admin_contrato')->where('id',  $orden->admin_contrato_id)->first();

        //$fecha = strftime("%d-%B-%Y", strtotime($orden->fechaorden));
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $fecha = array(date("d", strtotime($orden->fechaorden) ), $meses[date("n", strtotime($orden->fechaorden) )-1], date("Y", strtotime($orden->fechaorden) ) );

        $pdf = PDF::loadView('backend.reportes.orden_compra', compact('orden','cotizacion','proyecto','fecha','proveedor','det_cotizacion','administrador'));
        $customPaper = array(0,0,470.61,612.36);
        $pdf->setPaper($customPaper)->setWarnings(false);
        return $pdf->stream('Orden_Compra.pdf');
    }

}

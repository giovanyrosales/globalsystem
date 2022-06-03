<?php

namespace App\Http\Controllers\Backend\Orden;

use App\Http\Controllers\Controller;
use App\Models\Acta;
use App\Models\Administradores;
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
use Barryvdh\DomPDF\Facade\Pdf;

// Definir zona horaria o region.
date_default_timezone_set('America/El_Salvador');
setlocale(LC_TIME, "spanish");

//Estados de las Ordenes
// 0 - Default
// 1 - Activa
// 2 - Anulada

//Estados de las Cotizaciones
// 0 - Default
// 1 - Aprobada
// 2 - Denegada

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

        if($info = Orden::where('cotizacion_id', $request->idcoti)->first()){
            // no hacer nada, evita ordenes duplicadas
            return ['success' => 1, 'id' => $info->id];
        }else{
            $or = new Orden();
            $or->admin_contrato_id = $request->admin;
            $or->cotizacion_id = $request->idcoti;
            $or->fecha_orden = $request->fecha;
            $or->lugar = $request->lugar;
            $or->estado = 0;
            $or->save();
            return ['success' => 1, 'id' => $or->id];
        }
    }

    public function vistaPdfOrden($id){ // id de la orden

        $orden = Orden::where('id', $id)->first();
        $cotizacion = Cotizacion::where('id', $orden->cotizacion_id)->first();
        $requisicion = Requisicion::where('id',  $cotizacion->requisicion_id)->first();
        $proyecto =  Proyecto::where('id',  $requisicion->id_proyecto)->first();
        $proveedor =  Proveedores::where('id',  $cotizacion->proveedor_id)->first();
        $det_cotizacion = CotizacionDetalle::where('cotizacion_id',  $orden->cotizacion_id)->get();
        $administrador = Administradores::where('id',  $orden->admin_contrato_id)->first();

        //$fecha = strftime("%d-%B-%Y", strtotime($orden->fechaorden));
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $fecha = array(date("d", strtotime($orden->fecha_orden) ), $meses[date("n", strtotime($orden->fecha_orden) )-1], date("Y", strtotime($orden->fecha_orden) ) );

        $pdf = PDF::loadView('Backend.Admin.Reportes.pdfOrdenCompra', compact('orden','cotizacion','proyecto','fecha','proveedor','det_cotizacion','administrador'));
        //$customPaper = array(0,0,470.61,612.36);
        $customPaper = array(0,0,470.61,612.36);
        $pdf->setPaper($customPaper)->setWarnings(false);
        return $pdf->stream('Orden_Compra.pdf');
    }

    public function indexOrdenesCompras(){

        return view('Backend.Admin.Ordenes.vistaOrdenesCompra');
    }

    public function tablaOrdenesCompras(){

        $lista = orden::orderBy('fecha_orden')->get();

        foreach($lista as $val){
            $infoContizacion = Cotizacion::where('id', $val->cotizacion_id)->first();
            $infoRequisicion = Requisicion::where('id', $infoContizacion->requisicion_id)->first();
            $infoProveedor = Proveedores::where('id', $infoContizacion->proveedor_id)->first();
            $proyecto = Proyecto::where('id', $infoContizacion->proveedor_id)->first();
            //metemos nuevas variables en el arreglo $regdetalle
            $val->proyecto_cod = $proyecto->codigo;

            if($infoacta = Acta::where('orden_id', $val->id)->first()){
                $val->actaid = $infoacta->id;
            }else{
                $val->actaid = 0;
            }

            $val->requisicion_id = $infoContizacion->requisicion_id;
            $val->requidestino = $infoRequisicion->destino;
            $val->nomproveedor = $infoProveedor->nombre;
        }

        return view('Backend.Admin.Ordenes.tablaOrdenesCompra', compact('lista'));
    }

    public function anularCompra(Request $request){

        if(Orden::where('id', $request->id)->first()){

            Orden::where('id', $request->id)->update([
                'estado' => 1]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    function generarActa(Request $request){

        $regla = array(
            'idacta' => 'required',
            'horaacta' => 'required',
            'fechaacta' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0]; }

        $acta = new Acta();
        $acta->orden_id = $request->idacta;
        $acta->fecha_acta = $request->fechaacta;
        $acta->hora = $request->horaacta;
        $acta->estado = 1; // acta generada

        if($acta->save()) {
            return ['success' => 1, 'actaid'=> $acta->id];
        }else {
            return ['success' => 2];
        }
    }

    public function reporteActaGenerada($id){

        $acta = Acta::where('id',  $id)->first();
        $orden = Orden::where('id',  $acta->orden_id)->first();
        $cotizacion = Cotizacion::where('id', $orden->cotizacion_id)->first();
        $proveedor =  Proveedores::where('id',  $cotizacion->proveedor_id)->first();
        $administrador = Administradores::where('id',  $orden->admin_contrato_id)->first();
        $requisicion = Requisicion::where('id',  $cotizacion->requisicion_id)->first();
        $proyecto = Proyecto::where('id',  $requisicion->id_proyecto)->first();

        $fecha = strftime("%d-%B-%Y",strtotime($acta->fecha_acta));
        $hora = $acta->hora;

        $pdf = PDF::loadView('Backend.Admin.Reportes.pdfActaOrdenCompra', compact('acta','proyecto','fecha','proveedor','administrador','hora','orden'));
        $pdf->setPaper('letter', 'portrait')->setWarnings(false);
        return $pdf->stream('acta_orden_compra.pdf');
    }


}

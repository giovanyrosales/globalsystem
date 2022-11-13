<?php

namespace App\Http\Controllers\Backend\PresupuestoUnidad\Cotizaciones;

use App\Http\Controllers\Controller;
use App\Models\P_Departamento;
use App\Models\P_Materiales;
use App\Models\P_PresupUnidad;
use App\Models\P_UnidadMedida;
use App\Models\Proveedores;
use App\Models\RequisicionUnidad;
use App\Models\RequisicionUnidadDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CotizacionesUnidadController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    public function indexListarRequerimientosPendienteUnidad(){

        $proveedores = Proveedores::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.presupuestounidad.requerimientos.requerimientosunidad.revision.vistarequerimientosunidadrevision', compact('proveedores'));
    }

    public function indexTablaListarRequerimientosPendienteUnidad(){

        $data = DB::table('requisicion_unidad AS r')
            ->join('requisicion_unidad_detalle AS d', 'd.id_requisicion_unidad', '=', 'r.id')
            ->select('r.id')
            ->where('d.estado', 0)
            ->where('d.cancelado', 0)
            ->groupBy('r.id')
            ->get();

        $pilaIdRequisicion = array();

        foreach ($data as $dd){
            array_push($pilaIdRequisicion, $dd->id);
        }

        $listaRequisicion = RequisicionUnidad::whereIn('id', $pilaIdRequisicion)
            ->orderBy('fecha', 'DESC')
            ->get();

        foreach ($listaRequisicion as $ll){
            $ll->fecha = date("d-m-Y", strtotime($ll->fecha));

            $infoPresup = P_PresupUnidad::where('id', $ll->id_presup_unidad)->first();
            $infoDepar = P_Departamento::where('id', $infoPresup->id_departamento)->first();

            $ll->departamento = $infoDepar->nombre;
        }

        return view('backend.admin.presupuestounidad.requerimientos.requerimientosunidad.revision.tablarequerimientosunidadrevision', compact('listaRequisicion'));
    }

    // informacion de requisición para hacer la cotizacion
    public function informacionRequerimientoCotizarInfo(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($info = RequisicionUnidad::where('id', $request->id)->first()){

            $listado = RequisicionUnidadDetalle::where('id_requisicion_unidad', $request->id)
                ->where('estado', 0)
                ->where('cancelado', 0)
                ->get();

            foreach ($listado as $l){
                $data = P_Materiales::where('id', $l->id_material)->first();
                $data2 = P_UnidadMedida::where('id', $data->id_unidadmedida)->first();

                $l->nombre = $data->descripcion;
                $l->medida = $data2->nombre;
            }

            return ['success' => 1, 'info' => $info, 'listado' => $listado];
        }else{
            return ['success' => 2];
        }
    }


}

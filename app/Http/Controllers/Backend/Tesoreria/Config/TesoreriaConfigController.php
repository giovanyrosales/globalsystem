<?php

namespace App\Http\Controllers\Backend\Tesoreria\Config;

use App\Http\Controllers\Controller;
use App\Models\P_Departamento;
use App\Models\TesoreriaGarantia;
use App\Models\TesoreriaGarantiaPendienteEntrega;
use App\Models\TesoreriaProveedores;
use App\Models\TesoreriaTipoGarantia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TesoreriaConfigController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }


    public function indexProveedor(){
        return view('backend.admin.tesoreria.config.proveedores.vistaproveedores');
    }

    public function tablaProveedor(){

        $listado = TesoreriaProveedores::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.tesoreria.config.proveedores.tablaproveedores', compact('listado'));
    }

    public function nuevoProveedor(Request $request){

        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();
        try {

            $registro = new TesoreriaProveedores();
            $registro->nombre = $request->nombre;
            $registro->save();

            DB::commit();
            return ['success' => 1];

        }catch(\Throwable $e){
            Log::info('error: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // informacion
    public function informacionProveedor(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = TesoreriaProveedores::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar
    public function actualizarProveedor(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(TesoreriaProveedores::where('id', $request->id)->first()){

            TesoreriaProveedores::where('id', $request->id)->update([
                'nombre' => $request->nombre,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }





    //*****************  GARANTIAS ************************************



    public function indexGarantia(){
        return view('backend.admin.tesoreria.config.garantia.vistagarantia');
    }

    public function tablaGarantia(){

        $listado = TesoreriaGarantia::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.tesoreria.config.garantia.tablagarantia', compact('listado'));
    }

    public function nuevoGarantia(Request $request){

        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();
        try {

            $registro = new TesoreriaGarantia();
            $registro->nombre = $request->nombre;
            $registro->save();

            DB::commit();
            return ['success' => 1];

        }catch(\Throwable $e){
            Log::info('error: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // informacion
    public function informacionGarantia(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = TesoreriaGarantia::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar
    public function actualizarGarantia(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(TesoreriaGarantia::where('id', $request->id)->first()){

            TesoreriaGarantia::where('id', $request->id)->update([
                'nombre' => $request->nombre,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }







    //*****************  TIPO DE GARANTIAS ************************************



    public function indexTipoGarantia(){
        return view('backend.admin.tesoreria.config.tipogarantia.vistatipogarantia');
    }

    public function tablaTipoGarantia(){

        $listado = TesoreriaTipoGarantia::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.tesoreria.config.tipogarantia.tablatipogarantia', compact('listado'));
    }

    public function nuevoTipoGarantia(Request $request){

        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();
        try {

            $registro = new TesoreriaTipoGarantia();
            $registro->nombre = $request->nombre;
            $registro->save();

            DB::commit();
            return ['success' => 1];

        }catch(\Throwable $e){
            Log::info('error: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // informacion
    public function informacionTipoGarantia(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = TesoreriaTipoGarantia::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar
    public function actualizarTipoGarantia(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(TesoreriaTipoGarantia::where('id', $request->id)->first()){

            TesoreriaTipoGarantia::where('id', $request->id)->update([
                'nombre' => $request->nombre,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }



    //***************** NUEVO REGISTRO TESORERIA *********************************

    public function indexRegistroTesoreria(){

        $arrayProveedor = TesoreriaProveedores::orderBy('nombre', 'ASC')->get();
        $arrayGarantia = TesoreriaGarantia::orderBy('nombre', 'ASC')->get();
        $arrayTipoGarantia = TesoreriaTipoGarantia::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.tesoreria.registro.nuevoregistro', compact('arrayProveedor',
            'arrayGarantia', 'arrayTipoGarantia'));
    }


    public function nuevoRegistroTesoreria(Request $request)
    {

        DB::beginTransaction();
        try {

            $registro = new TesoreriaGarantiaPendienteEntrega();
            $registro->control_interno = $request->numcontrol;
            $registro->referencia = $request->referencia;
            $registro->descripcion_licitacion = $request->descripcion;
            $registro->id_proveedor = $request->proveedor;
            $registro->id_garantia = $request->garantia;
            $registro->id_tipo_garantia = $request->tipogarantia;
            $registro->monto_garantia = $request->monto;
            $registro->aseguradora = $request->aseguradora;
            $registro->vigencia_desde = $request->fechadesde;
            $registro->vigencia_hasta = $request->fechahasta;
            $registro->fecha_recibida = $request->fecharecibida;
            $registro->fecha_entrega = $request->fechaentrega;
            $registro->fecha_entrega_ucp = $request->fechaucp;
            $registro->save();

            DB::commit();
            return ['success' => 1];

        }catch(\Throwable $e){
            Log::info('error: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }



    public function vistaListadoRegistros()
    {

        $departamentos = P_Departamento::all();

        return view('backend.admin.tesoreria.listado.vistalistado', compact('departamentos'));
    }

    public function tablaListadoRegistros()
    {
        $listado = TesoreriaGarantiaPendienteEntrega::orderBy('control_interno', 'ASC')->get();

        foreach ($listado as $item) {

            $infoProveedor = TesoreriaProveedores::where('id', $item->id_proveedor)->first();
            $infoGarantia = TesoreriaGarantia::where('id', $item->id_garantia)->first();
            $infoTipoGarantia = TesoreriaTipoGarantia::where('id', $item->id_tipo_garantia)->first();

            $item->proveedor = $infoProveedor->nombre;
            $item->garantia = $infoGarantia->nombre;
            $item->tipoGarantia = $infoTipoGarantia->nombre;

            if($item->monto_garantia != null){
                $item->monto = '$' . number_format((float)$item->monto_garantia, 2, '.', ',');
            }


            if($item->vigencia_desde != null){
                $item->vigencia_desde = date('d-m-Y', strtotime($item->vigencia_desde));
            }

            if($item->vigencia_hasta != null){
                $item->vigencia_hasta = date('d-m-Y', strtotime($item->vigencia_hasta));
            }

            if($item->fecha_recibida != null){
                $item->fecha_recibida = date('d-m-Y', strtotime($item->fecha_recibida));
            }

            if($item->fecha_entrega != null){
                $item->fecha_entrega = date('d-m-Y', strtotime($item->fecha_entrega));
            }

            if($item->fecha_entrega_ucp != null){
                $item->fecha_entrega_ucp = date('d-m-Y', strtotime($item->fecha_entrega_ucp));
            }
        }

        return view('backend.admin.tesoreria.listado.tablalistado', compact('listado'));
    }

    public function borrarRegistro(Request $request)
    {
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        TesoreriaGarantiaPendienteEntrega::where('id', $request->id)->delete();

        return ['success' => 1];
    }


    public function informacionRegistro(Request $request)
    {
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $info = TesoreriaGarantiaPendienteEntrega::where('id', $request->id)->first();

        return ['success' => 1, 'info' => $info];
    }


    public function vistaListadoEdicion($id)
    {
        $info = TesoreriaGarantiaPendienteEntrega::where('id', $id)->first();

        $arrayProveedor = TesoreriaProveedores::orderBy('nombre', 'ASC')->get();
        $arrayGarantia = TesoreriaGarantia::orderBy('nombre', 'ASC')->get();
        $arrayTipoGarantia = TesoreriaTipoGarantia::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.tesoreria.listado.edicion.vistaedicion', compact('info',
        'arrayProveedor', 'arrayGarantia', 'arrayTipoGarantia'));
    }



    public function actualizarRegistro(Request $request)
    {
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        TesoreriaGarantiaPendienteEntrega::where('id', $request->id)->update([
            'control_interno' => $request->numcontrol,
            'referencia' => $request->referencia,
            'descripcion_licitacion' => $request->descripcion,
            'id_proveedor' => $request->proveedor,
            'id_garantia' => $request->garantia,
            'id_tipo_garantia' => $request->tipogarantia,
            'monto_garantia' => $request->monto,
            'aseguradora' => $request->aseguradora,
            'vigencia_desde' => $request->fechadesde,
            'vigencia_hasta' => $request->fechahasta,
            'fecha_recibida' => $request->fecharecibida,
            'fecha_entrega' => $request->fechaentrega,
            'fecha_entrega_ucp' => $request->fechaentregaucp,
        ]);

        return ['success' => 1];
    }






}

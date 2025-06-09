<?php

namespace App\Http\Controllers\Backend\Tesoreria\Config;

use App\Http\Controllers\Controller;
use App\Models\P_Departamento;
use App\Models\TesoreriaEstados;
use App\Models\TesoreriaGarantia;
use App\Models\TesoreriaGarantiaPendienteEntrega;
use App\Models\TesoreriaProveedores;
use App\Models\TesoreriaTipoGarantia;
use Carbon\Carbon;
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
        $arrayEstados = TesoreriaEstados::orderBy('nombre', 'ASC')->get();

        $anioActual = Carbon::now()->year;
        $conteo = TesoreriaGarantiaPendienteEntrega::whereYear('fecha_registro', $anioActual)->count();

        $correlativoNumero = str_pad($conteo + 1, 2, '0', STR_PAD_LEFT);
        $correlativo = $correlativoNumero . '-' . $anioActual;

        return view('backend.admin.tesoreria.registro.nuevoregistro', compact('arrayProveedor',
            'arrayGarantia', 'arrayTipoGarantia', 'correlativo', 'arrayEstados'));
    }


    public function nuevoRegistroTesoreria(Request $request)
    {
        DB::beginTransaction();

        // FECHA REGISTRO ES OBLIGATORIA

        try {

            $registro = new TesoreriaGarantiaPendienteEntrega();
            $registro->fecha_registro = $request->fechaRegistro;
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
            $registro->id_estado = $request->estados;

            $registro->save();

            DB::commit();
            return ['success' => 1];

        }catch(\Throwable $e){
            Log::info('error: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }



    public function vistaListadoRegistrosVigentes()
    {
        $departamentos = P_Departamento::all();

        return view('backend.admin.tesoreria.listado.vigentes.vistalistadovigentes', compact('departamentos'));
    }

    public function tablaListadoRegistrosVigentes()
    {
        $listado = TesoreriaGarantiaPendienteEntrega::where('id_estado', 1) // SOLO VIGENTES
            ->orderBy('control_interno', 'ASC')
            ->get();

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

            $vencida = 0;
            if($item->vigencia_desde != null && $item->vigencia_hasta != null){
                $now = Carbon::now('America/El_Salvador');
                $hasta = Carbon::parse($item->vigencia_hasta, 'America/El_Salvador');

                if ($now->gt($hasta)) {
                    // Ya te pasaste de la vigencia
                    $vencida = 1;
                }
            }

            $item->vencida = $vencida;
        }

        return view('backend.admin.tesoreria.listado.vigentes.tablalistadovigentes', compact('listado'));
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

        $arrayEstados = TesoreriaEstados::orderBy('nombre', 'ASC')->get();

        return ['success' => 1, 'info' => $info, 'arrayEstados' => $arrayEstados];
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
            'fecha_registro' => $request->fechaRegistro,
        ]);

        return ['success' => 1];
    }


    public function actualizarEstado(Request $request)
    {
        $regla = array(
            'id' => 'required',
            'estado' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        TesoreriaGarantiaPendienteEntrega::where('id', $request->id)->update([
            'id_estado' => $request->estado
        ]);

        return ['success' => 1];
    }



    //**************************************************************************************************



    public function vistaListadoRegistrosVencidas()
    {
        $departamentos = P_Departamento::all();

        return view('backend.admin.tesoreria.listado.vencidas.vistalistadovencidas', compact('departamentos'));
    }

    public function tablaListadoRegistrosVencidas()
    {
        $listado = TesoreriaGarantiaPendienteEntrega::where('id_estado', 2) // SOLO VENCIDAS
        ->orderBy('control_interno', 'ASC')
            ->get();

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

        return view('backend.admin.tesoreria.listado.vencidas.tablalistadovencidas', compact('listado'));
    }







    //**************************************************************************************************



    public function vistaListadoRegistrosUcp()
    {
        $departamentos = P_Departamento::all();

        return view('backend.admin.tesoreria.listado.ucp.vistalistadoucp', compact('departamentos'));
    }

    public function tablaListadoRegistrosUcp()
    {
        $listado = TesoreriaGarantiaPendienteEntrega::where('id_estado', 3) // SOLO UCP
        ->orderBy('control_interno', 'ASC')
            ->get();

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

        return view('backend.admin.tesoreria.listado.ucp.tablalistadoucp', compact('listado'));
    }




    //**************************************************************************************************



    public function vistaListadoRegistrosProveedor()
    {
        $departamentos = P_Departamento::all();

        return view('backend.admin.tesoreria.listado.proveedor.vistalistadoproveedor', compact('departamentos'));
    }

    public function tablaListadoRegistrosProveedor()
    {
        $listado = TesoreriaGarantiaPendienteEntrega::where('id_estado', 4) // SOLO PROVEEDOR
        ->orderBy('control_interno', 'ASC')
            ->get();

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

        return view('backend.admin.tesoreria.listado.proveedor.tablalistadoproveedor', compact('listado'));
    }



    //**************************************************************************************************



    public function vistaListadoRegistrosTodos()
    {
        $departamentos = P_Departamento::all();

        return view('backend.admin.tesoreria.listado.todas.vistalistadotodas', compact('departamentos'));
    }

    public function tablaListadoRegistrosTodos()
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

            $vencida = 0;
            if($item->vigencia_desde != null && $item->vigencia_hasta != null){
                $now = Carbon::now('America/El_Salvador');
                $hasta = Carbon::parse($item->vigencia_hasta, 'America/El_Salvador');

                if ($now->gt($hasta)) {
                    // Ya te pasaste de la vigencia
                    $vencida = 1;
                }
            }

            $item->vencida = $vencida;
        }

        return view('backend.admin.tesoreria.listado.todas.tablalistadotodas', compact('listado'));
    }




    //****************************************************

    public function indexDashboard()
    {

        $totalRegistros = TesoreriaGarantiaPendienteEntrega::count();
        $totalVigentes = TesoreriaGarantiaPendienteEntrega::where('id_estado', 1)->count();
        $totalVencidas = TesoreriaGarantiaPendienteEntrega::where('id_estado', 2)->count();
        $totalUcp = TesoreriaGarantiaPendienteEntrega::where('id_estado', 3)->count();


        return view('backend.admin.tesoreria.dashboard.vistadashboard', compact('totalRegistros',
            'totalVigentes', 'totalVencidas', 'totalUcp'));
    }



    //****************************************************
    public function indexReportes()
    {
        $arrayEstados = TesoreriaEstados::orderBy('nombre', 'ASC')->get();

        $listado = TesoreriaGarantiaPendienteEntrega::all();

        $arrayAnios = $listado->pluck('fecha_registro')
        ->filter() // Elimina nulos
        ->map(function ($fecha) {
            return Carbon::parse($fecha)->year;
        })
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        return view('backend.admin.tesoreria.reportes.general.vistareportegeneral', compact('arrayEstados', 'arrayAnios'));
    }



    public function reportePdfGeneralTesoreria($anio, $tipo, $check)
    {
        if ($check == 1) {
            $listado = TesoreriaGarantiaPendienteEntrega::where('id_estado', $tipo)
                ->orderBy('vigencia_hasta', 'ASC')
                ->get();
        } else {
            $listado = TesoreriaGarantiaPendienteEntrega::whereYear('fecha_registro', $anio)
                ->orderBy('vigencia_hasta', 'ASC')
                ->get();
        }


        $infoEstado = TesoreriaEstados::where('id', $tipo)->first();


        foreach ($listado as $item) {

            $infoProveedor = TesoreriaProveedores::where('id', $item->id_proveedor)->first();
            $infoGarantia = TesoreriaGarantia::where('id', $item->id_garantia)->first();
            $infoTipoGarantia = TesoreriaTipoGarantia::where('id', $item->id_tipo_garantia)->first();

            $item->proveedor = $infoProveedor->nombre;
            $item->garantia = $infoGarantia->nombre;
            $item->tipoGarantia = $infoTipoGarantia->nombre;

            if ($item->monto_garantia != null) {
                $item->monto = '$' . number_format((float)$item->monto_garantia, 2, '.', ',');
            }

            if ($item->vigencia_desde != null) {
                $item->vigencia_desde = date('d-m-Y', strtotime($item->vigencia_desde));
            }

            if ($item->vigencia_hasta != null) {
                $item->vigencia_hasta = date('d-m-Y', strtotime($item->vigencia_hasta));
            }

            if ($item->fecha_recibida != null) {
                $item->fecha_recibida = date('d-m-Y', strtotime($item->fecha_recibida));
            }

            if ($item->fecha_entrega != null) {
                $item->fecha_entrega = date('d-m-Y', strtotime($item->fecha_entrega));
            }

            if ($item->fecha_entrega_ucp != null) {
                $item->fecha_entrega_ucp = date('d-m-Y', strtotime($item->fecha_entrega_ucp));
            }

            $vencida = 0;
            if ($item->vigencia_desde != null && $item->vigencia_hasta != null) {
                $now = Carbon::now('America/El_Salvador');
                $hasta = Carbon::parse($item->vigencia_hasta, 'America/El_Salvador');

                if ($now->gt($hasta)) {
                    // Ya te pasaste de la vigencia
                    $vencida = 1;
                }
            }

            $item->vencida = $vencida;
        }



        //**********************************************************************************



        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER-L']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER-L']);

       /* $mpdf = new \Mpdf\Mpdf([
            'tempDir' => sys_get_temp_dir(),
            'format' => 'Letter-L' // La L al final indica "Landscape"
        ]);*/


        $mpdf->SetTitle('Reporte General');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/gobiernologo.jpg';
        $logosantaana = 'images/logo.png';

        $tabla = "
            <table style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <!-- Logo izquierdo -->
                    <td style='width: 15%; text-align: left;'>
                        <img src='$logosantaana' alt='Santa Ana Norte' style='max-width: 100px; height: auto;'>
                    </td>
                    <!-- Texto centrado -->
                    <td style='width: 60%; text-align: center;'>
                        <h1 style='font-size: 16px; margin: 0; color: #003366; text-transform: uppercase;'>ALCALDÍA MUNICIPAL DE SANTA ANA NORTE</h1>
                    </td>
                    <!-- Logo derecho -->
                    <td style='width: 10%; text-align: right;'>
                        <img src='$logoalcaldia' alt='Gobierno de El Salvador' style='max-width: 60px; height: auto;'>
                    </td>
                </tr>
            </table>
            <hr style='border: none; border-top: 2px solid #003366; margin: 0;'>
            ";

        $tabla .= "
            <div style='text-align: center; margin-top: 20px;'>
                <h1 style='font-size: 16px; margin: 0; color: #000;'>REPORTE GARANTIAS</h1>
            </div>
            <div style='text-align: left; margin-top: 10px;'>
        </div>
      ";


        $tabla .= "
            <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 13px; margin: 0; color: #000;'><strong>Estado:</strong> $infoEstado->nombre</p>
            </div>
            <div style='text-align: left; margin-top: 10px;'>
        </div>
      ";


        // Encabezado de la tabla
        $tabla .= "<table width='100%' id='tablaFor' style='margin-top: 30px'>
            <thead>
                <tr>
                    <th style='font-weight: bold; width: 6%; font-size: 10px; text-align: center;'>CONTROL INTERNO</th>
                    <th style='font-weight: bold; width: 6%; font-size: 10px; text-align: center;'>ESTADO</th>
                    <th style='font-weight: bold; width: 8%; font-size: 10px; text-align: center;'>REFERENCIA</th>
                    <th style='font-weight: bold; width: 8%; font-size: 10px; text-align: center;'>PROVEEDOR</th>
                    <th style='font-weight: bold; width: 8%; font-size: 10px; text-align: center;'>GARANTIA</th>
                    <th style='font-weight: bold; width: 8%; font-size: 10px; text-align: center;'>TIPO GARANTIA</th>
                    <th style='font-weight: bold; width: 10%; font-size: 10px; text-align: center;'>MONTO</th>
                    <th style='font-weight: bold; width: 10%; font-size: 10px; text-align: center;'>ASEGURADORA</th>
                    <th style='font-weight: bold; width: 10%; font-size: 10px; text-align: center;'>V. DESDE</th>
                    <th style='font-weight: bold; width: 10%; font-size: 10px; text-align: center;'>V. HASTA</th>
                    <th style='font-weight: bold; width: 10%; font-size: 10px; text-align: center;'>F. RECIBIDA</th>
                </tr>
            </thead>
            <tbody>";


        foreach ($listado as $dato) {

            $tabla .= "<tr>
                    <td style='font-size: 10px; font-weight: normal'>$dato->control_interno</td>
                    <td style='font-size: 10px; font-weight: normal'>$dato->nombreEstado</td>
                    <td style='font-size: 10px; font-weight: normal'>$dato->referencia</td>
                    <td style='font-size: 10px; font-weight: normal'>$dato->proveedor</td>
                    <td style='font-size: 10px; font-weight: normal'>$dato->garantia</td>
                    <td style='font-size: 10px; font-weight: normal'>$dato->tipoGarantia</td>
                    <td style='font-size: 10px; font-weight: normal'>$dato->monto_garantia</td>
                    <td style='font-size: 10px; font-weight: normal'>$dato->aseguradora</td>
                    <td style='font-size: 10px; font-weight: normal'>$dato->vigencia_desde</td>
                    <td style='font-size: 10px; font-weight: normal'>$dato->vigencia_hasta</td>
                    <td style='font-size: 10px; font-weight: normal'>$dato->fecha_recibida</td>
                </tr>";
        }


        $tabla .= "</tbody></table>";

        $stylesheet = file_get_contents('css/cssbodega.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla,2);

        $mpdf->Output();
    }









}

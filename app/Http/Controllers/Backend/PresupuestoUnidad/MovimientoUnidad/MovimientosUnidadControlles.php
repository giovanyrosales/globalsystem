<?php

namespace App\Http\Controllers\Backend\PresupuestoUnidad\MovimientoUnidad;

use App\Http\Controllers\Controller;
use App\Models\CotizacionUnidad;
use App\Models\Cuenta;
use App\Models\CuentaUnidad;
use App\Models\MoviCuentaUnidad;
use App\Models\ObjEspecifico;
use App\Models\P_AnioPresupuesto;
use App\Models\P_Departamento;
use App\Models\P_Materiales;
use App\Models\P_PresupUnidad;
use App\Models\P_PresupUnidadDetalle;
use App\Models\P_SolicitudMaterial;
use App\Models\P_SolicitudMaterialDetalle;
use App\Models\P_UnidadMedida;
use App\Models\P_UsuarioDepartamento;
use App\Models\RequisicionUnidad;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class MovimientosUnidadControlles extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    function redondear_dos_decimal($valor){
        $float_redondeado = round($valor * 100) / 100;
        return $float_redondeado;
    }


    public function indexMovimientoCuentaUnidad($idpresup){

        // obtener usuario
        $user = Auth::user();

        // conseguir el departamento
        $infoDepartamento = P_UsuarioDepartamento::where('id_usuario', $user->id)->first();

        // permiso para realizar un movimiento de cuenta
        $permiso = $infoDepartamento->permiso_movi_unidad;

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.vistamovimientocuentaunidad', compact('idpresup', 'permiso'));
    }

    public function tablaMovimientoCuentaUnidad($idpresup){

        // obtener usuario
        $user = Auth::user();

        // conseguir el departamento
        $infoUsuario = P_UsuarioDepartamento::where('id_usuario', $user->id)->first();
        $infoDepartamento = P_Departamento::where('id', $infoUsuario->id_departamento)->first();

        // permiso para realizar un movimiento de cuenta
        $permiso = $infoDepartamento->permiso_movi_unidad;

        $presupuesto = DB::table('cuenta_unidad AS cu')
            ->join('obj_especifico AS obj', 'cu.id_objespeci', '=', 'obj.id')
            ->select('obj.nombre', 'obj.id AS idcodigo', 'cu.id_presup_unidad',
                'obj.codigo', 'cu.id', 'cu.saldo_inicial', 'cu.saldo_inicial_fijo')
            ->where('cu.id_presup_unidad', $idpresup)
            ->get();

        foreach ($presupuesto as $info) {

            // UTILIZADO PARA MOSTRAR O NO EL BOTON (AUMENTAR)
            $info->permiso = $permiso;

            $info->saldo_inicial = number_format((float)$info->saldo_inicial, 2, '.', ',');
            $info->saldo_inicial_fijo = number_format((float)$info->saldo_inicial_fijo, 2, '.', ',');
        }

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.tablamovimientocuentaunidad', compact('presupuesto'));
    }


    public function informacionMoviCuentaUnidad(Request $request){

        $regla = array(
            'id' => 'required', // ID CUENTA UNIDAD
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if ($lista = CuentaUnidad::where('id', $request->id)->first()) {

            $infoObjeto = ObjEspecifico::where('id', $lista->id_objespeci)->first();
            $infoCuenta = Cuenta::where('id', $infoObjeto->id_cuenta)->first();
            $cuenta = $infoCuenta->nombre;

            // obtener CUENTA UNIDAD. menos la seleccionada
            $arrayCuentaUnidad = DB::table('cuenta_unidad AS cu')
                ->join('obj_especifico AS obj', 'cu.id_objespeci', '=', 'obj.id')
                ->select('obj.nombre', 'obj.codigo', 'cu.id', 'cu.id_presup_unidad')
                ->where('cu.id', '!=', $lista->id)
                ->where('cu.id_presup_unidad', $lista->id_presup_unidad)
                ->get();

            // CÁLCULOS

            $totalRestanteSaldo = "$" . number_format((float)$lista->saldo_inicial, 2, '.', ',');

            return ['success' => 1, 'info' => $lista,
                'objeto' => $infoObjeto, 'cuenta' => $cuenta,
                'restante' => $totalRestanteSaldo, 'arraycuentaunidad' => $arrayCuentaUnidad];
        } else {
            return ['success' => 2];
        }
    }

    // al mover select de movimiento cuenta unidad, retorna saldo restante del obj seleccionado
    public function infoSaldoRestanteCuentaUnidad(Request $request){

        $regla = array(
            'id' => 'required', // ID CUENTA UNIDAD
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if ($lista = CuentaUnidad::where('id', $request->id)->first()) {

            $totalCalculado = "$" . number_format((float)$lista->saldo_inicial, 2, '.', ',');

            return ['success' => 1, 'restante' => $totalCalculado];
        } else {
            return ['success' => 2];
        }
    }

    // registrar un nuevo movimiento de cuenta unidad por jefe de unidad
    public function nuevaMoviCuentaUnidad(Request $request){

        // REQUEST

        // idcuentaunidad     id de cuenta unidad que subira
        // saldomodificar     dinero
        // selectcuenta       id cuenta unidad a descontar
        // fecha

        DB::beginTransaction();

        try {

            // SOLO SE TOMA EN CONSIDERACION LA CUENTA UNIDAD QUE BAJARA
            $infoCuentaBajara = CuentaUnidad::where('id', $request->selectcuenta)->first();

            $resta = $infoCuentaBajara->saldo_inicial = $request->saldomodificar;

            // DINERO NO ALCALZA PARA BAJARLE
            if($resta < 0){

                $saldoActual = '$' . number_format((float)$infoCuentaBajara->saldo_inicial, 2, '.', ',');

                return ['success' => 1, 'saldoactual' => $saldoActual];
            }

            $co = new MoviCuentaUnidad();
            $co->id_cuentaunidad_sube = $request->idcuentaunidad;
            $co->dinero = $request->saldomodificar;
            $co->id_cuentaunidad_baja = $request->selectcuenta;
            $co->fecha = $request->fecha;
            $co->reforma = null;
            $co->autorizado = 0;
            $co->save();

            // setear para que no agregue más movimientos

            $user = Auth::user();

            // conseguir el departamento
            $infoUsuario = P_UsuarioDepartamento::where('id_usuario', $user->id)->first();
            P_Departamento::where('id',  $infoUsuario->id_departamento)->update([
                'permiso_movi_unidad' => 0,
            ]);

            DB::commit();
            return ['success' => 2];

        } catch (\Throwable $e) {
            Log::info('ee' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // retorna vista con los historicos movimientos cuenta unidad por ID PRESUP UNIDAD
    public function indexMoviCuentaUnidadHistorico($id){
        // ID: PRESUP UNIDAD
        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.historico.vistamovicuentaunidadhistorico', compact('id'));
    }

    // retorna tabla con los historicos movimientos cuenta unidad por ID PRESUP UNIDAD
    public function tablaMoviCuentaUnidadHistorico($id){
        // ID: PRESUP UNIDAD

        $pilaIdCuentaUnidad = array();
        $listado = CuentaUnidad::where('id_presup_unidad', $id)->get();

        foreach ($listado as $ll) {
            array_push($pilaIdCuentaUnidad, $ll->id);
        }

        $infoMovimiento = MoviCuentaUnidad::whereIn('id_cuentaunidad_sube', $pilaIdCuentaUnidad)
            ->orderBy('fecha', 'ASC')
            ->get();

        foreach ($infoMovimiento as $dd) {

            $infoCuentaUnidadAumenta = CuentaUnidad::where('id', $dd->id_cuentaunidad_sube)->first();
            $infoCuentaUnidadBaja = CuentaUnidad::where('id', $dd->id_cuentaunidad_baja)->first();

            $infoObjetoAumenta = ObjEspecifico::where('id', $infoCuentaUnidadAumenta->id_objespeci)->first();
            $infoObjetoBaja = ObjEspecifico::where('id', $infoCuentaUnidadBaja->id_objespeci)->first();

            $dd->cuentaaumenta = $infoObjetoAumenta->codigo . " - " . $infoObjetoAumenta->nombre;
            $dd->cuentabaja = $infoObjetoBaja->codigo . " - " . $infoObjetoBaja->nombre;

            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));
        }

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.historico.tablamovicuentaunidadhistorico', compact('infoMovimiento'));
    }


    // ver los movimientos historicos para que jefe presupuesto los apruebe
    public function indexMovimientoCuentaUnidadTodos(){
        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.historicorevision.vistamovicuentaunidadhistoricorevision');
    }

    // ver tabla de los movimientos historicos de cuenta unidad, jefatura presupuesto para aprobar
    public function tablaMovimientoCuentaUnidadTodos(){

        $pilaIdCuentaUnidad = array();
        $listado = CuentaUnidad::get();

        foreach ($listado as $ll) {
            array_push($pilaIdCuentaUnidad, $ll->id);
        }

        $infoMovimiento = MoviCuentaUnidad::whereIn('id_cuentaunidad_sube', $pilaIdCuentaUnidad)
            ->orderBy('fecha', 'ASC')
            ->where('autorizado', 0) // NO AUTORIZADO AUN
            ->get();

        foreach ($infoMovimiento as $dd) {

            $infoCuentaUnidadAumenta = CuentaUnidad::where('id', $dd->id_cuentaunidad_sube)->first();
            $infoCuentaUnidadBaja = CuentaUnidad::where('id', $dd->id_cuentaunidad_baja)->first();

            $infoObjetoAumenta = ObjEspecifico::where('id', $infoCuentaUnidadAumenta->id_objespeci)->first();
            $infoObjetoBaja = ObjEspecifico::where('id', $infoCuentaUnidadBaja->id_objespeci)->first();

            $dd->cuentaaumenta = $infoObjetoAumenta->codigo . " - " . $infoObjetoAumenta->nombre;
            $dd->cuentabaja = $infoObjetoBaja->codigo . " - " . $infoObjetoBaja->nombre;

            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));

            // obtener departamento
            $infoPresuUnidad = P_PresupUnidad::where('id', $infoCuentaUnidadAumenta->id_presup_unidad)->first();
            $infoDepartamento = P_Departamento::where('id', $infoPresuUnidad->id_departamento)->first();

            $dd->departamento = $infoDepartamento->nombre;
        }

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.historicorevision.tablamovicuentaunidadhistoricorevision', compact('infoMovimiento'));
    }

    // información para jefe de presupuesto para que revise un movimiento de cuenta unidad
    public function infoHistoricoMovimientoUnidadParaAutorizar(Request $request){

        $regla = array(
            'id' => 'required', // ID movicuentaunidad
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if ($infoMovi = MoviCuentaUnidad::where('id', $request->id)->first()) {

            // PUEDO TOMAR YA SEA EL SUBE O BAJA, YA QUE TODOS PERTENECEN AL MISMO PROYECTO

            $infoCuentaUnidadSube = CuentaUnidad::where('id', $infoMovi->id_cuentaunidad_sube)->first();
            $infoCuentaUnidadBaja = CuentaUnidad::where('id', $infoMovi->id_cuentaunidad_baja)->first();

            $infoObjetoSube = ObjEspecifico::where('id', $infoCuentaUnidadSube->id_objespeci)->first();
            $infoObjetoBaja = ObjEspecifico::where('id', $infoCuentaUnidadBaja->id_objespeci)->first();

            $infoCuentaSube = Cuenta::where('id', $infoObjetoSube->id_cuenta)->first();
            $infoCuentaBaja = Cuenta::where('id', $infoObjetoBaja->id_cuenta)->first();

            $objetoaumenta = $infoObjetoSube->codigo . " - " . $infoObjetoSube->nombre;
            $objetobaja = $infoObjetoBaja->codigo . " - " . $infoObjetoBaja->nombre;

            $cuentaaumenta = $infoCuentaSube->codigo . " - " . $infoCuentaSube->nombre;
            $cuentabaja = $infoCuentaBaja->codigo . " - " . $infoCuentaBaja->nombre;

            $fecha = date("d-m-Y", strtotime($infoMovi->fecha));

            // OBTENER SALDO RESTANTE, PARA EL OBJETO ESPECÍFICO QUE SE QUITARA DINERO

            // CÁLCULOS

            $infoCuentaUnidad = CuentaUnidad::where('id', $infoMovi->id_cuentaunidad_baja)->first();
            $totalRestanteSaldo = number_format((float)$infoCuentaUnidad->saldo_inicial, 2, '.', ',');

            return ['success' => 1, 'info' => $infoMovi, 'cuentaaumenta' => $cuentaaumenta,
                'cuentabaja' => $cuentabaja, 'objetosube' => $objetoaumenta, 'objetobaja' => $objetobaja,
                'fecha' => $fecha, 'restantecuentabaja' => $totalRestanteSaldo];
        } else {
            return ['success' => 2];
        }
    }

    // borrar movimiento de cuenta para unidades
    public function denegarBorrarMovimientoCuentaUnidad(Request $request){
        $regla = array(
            'id' => 'required', // ID movicuentaproy
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if ($infoMovi = MoviCuentaUnidad::where('id', $request->id)->first()) {

            // no borrar porque ya esta autorizado
            if ($infoMovi->autorizado == 1) {
                return ['success' => 1];
            }

            // borrar fila
            MoviCuentaUnidad::where('id', $request->id)->delete();

            return ['success' => 2];
        } else {
            return ['success' => 99];
        }
    }

    // autorizar movimiento de cuenta unidad
    public function autorizarMovimientoCuentaUnidad(Request $request){

        // ID movicuentaunidad

        DB::beginTransaction();

        try {

            if ($infoMovimiento = MoviCuentaUnidad::where('id', $request->id)->first()) {

               // EVITAR QUE SE AUTORIZE DE NUEVO
                if ($infoMovimiento->autorizado == 1) {
                    return ['success' => 1];
                }

                // INFO DE LA CUENTA QUE VA A BAJAR
                $infoCuentaBajara = CuentaUnidad::where('id', $infoMovimiento->id_cuentaunidad_baja)->first();

                $resta = $infoCuentaBajara->saldo_inicial - $infoMovimiento->dinero;

                // DINERO NO ALCALZA PARA BAJARLE
                if($resta < 0){

                    $saldoActual = '$' . number_format((float)$infoCuentaBajara->saldo_inicial, 2, '.', ',');
                    return ['success' => 2, 'saldoactual' => $saldoActual];
                }


                if ($request->hasFile('documento')) {
                    $cadena = Str::random(15);
                    $tiempo = microtime();
                    $union = $cadena . $tiempo;
                    $nombre = str_replace(' ', '_', $union);

                    $extension = '.' . $request->documento->getClientOriginalExtension();
                    $nomDocumento = $nombre . strtolower($extension);
                    $avatar = $request->file('documento');
                    $estado = Storage::disk('archivos')->put($nomDocumento, \File::get($avatar));

                    if ($estado) {

                        // pasar estado autorizado y guardar documento

                        MoviCuentaUnidad::where('id', $request->id)->update([
                            'reforma' => $nomDocumento,
                            'autorizado' => 1
                        ]);

                        // DESCONTAR DINERO A LA CUENTA UNIDAD
                        CuentaUnidad::where('id', $infoCuentaBajara->id)->update([
                            'saldo_inicial' => $resta,
                        ]);

                        DB::commit();
                        return ['success' => 3];
                    } else {
                        return ['success' => 99];
                    }
                } else {

                    MoviCuentaUnidad::where('id', $request->id)->update([
                        'autorizado' => 1
                    ]);

                    // DESCONTAR DINERO A LA CUENTA UNIDAD
                    CuentaUnidad::where('id', $infoCuentaBajara->id)->update([
                        'saldo_inicial' => $resta,
                    ]);

                    DB::commit();
                    return ['success' => 3];
                }
            } else {
                return ['success' => 99];
            }

        } catch (\Throwable $e) {
            Log::info('ee' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function indexMovimientoCuentaUnidadAprobadosAnio(){
        $anios = P_AnioPresupuesto::orderBy('nombre', 'DESC')->get();

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.historicoaprobado.vistaaniocuentahistoricounidad', compact('anios'));
    }

    // ver los movimientos de cuenta unidad aprobados
    public function indexMovimientoCuentaUnidadAprobados($idanio){

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.historicoaprobado.vistamovicuentaunidadhistoricoaprobado', compact('idanio'));
    }

    // ver tabla de los movimientos historicos de cuenta unidad aprobados
    public function tablaMovimientoCuentaUnidadAprobados($idanio){

        $pilaIdCuentaUnidad = array();

        $listado = DB::table('cuenta_unidad AS cu')
            ->join('p_presup_unidad AS pru', 'cu.id_presup_unidad', '=', 'pru.id')
            ->select('cu.id', 'cu.id_presup_unidad', 'pru.id_anio', 'cu.id_objespeci', 'cu.saldo_inicial')
            ->where('pru.id_anio', $idanio)
            ->get();

        foreach ($listado as $ll) {
            array_push($pilaIdCuentaUnidad, $ll->id);
        }

        $infoMovimiento = MoviCuentaUnidad::whereIn('id_cuentaunidad_sube', $pilaIdCuentaUnidad)
            ->orderBy('fecha', 'ASC')
            ->where('autorizado', 1) // AUTORIZADOS
            ->get();

        foreach ($infoMovimiento as $dd) {

            $infoCuentaUnidadAumenta = CuentaUnidad::where('id', $dd->id_cuentaunidad_sube)->first();
            $infoCuentaUnidadBaja = CuentaUnidad::where('id', $dd->id_cuentaunidad_baja)->first();

            $infoObjetoAumenta = ObjEspecifico::where('id', $infoCuentaUnidadAumenta->id_objespeci)->first();
            $infoObjetoBaja = ObjEspecifico::where('id', $infoCuentaUnidadBaja->id_objespeci)->first();

            $dd->cuentaaumenta = $infoObjetoAumenta->codigo . " - " . $infoObjetoAumenta->nombre;
            $dd->cuentabaja = $infoObjetoBaja->codigo . " - " . $infoObjetoBaja->nombre;

            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));

            // obtener departamento
            $infoPresuUnidad = P_PresupUnidad::where('id', $infoCuentaUnidadAumenta->id_presup_unidad)->first();
            $infoDepartamento = P_Departamento::where('id', $infoPresuUnidad->id_departamento)->first();

            $dd->departamento = $infoDepartamento->nombre;
        }

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.historicoaprobado.tablamovicuentaunidadhistoricoaprobado', compact('infoMovimiento'));
    }

    // descargar documento reforma de movimiento cuenta unidad
    public function descargarReformaMovimientoUnidades($id){

        $url = MoviCuentaUnidad::where('id', $id)->pluck('reforma')->first();
        $pathToFile = "storage/archivos/" . $url;
        $extension = pathinfo(($pathToFile), PATHINFO_EXTENSION);
        $nombre = "Documento." . $extension;
        return response()->download($pathToFile, $nombre);
    }


    public function guardarDocumentoReformaMoviUnidad(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {
            $infoMovimiento = MoviCuentaUnidad::where('id', $request->id)->first();

            if ($request->hasFile('documento')) {

                $cadena = Str::random(15);
                $tiempo = microtime();
                $union = $cadena . $tiempo;
                $nombre = str_replace(' ', '_', $union);

                $extension = '.' . $request->documento->getClientOriginalExtension();
                $nomDocumento = $nombre . strtolower($extension);
                $avatar = $request->file('documento');
                $archivo = Storage::disk('archivos')->put($nomDocumento, \File::get($avatar));

                if ($archivo) {

                    if (Storage::disk('archivos')->exists($infoMovimiento->reforma)) {
                        Storage::disk('archivos')->delete($infoMovimiento->reforma);
                    }

                    MoviCuentaUnidad::where('id', $request->id)->update([
                        'reforma' => $nomDocumento
                    ]);

                    DB::commit();
                    return ['success' => 1];
                } else {
                    return ['success' => 99];
                }
            } else {
                return ['success' => 99];
            }
        } catch (\Throwable $e) {

            DB::rollback();
            return ['success' => 99];
        }
    }


    //* *************************

    // retorna vista para ver materiales solicitados y se quita dinero de un código
    public function indexSolicitudMovimientoUnidadMaterial($idpresubunidad){
        // ID: PRESUP UNIDAD

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.solicitudmaterial.vistamovimientounidadsolicitudmaterial', compact('idpresubunidad'));
    }

    // retorna tabla para ver materiales solicitados y se quita dinero de un código
    public function tablaSolicitudMovimientoUnidadMaterial($idpresubunidad){

        // ID: PRESUP UNIDAD

        $lista = P_SolicitudMaterial::where('id_presup_unidad', $idpresubunidad)->get();

        foreach ($lista as $dd) {

            $infoMaterial = P_Materiales::where('id', $dd->id_material)->first();
            $infoObj = ObjEspecifico::where('id', $infoMaterial->id_objespecifico)->first();

            $dd->material = $infoMaterial->descripcion;
            $dd->objnombre = $infoObj->codigo . ' - ' . $infoObj->nombre;

            $total = ($dd->cantidad * $infoMaterial->costo) * $dd->periodo;

            $total = "$" . number_format((float)$total, 2, '.', ',');

            $dd->total = $total;

            $dd->fechahora = date("d-m-Y h:i A", strtotime($dd->fechahora));
        }

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.solicitudmaterial.tablamovimientounidadsolicitudmaterial', compact('lista'));
    }

    // buscador de material de solicitud
    public function buscadorMaterialSolicitudUnidad(Request $request){

        if($request->get('query')){
            $query = $request->get('query');

            // idpresuunidad

            $arrayPresuDetalle = P_PresupUnidadDetalle::where('id_presup_unidad', $request->idpresuunidad)->get();

            $pilaIdMateriales = array();

            foreach ($arrayPresuDetalle as $dd){
                array_push($pilaIdMateriales, $dd->id_material);
            }

            // array de materiales materiales adicionales
            $arrayMateriales = P_Materiales::whereNotIn('id', $pilaIdMateriales)
                ->where('descripcion', 'LIKE', "%{$query}%")
                ->take(40)
                ->get();

            // BÚSQUEDA

            $output = '<ul class="dropdown-menu" style="display:block; position:relative;">';
            $tiene = true;
            foreach($arrayMateriales as $row){

                $infoUnidad = P_UnidadMedida::where('id', $row->id_unidadmedida)->first();
                $row->unido = $row->descripcion . ' - ' . $infoUnidad->nombre;

                // si solo hay 1 fila, No mostrara el hr, salto de linea
                if(count($arrayMateriales) == 1){
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValorSolicitud(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->unido.'</a></li>
                ';
                    }
                }

                else{
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValorSolicitud(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->unido.'</a></li>
                   <hr>
                ';
                    }
                }
            }

            $output .= '</ul>';
            if($tiene){
                $output = '';
            }
            echo $output;
        }
    }


    public function buscadorObjEspeciSolicitudMaterial(Request $request){

        $regla = array(
            'idmaterial' => 'required',
            'idpresup' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = P_Materiales::where('id', $request->idmaterial)->first()){

            $detalle = DB::table('p_presup_unidad_detalle AS ppu')
            ->join('p_materiales AS m', 'ppu.id_material', '=', 'm.id')
            ->select('m.id_objespecifico')
            ->where('ppu.id_presup_unidad', $request->idpresup)
            //->whereNotIn('m.id_objespecifico', [$lista->id_objespecifico])
            ->groupBy('m.id_objespecifico')
            ->get();

            $pilaArray = array();

            foreach ($detalle as $p){
                array_push($pilaArray, $p->id_objespecifico);
            }

            $arrayobj = ObjEspecifico::whereIn('id', $pilaArray)->get();

            $costoactual = "$" . number_format((float)$lista->costo, 2, '.', ',');

            return ['success' => 1, 'arrayobj' => $arrayobj, 'costoactual' => $costoactual];
        }else{
            return ['success' => 2];
        }
    }

    // obtener saldo restando MENOS el saldo retenido de un obj especifico
    public function infoSaldoRestanteSolicitudMaterial(Request $request){

        $regla = array(
            'idobj' => 'required', // ID objeto específico
            'idpresup' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if ($lista = CuentaUnidad::where('id_presup_unidad', $request->idpresup)
            ->where('id_objespeci', $request->idobj)
            ->first()) {

            // CÁLCULOS

            $totalRestante = 0;
            $totalRetenido = 0;

            $infoMoviCuentaUnidadSube = MoviCuentaUnidad::where('id_cuentaunidad_sube', $lista->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $infoMoviCuentaUnidadBaja = MoviCuentaUnidad::where('id_cuentaunidad_baja', $lista->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $totalMoviCuenta = $infoMoviCuentaUnidadSube - $infoMoviCuentaUnidadBaja;

            // obtener saldos restante
            $arrayRestante = DB::table('cuentaunidad_restante AS pd')
                ->join('requisicion_unidad_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero')
                ->where('pd.id_cuenta_unidad', $lista->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRestante as $dd) {
                $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
            }

            // información de saldos retenidos
            $arrayRetenido = DB::table('cuentaunidad_retenido AS psr')
                ->join('requisicion_unidad_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('psr.id_cuenta_unidad', $lista->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRetenido as $dd) {
                $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
            }

            // aquí se obtiene el Saldo Restante del código
            $totalRestanteSaldo = $totalMoviCuenta + ($lista->saldo_inicial - $totalRestante);

            // se debe quitar el retenido
            $totalCalculado = $totalRestanteSaldo - $totalRetenido;

            $totalCalculado = "$" . number_format((float)$totalCalculado, 2, '.', ',');

            return ['success' => 1, 'restante' => $totalCalculado];
        } else {
            return ['success' => 2];
        }
    }

    // guardar solicitud de materiales
    public function guardarSolicitudMaterialUnidad(Request $request){

        $regla = array(
            'idpresup' => 'required',
            'idmaterial' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            // guardar ya
            $deta = new P_SolicitudMaterial();
            $deta->id_presup_unidad = $request->idpresup;
            $deta->id_material = $request->idmaterial;
            $deta->cantidad = 0;
            $deta->periodo = 0;
            $deta->fechahora = Carbon::now('America/El_Salvador');
            $deta->save();

            DB::commit();
            return ['success' => 1];


        } catch (\Throwable $e) {
            DB::rollback();
            return ['success' => 99];
        }
    }

    // retorna vista para ver materiales solicitados y se quita dinero de un código
    public function indexRevisionSolicitudMaterialUnidad(){
        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.solicitudmaterial.revision.vistarevisionsolicitudmaterial');
    }

    // revision por presupuesto de material solicitado por una unidad
    public function tablaRevisionSolicitudMaterialUnidad(){

        $lista = P_SolicitudMaterial::all();

        foreach ($lista as $dd) {

            $infoPresup = P_PresupUnidad::where('id', $dd->id_presup_unidad)->first();
            $infoDepar = P_Departamento::where('id', $infoPresup->id_departamento)->first();

            $dd->departamento = $infoDepar->nombre;

            $infoMaterial = P_Materiales::where('id', $dd->id_material)->first();
            $infoObj = ObjEspecifico::where('id', $infoMaterial->id_objespecifico)->first();
            $infoMedida = P_UnidadMedida::where('id', $infoMaterial->id_unidadmedida)->first();

            $dd->material = $infoMaterial->descripcion;
            $dd->objnombre = $infoObj->codigo . ' - ' . $infoObj->nombre;
            $dd->unidadmedida = $infoMedida->nombre;

            $dd->costoactual = '$' . number_format((float)$infoMaterial->costo, 2, '.', ',');
        }

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.solicitudmaterial.revision.tablarevisionsolicitudmaterial', compact('lista'));
    }

    // revision por presupuesto de material solicitado por una unidad
    public function informacionSolicitudMaterialPresupuesto(Request $request){

        $regla = array(
            'idsolicitud' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if($infoSoli = P_SolicitudMaterial::where('id', $request->idsolicitud)->first()){

            $infoMaterial = P_Materiales::where('id', $infoSoli->id_material)->first();
            $infoObjeto = ObjEspecifico::where('id', $infoMaterial->id_objespecifico)->first();

            $nommaterial = $infoMaterial->descripcion;

            $objeto = $infoObjeto->codigo . ' - ' . $infoObjeto->nombre;

            return ['success' => 1, 'nommaterial' => $nommaterial, 'info' => $infoSoli,
                'objeto' => $objeto];
        }else{
            return ['success' => 2];
        }
    }

    // borrar solicitud material solicitado
    public function borrarSolicitudMaterialPresupuesto(Request $request){

        $regla = array(
            'idsolicitud' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if(P_SolicitudMaterial::where('id', $request->idsolicitud)->first()){

            P_SolicitudMaterial::where('id', $request->idsolicitud)->delete();
            return ['success' => 1];
        }
        else {
            return ['success' => 2];
        }
    }

    // aprobar solicitud de material solicitado y sumar a obj y descontar a otro obj
    function aprobarSolicitudMaterialPresupuesto(Request $request){

        $regla = array(
            'idsolicitud' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $infoSolicitud = P_SolicitudMaterial::where('id', $request->idsolicitud)->first();

            // guardar material, esperar confirmación por jefe de presupuesto
            $nuevoMate = new P_PresupUnidadDetalle();
            $nuevoMate->id_presup_unidad = $infoSolicitud->id_presup_unidad;
            $nuevoMate->id_material = $infoSolicitud->id_material;
            $nuevoMate->cantidad = 0;
            $nuevoMate->precio = 0;
            $nuevoMate->periodo = 0;
            $nuevoMate->save();

            // hoy guardar una copia de la aprobacion de ese material
            $copia = new P_SolicitudMaterialDetalle();
            $copia->id_presup_unidad = $infoSolicitud->id_presup_unidad;
            $copia->id_material = $infoSolicitud->id_material;
            $copia->fechahora = Carbon::now('America/El_Salvador');
            $copia->save();

            $infoMaterial = P_Materiales::where('id', $infoSolicitud->id_material)->first();

            // Verificar si este material con el obj especifico, está en CUENTA UNIDAD
            // si no está, se debera registrar
            if(CuentaUnidad::where('id_presup_unidad', $infoSolicitud->id_presup_unidad)
                ->where('id_objespeci', $infoMaterial->id_objespecifico)
                ->first()){
                // YA EXISTE EN CUENTA UNIDAD ESTE OBJ ESPECIFICO
            }else{
                // AGREGAR REGISTRO

                $cuenta = new CuentaUnidad();
                $cuenta->id_presup_unidad = $infoSolicitud->id_presup_unidad;
                $cuenta->id_objespeci = $infoMaterial->id_objespecifico;
                $cuenta->saldo_inicial = 0; // SIEMPRE SERA 0, ya que se agregaron despues de crear presupuesto
                $cuenta->saldo_inicial_fijo = 0;
                $cuenta->save();
            }

            P_SolicitudMaterial::where('id', $request->idsolicitud)->delete();

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('ee' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


    public function verCatalogoMaterialRequisicionUnidad($id){

        // id presupuesto unidad

        // presupuesto
        $presupuesto = DB::table('p_presup_unidad_detalle AS p')
            ->join('p_materiales AS m', 'p.id_material', '=', 'm.id')
            ->join('obj_especifico AS obj', 'm.id_objespecifico', '=', 'obj.id')
            ->select('m.descripcion', 'm.id AS idmaterial', 'obj.codigo', 'm.costo', 'p.id_presup_unidad', 'm.id_objespecifico', 'm.id_unidadmedida')
            ->where('p.id_presup_unidad', $id)
            ->orderBy('obj.codigo', 'ASC')
            ->get();

        foreach ($presupuesto as $pp){

            $infoMedida = P_UnidadMedida::where('id', $pp->id_unidadmedida)->first();
            $pp->medida = $infoMedida->nombre;

            $infoObjeto = ObjEspecifico::where('id', $pp->id_objespecifico)->first();

            $pp->objcodigo = $infoObjeto->codigo;
            $pp->objnombre = $infoObjeto->nombre;

            $pp->actual = '$' . number_format((float)$pp->costo, 2, '.', ',');
        }

        return view('backend.admin.presupuestounidad.requerimientos.modal.modalcatalogomaterial', compact('presupuesto'));
    }


    public function vistaAñoPresupuestoMaterialAprobados(){
        $anios = P_AnioPresupuesto::orderBy('nombre', 'DESC')->get();

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.solicitudmaterial.aprobados.vistaaniosolicitudmaterialaprobados', compact('anios'));
    }

    public function indexRevisionSolicitudMaterialAprobada($idanio){

        $infoAnio = P_AnioPresupuesto::where('id', $idanio)->first();
        $anio = $infoAnio->nombre;

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.solicitudmaterial.aprobados.vistarevisionsolicitudmaterialaprobados', compact('idanio', 'anio'));
    }


    public function tablaRevisionSolicitudMaterialUnidadAprobados($idanio){

        $lista = DB::table('p_solicitud_material_detalle AS ps')
            ->join('p_presup_unidad AS pp', 'ps.id_presup_unidad', '=', 'pp.id')
            ->select('pp.id_anio', 'ps.fechahora', 'pp.id', 'ps.id_material')
            ->where('pp.id_anio', $idanio)
            ->get();

        foreach ($lista as $dd){

            $infoPresup = P_PresupUnidad::where('id', $dd->id )->first();
            $infoMaterial = P_Materiales::where('id', $dd->id_material)->first();
            $infoDepartamento = P_Departamento::where('id', $infoPresup->id_departamento)->first();
            $infoMedida = P_UnidadMedida::where('id', $infoMaterial->id_unidadmedida)->first();

            $dd->material = $infoMaterial->descripcion;
            $dd->departamento = $infoDepartamento->nombre;
            $dd->unidadmedida = $infoMedida->nombre;

            $dd->fechahora = date("d-m-Y h:i A", strtotime($dd->fechahora));
        }

        return view('backend.admin.presupuestounidad.requerimientos.movimientosunidad.solicitudmaterial.aprobados.tablarevisionsolicitudmaterialaprobados', compact('lista'));
    }

    public function presupuestoMaterialAprobadosInformacion(Request $request){

        $lista = DB::table('p_solicitud_material_detalle AS ps')
            ->join('p_presup_unidad AS pp', 'ps.id_presup_unidad', '=', 'pp.id')
            ->select('pp.id_anio', 'pp.id AS idpresup', 'ps.id', 'ps.id_material', 'ps.id_cuentaunidad_sube', 'ps.id_cuentaunidad_baja',
                'ps.unidades', 'ps.periodo', 'ps.copia_saldoini_antes_subir', 'ps.copia_saldoini_antes_bajar',
                'ps.dinero_solicitado', 'ps.cuenta_creada', 'ps.fechahora')
            ->where('ps.id', $request->id)
            ->get();

        foreach ($lista as $dd) {

            $infoPresup = P_PresupUnidad::where('id', $dd->idpresup)->first();
            $infoMaterial = P_Materiales::where('id', $dd->id_material)->first();
            $infoDepartamento = P_Departamento::where('id', $infoPresup->id_departamento)->first();

            $dd->material = $infoMaterial->descripcion;
            $dd->departamento = $infoDepartamento->nombre;

            $dd->solicitado = '$' . number_format((float)$dd->dinero_solicitado, 2, '.', ',');

            $dd->antessubir = '$' . number_format((float)$dd->copia_saldoini_antes_subir, 2, '.', ',');

            $dd->antesbajar = '$' . number_format((float)$dd->copia_saldoini_antes_bajar, 2, '.', ',');


            // *** SUBIDA

            // objeto especifico que subio
            $infoCuentaSube = CuentaUnidad::where('id', $dd->id_cuentaunidad_sube)->first();
            $infoObjSube = ObjEspecifico::where('id', $infoCuentaSube->id_objespeci)->first();

            $dd->txtobjsube = $infoObjSube->codigo . ' - ' . $infoObjSube->nombre;


            // *** BAJADA
            $infoCuentaBaja = CuentaUnidad::where('id', $dd->id_cuentaunidad_baja)->first();
            $infoObjBaja = ObjEspecifico::where('id', $infoCuentaBaja->id_objespeci)->first();

            $dd->txtobjbaja = $infoObjBaja->codigo . ' - ' . $infoObjBaja->nombre;

            if($dd->cuenta_creada == 1){
                $txtcreada = "Si";
            }else{
                $txtcreada = "No";
            }

            $dd->txtcreada = $txtcreada;
        }

        return ['success' => 1, 'infolista' => $lista];
    }



}

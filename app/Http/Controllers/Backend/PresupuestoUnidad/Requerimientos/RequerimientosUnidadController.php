<?php

namespace App\Http\Controllers\Backend\PresupuestoUnidad\Requerimientos;

use App\Http\Controllers\Controller;
use App\Models\CotizacionUnidad;
use App\Models\CotizacionUnidadDetalle;
use App\Models\Cuenta;
use App\Models\CuentaUnidad;
use App\Models\CuentaUnidadRetenido;
use App\Models\MoviCuentaUnidad;
use App\Models\ObjEspecifico;
use App\Models\OrdenUnidad;
use App\Models\P_AnioPresupuesto;
use App\Models\P_Departamento;
use App\Models\P_Materiales;
use App\Models\P_PresupUnidad;
use App\Models\P_PresupUnidadDetalle;
use App\Models\P_UnidadMedida;
use App\Models\P_UsuarioDepartamento;
use App\Models\RequisicionAgrupada;
use App\Models\RequisicionAgrupadaDetalle;
use App\Models\RequisicionUnidad;
use App\Models\RequisicionUnidadDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\ErrorHandler\Debug;

class RequerimientosUnidadController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    // retornar vista para poder elegir año de presupuesto para solicitar requerimiento
    public function indexBuscarAñoPresupuesto(){
        $anios = P_AnioPresupuesto::orderBy('nombre', 'DESC')->get();
        return view('backend.admin.presupuestounidad.requerimientos.buscaraniopresupuesto.vistaaniorequerimiento', compact('anios'));
    }

    // verifica si puede hacer requerimientos segun año de presupuesto
    public function verificarEstadoPresupuesto(Request $request){

        $idusuario = Auth::id();

        // primero ver si está registrado en un departamento el usuario
        if(!P_UsuarioDepartamento::where('id_usuario', $idusuario)->first()){
           return ['success' => 1];
        }

        $infoUsuarioDepa = P_UsuarioDepartamento::where('id_usuario', $idusuario)->first();

        // verificar si presupuesto esta creado y aprobado
        if($infoPresuUnidad = P_PresupUnidad::where('id_anio', $request->anio)
            ->where('id_departamento', $infoUsuarioDepa->id_departamento)
            ->first()){

            // en modo desarrollo
            if($infoPresuUnidad->id_estado == 1){
                return ['success' => 2];
            }

            // en modo revisión
            if($infoPresuUnidad->id_estado == 2){
                return ['success' => 3];
            }

            // está aprobado, pero es de ver si ya crearon las cuentas unidades

            if(CuentaUnidad::where('id_presup_unidad', $infoPresuUnidad->id)->first()){
               // PASAR A PANTALLA REQUERIMIENTOS
                return ['success' => 4];
            }else{
               return ['success' => 5];
            }

        }else{
            // no está creado aun, asi que agregar a pendientes
            return ['success' => 6];
        }
    }

    // retorna vista donde están los requerimientos por año
    public function indexRequerimientosUnidades($idanio){

        // obtener usuario
        $user = Auth::user();

        // conseguir el departamento
        $infoDepartamento = P_UsuarioDepartamento::where('id_usuario', $user->id)->first();
        $infoAnio = P_AnioPresupuesto::where('id', $idanio)->first();

        $txtanio = $infoAnio->nombre;
        $bloqueo = $infoAnio->permiso;

        $infoPresuUnidad = P_PresupUnidad::where('id_anio', $idanio)
            ->where('id_departamento', $infoDepartamento->id_departamento)
            ->first();

        $monto = CuentaUnidad::where('id_presup_unidad', $infoPresuUnidad->id)->sum('saldo_inicial_fijo');

        $monto = '$' . number_format((float)$monto, 2, '.', ',');

        $conteo = RequisicionUnidad::where('id_presup_unidad', $infoPresuUnidad->id)->count();
        if($conteo == null){
            $conteo = 1;
        }else{
            $conteo += 1;
        }

        $idpresubunidad = $infoPresuUnidad->id;

        return view('backend.admin.presupuestounidad.requerimientos.requerimientosunidad.vistarequerimientosunidad', compact('monto'
        , 'txtanio', 'idanio', 'conteo', 'idpresubunidad', 'bloqueo'));
    }

    // retorna tabla donde están los requerimientos por año
    public function tablaRequerimientosUnidades($idpresubunidad){

        // listado de requisiciones
        $listaRequisicion = RequisicionUnidad::where('id_presup_unidad', $idpresubunidad)
            ->orderBy('fecha', 'ASC')
            ->get();

        $contador = 0;
        foreach ($listaRequisicion as $info){
            $contador += 1;
            $info->numero = $contador;
            $info->fecha = date("d-m-Y", strtotime($info->fecha));
        }

        return view('backend.admin.presupuestounidad.requerimientos.requerimientosunidad.tablarequerimientosunidad', compact('listaRequisicion'));
    }

    // visualizar MODAL DE SALDOS para unidades. se recibe id p_presup_unidad
    public function infoModalSaldoUnidad($idpresup){

        // presupuesto
        $presupuesto = DB::table('cuenta_unidad AS cu')
            ->join('obj_especifico AS obj', 'cu.id_objespeci', '=', 'obj.id')
            ->select('obj.nombre', 'obj.id AS idcodigo', 'obj.codigo', 'cu.id', 'cu.saldo_inicial',
            'cu.saldo_inicial_fijo')
            ->where('cu.id_presup_unidad', $idpresup)
            ->get();

        foreach ($presupuesto as $pp){

            // ANTES AQUI ERA CALCULADO PARA OBTENER EL SALDO DE CADA CUENTA,
            // hoy se tomara directamente de la tabla cuenta_unidad que es saldo_inicial

            $pp->saldo_inicial = '$' . number_format((float)$pp->saldo_inicial, 2, '.', ',');
            $pp->saldo_inicial_fijo = '$' . number_format((float)$pp->saldo_inicial_fijo, 2, '.', ',');
        }

        return view('backend.admin.presupuestounidad.requerimientos.modal.vistamodalsaldounidad', compact('presupuesto'));
    }


    // VER ESTADO DE LOS MATERIALES DE UNA REQUISICION
    public function infoModalEstadoMaterial($idrequi){

        // presupuesto
        $arrayRequiDetalle = RequisicionUnidadDetalle::where('id_requisicion_unidad', $idrequi)->get();

        foreach ($arrayRequiDetalle as $info){

            $infoMaterial = P_Materiales::where('id', $info->id_material)->first();
            $info->nommaterial = $infoMaterial->descripcion;

            $estado = "Pendiente";

            // FALTA AGREGAR MAS ESTADOS.   13/06/2023



            $info->estado = $estado;
        }

        return view('backend.admin.presupuestounidad.requerimientos.modal.vistamodalmaterialesestado', compact('arrayRequiDetalle'));
    }







    function redondear_dos_decimal($valor){
        $float_redondeado = round($valor * 100) / 100;
        return $float_redondeado;
    }



    public function buscadorMaterialRequisicionUnidad(Request $request){

        if($request->get('query')){
            $query = $request->get('query');

            // idpresuunidad

            $arrayPresuDetalle = P_PresupUnidadDetalle::where('id_presup_unidad', $request->idpresuunidad)->get();

            $pilaIdMateriales = array();

            foreach ($arrayPresuDetalle as $dd){
                array_push($pilaIdMateriales, $dd->id_material);
            }

            // array de materiales materiales adicionales
            $arrayMateriales = P_Materiales::whereIn('id', $pilaIdMateriales)
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
                 <li onclick="modificarValorRequisicion(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->unido.'</a></li>
                ';
                    }
                }

                else{
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValorRequisicion(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->unido.'</a></li>
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

    // registrar una nueva requisición para unidades
    public function nuevoRequisicionUnidades(Request $request){

        $rules = array(
            'fecha' => 'required',
            'idpresubuni' => 'required',
            'idanio' => 'required'
        );

        // destino
        // necesidad

        // cantidad[]
        // datainfo []
        // materialDescriptivo[]

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            // VERIFICAR SI ES PERMITIDO REALIZAR REQUISICION
            $infoAnioPre = P_AnioPresupuesto::where('id', $request->idanio)->first();
            if($infoAnioPre->permiso == 0){
                return ['success' => 1];
            }

            // obtener usuario
            $user = Auth::user();

            // primero se crea, y después verificamos
            $addRequiUnidad = new RequisicionUnidad();
            $addRequiUnidad->id_presup_unidad = $request->idpresubuni;
            $addRequiUnidad->destino = $request->destino;
            $addRequiUnidad->fecha = $request->fecha;
            $addRequiUnidad->necesidad = $request->necesidad;
            $addRequiUnidad->solicitante = $user->nombre;

            $addRequiUnidad->save();

            for ($i = 0; $i < count($request->cantidad); $i++) {

                $infoCatalogo = P_Materiales::where('id', $request->datainfo[$i])->first();

                // en esta parte ya debera tener objeto específico
                $infoObjeto = ObjEspecifico::where('id', $infoCatalogo->id_objespecifico)->first();
                $txtObjeto = $infoObjeto->codigo . " - " . $infoObjeto->nombre;


                // como siempre busco material que estaban en el presupuesto, siempre encontrara
                // AQUI SE OBTIENE EL SALDO QUE TIENE ESA CUENTA CON EL MATERIAL
                $infoCuentaUnidad = CuentaUnidad::where('id_presup_unidad', $request->idpresubuni)
                    ->where('id_objespeci', $infoCatalogo->id_objespecifico)
                    ->first();


                // DEBO MULTIPLICAR LA CANTIDAD QUE SE PIDE X EL PRECIO QUE ESTE MATERIAL TIENE ACTUALMENTE
                $multiplicar = $request->cantidad[$i] * $infoCatalogo->costo;

                // OBTENER EL DINERO ACTUAL DEL CODIGO Y RESTARLO
                $redondeo1 = $infoCuentaUnidad->saldo_inicial;
                $redondeo2 = $multiplicar;

                $resta = $redondeo1 - $redondeo2;

                // DINERO NO ALCALZA PARA BAJARLE
                if($resta < 0){

                    // RESTANDO DE LA CUENTA UNIDAD
                    $restanteFormat = '$' . number_format((float)$infoCuentaUnidad->saldo_inicial, 2, '.', ',');
                    // MULTIPLICADO CANTIDAD * MONTO DEL MATERIAL
                    $multiplicar = '$' . number_format((float)$multiplicar, 2, '.', ',');

                    return ['success' => 1, 'fila' => $i,
                        'obj' => $txtObjeto,
                        'saldoactual' => $restanteFormat,
                        'solicita' => $multiplicar, // el usuario solicita este dinero
                    ];
                }else{

                    $rDetalle = new RequisicionUnidadDetalle();
                    $rDetalle->id_requisicion_unidad = $addRequiUnidad->id;
                    $rDetalle->id_material = $request->datainfo[$i];
                    $rDetalle->cantidad = $request->cantidad[$i];
                    $rDetalle->material_descripcion = $request->materialDescriptivo[$i];
                    $rDetalle->agrupado = 0;
                    $rDetalle->dinero = $infoCatalogo->costo; // lo que vale el material en ese momento
                    $rDetalle->dinero_fijo = $infoCatalogo->costo; // lo que vale el material en ese momento
                    $rDetalle->cancelado = 0;
                    $rDetalle->save();

                    // DESCONTAR AL CODIGO

                    CuentaUnidad::where('id', $infoCuentaUnidad->id)->update([
                        'saldo_inicial' => $resta,
                    ]);

                }
            }


            $contador = RequisicionUnidadDetalle::where('id_requisicion_unidad', $addRequiUnidad->id)->count();
            $contador += 1;

            DB::commit();
            return ['success' => 2, 'contador' => $contador];

        }catch(\Throwable $e){
            Log::info('ERROR ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // borrar requisición de unidades
    public function borrarRequisicionUnidades(Request $request){

        $regla = array(
            'id' => 'required', // id requisicion unidad
            'idanio' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        if(RequisicionUnidad::where('id', $request->id)->first()){

            $infoAnio = P_AnioPresupuesto::where('id', $request->idanio)->first();

            if($infoAnio->permiso == 0){
                // no hay permiso para modificar requisiciones
                return ['success' => 1];
            }

            // buscar si no hay ningún material ya cotizado
            if(CotizacionUnidad::where('id_requisicion_unidad', $request->id)->first()){
                // SE ENCONTRO UN MATERIAL COTIZADO, RETORNAR.
                return ['success' => 2];
            }

            // obtener todos los ID REQUISICION DETALLE CON EL ID REQUISICION
            $arrayID = RequisicionUnidadDetalle::where('id_requisicion_unidad', $request->id)
                ->select('id')
                ->get();





            // LIBERAR SALDO RETENIDO
            CuentaUnidadRetenido::whereIn('id_requi_detalle', $arrayID)->delete();

            // borrar listado detalle
            RequisicionUnidadDetalle::where('id_requisicion_unidad', $request->id)->delete();

            // borrar requisicion
            RequisicionUnidad::where('id', $request->id)->delete();

            return ['success' => 3];
        }else{
            return ['success' => 99];
        }
    }

    // INFORMACION DE LA REQUISICION DE LA UNIDAD (BOTON EDITAR)
    function informacionRequisicionUnidad(Request $request){
        $rules = array(
            'id' => 'required', // id fila requisicion
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){
            return ['success' => 0];
        }

        if($infoRequiUnidad = RequisicionUnidad::where('id', $request->id)->first()){

            $detalle = RequisicionUnidadDetalle::where('id_requisicion_unidad', $request->id)
                ->orderBy('id', 'ASC')
                ->get();

            // REGLA: SI UN MATERIAL ESTA AGRUPADO NO PODRA EDITAR DATOS
            // 1: si puede editar
            // 0: no puede
            $puedeEditar = 1;


            foreach ($detalle as $deta) {

                if($deta->agrupado == 1){
                    $puedeEditar = 0; // ya no puede actualizar requisicion detalle
                }

                $multi = ($deta->cantidad * $deta->dinero);
                $multi = number_format((float)$multi, 2, '.', ',');
                $deta->multiplicado = $multi;

                $infoCatalogo = P_Materiales::where('id', $deta->id_material)->first();

                //-------------------------------------------------

                $infoObjeto = ObjEspecifico::where('id', $infoCatalogo->id_objespecifico)->first();
                $infoUnidad = P_UnidadMedida::where('id', $infoCatalogo->id_unidadmedida)->first();

                $unidoCodigo = $infoObjeto->codigo . " - " . $infoObjeto->nombre;
                $unidoNombre = $infoCatalogo->descripcion . " - " . $infoUnidad->nombre;

                $deta->descripcion = $unidoNombre;
                $deta->codigo = $unidoCodigo;
            }


            return ['success' => 1, 'info' => $infoRequiUnidad, 'detalle' => $detalle, 'btneditar' => $puedeEditar];
        }
        return ['success' => 2];
    }

    // modificar las requisiciones de unidad
    public function editarRequisicionUnidad(Request $request){

        $regla = array(
            'idrequisicion' => 'required', // id requisicion unidad
            'idanio' => 'required'
        );

        $validator = Validator::make($request->all(), $regla);

        if ($validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $infoAnio = P_AnioPresupuesto::where('id', $request->idanio)->first();

            if($infoAnio->permiso == 0){
                // no hay permiso para modificar requisiciones
                return ['success' => 1];
            }


            // BLOQUEAR SI 1 MATERIAL ESTA AGRUPADO

            $infoRequiDetalle = RequisicionUnidadDetalle::where('id_requisicion_unidad', $request->idrequisicion)->get();

            foreach ($infoRequiDetalle as $dato){

                // BLOQUEADO PORQUE HAY 1 MATERIAL AGRUPADO YA
                if($dato->agrupado == 1){
                    return ['success' => 2];
                }
            }


            RequisicionUnidad::where('id', $request->idrequisicion)->update([
                'destino' => $request->destino,
                'fecha' => $request->fecha,
                'necesidad' => $request->necesidad,
            ]);

            DB::commit();
            return ['success' => 3];

        }catch(\Throwable $e){
            Log::info('ee ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // cancelar material de requisicion unidad detalle
    public function cancelarMaterialRequisicionUnidad(Request $request){

       // $request   (id) de requisicion unidad detalle

        $regla = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $regla);

        if ($validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $infoRequiDetalle = RequisicionUnidadDetalle::where('id', $request->id)->first();
            $infoRequicision = RequisicionUnidad::where('id', $infoRequiDetalle->id_requisicion_unidad)->first();
            $infoMaterial = P_Materiales::where('id', $infoRequiDetalle->id_material)->first();


            // no se puede cancelar porque ya esta agrupado
            if($infoRequiDetalle->agrupado == 1){
                return ['success' => 1];
            }

            // hacer esto 1 sola vez
            if($infoRequiDetalle->cancelado == 0){

                RequisicionUnidadDetalle::where('id', $infoRequiDetalle->id)->update([
                    'cancelado' => 1,
                ]);

                // DEVOLVER DINERO A SU CODIGO



                // obtener la cuenta unidad
                $infoCuenta = CuentaUnidad::where('id_presup_unidad', $infoRequicision->id_presup_unidad)
                    ->where('id_objespeci', $infoMaterial->id_objespecifico)
                    ->first();

                $suma = $infoCuenta->saldo_inicial + ($infoRequiDetalle->dinero_fijo * $infoRequiDetalle->cantidad);

                CuentaUnidad::where('id', $infoCuenta->id)->update([
                    'saldo_inicial' => $suma,
                ]);


                DB::commit();
                return ['success' => 2];
            }else{
                return ['success' => 2];
            }

        }catch(\Throwable $e){
            Log::info('ee ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }



    // DENEGAR UN AGRUPADO COMPLETAMENTE
    public function denegarAgrupadoPorUCP(Request $request){

        $regla = array(
            'id' => 'required', // id requisicion_agrupada
            'nota' => 'required'
        );

        $validator = Validator::make($request->all(), $regla);

        if ($validator->fails()) {
            return ['success' => 0];
        }


        // VERIFICAR SI SE PUEDE DENEGAR, SI 1 MATERIAL DEL AGRUPADO ESTA COTIZADO,
        // YA NO SE PODRA CANCELAR

        $infoAgrupado = RequisicionAgrupada::where('id', $request->id)->first();

        if ($infoAgrupado->estado == 1) {
            // YA ESTE DENEGADO ESTE AGRUPADO
            return ['success' => 1];
        }

        // BUSCAR QUE UN MATERIAL DE ESTE AGRUPADO ESTE EN UNA COTIZACION, SI LO ESTA, YA NO PODRA CANCELAR
        // TODOS EL AGRUPADO

        $arrayAgrupadoDetalle = RequisicionAgrupadaDetalle::where('id_requi_agrupada', $infoAgrupado->id)->get();

        foreach ($arrayAgrupadoDetalle as $dato){


            // TAN SOLO QUE HAYA UNA COTIZACION YA NO SE PUEDE DENEGAR, YA QUE PARA CANCELAR
            // SE DEBE DENEGAR TODOS EL AGRUPADO
            if(CotizacionUnidadDetalle::where('id_requi_unidaddetalle', $dato->id_requi_unidad_detalle)->first()){

                // UN MATERIAL YA ESTA COTIZADO (APROBADO O NO) DE ESTE AGRUPADO, YA NO SE PUEDE DENEGAR
                return ['success' => 2];
            }
        }


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


                DB::beginTransaction();

                try {

                    // SOLO HACERLO 1 SOLA VEZ
                    if ($infoAgrupado->estado == 0) {

                        // ACTUALIZAR DATOS

                        RequisicionAgrupada::where('id', $infoAgrupado->id)->update([
                            'estado' => 1,
                            'nota_cancelado' => $request->nota,
                            'documento' => $nomDocumento
                        ]);


                        // DEVOLVER DINERO

                        foreach ($arrayAgrupadoDetalle as $info) {

                            $infoRequiDetalle = RequisicionUnidadDetalle::where('id', $info->id_requi_unidad_detalle)->first();
                            $infoRequiUnidad = RequisicionUnidad::where('id', $infoRequiDetalle->id_requisicion_unidad)->first();
                            $infoMaterial = P_Materiales::where('id', $infoRequiDetalle->id_material)->first();

                            // obtener la cuenta unidad
                            $infoCuenta = CuentaUnidad::where('id_presup_unidad', $infoRequiUnidad->id_presup_unidad)
                                ->where('id_objespeci', $infoMaterial->id_objespecifico)
                                ->first();

                            $suma = $infoCuenta->saldo_inicial + ($infoRequiDetalle->dinero_fijo * $infoRequiDetalle->cantidad);

                            RequisicionUnidadDetalle::where('id', $infoRequiDetalle->id)->update([
                                'cancelado' => 1
                            ]);

                            CuentaUnidad::where('id', $infoCuenta->id)->update([
                                'saldo_inicial' => $suma,
                            ]);
                        }

                        // TODOS CORRECTO
                        DB::commit();
                        return ['success' => 3];
                    }

                    // solo decir que ya estuvo
                    return ['success' => 3];

                } catch (\Throwable $e) {
                    Log::info('ee ' . $e);
                    DB::rollback();
                    return ['success' => 99];
                }



            } else {

                return ['success' => 99]; // error
            }
        }
        else{
            // NO LLEVA DOCUMENTO

            DB::beginTransaction();

            try {

                // SOLO HACERLO 1 SOLA VEZ
                if ($infoAgrupado->estado == 0) {


                    // REGISTRAR

                    RequisicionAgrupada::where('id', $infoAgrupado->id)->update([
                        'estado' => 1,
                        'nota_cancelado' => $request->nota,
                    ]);



                    // DEVOLVER DINERO

                    foreach ($arrayAgrupadoDetalle as $info) {

                        $infoRequiDetalle = RequisicionUnidadDetalle::where('id', $info->id_requi_unidad_detalle)->first();
                        $infoRequiUnidad = RequisicionUnidad::where('id', $infoRequiDetalle->id_requisicion_unidad)->first();
                        $infoMaterial = P_Materiales::where('id', $infoRequiDetalle->id_material)->first();

                        // obtener la cuenta unidad
                        $infoCuenta = CuentaUnidad::where('id_presup_unidad', $infoRequiUnidad->id_presup_unidad)
                            ->where('id_objespeci', $infoMaterial->id_objespecifico)
                            ->first();

                        $suma = $infoCuenta->saldo_inicial + ($infoRequiDetalle->dinero_fijo * $infoRequiDetalle->cantidad);

                        RequisicionUnidadDetalle::where('id', $infoRequiDetalle->id)->update([
                            'cancelado' => 1
                        ]);


                        CuentaUnidad::where('id', $infoCuenta->id)->update([
                            'saldo_inicial' => $suma,
                        ]);
                    }

                    // TODOS CORRECTO
                    DB::commit();
                    return ['success' => 3];
                }

                // solo decir que ya estuvo
                return ['success' => 3];

            } catch (\Throwable $e) {
                Log::info('ee ' . $e);
                DB::rollback();
                return ['success' => 99];
            }

        }
    }








}

<?php

namespace App\Http\Controllers\Backend\PresupuestoUnidad\Presupuesto;

use App\Http\Controllers\Controller;
use App\Models\Cuenta;
use App\Models\ObjEspecifico;
use App\Models\P_AnioPresupuesto;
use App\Models\P_Departamento;
use App\Models\P_Estado;
use App\Models\P_Materiales;
use App\Models\P_MaterialesDetalle;
use App\Models\P_PresupUnidad;
use App\Models\P_PresupUnidadDetalle;
use App\Models\P_UnidadMedida;
use App\Models\P_UsuarioDepartamento;
use App\Models\Rubro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ConfiguracionPresupuestoUnidadController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    // retorna vista para revisión de presupuesto por unidad y año
    public function indexRevisionPresupuestoUnidad(){

        $departamentos = P_Departamento::orderBy('nombre')->get();
        $anios = P_AnioPresupuesto::orderBy('nombre')->get();

        return view('backend.admin.presupuestounidad.revisar.vistarevisar', compact('departamentos', 'anios'));
    }

    // retorna vista para generar reportes y consolidado de presupuesto de unidades
    public function indexReportePresupuestoUnidad(){
        $departamentos = P_Departamento::orderBy('nombre')->get();
        $anios = P_AnioPresupuesto::orderBy('nombre')->get();

        return view('backend.admin.presupuestounidad.reportes.vistareportespresupuestounidad', compact('departamentos', 'anios'));
    }

    // retorna vista para crear nuevo presupuesto de la unidad
    public function indexCrearPresupuestoUnidad(){

        // verificar si hay presupuesto pendiente por crear

        $idusuario = Auth::id();

        // si este id de usuario no esta registrado con departamento. mostrar alerta
        if(!P_UsuarioDepartamento::where('id_usuario', $idusuario)->first()){
            return view('backend.admin.presupuestounidad.crear.vistadepartamentonoasignado');
        }

        $infoDepa = P_UsuarioDepartamento::where('id_usuario', $idusuario)->first();

        // solo sera necesario verificar con tabla presub_unidad

        // obtener lista de anios del departamento
        $listaAnios = P_PresupUnidad::where('id_departamento', $infoDepa->id_departamento)->get();

        $pila = array();

        foreach ($listaAnios as $p){
            array_push($pila, $p->id_anio);
        }

        $listado = P_AnioPresupuesto::whereNotIn('id', $pila)->get();

        // redireccionar a vista si ya no hay presupuesto por crear
        if($listado->isEmpty()){
            return view('backend.admin.presupuestounidad.crear.vistanohayanionuevo');
        }

        $unidad = P_UnidadMedida::orderBy('nombre')->get();

        return view('backend.admin.presupuestounidad.crear.vistacrearpresupuestounidad', compact( 'listado', 'unidad'));
    }

    // esta vista retorna con el presupuesto nuevo. y al cargarse desactiva el modal loading de carga
    public function contenedorNuevoPresupuesto(){

        $rubro = Rubro::orderBy('codigo', 'ASC')->get();

        $resultsBloque = array();
        $index = 0;
        $resultsBloque2 = array();
        $index2 = 0;
        $resultsBloque3 = array();
        $index3 = 0;

        // agregar cuentas
        foreach($rubro as $secciones){
            array_push($resultsBloque,$secciones);

            $subSecciones = Cuenta::where('id_rubro', $secciones->id)
                ->orderBy('codigo', 'ASC')
                ->get();

            // agregar objetos
            foreach ($subSecciones as $lista){

                array_push($resultsBloque2, $lista);

                $subSecciones2 = ObjEspecifico::where('id_cuenta', $lista->id)
                    ->orderBy('codigo', 'ASC')
                    ->get();

                // agregar materiales

                foreach ($subSecciones2 as $ll){

                    array_push($resultsBloque3, $ll);



                    if($ll->codigo == 61109){
                        $ll->nombre = $ll->nombre . " ( ACTIVOS FIJOS MENORES A $600.00 )";
                    }

                    $subSecciones3 = P_Materiales::where('id_objespecifico', $ll->id)
                        ->orderBy('descripcion', 'ASC')
                        ->where('visible', 1) // solo materiales visibles, ya que admin puede ocultar
                        ->get();


                    foreach ($subSecciones3 as $subLista){

                        $infoUnidad = P_UnidadMedida::where('id', $subLista->id_unidadmedida)->first();
                        $subLista->unimedida = $infoUnidad->nombre;
                    }

                    $resultsBloque3[$index3]->material = $subSecciones3;
                    $index3++;
                }

                $resultsBloque2[$index2]->objeto = $subSecciones2;
                $index2++;
            }

            $resultsBloque[$index]->cuenta = $subSecciones;
            $index++;
        }

        return view('backend.admin.presupuestounidad.crear.contenedorcrearpresupuesto', compact('rubro'));
    }

    // busca material del catálogo de materiales para unidades
    public function buscarMaterialPresupuestoUnidad(Request $request){

        $data = P_Materiales::where('descripcion', 'LIKE', "%{$request->texto}%")
            ->take(25)
            ->get();

        $haymaterial = true;
        if(sizeof($data) == 0){
            $haymaterial = false;
        }

        foreach ($data as $dd){

            $infoObj = ObjEspecifico::where('id', $dd->id_objespecifico)->first();
            $infoCuenta = Cuenta::where('id', $infoObj->id_cuenta)->first();
            $infoRubro = Rubro::where('id', $infoCuenta->id_rubro)->first();

            $dd->objeto = $infoObj->codigo . " - " . $infoObj->nombre;
            $dd->cuenta = $infoCuenta->codigo . " - " . $infoCuenta->nombre;
            $dd->rubro = $infoRubro->codigo . " - " . $infoRubro->nombre;
        }

        return ['success' => 1, 'info' => $data, 'conteo' => $haymaterial];
    }

    // crea el nuevo presupuesto del año correspondiente
    public function nuevoPresupuestoUnidades(Request $request){

        $rules = array(
            'anio' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        // obtener información del usuario, saber quien esta agregando el presupuesto
        $idusuario = Auth::id();
        $infoDepa = P_UsuarioDepartamento::where('id_usuario', $idusuario)->first();

        // verificar que aun no exista el presupuesto
        if(P_PresupUnidad::where('id_anio', $request->anio)
            ->where('id_departamento', $infoDepa->id_departamento)
            ->first()){
            return ['success' => 1];
        }

        DB::beginTransaction();

        try {

            $pr = new P_PresupUnidad();
            $pr->id_anio = $request->anio;
            $pr->id_departamento = $infoDepa->id_departamento;
            $pr->id_estado = 1; // editable
            $pr->save();

            if($request->idmaterial != null) {
                for ($i = 0; $i < count($request->idmaterial); $i++) {

                    $prDetalle = new P_PresupUnidadDetalle();
                    $prDetalle->id_presup_unidad = $pr->id;
                    $prDetalle->id_material = $request->idmaterial[$i];
                    $prDetalle->cantidad = $request->unidades[$i];
                    $prDetalle->periodo = $request->periodo[$i];
                    $prDetalle->save();
                }
            }

            // ingreso de materiales extra
            if($request->descripcionfila != null) {
                for ($j = 0; $j < count($request->descripcionfila); $j++) {

                    $mtrDetalle = new P_MaterialesDetalle();
                    $mtrDetalle->id_presup_unidad = $pr->id;
                    $mtrDetalle->id_unidadmedida = $request->unidadmedida[$j];
                    $mtrDetalle->descripcion = $request->descripcionfila[$j];
                    $mtrDetalle->costo = $request->costoextrafila[$j];
                    $mtrDetalle->cantidad = $request->cantidadextrafila[$j];
                    $mtrDetalle->periodo = $request->periodoextrafila[$j];
                    $mtrDetalle->save();
                }
            }

            DB::commit();
            return ['success' => 2];
        }catch(\Throwable $e){
            DB::rollback();
            Log::info('ee' . $e);
            return ['success' => 99];
        }
    }

    // retorna vista editar un presupuesto
    public function indexEditarPresupuestoUnidad(){

        // verificar si hay presupuesto pendiente por crear

        $idusuario = Auth::id();

        // si este id de usuario no esta registrado con departamento. mostrar alerta
        if(!P_UsuarioDepartamento::where('id_usuario', $idusuario)->first()){
            return view('backend.admin.presupuestounidad.crear.vistadepartamentonoasignado');
        }

        $infoDepa = P_UsuarioDepartamento::where('id_usuario', $idusuario)->first();

        // obtener lista de anios del departamento
        $listaAnios = P_PresupUnidad::where('id_departamento', $infoDepa->id_departamento)->get();

        $pilaAnios = array();

        foreach ($listaAnios as $p){
            array_push($pilaAnios, $p->id_anio);
        }

        $listado = P_AnioPresupuesto::whereIn('id', $pilaAnios)->get();

        return view('backend.admin.presupuestounidad.editar.vistaanioeditarpresupuesto', compact('listado'));
    }

    // retorna vista para seleccionar año para editar un presupuesto
    public function indexPresupuestoUnidadEdicion($id){
        // mando ID del año
        return view('backend.admin.presupuestounidad.editar.vistaeditarpresupuesto', compact( 'id'));
    }

    // retorna contenedor para editar un presupuesto
    public function contenedorEditarPresupuestoUnidad($idAnio){
        // recibimos id año

        $idusuario = Auth::id();

        // si este id de usuario no esta registrado con departamento. mostrar alerta
        if(!P_UsuarioDepartamento::where('id_usuario', $idusuario)->first()){
            return view('backend.admin.presupuestounidad.crear.vistadepartamentonoasignado');
        }

        $infoDepa = P_UsuarioDepartamento::where('id_usuario', $idusuario)->first();

        // siempre habra un registro
        $infoPresupUnidad = P_PresupUnidad::where('id_departamento', $infoDepa->id_departamento)
            ->where('id_anio', $idAnio)->first();

        // listado de presupuesto por anio y departamento
        $listadoPresupuesto = P_PresupUnidad::where('id_departamento', $infoDepa->id_departamento)
            ->where('id_anio', $idAnio)->get();

        $pila = array();

        foreach ($listadoPresupuesto as $lp){
            array_push($pila, $lp->id);
        }

        $idpresupuesto = $infoPresupUnidad->id;
        $estado = $infoPresupUnidad->id_estado;
        $preanio = P_AnioPresupuesto::where('id', $idAnio)->pluck('nombre')->first();


        $unidad = P_UnidadMedida::orderBy('nombre')->get();
        $rubro = Rubro::orderBy('codigo')->get();


        $resultsBloque = array();
        $index = 0;
        $resultsBloque2 = array();
        $index2 = 0;
        $resultsBloque3 = array();
        $index3 = 0;

        $totalvalor = 0;


        // agregar cuentas
        foreach($rubro as $secciones){
            array_push($resultsBloque,$secciones);

            $sumaRubro = 0;

            $subSecciones = Cuenta::where('id_rubro', $secciones->id)
                ->orderBy('codigo', 'ASC')
                ->get();

            // agregar objetos
            foreach ($subSecciones as $lista){

                array_push($resultsBloque2, $lista);

                $subSecciones2 = ObjEspecifico::where('id_cuenta', $lista->id)
                    ->orderBy('codigo', 'ASC')
                    ->get();

                $sumaObjetoTotal = 0;

                // agregar materiales
                foreach ($subSecciones2 as $ll){

                    array_push($resultsBloque3, $ll);

                    if($ll->codigo == 61109){
                        $ll->nombre = $ll->nombre . " ( ACTIVOS FIJOS MENORES A $600.00 )";
                    }

                    $subSecciones3 = P_Materiales::where('id_objespecifico', $ll->id)
                        ->where('visible', 1)
                        ->orderBy('descripcion', 'ASC')
                        ->get();

                    $sumaObjeto = 0;

                    foreach ($subSecciones3 as $subLista){

                        $uni = P_UnidadMedida::where('id', $subLista->id_unidadmedida)->first();
                        $unimedida = $uni->nombre;

                        $subLista->unimedida = $unimedida;

                        // ingresar los datos a editar
                        if($data = P_PresupUnidadDetalle::where('id_presup_unidad', $infoPresupUnidad->id)
                            ->where('id_material', $subLista->id)->first()){

                            $subLista->cantidad = $data->cantidad;
                            $subLista->periodo = $data->periodo;
                            $total = ($subLista->costo * $data->cantidad) * $data->periodo;
                            $subLista->total = '$' . number_format((float)$total, 2, '.', '');

                            $sumaObjeto = $sumaObjeto + $total;
                        }else{
                            $subLista->cantidad = '';
                            $subLista->periodo = '';
                            $subLista->total = '';
                        }
                    }

                    $sumaObjetoTotal = $sumaObjetoTotal + $sumaObjeto;
                    $ll->sumaobjeto = number_format((float)$sumaObjeto, 2, '.', '');

                    $resultsBloque3[$index3]->material = $subSecciones3;
                    $index3++;
                }

                $sumaRubro = $sumaRubro + $sumaObjetoTotal;
                $lista->sumaobjetototal = number_format((float)$sumaObjetoTotal, 2, '.', '');

                $resultsBloque2[$index2]->objeto = $subSecciones2;
                $index2++;
            }

            $totalvalor = $totalvalor + $sumaRubro;
            $secciones->sumarubro = number_format((float)$sumaRubro, 2, '.', '');

            $resultsBloque[$index]->cuenta = $subSecciones;
            $index++;
        }

        $totalvalor = number_format((float)$totalvalor, 2, '.', '');

        // obtener listado de materiales extra
        $listado = P_MaterialesDetalle::where('id_presup_unidad', $infoPresupUnidad->id)->get();

        foreach ($listado as $dd){
            $infoMedida = P_UnidadMedida::where('id', $dd->id_unidadmedida)->first();
            $dd->unidadmedida = $infoMedida->nombre;
        }

        return view('backend.admin.presupuestounidad.editar.contenedoreditarpresupuesto', compact( 'estado', 'totalvalor', 'listado', 'idAnio', 'idpresupuesto', 'preanio', 'unidad', 'rubro'));
    }

    // petición para editar un presupuesto si no esta en revisión o aprobado
    public function editarPresupuestoUnidad(Request $request){

        $rules = array(
            'idpresupuesto' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

        $infoPresu = P_PresupUnidad::where('id', $request->idpresupuesto)->first();

        if($infoPresu->estado == 2){
            // presupuesto esta en revisión
            return ['success' => 1];
        }

        if($infoPresu->estado == 3){
            // presupuesto esta aprobado
            return ['success' => 2];
        }

            // borrar todos el presupuesto base
            P_PresupUnidadDetalle::where('id_presup_unidad', $request->idpresupuesto)->delete();

            // borrar materiales extra
            P_MaterialesDetalle::where('id_presup_unidad', $request->idpresupuesto)->delete();

            if($request->unidades != null) {
                // crear de nuevo presupuesto base
                for ($i = 0; $i < count($request->unidades); $i++) {

                    $prDetalle = new P_PresupUnidadDetalle();
                    $prDetalle->id_presup_unidad = $request->idpresupuesto;
                    $prDetalle->id_material = $request->idmaterial[$i];
                    $prDetalle->cantidad = $request->unidades[$i];
                    $prDetalle->periodo = $request->periodo[$i];
                    $prDetalle->save();
                }
            }

            // ingresar materiales extra

            if($request->descripcionfila != null) {
                for ($j = 0; $j < count($request->descripcionfila); $j++) {

                    $mtrDetalle = new P_MaterialesDetalle();
                    $mtrDetalle->id_presup_unidad = $request->idpresupuesto;
                    $mtrDetalle->id_unidadmedida = $request->unidadmedida[$j];
                    $mtrDetalle->descripcion = $request->descripcionfila[$j];
                    $mtrDetalle->costo = $request->costoextrafila[$j];
                    $mtrDetalle->cantidad = $request->cantidadextrafila[$j];
                    $mtrDetalle->periodo = $request->periodoextrafila[$j];
                    $mtrDetalle->save();
                }
            }

            DB::commit();

            return ['success' => 3];
        }catch(\Throwable $e){
            Log::info('err ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // retorna vista revisar presupuesto y ver si se aprueba, se envía ID departamento y ID unidad
    public function indexPresupuestoParaAprobar($iddepa, $idanio){

        // buscar presupuesto
        if($infoPresupuesto = P_PresupUnidad::where('id_anio', $idanio)
            ->where('id_departamento', $iddepa)
            ->first()){

            $infoAnio = P_AnioPresupuesto::where('id', $infoPresupuesto->id_anio)->first();

            $estado = $infoPresupuesto->id_estado;

            $arrayestado = P_Estado::orderBy('id', 'ASC')->get();

            $idpre = $infoPresupuesto->id;

            return view('backend.admin.presupuestounidad.revisar.presupuestoindividual', compact( 'iddepa', 'idpre','arrayestado', 'idanio', 'infoAnio', 'estado'));
        }else{
            // presupuesto no encontrado
            return view('backend.admin.presupuestounidad.revisar.vistapresupuestonoencontrado');
        }
    }

    // retorna contenedor de presupuesto para revisión
    public function contenedorPresupuestoIndividual($iddepa, $idanio){
        // id anio y departamento

        $presupuesto = P_PresupUnidad::where('id_anio', $idanio)
            ->where('id_departamento', $iddepa)
            ->first();

        $estado = P_Estado::orderBy('id', 'ASC')->get();
        $preanio = P_AnioPresupuesto::where('id', $idanio)->pluck('nombre')->first();

        $idestado = $presupuesto->id_estado;

        $rubro = Rubro::orderBy('codigo', 'ASC')->get();
        $objeto = ObjEspecifico::orderBy('codigo', 'ASC')->get();

        $resultsBloque = array();
        $index = 0;
        $resultsBloque2 = array();
        $index2 = 0;
        $resultsBloque3 = array();
        $index3 = 0;

        $totalvalor = 0;

        $listadoPresupuesto = P_PresupUnidad::where('id_departamento', $iddepa)
            ->where('id_anio', $idanio)->get();

        $pila = array();

        foreach ($listadoPresupuesto as $lp){
            array_push($pila, $lp->id);
        }

        // agregar cuentas
        foreach($rubro as $secciones){
            array_push($resultsBloque,$secciones);

            $sumaRubro = 0;

            $subSecciones = Cuenta::where('id_rubro', $secciones->id)
                ->orderBy('codigo', 'ASC')
                ->get();

            // agregar objetos
            foreach ($subSecciones as $lista){

                array_push($resultsBloque2, $lista);

                $subSecciones2 = ObjEspecifico::where('id_cuenta', $lista->id)
                    ->orderBy('codigo', 'ASC')
                    ->get();

                $sumaObjetoTotal = 0;

                // agregar materiales
                foreach ($subSecciones2 as $ll){

                    array_push($resultsBloque3, $ll);

                    if($ll->codigo == 61109){
                        $ll->nombre = $ll->nombre . " ( ACTIVOS FIJOS MENORES A $600.00 )";
                    }

                    $subSecciones3 = P_Materiales::where('id_objespecifico', $ll->id)
                        ->orderBy('descripcion', 'ASC')
                        ->get();

                    $sumaObjeto = 0;

                    foreach ($subSecciones3 as $subLista){

                        $uni = P_UnidadMedida::where('id', $subLista->id_unidadmedida)->first();
                        $subLista->unimedida = $uni->nombre;

                        // ingresar los datos a editar
                        if($data = P_PresupUnidadDetalle::where('id_presup_unidad', $presupuesto->id)
                            ->where('id_material', $subLista->id)->first()){

                            $subLista->cantidad = $data->cantidad;
                            $subLista->periodo = $data->periodo;
                            $total = ($subLista->costo * $data->cantidad) * $data->periodo;
                            $subLista->total = number_format((float)$total, 2, '.', '');

                            $sumaObjeto = $sumaObjeto + $total;

                        }else{
                            $subLista->cantidad = '';
                            $subLista->periodo = '';
                            $subLista->total = '';
                        }
                    }

                    $sumaObjetoTotal = $sumaObjetoTotal + $sumaObjeto;
                    $ll->sumaobjeto = number_format((float)$sumaObjeto, 2, '.', '');

                    $resultsBloque3[$index3]->material = $subSecciones3;
                    $index3++;
                }

                $sumaRubro = $sumaRubro + $sumaObjetoTotal;
                $lista->sumaobjetototal = number_format((float)$sumaObjetoTotal, 2, '.', '');

                $resultsBloque2[$index2]->objeto = $subSecciones2;
                $index2++;
            }
            $totalvalor = $totalvalor + $sumaRubro;
            $secciones->sumarubro = number_format((float)$sumaRubro, 2, '.', '');

            $resultsBloque[$index]->cuenta = $subSecciones;
            $index++;
        }

        // obtener listado de materiales extra
        $listado = P_MaterialesDetalle::where('id_presup_unidad', $presupuesto->id)->get();

        foreach ($listado as $lista){
            $uni = P_UnidadMedida::where('id', $lista->id_unidadmedida)->first();
            $lista->simbolo = $uni->nombre;
        }

        $idpresupuesto = $presupuesto->id;

        $totalvalor = number_format((float)$totalvalor, 2, '.', ',');

        return view('backend.admin.presupuestounidad.revisar.contenedorpresupuestoindividual', compact( 'estado', 'idpresupuesto', 'idestado', 'totalvalor', 'objeto', 'listado', 'preanio', 'rubro'));
    }

    // petición para transferir material solicitado por una unidad y agregar a base de materiales
    public function transferirNuevoMaterial(Request $request){

        $regla = array(
            'objeto' => 'required',
            'idpresupuesto' => 'required',
            'idfila' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();

        try {

            $infoPresupuesto = P_PresupUnidad::where('id', $request->idpresupuesto)->first();

            if($infoPresupuesto->id_estado == 3){
                // si esta aprobado no se puede agregar
                return ['success' => 1];
            }

            $infoDepa = P_Departamento::where('id', $infoPresupuesto->id_departamento)->first();

            // obtener informacion del material extra detalle
            $info = P_MaterialesDetalle::where('id', $request->idfila)->first();

            // agregar a materiales base
            $base = new P_Materiales();
            $base->descripcion = strtoupper($info->descripcion);
            $base->id_unidadmedida = $info->id_unidadmedida;
            $base->id_objespecifico = $request->objeto;
            $base->costo = $info->costo;
            $base->visible = 1;
            $base->save();

            // agregar material a la unidad detalle
            $prDetalle = new P_PresupUnidadDetalle();
            $prDetalle->id_presup_unidad = $request->idpresupuesto;
            $prDetalle->id_material = $base->id;
            $prDetalle->cantidad = $info->cantidad;
            $prDetalle->periodo = $info->periodo;
            $prDetalle->save();

            // borrar el material extra
            P_MaterialesDetalle::where('id', $request->idfila)->delete();

            // DB::commit();
            return ['success' => 2, 'unidad' => $infoDepa->nombre];
        }catch(\Throwable $e){
            Log::info('ee' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // actualizar estado de un presupuesto
    public function editarEstadoPresupuesto(Request $request){

        $regla = array(
            'idpresupuesto' => 'required',
            'idestado' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(P_PresupUnidad::where('id', $request->idpresupuesto)->first()){

            P_PresupUnidad::where('id', $request->idpresupuesto)->update([
                'id_estado' => $request->idestado
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // verifica si todos los presupuestos esten aprobados para generar consolidado PDF
    public function verificarConsolidadoPresupuesto(Request $request){

        $rules = array(
            'anio' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        // veirificar que todos los presupuestos este aprobados

        // obtener listado de departamentos
        $depar = P_Departamento::orderBy('nombre')->get();
        $pila = array();

        foreach ($depar as $de){

            if($pre = P_PresupUnidad::where('id_anio', $request->anio)
                ->where('id_departamento', $de->id)->first()){

                if($pre->id_estado == 1){
                    array_push($pila, $de->id);
                }

            }else{
                // no esta creado aun
                array_push($pila, $de->id);
            }
        }

        $lista = P_Departamento::whereIn('id', $pila)
            ->orderBy('nombre', 'ASC')
            ->get();

        if($lista->isEmpty()){
            return ['success' => 1];
        }

        return ['success' => 2, 'lista' => $lista];
    }




}

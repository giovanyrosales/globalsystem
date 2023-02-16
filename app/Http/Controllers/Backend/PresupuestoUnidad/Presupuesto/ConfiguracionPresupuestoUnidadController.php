<?php

namespace App\Http\Controllers\Backend\PresupuestoUnidad\Presupuesto;

use App\Http\Controllers\Controller;
use App\Models\AreaGestion;
use App\Models\Cuenta;
use App\Models\FuenteRecursos;
use App\Models\LineaTrabajo;
use App\Models\ObjEspecifico;
use App\Models\P_AnioPresupuesto;
use App\Models\P_Departamento;
use App\Models\P_Estado;
use App\Models\P_Materiales;
use App\Models\P_MaterialesDetalle;
use App\Models\P_PresupUnidad;
use App\Models\P_PresupUnidadDetalle;
use App\Models\P_ProyectosAprobados;
use App\Models\P_ProyectosPendientes;
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
        $anios = P_AnioPresupuesto::orderBy('nombre', 'DESC')->get();

        return view('backend.admin.presupuestounidad.revisar.vistarevisar', compact('departamentos', 'anios'));
    }

    // retorna vista para generar reportes y consolidado de presupuesto de unidades
    public function indexReportePresupuestoUnidad(){
        $departamentos = P_Departamento::orderBy('nombre')->get();
        $anios = P_AnioPresupuesto::orderBy('nombre', 'DESC')->get();

        return view('backend.admin.presupuestounidad.reportes.vistareportespresupuestounidad', compact('departamentos', 'anios'));
    }
    // retorna vista para generar reportes en uaci de unidades
    public function indexReporteUaciUnidad(){
        $departamentos = P_Departamento::orderBy('nombre')->get();
        $anios = P_AnioPresupuesto::orderBy('nombre', 'DESC')->get();

        return view('backend.admin.presupuestounidad.reportes.vistareportesuaciunidad', compact('departamentos', 'anios'));
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

        // solo será necesario verificar con tabla presub_unidad

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

    // Esta vista retorna con el presupuesto nuevo. y al cargarse desactiva el modal loading de carga
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
            ->where('visible', 0) // solo buscar materiales visibles
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

        // verificar que a un no exista el presupuesto
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
            $pr->saldo_aprobado = 0; // cuando Presupuesto cree cuenta unidad esto se va a Setear.
            $pr->save();

            if($request->idmaterial != null) {
                for ($i = 0; $i < count($request->idmaterial); $i++) {

                    $infoMaterial = P_Materiales::where('id', $request->idmaterial[$i])->first();

                    $prDetalle = new P_PresupUnidadDetalle();
                    $prDetalle->id_presup_unidad = $pr->id;
                    $prDetalle->id_material = $request->idmaterial[$i];
                    $prDetalle->cantidad = $request->unidades[$i];
                    $prDetalle->precio = $infoMaterial->costo;
                    $prDetalle->periodo = $request->periodo[$i];
                    $prDetalle->save();
                }
            }

            // INGRESO DE MATERIALES EXTRA
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

            // INGRESO DE PROYECTOS EXTRA
            if($request->descripcionfilaproyecto != null) {
                for ($jp = 0; $jp < count($request->descripcionfilaproyecto); $jp++) {

                    $prdDetalle = new P_ProyectosPendientes();
                    $prdDetalle->id_presup_unidad = $pr->id;
                    $prdDetalle->descripcion = $request->descripcionfilaproyecto[$jp];
                    $prdDetalle->costo = $request->costoextrafilaproyecto[$jp];
                    $prdDetalle->save();
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

        $listado = P_AnioPresupuesto::whereIn('id', $pilaAnios)->orderBy('nombre', 'DESC')->get();

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


        $pilaArrayMaterialVisib = array();

        // estos materiales de mi presupuesto, pudieron haber sido ocultados, siempre
        // quiero que se muestren
        $arrayPresUniDetalle = P_PresupUnidadDetalle::where('id_presup_unidad', $infoPresupUnidad->id)->get();

        foreach ($arrayPresUniDetalle as $p){
            array_push($pilaArrayMaterialVisib, $p->id_material);
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

        // LISTADO DE PROYECTO APROBADOS
        $listadoProyectoAprobados = P_ProyectosAprobados::where('id_presup_unidad', $idpresupuesto)
            ->orderBy('descripcion', 'ASC')
            ->get();

        foreach ($listadoProyectoAprobados as $dd){

            $infoObjeto = ObjEspecifico::where('id', $dd->id_objespeci)->first();
            $infoFuenteR = ObjEspecifico::where('id', $dd->id_fuenter)->first();
            $infoLinea = ObjEspecifico::where('id', $dd->id_lineatrabajo)->first();
            $infoArea = ObjEspecifico::where('id', $dd->id_areagestion)->first();

            $dd->codigoobj = $infoObjeto->codigo;
            $dd->objeto = $infoObjeto->codigo . " - " . $infoObjeto->nombre;
            $dd->fuenterecurso = $infoFuenteR->codigo . " - " . $infoFuenteR->nombre;
            $dd->lineatrabajo = $infoLinea->codigo . " - " . $infoLinea->nombre;
            $dd->areagestion = $infoArea->codigo . " - " . $infoArea->nombre;

            $dd->costoFormat = '$' . number_format((float)$dd->costo, 2, '.', ',');
        }


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
                        ->where('visible', 1) // solo materiales visibles
                        ->orderBy('descripcion', 'ASC')
                        ->get();

                    $sumaObjeto = 0;

                    foreach ($subSecciones3 as $subLista){

                        $uni = P_UnidadMedida::where('id', $subLista->id_unidadmedida)->first();
                        $unimedida = $uni->simbolo;

                        $subLista->unimedida = $unimedida;

                        // ingresar los datos a editar
                        if($data = P_PresupUnidadDetalle::where('id_presup_unidad', $infoPresupUnidad->id)
                            ->where('id_material', $subLista->id)->first()){

                            $subLista->precio = $data->precio;

                            $subLista->cantidad = $data->cantidad;
                            $subLista->periodo = $data->periodo;
                            $total = ($data->precio * $data->cantidad) * $data->periodo;
                            $subLista->total = '$' . number_format((float)$total, 2, '.', ',');

                            $sumaObjeto = $sumaObjeto + $total;
                        }else{
                            $subLista->cantidad = '';
                            $subLista->periodo = '';
                            $subLista->total = '';
                            $subLista->precio = $subLista->costo;
                        }

                    }

                    foreach ($listadoProyectoAprobados as $lpa){
                        if($ll->codigo == $lpa->codigoobj){
                            $sumaObjeto += $lpa->costo;
                        }
                    }


                    $sumaObjetoTotal = $sumaObjetoTotal + $sumaObjeto;
                    $ll->sumaobjeto = number_format((float)$sumaObjeto, 2, '.', ',');

                    $resultsBloque3[$index3]->material = $subSecciones3;
                    $index3++;
                }

                $sumaRubro = $sumaRubro + $sumaObjetoTotal;
                $lista->sumaobjetototal = number_format((float)$sumaObjetoTotal, 2, '.', ',');

                $resultsBloque2[$index2]->objeto = $subSecciones2;
                $index2++;
            }

            $totalvalor = $totalvalor + $sumaRubro;
            $secciones->sumarubro = number_format((float)$sumaRubro, 2, '.', ',');

            $resultsBloque[$index]->cuenta = $subSecciones;
            $index++;
        }


        $totalvalor = number_format((float)$totalvalor, 2, '.', ',');

        // LISTADO DE MATERIALES
        $listado = P_MaterialesDetalle::where('id_presup_unidad', $infoPresupUnidad->id)
            ->orderBy('descripcion', 'ASC')
            ->get();

        foreach ($listado as $dd){
            $infoMedida = P_UnidadMedida::where('id', $dd->id_unidadmedida)->first();
            $dd->unidadmedida = $infoMedida->nombre;
        }

        // LISTADO DE PROYECTO
        $listadoProyecto = P_ProyectosPendientes::where('id_presup_unidad', $infoPresupUnidad->id)
            ->orderBy('descripcion', 'ASC')
            ->get();


        return view('backend.admin.presupuestounidad.editar.contenedoreditarpresupuesto', compact( 'estado', 'totalvalor',
            'listado', 'idAnio', 'idpresupuesto', 'preanio', 'unidad', 'rubro', 'listadoProyecto', 'listadoProyectoAprobados'));
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

        if($infoPresu->id_estado == 2){
            // presupuesto esta en revisión
            return ['success' => 1];
        }

        if($infoPresu->id_estado == 3){
            // presupuesto esta aprobado
            return ['success' => 2];
        }

            // borrar todos el presupuesto base
            P_PresupUnidadDetalle::where('id_presup_unidad', $request->idpresupuesto)->delete();

            // borrar materiales extra
            P_MaterialesDetalle::where('id_presup_unidad', $request->idpresupuesto)->delete();

            // borrar solicitud de proyectos
            P_ProyectosPendientes::where('id_presup_unidad', $request->idpresupuesto)->delete();


            if($request->unidades != null) {
                // crear de nuevo presupuesto base
                for ($i = 0; $i < count($request->unidades); $i++) {

                    $infoMaterial = P_Materiales::where('id', $request->idmaterial[$i])->first();

                    $prDetalle = new P_PresupUnidadDetalle();
                    $prDetalle->id_presup_unidad = $request->idpresupuesto;
                    $prDetalle->id_material = $request->idmaterial[$i];
                    $prDetalle->cantidad = $request->unidades[$i];
                    $prDetalle->precio = $infoMaterial->costo;
                    $prDetalle->periodo = $request->periodo[$i];
                    $prDetalle->save();
                }
            }

            // INGRESO DE MATERIALES EXTRA

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

            // INGRESO DE SOLICITUD DE PROYECTOS
            if($request->descripcionfilaproyecto != null) {
                for ($jp = 0; $jp < count($request->descripcionfilaproyecto); $jp++) {

                    $prdDetalle = new P_ProyectosPendientes();
                    $prdDetalle->id_presup_unidad = $request->idpresupuesto;
                    $prdDetalle->descripcion = $request->descripcionfilaproyecto[$jp];
                    $prdDetalle->costo = $request->costoextrafilaproyecto[$jp];
                    $prdDetalle->save();
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

            $arrayObjeto = ObjEspecifico::orderBy('codigo', 'ASC')->get();
            $arrayFuente = FuenteRecursos::where('id_p_anio', $idanio)
            ->orderBy('nombre', 'ASC')->get();
            $arrayLinea = LineaTrabajo::orderBy('nombre', 'ASC')->get();
            $arrayGestion = AreaGestion::orderBy('nombre', 'ASC')->get();

            return view('backend.admin.presupuestounidad.revisar.presupuestoindividual', compact( 'iddepa', 'idpre','arrayestado',
                'idanio', 'infoAnio', 'estado', 'arrayObjeto', 'arrayFuente', 'arrayLinea', 'arrayGestion'));
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

        $pilaArrayMaterialUnicos = array();

        // unicos materiales, para no mostrar la gran lista
        $arrayPresUniDetalle = P_PresupUnidadDetalle::where('id_presup_unidad', $presupuesto->id)->get();

        foreach ($arrayPresUniDetalle as $p){
            array_push($pilaArrayMaterialUnicos, $p->id_material);
        }

        $idpresupuesto = $presupuesto->id;

        // LISTADO DE PROYECTO APROBADOS
        $listadoProyectoAprobados = P_ProyectosAprobados::where('id_presup_unidad', $idpresupuesto)
            ->orderBy('descripcion', 'ASC')
            ->get();

        foreach ($listadoProyectoAprobados as $dd){

            $infoObjeto = ObjEspecifico::where('id', $dd->id_objespeci)->first();
            $infoFuenteR = ObjEspecifico::where('id', $dd->id_fuenter)->first();
            $infoLinea = ObjEspecifico::where('id', $dd->id_lineatrabajo)->first();
            $infoArea = ObjEspecifico::where('id', $dd->id_areagestion)->first();

            $dd->codigoobj = $infoObjeto->codigo;
            $dd->objeto = $infoObjeto->codigo . " - " . $infoObjeto->nombre;
            $dd->fuenterecurso = $infoFuenteR->codigo . " - " . $infoFuenteR->nombre;
            $dd->lineatrabajo = $infoLinea->codigo . " - " . $infoLinea->nombre;
            $dd->areagestion = $infoArea->codigo . " - " . $infoArea->nombre;

            $dd->costoFormat = '$' . number_format((float)$dd->costo, 2, '.', ',');
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
                        ->whereIn('id', $pilaArrayMaterialUnicos) // solo materiales que tienen en presupuesto
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

                            // periodo siempre sera mínimo 1
                            $total = ($data->precio * $data->cantidad) * $data->periodo;
                            $subLista->total = number_format((float)$total, 2, '.', ',');

                            $subLista->precio = $data->precio;
                            $sumaObjeto = $sumaObjeto + $total;
                        }else{
                            $subLista->cantidad = '';
                            $subLista->periodo = '';
                            $subLista->total = '';
                            $subLista->precio = $subLista->costo;
                        }
                    }

                    foreach ($listadoProyectoAprobados as $lpa){
                        if($ll->codigo == $lpa->codigoobj){
                            $sumaObjeto += $lpa->costo;
                        }
                    }

                    $sumaObjetoTotal = $sumaObjetoTotal + $sumaObjeto;
                    $ll->sumaobjeto = number_format((float)$sumaObjeto, 2, '.', ',');

                    $resultsBloque3[$index3]->material = $subSecciones3;
                    $index3++;
                }

                $sumaRubro = $sumaRubro + $sumaObjetoTotal;
                $lista->sumaobjetototal = number_format((float)$sumaObjetoTotal, 2, '.', ',');

                $resultsBloque2[$index2]->objeto = $subSecciones2;
                $index2++;
            }
            $totalvalor = $totalvalor + $sumaRubro;
            $secciones->sumarubro = number_format((float)$sumaRubro, 2, '.', ',');

            $resultsBloque[$index]->cuenta = $subSecciones;
            $index++;
        }

        // obtener listado de materiales extra
        $listado = P_MaterialesDetalle::where('id_presup_unidad', $presupuesto->id)->get();

        foreach ($listado as $lista){
            $uni = P_UnidadMedida::where('id', $lista->id_unidadmedida)->first();
            $lista->simbolo = $uni->nombre;
        }


        $totalvalor = number_format((float)$totalvalor, 2, '.', ',');


        // LISTADO DE PROYECTO PENDIENTE
        $listadoProyecto = P_ProyectosPendientes::where('id_presup_unidad', $idpresupuesto)
            ->orderBy('descripcion', 'ASC')
            ->get();

        return view('backend.admin.presupuestounidad.revisar.contenedorpresupuestoindividual', compact( 'estado',
            'idpresupuesto', 'idestado', 'totalvalor',
            'objeto', 'listado', 'preanio', 'rubro', 'listadoProyecto', 'listadoProyectoAprobados'));
    }

    // petición para transferir material solicitado por una unidad y agregar a base de materiales
    public function transferirNuevoMaterial(Request $request){

        $regla = array(
            'idpresupuesto' => 'required',
            'idobj' => 'required',
            'idborrarmaterial' => 'required'
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

            // agregar a materiales base
            $base = new P_Materiales();
            $base->descripcion = $request->descripcion;
            $base->id_unidadmedida = $request->idunidadmedida;
            $base->id_objespecifico = $request->idobj;
            $base->costo = $request->costo;
            $base->visible = 1; // material visible
            $base->save();

            // agregar material a la unidad detalle
            $prDetalle = new P_PresupUnidadDetalle();
            $prDetalle->id_presup_unidad = $request->idpresupuesto;
            $prDetalle->id_material = $base->id;
            $prDetalle->cantidad = $request->cantidad;
            $prDetalle->precio = $request->costo;
            $prDetalle->periodo = $request->periodo;
            $prDetalle->save();

            // borrar el material extra
            P_MaterialesDetalle::where('id', $request->idborrarmaterial)->delete();

             DB::commit();
            return ['success' => 2];

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

            // verificar cuando se pone en modo aprobado
            if($request->idestado == 3){

                $conteo = P_PresupUnidadDetalle::where('id_presup_unidad', $request->idpresupuesto)->count();

                if($conteo == 0){
                    // no hay ninguna fila registrada
                    return ['success' => 1];
                }
            }

            P_PresupUnidad::where('id', $request->idpresupuesto)->update([
                'id_estado' => $request->idestado
            ]);

            return ['success' => 2];
        }else{
            return ['success' => 99];
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

                // en desarrollo o en revisión
                if($pre->id_estado == 1 || $pre->id_estado == 2){
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

        // todos los presupuestos estan creados
        if($lista->isEmpty()){
            return ['success' => 1];
        }

        // es decir aquí hacen falta
        return ['success' => 2, 'lista' => $lista];
    }

    // ver si existe el presupuesto departamento y su año
    public function verificarSiExistePresupuesto(Request $request){

        $rules = array(
            'idanio' => 'required',
            'iddepartamento' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        if(P_PresupUnidad::where('id_anio', $request->idanio)
            ->where('id_departamento', $request->iddepartamento)
            ->first()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // ver si existe el presupuesto departamento y su año
    public function verificarSiExistePresupuestoTodoDepa(Request $request){

        $rules = array(
            'idanio' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        $porciones = explode("-", $request->unidades);

        // conteo

        $conteo = P_PresupUnidad::where('id_anio', $request->idanio)
            ->whereIn('id_departamento', $porciones)
            ->where('id_estado', 3) // solo aprobados
            ->orderBy('id', 'ASC')
            ->count();

        if($conteo == 0){
            // ninguno encontrado
            return ['success' => 3];
        }

        // filtrado por x departamento y x año
        $arrayPresupUnidad = P_PresupUnidad::where('id_anio', $request->idanio)
            ->whereIn('id_departamento', $porciones)
            ->where('id_estado', 3) // solo aprobados
            ->orderBy('id', 'ASC')
            ->get();

        // verificar que existan todos los departamentos
        foreach ($porciones as $dd){

            $boolEsta = true;
            foreach ($arrayPresupUnidad as $pp){
                if($dd == $pp->id_departamento){
                    $boolEsta = false;
                }
            }

            // es true, este departamento no se encontro
            if($boolEsta){
                $infoDepartamento = P_Departamento::where('id', $dd)->first();
                return ['success' => 1, 'departamento' => $infoDepartamento->nombre];
            }
        }

        // todos estan

        return ['success' => 2];
    }

    // registrar un nuevo proyecto para presupuesto de la unidad
    public function registrarProyectoPresupuestoUnidad(Request $request){

        $rules = array(
            'id' => 'required',
            'proidborrar' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            if(P_PresupUnidad::where('id', $request->id)->first()){

                // REGISTRAR

                $deta = new P_ProyectosAprobados();
                $deta->id_presup_unidad = $request->id;
                $deta->id_objespeci = $request->objeto;
                $deta->id_fuenter = $request->fuenter;
                $deta->id_lineatrabajo = $request->linea;
                $deta->id_areagestion = $request->areagestion;
                $deta->descripcion = $request->descripcion;
                $deta->costo = $request->costo;
                $deta->save();

                // borrar proyectos pendiente

                P_ProyectosPendientes::where('id', $request->proidborrar)->delete();

                DB::commit();
                return ['success' => 1];
            }else{
                return ['success' => 99];
            }

        }catch(\Throwable $e){
            DB::rollback();
            Log::info('ee' . $e);
            return ['success' => 99];
        }
    }

    // solo obtener las unidades de medida para registrar material solicitado por unidad
    public function informacionUnidadMedidaPresupuesto(Request $request){

        $arrayUnidad = P_UnidadMedida::orderBy('nombre', 'ASC')->get();

        return ['success' => 1, 'arrayunidad' => $arrayUnidad];
    }



}

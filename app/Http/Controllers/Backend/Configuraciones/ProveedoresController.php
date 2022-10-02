<?php

namespace App\Http\Controllers\Backend\Configuraciones;

use App\Http\Controllers\Controller;
use App\Models\CatalogoMateriales;
use App\Models\Clasificaciones;
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
use App\Models\Proveedores;
use App\Models\Rubro;
use App\Models\UnidadMedida;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProveedoresController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    // retorna vista con los proveedores para cotizaciones
    public function indexVistaProveedor(){
        return view('backend.admin.proyectos.configuraciones.proveedores.vistaproveedor');
    }

    // retorna tabla con los proveedores para cotizaciones
    public function tablaVistaProveedor(){
        $lista = Proveedores::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.proyectos.configuraciones.proveedores.tablaproveedor', compact('lista'));
    }

    // registra nuevo proveedor
    public function nuevoProveedor(Request $request){

        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new Proveedores();
        $dato->nombre = $request->nombre;
        $dato->telefono = $request->telefono;
        $dato->nit = $request->nit;
        $dato->nrc = $request->nrc;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // obtener información de un proveedor
    public function informacionProveedor(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Proveedores::where('id', $request->id)->first()){

            return ['success' => 1, 'lista' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // edita la información de proveedor
    public function editarProveedor(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Proveedores::where('id', $request->id)->first()){

            Proveedores::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'telefono' => $request->telefono,
                'nit' => $request->nit,
                'nrc' => $request->nrc
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


    // **************** RUBRO ****************

    // retorna vista de rubros
    public function indexRubro(){
        return view('backend.admin.configuraciones.rubro.vistarubro');
    }


    public function tablaRubro(){
        $lista = Rubro::orderBy('nombre')->get();
        return view('backend.admin.configuraciones.rubro.tablarubro', compact('lista'));
    }

    // registra un nuevo rubro
    public function nuevaRubro(Request $request){

        $regla = array(
            'nombre' => 'required',
            'numero' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        $dato = new Rubro();
        $dato->nombre = $request->nombre;
        $dato->codigo = $request->numero;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // obtener información de un rubro
    public function informacionRubro(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Rubro::where('id', $request->id)->first()){

            return ['success' => 1, 'rubro' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar un rubro
    public function editarRubro(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'numero' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Rubro::where('id', $request->id)->first()){

            Rubro::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'codigo' => $request->numero
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
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



                    if($ll->numero == 61109){
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


    public function indexPresupuestoUnidadEdicion($id){
        // mando ID del año
        return view('backend.admin.presupuestounidad.editar.vistaeditarpresupuesto', compact( 'id'));
    }


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

                    if($ll->numero == 61109){
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

        $pilaAños = array();

        foreach ($listaAnios as $p){
            array_push($pilaAños, $p->id_anio);
        }

        $listado = P_AnioPresupuesto::whereIn('id', $pilaAños)->get();

        return view('backend.admin.presupuestounidad.editar.vistaanioeditarpresupuesto', compact('listado'));
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

    public function editarPresupuestoUnidad(Request $request){

        $rules = array(
            'idpresupuesto' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        $infoPresu = P_PresupUnidad::where('id', $request->idpresupuesto)->first();

        if($infoPresu->estado == 2){
            // presupuesto ya aprobado
            return ['success' => 1];
        }

        DB::beginTransaction();

        try {

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

            return ['success' => 2];
        }catch(\Throwable $e){
            DB::rollback();
            return ['success' => 3];
        }
    }


    public function indexPresupuestoParaAprobar($iddepa, $idanio){

        // buscar presupuesto
        if($infoPresupuesto = P_PresupUnidad::where('id_anio', $idanio)
            ->where('id_departamento', $iddepa)
            ->first()){

            $infoAnio = P_AnioPresupuesto::where('id', $infoPresupuesto->id_anio)->first();

            $estado = $infoPresupuesto->id_estado;

            return view('backend.admin.presupuestounidad.revisar.presupuestoindividual', compact( 'iddepa', 'idanio', 'infoAnio', 'estado'));
        }else{
            // presupuesto no encontrado
            return view('backend.admin.presupuestounidad.revisar.vistapresupuestonoencontrado');
        }
    }

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

        return view('backend.admin.presupuestounidad.revisar.contenedorpresupuestoindividual', compact( 'estado', 'idestado', 'totalvalor', 'objeto', 'listado', 'idpresupuesto', 'preanio', 'rubro'));
    }


}

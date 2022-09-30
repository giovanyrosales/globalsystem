<?php

namespace App\Http\Controllers\Backend\Configuraciones;

use App\Http\Controllers\Controller;
use App\Models\CatalogoMateriales;
use App\Models\Clasificaciones;
use App\Models\Cuenta;
use App\Models\ObjEspecifico;
use App\Models\P_AnioPresupuesto;
use App\Models\P_Departamento;
use App\Models\P_Materiales;
use App\Models\P_PresupUnidad;
use App\Models\P_UnidadMedida;
use App\Models\Proveedores;
use App\Models\Rubro;
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

    public function index(){
        return view('backend.admin.configuraciones.proveedores.vistaproveedor');
    }

    public function tabla(){
        $lista = Proveedores::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.configuraciones.proveedores.tablaproveedor', compact('lista'));
    }

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

    // informacion
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

    // editar
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

    public function indexRubro(){
        return view('Backend.Admin.Configuraciones.Rubro.vistaRubro');
    }

    public function tablaRubro(){
        $lista = Rubro::orderBy('nombre')->get();
        return view('Backend.Admin.Configuraciones.Rubro.tablaRubro', compact('lista'));
    }

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

    // informacion
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

    // editar
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


    // ******************  PRESUPUESTO DE LOS DEPARTAMENTOS  *******************************





    //*************************************************



    ///*********************************************************************




    //*****************************************************

    // vista para registrar un nuevo material



    public function indexRevisionPresupuestoUnidad(){

        $departamentos = P_Departamento::orderBy('nombre')->get();
        $anios = P_AnioPresupuesto::orderBy('nombre')->get();

        return view('backend.admin.presupuestounidad.revisar.vistarevisar', compact('departamentos', 'anios'));
    }

    public function indexReportePresupuestoUnidad(){
        $departamentos = P_Departamento::orderBy('nombre')->get();
        $anios = P_AnioPresupuesto::orderBy('nombre')->get();

        return view('backend.admin.presupuestounidad.reportes.vistareportespresupuestounidad', compact('departamentos', 'anios'));
    }

    public function indexCargadora(){

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

        return view('backend.admin.presupuestounidad.crear.contenedorcatalogorubro', compact('rubro'));
    }

    public function indexCrearPresupuestoUnidad(){

        // verificar si hay presupuesto pendiente por crear

        $idusuario = Auth::id();
        $infouser = Usuario::where('id', $idusuario)->first();

        // solo sera necesario verificar con tabla presub_unidad

        // obtener lista de anios del departamento
        $listaAnios = P_PresupUnidad::where('id_departamento', $infouser->id_departamento)->get();

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

    public function nuevoPresupuestoUnidades(Request $request){

        $rules = array(
            'anio' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        // obtener información del usuario, saber quien eta agregando el presupuesto
        $idusuario = Auth::id();
        $userData = Usuario::where('id', $idusuario)->first();

        // verificar que aun no exista el presupuesto
        if(P_PresupUnidad::where('id_anio', $request->anio)
            ->where('id_departamento', $userData->id_departamento)
            ->first()){
            return ['success' => 1];
        }

        DB::beginTransaction();

        try {

            $pr = new PresupUnidad();
            $pr->id_anio = $request->anio;
            $pr->id_departamento = $userData->id_departamento;
            $pr->id_estado = 1; // editable
            $pr->save();

            if($request->idmaterial != null) {
                for ($i = 0; $i < count($request->idmaterial); $i++) {

                    $prDetalle = new PresupUnidadDetalle();
                    $prDetalle->id_presup_unidad = $pr->id;
                    $prDetalle->id_material = $request->idmaterial[$i];
                    $prDetalle->cantidad = $request->unidades[$i];
                    $prDetalle->periodo = $request->periodo[$i];
                    $prDetalle->save();
                }
            }

            // ingreso de materiales extra
            if($request->descripcion != null) {
                for ($j = 0; $j < count($request->descripcion); $j++) {

                    $mtrDetalle = new MaterialExtraDetalle();
                    $mtrDetalle->id_presup_unidad = $pr->id;
                    $mtrDetalle->id_unidad = $request->unidadmedida[$j];
                    $mtrDetalle->descripcion = $request->descripcion[$j];
                    $mtrDetalle->costo = $request->costoextra[$j];
                    $mtrDetalle->cantidad = $request->cantidadextra[$j];
                    $mtrDetalle->periodo = $request->periodoextra[$j];
                    $mtrDetalle->save();
                }
            }

            DB::commit();
            return ['success' => 2];
        }catch(\Throwable $e){
            DB::rollback();
            Log::info('ee' . $e);
            return ['success' => 3];
        }

    }

}

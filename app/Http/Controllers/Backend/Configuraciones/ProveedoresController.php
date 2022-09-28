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

    public function indexAnioPresupuesto(){
        return view('backend.admin.presupuestounidad.configuracion.aniopresupuesto.vistaaniopresupuesto');
    }

    public function tablaAnioPresupuesto(){
        $lista = P_AnioPresupuesto::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.presupuestounidad.configuracion.aniopresupuesto.tablaaniopresupuesto', compact('lista'));
    }

    public function nuevoAnioPresupuesto(Request $request){
        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new P_AnioPresupuesto();
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }

    }

    public function informacionAnioPresupuesto(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = P_AnioPresupuesto::where('id', $request->id)->first()){

            return ['success' => 1, 'lista' => $lista];
        }else{
            return ['success' => 2];
        }

    }

    public function editarAnioPresupuesto(Request $request){
        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(P_AnioPresupuesto::where('id', $request->id)->first()){

            P_AnioPresupuesto::where('id', $request->id)->update([
                'nombre' => $request->nombre,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


    //*************************************************

    public function indexDepartamentos(){
        return view('backend.admin.presupuestounidad.configuracion.departamentos.vistadepartamentopresupuesto');
    }

    public function tablaDepartamentos(){
        $lista = P_Departamento::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.presupuestounidad.configuracion.departamentos.tabladepartamentopresupuesto', compact('lista'));
    }

    public function nuevoDepartamentos(Request $request){
        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new P_Departamento();
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }

    }

    public function informacionDepartamentos(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = P_Departamento::where('id', $request->id)->first()){

            return ['success' => 1, 'lista' => $lista];
        }else{
            return ['success' => 2];
        }

    }

    public function editarDepartamentos(Request $request){
        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(P_Departamento::where('id', $request->id)->first()){

            P_Departamento::where('id', $request->id)->update([
                'nombre' => $request->nombre,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


    ///*********************************************************************


    public function indexUnidadMedida(){
        return view('backend.admin.presupuestounidad.configuracion.unidadmedida.vistaunidadmedida');
    }

    public function tablaUnidadMedida(){
        $lista = P_UnidadMedida::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.presupuestounidad.configuracion.unidadmedida.tablaunidadmedida', compact('lista'));
    }

    public function nuevoUnidadMedida(Request $request){
        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new P_UnidadMedida();
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionUnidadMedida(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = P_UnidadMedida::where('id', $request->id)->first()){

            return ['success' => 1, 'lista' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarUnidadMedida(Request $request){
        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(P_UnidadMedida::where('id', $request->id)->first()){

            P_UnidadMedida::where('id', $request->id)->update([
                'nombre' => $request->nombre,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


    //*****************************************************

    // vista para registrar un nuevo material
    public function indexMaterialesPresupuesto(){
        $lUnidad = P_UnidadMedida::orderBy('nombre', 'ASC')->get();
        $lObjEspeci = ObjEspecifico::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.presupuestounidad.configuracion.materiales.vistamaterialespresupuesto', compact('lUnidad', 'lObjEspeci'));
    }

    public function tablaMaterialesPresupuesto(){
        $lista = P_Materiales::orderBy('descripcion', 'ASC')->get();

        foreach ($lista as $item) {

            $unidadmedida = '';
            $objespecifico = '';

            if($dataUnidad = P_UnidadMedida::where('id', $item->id_unidadmedida)->first()){
                $unidadmedida = $dataUnidad->nombre;
            }

            if($dataObj = ObjEspecifico::where('id', $item->id_objespecifico)->first()){
                $objespecifico = $dataObj->codigo . ' - ' . $dataObj->nombre;
            }

            $item->unidadmedida = $unidadmedida;
            $item->objespecifico = $objespecifico;

            $item->costo = number_format((float)$item->costo, 2, '.', ',');
        }

        return view('backend.admin.presupuestounidad.configuracion.materiales.tablamaterialespresupuesto', compact('lista'));
    }

    public function nuevoMaterialesPresupuesto(Request $request){

        $regla = array(
            'nombre' => 'required',
            'precio' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(P_Materiales::where('id_objespecifico', $request->objespecifico)
            ->where('descripcion', $request->nombre)
            ->where('id_unidadmedida', $request->unidad)
            ->first()){
            return ['success' => 1];
        }

        $dato = new P_Materiales();
        $dato->id_unidadmedida = $request->unidad;
        $dato->id_objespecifico = $request->objespecifico;
        $dato->descripcion = $request->nombre;
        $dato->costo = $request->precio;
        $dato->visible = 0;

        if($dato->save()){
            return ['success' => 2];
        }else{
            return ['success' => 99];
        }
    }

    public function informacionMaterialesPresupuesto(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = P_Materiales::where('id', $request->id)->first()){

            $arrayClasificacion = Clasificaciones::orderBy('nombre', 'ASC')->get();
            $arrayUnidad = P_UnidadMedida::orderBy('nombre', 'ASC')->get();
            $arrayCodiEspec = ObjEspecifico::orderBy('nombre', 'ASC')->get();

            $arrayDatos = [
                "idmedida" => $lista->id_unidadmedida,
                "idcodigo" => $lista->id_objespecifico,
                "idclasifi" => $lista->id_clasificacion
            ];

            // SI ESTE MATERIAL YA ESTA EN USO EN UN PRESUPUESTO. NO SE PODRA EDITAR UNOS CAMPOS
            $bloqueo = false;
            /*if(PartidaDetalle::where('material_id', $request->id)->first()){
                $bloqueo = true;
            }*/

            return ['success' => 1, 'registro' => $lista, 'bloqueo' => $bloqueo, 'clasificacion' => $arrayClasificacion,
                'unidad' => $arrayUnidad, 'codigo' => $arrayCodiEspec, 'arraydatos' => $arrayDatos];
        }else{
            return ['success' => 99];
        }
    }

    public function editarMaterialesPresupuesto(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'precio' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        // VERIFICAR QUE LOS DATOS NO HAYAN CAMBIADO SI YA ESTABA EN UN PRESUPUESTO EL MATERIAL
        /*if(PartidaDetalle::where('material_id', $request->id)->first()){

            $infoCatalogo = CatalogoMateriales::where('id', '=', $request->id)->first();

            // MISMOS RETORNOS QUE UN DATO HA CAMBIADO

            if($infoCatalogo->nombre !== $request->nombre){
                return ['success' => 1];
            }

            if($infoCatalogo->id_objespecifico !== $request->codigo){
                return ['success' => 1];
            }

            if($infoCatalogo->id_unidadmedida !== $request->unidad){
                return ['success' => 1];
            }
        }*/

        // VERIFICAR MATERIAL REPETIDO
        if(P_Materiales::where('id', '!=', $request->id)
            ->where('id_objespecifico', $request->codigo)
            ->where('descripcion', $request->nombre)
            ->where('id_unidadmedida', $request->unidad)
            ->first()){
            return ['success' => 1];
        }

        // MATERIAL ACTUALIZADO
        P_Materiales::where('id', $request->id)->update([
            'id_unidadmedida' => $request->unidad,
            'id_objespecifico' => $request->codigo,
            'descripcion' => $request->nombre,
            'costo' => $request->precio
        ]);

        return ['success' => 2];
    }


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
            return view('backend.admin.encargado.crear.indexvacio');
        }

        $unidad = P_UnidadMedida::orderBy('nombre')->get();

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
                        ->where('visible', 1)
                        ->get();

                    foreach ($subSecciones3 as $subLista){

                        $infoUnidad = P_UnidadMedida::where('id', $subLista->id_unimedida)->first();
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

        return view('backend.admin.presupuestounidad.crear.vistacrearpresupuestounidad', compact('listado', 'unidad', 'rubro'));
    }


}

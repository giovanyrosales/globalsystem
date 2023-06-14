<?php

namespace App\Http\Controllers\Backend\Configuraciones;

use App\Http\Controllers\Controller;
use App\Models\CatalogoMateriales;
use App\Models\Clasificaciones;
use App\Models\ObjEspecifico;
use App\Models\Cuenta;
use App\Models\PartidaAdicionalDetalle;
use App\Models\PartidaDetalle;
use App\Models\SoliMaterialIng;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\Array_;

class MaterialesController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    // retorna vista con catálogo de materiales para proyecto
    public function indexCatalogoMaterial(){
        $lClasificacion = Clasificaciones::orderBy('nombre', 'ASC')->get();
        $lUnidad = UnidadMedida::orderBy('medida', 'ASC')->get();
        $lObjEspeci = ObjEspecifico::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.proyectos.configuraciones.materiales.vistacatalogomateriales', compact('lClasificacion',
        'lUnidad', 'lObjEspeci'));
    }

    // retorna tabla con catálogo de materiales para proyecto
    public function tablaCatalogoMaterial(){
        $lista = CatalogoMateriales::orderBy('nombre', 'ASC')->get();

        foreach ($lista as $item) {

            $clasificacion = '';
            $unidadmedida = '';
            $objespecifico = '';

            if($dataClasi = Clasificaciones::where('id', $item->id_clasificacion)->first()){
               $clasificacion = $dataClasi->nombre;
            }

            if($dataUnidad = UnidadMedida::where('id', $item->id_unidadmedida)->first()){
               $unidadmedida = $dataUnidad->medida;
            }

            if($dataObj = ObjEspecifico::where('id', $item->id_objespecifico)->first()){
                $objespecifico = $dataObj->codigo . ' - ' . $dataObj->nombre;
            }

            $item->clasificacion = $clasificacion;
            $item->unidadmedida = $unidadmedida;
            $item->objespecifico = $objespecifico;
        }

        return view('backend.admin.proyectos.configuraciones.materiales.tablacatalogomateriales', compact('lista'));
    }

    // registra nuevo material para proyectos
    public function nuevoMaterial(Request $request){

        $regla = array(
            'nombre' => 'required',
            'precio' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        $dato = new CatalogoMateriales();
        $dato->id_clasificacion = $request->clasificacion;
        $dato->id_unidadmedida = $request->unidad;
        $dato->id_objespecifico = $request->objespecifico;
        $dato->nombre = $request->nombre;
        $dato->pu = $request->precio;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // obtener información de un material de proyecto
    public function informacionCatalogoMaterial(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = CatalogoMateriales::where('id', $request->id)->first()){

            $arrayClasificacion = Clasificaciones::orderBy('nombre', 'ASC')->get();
            $arrayUnidad = UnidadMedida::orderBy('medida', 'ASC')->get();
            $arrayCodiEspec = ObjEspecifico::orderBy('nombre', 'ASC')->get();

            $arrayDatos = [
                "idmedida" => $lista->id_unidadmedida,
                "idcodigo" => $lista->id_objespecifico,
                "idclasifi" => $lista->id_clasificacion
            ];

            // SI ESTE MATERIAL YA TIENE UN PRESUPUESTO NO SE PODRA EDITAR
            // OBJETO ESPECIFICO, NOMBRE, UNIDAD MEDIDA
            $bloqueo = false;
            if(PartidaDetalle::where('material_id', $request->id)->first()){
                $bloqueo = true;
            }

            if(PartidaAdicionalDetalle::where('id_material', $request->id)->first()){
                $bloqueo = true;
            }

            return ['success' => 1, 'registro' => $lista, 'bloqueo' => $bloqueo, 'clasificacion' => $arrayClasificacion,
                'unidad' => $arrayUnidad, 'codigo' => $arrayCodiEspec, 'arraydatos' => $arrayDatos];
        }else{
            return ['success' => 2];
        }
    }

    // editar catálogo de material de proyecto
    public function editarMaterial(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'precio' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();

        try {

        // PODRA MODIFICAR SI EL MATERIAL NO ESTA AGREGADO A PARTIDA DETALLE
        if(!PartidaDetalle::where('material_id', $request->id)->first()){

            CatalogoMateriales::where('id', $request->id)->update([
                'id_clasificacion' => $request->clasificacion,
                'id_unidadmedida' => $request->unidad,
                'id_objespecifico' => $request->codigo,
                'nombre' => $request->nombre,
                'pu' => $request->precio
            ]);
        }else{
            // UNICAMENTE ESTO PODRA EDITAR
            CatalogoMateriales::where('id', $request->id)->update([
                'id_clasificacion' => $request->clasificacion,
                'pu' => $request->precio
            ]);
        }

            DB::commit();
            return ['success' => 1];
        }catch(\Throwable $e){

            DB::rollback();
            return ['success' => 99];
        }
    }


    // retorna vista con materiales solicitados para agregar catálogo de materiales
    public function indexSolicitudMaterialIng(){
        $lClasificacion = Clasificaciones::orderBy('nombre', 'ASC')->get();
        $lUnidad = UnidadMedida::orderBy('medida', 'ASC')->get();
        $lObjEspeci = ObjEspecifico::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.proyectos.configuraciones.solicitudes.ingenieria.vistasolimaterialing', compact('lClasificacion',
        'lUnidad', 'lObjEspeci'));
    }

    // retorna tabla con materiales solicitados para agregar catálogo de materiales
    public function tablaSolicitudMaterialIng(){
        $lista = SoliMaterialIng::orderBy('nombre')->get();

        return view('backend.admin.proyectos.configuraciones.solicitudes.ingenieria.tablasolimaterialing', compact('lista'));
    }

    // nuevo registro de material solicitado
    public function nuevoSolicitudMaterialIng(Request $request){

        $regla = array(
            'nombre' => 'required',
            'medida' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new SoliMaterialIng();
        $dato->nombre = $request->nombre;
        $dato->medida = $request->medida;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];

        }
    }

    // borrar material solicitado
    public function borrarSolicitudMaterialIng(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(SoliMaterialIng::where('id', $request->id)->first()){
            SoliMaterialIng::where('id', $request->id)->delete();
        }

        return ['success' => 1];
    }

    // información para editar material solicitado
    public function informacionSolicitudMaterialIng(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($info = SoliMaterialIng::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $info];
        }else{
            return ['success' => 2];
        }
    }


    // agregar material solicitado por ingenieria
    public function agregarSolicitudMaterialIng(Request $request){

        $regla = array(
            'nombre' => 'required',
            'precio' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(CatalogoMateriales::where('id_objespecifico', $request->objespecifico)
            ->where('nombre', $request->nombre)
            ->where('id_unidadmedida', $request->unidad)
            ->where('id_clasificacion', $request->clasificacion)
            ->first()){
            return ['success' => 3];
        }

        $dato = new CatalogoMateriales();
        $dato->id_clasificacion = $request->clasificacion;
        $dato->id_unidadmedida = $request->unidad;
        $dato->id_objespecifico = $request->objespecifico;
        $dato->nombre = $request->nombre;
        $dato->pu = $request->precio;

        if($dato->save()){
            // borrar material solicitado, ya que fue agregado
            if(SoliMaterialIng::where('id', $request->id)->first()){
                SoliMaterialIng::where('id', $request->id)->delete();
            }

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // retorna vista con todos los materiales de catálogo para que unicamente pueda verse
    public function indexVistaCatalogoMaterial(){
        return view('backend.admin.proyectos.configuraciones.vistacatalogomateriales.vistacatalogomateriales');
    }

    // retorna tabla con todos los materiales de catálogo para que unicamente pueda verse
    public function tablaVistaCatalogoMaterial(){
        $lista = CatalogoMateriales::orderBy('nombre', 'ASC')->get();

        foreach ($lista as $item) {

            $clasificacion = '';
            $unidadmedida = '';
            $objespecifico = '';

            if($dataClasi = Clasificaciones::where('id', $item->id_clasificacion)->first()){
                $clasificacion = $dataClasi->nombre;
            }

            if($dataUnidad = UnidadMedida::where('id', $item->id_unidadmedida)->first()){
                $unidadmedida = $dataUnidad->medida;
            }

            if($dataObj = ObjEspecifico::where('id', $item->id_objespecifico)->first()){
                $objespecifico = $dataObj->codigo . ' - ' . $dataObj->nombre;
            }

            $item->clasificacion = $clasificacion;
            $item->unidadmedida = $unidadmedida;
            $item->objespecifico = $objespecifico;
        }

        return view('backend.admin.proyectos.configuraciones.vistacatalogomateriales.tablacatalogomateriales', compact('lista'));
    }





}

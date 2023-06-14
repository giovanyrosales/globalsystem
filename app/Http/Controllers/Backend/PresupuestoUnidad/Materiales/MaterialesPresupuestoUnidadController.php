<?php

namespace App\Http\Controllers\Backend\PresupuestoUnidad\Materiales;

use App\Http\Controllers\Controller;
use App\Models\Clasificaciones;
use App\Models\ObjEspecifico;
use App\Models\P_AnioPresupuesto;
use App\Models\P_Materiales;
use App\Models\P_UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaterialesPresupuestoUnidadController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    // ************** CREAR CATÁLOGO DE MATERIALES PARA PRESUPUESTO DE UNIDAD **************

    // retorna vista catálogo de materiales para presupuesto de unidades
    public function indexMaterialesPresupuesto(){
        $lUnidad = P_UnidadMedida::orderBy('nombre', 'ASC')->get();
        $lObjEspeci = ObjEspecifico::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.presupuestounidad.configuracion.materiales.vistamaterialespresupuesto', compact('lUnidad', 'lObjEspeci'));
    }

    // retorna tabla catálogo de materiales para presupuesto de unidades
    public function tablaMaterialesPresupuesto(){
        $lista = P_Materiales::orderBy('descripcion', 'ASC')
        ->where('visible', 1) // solo materiales visibles
        ->get();

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

    // registrar un nuevo material
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
        $dato->visible = 1; // material visible

        if($dato->save()){
            return ['success' => 2];
        }else{
            return ['success' => 99];
        }
    }

    // obtener información de material
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

    // editar un material
    public function editarMaterialesPresupuesto(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'precio' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        // MATERIAL ACTUALIZADO
        P_Materiales::where('id', $request->id)->update([
            'id_unidadmedida' => $request->unidad,
            'id_objespecifico' => $request->codigo,
            'descripcion' => $request->nombre,
            'costo' => $request->precio
        ]);

        return ['success' => 1];
    }

    // oculta un material, pero siempre será visible si usuario ya había seleccionado ese material
    public function ocultarMaterialesPresupuesto(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(P_Materiales::where('id', $request->id)->first()){

            P_Materiales::where('id', $request->id)->update([
                'visible' => 0,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 99];
        }

    }


}

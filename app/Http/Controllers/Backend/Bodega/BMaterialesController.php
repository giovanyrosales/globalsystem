<?php

namespace App\Http\Controllers\Backend\Bodega;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\bodega_material;
use App\Models\bodega_salida;
use App\Models\bodega_entrada;
use App\Models\bodega_detalle_entrada;
use App\Models\bodega_detalle_salida;
use App\Models\ObjEspecifico;
use App\Models\UnidadMedida;
use Illuminate\Support\Facades\Validator;

class BMaterialesController extends Controller
{
     // retorna vista de materiales de la seccion de bodega
     public function indexBodegaMateriales(){
        $unidadmedida = UnidadMedida::orderBy('medida', 'ASC')->get();
        $objespecifico = ObjEspecifico::orderBy('codigo', 'ASC')->get();

        return view('backend.admin.bodega.vistamateriales', compact('unidadmedida', 'objespecifico'));
    }
    // retorna tabla de materiales registrados
    public function tablaMateriales(){
        $lista = bodega_material::orderBy('id', 'ASC')->get();
        foreach ($lista as $l){
            $infoUnidadMedida = UnidadMedida::where('id', $l->id_unidadmedida)->first();
            $l->id_unidadmedida = $infoUnidadMedida->medida;
            $infoObjEspecifico = ObjEspecifico::where('id', $l->id_objespecifico)->first();
            $l->id_objespecifico = $infoObjEspecifico->codigo . " - " . $infoObjEspecifico->nombre;
        }

        return view('backend.admin.bodega.tablamateriales', compact('lista'));
    }

    // registrar un nuevo material // No incluye cantidad ni precio porque esta no se si ira aqui aun
    public function nuevoMaterial(Request $request){

        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new bodega_material();
        $dato->nombre = $request->nombre;
        $dato->id_unidadmedida = $request->id_unidadmedida;
        $dato->id_objespecifico = $request->id_objespecifico;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

     // obtener información de un material en especifico
    public function informacionMaterial(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = bodega_material::where('id', $request->id)->first()){

            $objespecifico = ObjEspecifico::orderBy('codigo')->get();
            $unidadmedida = UnidadMedida::orderBy('id')->get();

            return ['success' => 1, 'lista' => $lista, 'obj' => $objespecifico, 'um' => $unidadmedida];
        }else{
            return ['success' => 2];
        }
    }
    // editar un material
    public function editarMaterial(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'id_unidadmedida' => 'required',
            'id_objespecifico' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(bodega_material::where('id', $request->id)->first()){

            bodega_material::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'id_unidadmedida' => $request->id_unidadmedida,
                'id_objespecifico' => $request->id_objespecifico
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }
}

<?php

namespace App\Http\Controllers\Backend\Bodega;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\bodega_material;
use App\Models\bodega_salida;
use App\Models\bodega_entrada;
use App\Models\bodega_detalle_entrada;
use App\Models\bodega_detalle_salida;
use Illuminate\Support\Facades\Validator;

class BMaterialesController extends Controller
{
     // retorna vista de materiales de la seccion de bodega
     public function indexBodegaMateriales(){
        return view('backend.admin.bodega.vistamateriales');
    }
    // retorna tabla de materiales registrados
    public function tablaMateriales(){
        $lista = bodega_material::orderBy('id', 'ASC')->get();

        return view('backend.admin.bodega.tablamateriales', compact('lista'));
    }

    // registrar un nuevo material // No incluye cantidad
    public function nuevoMaterial(Request $request){

        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new Adescos();
        $dato->nombre = $request->nombre;
        $dato->presidente = $request->presidente;
        $dato->tel = $request->tel;
        $dato->dui = $request->dui;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }
}

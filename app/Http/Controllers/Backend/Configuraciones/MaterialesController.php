<?php

namespace App\Http\Controllers\Backend\Configuraciones;

use App\Http\Controllers\Controller;
use App\Models\CatalogoMateriales;
use App\Models\Clasificaciones;
use App\Models\ObjEspecifico;
use App\Models\Cuenta;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\Array_;

class MaterialesController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        $lClasificacion = Clasificaciones::orderBy('nombre', 'ASC')->get();
        $lUnidad = UnidadMedida::orderBy('medida', 'ASC')->get();
        $lObjEspeci = ObjEspecifico::orderBy('nombre', 'ASC')->get();

        return view('Backend.Admin.Configuraciones.Materiales.vistaCatalogoMateriales', compact('lClasificacion',
        'lUnidad', 'lObjEspeci'));
    }

    public function tabla(){
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

        return view('Backend.Admin.Configuraciones.Materiales.tablaCatalogoMateriales', compact('lista'));
    }

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

    public function informacion(Request $request){
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

            return ['success' => 1, 'registro' => $lista, 'clasificacion' => $arrayClasificacion,
                'unidad' => $arrayUnidad, 'codigo' => $arrayCodiEspec, 'arraydatos' => $arrayDatos];
        }else{
            return ['success' => 2];
        }
    }

    public function editarMaterial(Request $request){

        $regla = array(
            'nombre' => 'required',
            'precio' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        CatalogoMateriales::where('id', $request->id)->update([
            'id_clasificacion' => $request->clasificacion,
            'id_unidadmedida' => $request->unidad,
            'id_objespecifico' => $request->codigo,
            'nombre' => $request->nombre,
            'pu' => $request->precio
        ]);

        return ['success' => 1];
    }

}

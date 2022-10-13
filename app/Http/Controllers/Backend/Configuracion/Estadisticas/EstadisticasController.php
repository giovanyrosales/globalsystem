<?php

namespace App\Http\Controllers\Backend\Configuracion\Estadisticas;

use App\Http\Controllers\Controller;
use App\Models\P_Materiales;
use App\Models\P_PresupUnidadDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstadisticasController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    // vista para estadísticas
    public function indexEstadisticas(){
        return view('backend.admin.estadisticas.vistaestadisticas');
    }

    public function realizarCopia(Request $request){


        DB::beginTransaction();

        try {

            $arrayPresup = P_PresupUnidadDetalle::all();

            foreach ($arrayPresup as $dd){

                if($info = P_Materiales::where('id', $dd->id_material)->first()){

                    P_PresupUnidadDetalle::where('id', $dd->id)->update([
                        'precio' => $info->costo,
                    ]);
                }
            }

            DB::commit();

            return ['success' => 1];
        }catch(\Throwable $e){
            DB::rollback();
            return ['success' => 99];
        }
    }
}

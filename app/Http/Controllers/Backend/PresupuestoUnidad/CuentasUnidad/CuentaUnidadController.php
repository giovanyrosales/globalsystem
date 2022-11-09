<?php

namespace App\Http\Controllers\Backend\PresupuestoUnidad\CuentasUnidad;

use App\Http\Controllers\Controller;
use App\Models\CuentaUnidad;
use App\Models\ObjEspecifico;
use App\Models\P_AnioPresupuesto;
use App\Models\P_Departamento;
use App\Models\P_PresupUnidad;
use App\Models\P_PresupUnidadDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CuentaUnidadController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    // retorna vista con las cuentas de unidades
    public function indexCuentasUnidades(){

        // solo mostrar años que no tienen cuenta unidad creados
        $listado = CuentaUnidad::select('id_anio')->groupBy('id_anio')->get();
        $anios = P_AnioPresupuesto::whereNotIn('id', $listado)->orderBy('nombre')->get();
        $aniostodos = P_AnioPresupuesto::orderBy('nombre')->get();
        $departamentos = P_Departamento::orderBy('nombre')->get();

        return view('backend.admin.presupuestounidad.configuracion.cuentaunidades.vistacuentaunidades', compact('anios', 'aniostodos', 'departamentos'));
    }

    // retorna tabla con las cuentas de unidades
    public function tablaCuentasUnidades(){



        return view('backend.admin.presupuestounidad.configuracion.cuentaunidades.tablacuentaunidades');
    }

    // crear las cuentas unidades para todos los presupuesto aprobado
    public function registrarCuentasUnidades(Request $request){

        $rules = array(
            'idanio' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            // verificar si existe al menos 1 una vez el mismo año
            if(CuentaUnidad::where('id_anio', $request->idanio)->first()){
                return ['success' => 1];
            }

            //********************************************************************

            // verificar que todos los departamentos tengan presupuesto creado o aprobados

            // obtener listado de departamentos
            $depar = P_Departamento::orderBy('nombre')->get();
            $pila = array();

            foreach ($depar as $de){

                if($pre = P_PresupUnidad::where('id_anio', $request->idanio)
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

            // si la lista no está vacía, es decir, que faltan presupuestos por aprobar o no están creados
            if(!$lista->isEmpty()){
                return ['success' => 2, 'lista' => $lista];
            }

            //********************************************************************

            // CREAR CUENTAS UNIDAD

            $arrayPresupUnidad = P_PresupUnidad::where('id_anio', $request->idanio)
                ->where('id_estado' , 3) // solo aprobados
                ->get();

            $arrayObjetos = ObjEspecifico::orderBy('id')->get();

            foreach ($arrayPresupUnidad as $dd){

                foreach ($arrayObjetos as $obj){

                    $arrayPresupUnidadDetalle = DB::table('p_presup_unidad_detalle AS pre')
                        ->join('p_materiales AS pm', 'pre.id_material', '=', 'pm.id')
                        ->select('pre.cantidad', 'pre.id_presup_unidad', 'pre.precio', 'pre.periodo', 'pm.id_objespecifico')
                        ->where('pm.id_objespecifico', $obj->id)
                        ->where('pre.id_presup_unidad', $dd->id)
                        ->get();

                    $dineroObjeto = 0;

                    foreach ($arrayPresupUnidadDetalle as $apud){

                        // PERIODO SIEMPRE MÍNIMO 1
                        // dinero para el objeto específico
                        $dineroObjeto += ($apud->cantidad  * $apud->periodo) * $apud->precio;
                    }

                    if($dineroObjeto > 0){
                        // GUARDAR CUENTA UNIDAD
                        $dato = new CuentaUnidad();
                        $dato->id_anio = $dd->id_anio;
                        $dato->id_departamento = $dd->id_departamento;
                        $dato->id_objespeci = $obj->id;
                        $dato->saldo_inicial = $dineroObjeto;
                        $dato->save();
                    }
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

    // cuando hace falta un departamento nuevo y ya se creó cuenta unidad anteriormente se hara manual
    public function registrarCuentasUnidadManual(Request $request){

        $rules = array(
            'idanio' => 'required',
            'iddepartamento' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $conteo = CuentaUnidad::where('id_anio', $request->idanio)->count();
            if($conteo == 0){
                // no hay ninguno con este año, por lo tanto primero pasar por registro automático
                return ['success' => 1];
            }


            // verificar que esté en modo aprobación únicamente
            if($infoPresuUnidad = P_PresupUnidad::where('id_anio', $request->idanio)
                ->where('id_departamento', $request->iddepartamento)
                ->first()){

                // si no esta aprobado
                if($infoPresuUnidad->id_estado != 3){
                    return ['success' => 2];
                }


                // YA ESTABA REGISTRADO EN CUENTA UNIDAD EL ANIO Y DEPARTAMENTO
                if(CuentaUnidad::where('id_anio', $request->idanio)
                    ->where('id_departamento', $request->iddepartamento)
                    ->first()){
                    return ['success' => 3];
                }

                // PUEDE GUARDAR LA CUENTA UNIDAD

                $arrayPresupUnidad = P_PresupUnidad::where('id_anio', $request->idanio)
                    ->where('id_departamento', $request->iddepartamento)
                    //->where('id_estado' , 3) // ya fue comprado arriba
                    ->get();

                $arrayObjetos = ObjEspecifico::orderBy('id')->get();

                foreach ($arrayPresupUnidad as $dd){

                    foreach ($arrayObjetos as $obj){

                        $arrayPresupUnidadDetalle = DB::table('p_presup_unidad_detalle AS pre')
                            ->join('p_materiales AS pm', 'pre.id_material', '=', 'pm.id')
                            ->select('pre.cantidad', 'pre.id_presup_unidad', 'pre.precio', 'pre.periodo', 'pm.id_objespecifico')
                            ->where('pm.id_objespecifico', $obj->id)
                            ->where('pre.id_presup_unidad', $dd->id)
                            ->get();

                        $dineroObjeto = 0;

                        foreach ($arrayPresupUnidadDetalle as $apud){

                            // PERIODO SIEMPRE MÍNIMO 1
                            // dinero para el objeto específico
                            $dineroObjeto += ($apud->cantidad  * $apud->periodo) * $apud->precio;
                        }

                        if($dineroObjeto > 0){
                            // GUARDAR CUENTA UNIDAD
                            $dato = new CuentaUnidad();
                            $dato->id_anio = $dd->id_anio;
                            $dato->id_departamento = $dd->id_departamento;
                            $dato->id_objespeci = $obj->id;
                            $dato->saldo_inicial = $dineroObjeto;
                            $dato->save();
                        }
                    }
                }

                //DB::commit();
                return ['success' => 4];


            }else{
                // No creado aun
                return ['success' => 5];
            }
        }catch(\Throwable $e){
            Log::info('err ' . $e);
            DB::rollback();
            return ['success' => 99];
        }

    }


}

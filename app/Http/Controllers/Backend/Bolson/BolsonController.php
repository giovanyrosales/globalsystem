<?php

namespace App\Http\Controllers\Backend\Bolson;

use App\Http\Controllers\Controller;
use App\Models\Bolson;
use App\Models\BolsonDetalle;
use App\Models\CatalogoMateriales;
use App\Models\Cuenta;
use App\Models\FuenteFinanciamiento;
use App\Models\FuenteRecursos;
use App\Models\MovimientoBolson;
use App\Models\ObjEspecifico;
use App\Models\P_AnioPresupuesto;
use App\Models\P_Departamento;
use App\Models\P_Materiales;
use App\Models\P_PresupUnidad;
use App\Models\P_PresupUnidadDetalle;
use App\Models\PartidaAdicionalContenedor;
use App\Models\Proyecto;
use App\Models\TipoMovimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BolsonController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    // retorna vista con lista de bolsones
    public function indexBolson(){

        // obtener listado de bolsones para buscar que año han sido creados
        $listaBolson = Bolson::all();

        $pilaArrayAnio = array();

        foreach ($listaBolson as $p){
            array_push($pilaArrayAnio, $p->id_anio);
        }

        $listadoanios = P_AnioPresupuesto::whereNotIn('id', $pilaArrayAnio)->get();
        $arrayobj = ObjEspecifico::orderBy('codigo', 'ASC')->get();

        $puedeAgregar = false;
        if(sizeof($listadoanios) > 0){
            $puedeAgregar = true;
        }

        $arrayFuenteFinanciamiento = FuenteFinanciamiento::orderBy('nombre')->get();

        return view('backend.admin.proyectos.bolson.registro.vistabolson', compact('listadoanios', 'arrayobj',
            'puedeAgregar', 'arrayFuenteFinanciamiento'));
    }

    // retorna tabla con lista de bolsones
    public function tablaBolson(){

        $lista = Bolson::orderBy('fecha')->get();

        foreach ($lista as $dd){

            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));

            //*****************

            // obtener cuanto dinero queda en bolsón, ya que puede haber muchos proyectos asignados a un bolsón

            $proyectoMontoBolson = Proyecto::where('id_bolson', $dd->id)
                ->sum('monto');

            //*****************

            // obtener dinero descontado cuando una partida adicional está aprobada


            $partidaAdicionalMonto = DB::table('proyectos AS p')
                ->join('partida_adicional_contenedor AS pac', 'pac.id_proyecto', '=', 'p.id')
                ->select('pac.monto_aprobado')
                ->where('p.id_bolson', $dd->id)
                ->where('pac.estado', 2) // partidas adicionales Aprobadas
                ->sum('pac.monto_aprobado');


            //*****************

            // obtener monto de los proyectos que han finalizado

            $proyectoFinalizadoMonto = Proyecto::where('id_bolson', $dd->id)
                ->where('id_estado', 4)
                ->sum('monto_finalizado');

            //*****************

            // dinero inicial de bolsón

            // montoPartida: es el monto de las partidas presupuesto de proyecto para aprobar
            // proyectoMontoBolson: es el monto de las partidas aprobadas de todos los proyectos a bolsón
            // partidaAdicionalMonto: es el monto de las partidas adicionales aprobadas
            // proyectoFinalizadoMonto: es el monto sobrante de un proyecto cuando se finaliza

            // restar a monto inicial y despues sumarle
            $restaBolsonInicial = $dd->monto_inicial - ($proyectoMontoBolson + $partidaAdicionalMonto);

            // BOLSÓN ACTUAL LO QUE HAY $
            $restaBolsonInicial += $proyectoFinalizadoMonto;

            $restaBolsonInicial = number_format((float)$restaBolsonInicial, 2, '.', ',');

            $dd->montorestante = '$' . $restaBolsonInicial;
            $dd->monto_inicial = "$" . number_format((float)$dd->monto_inicial, 2, '.', ',');


            $infoFuenteR = FuenteRecursos::where('id', $dd->id_fuenter)->first();
            $infoAnio = P_AnioPresupuesto::where('id', $infoFuenteR->id_p_anio)->first();

            $dd->fuenterecursos = $infoFuenteR->codigo . ' - ' . $infoFuenteR->nombre . ' (' . $infoAnio->nombre . ')';

        }

        return view('backend.admin.proyectos.bolson.registro.tablabolson', compact('lista'));
    }


    public function verificarSaldosObjetos(Request $request){

        // verificar que no haya bolsón con este año
        if(Bolson::where('id_anio', $request->anio)->first()){
            return ['success' => 1];
        }

        //---------------------------------------------------------------

        // verificar que estén aprobados todos los presupuestos del x año

        // obtener listado de departamentos
        $depar = P_Departamento::all();
        $pilaIDDepartamento = array();

        foreach ($depar as $de){

            if($pre = P_PresupUnidad::where('id_anio', $request->anio)
                ->where('id_departamento', $de->id)->first()){

                // estados
                // 0: default
                // 1: listo para revisión
                // 2: aprobados

                if($pre->id_estado == 0 || $pre->id_estado == 1){
                    array_push($pilaIDDepartamento, $de->id);
                }

            }else{
                // no está creado aun
                array_push($pilaIDDepartamento, $de->id);
            }
        }

        $listaDepa = P_Departamento::whereIn('id', $pilaIDDepartamento)
            ->orderBy('nombre', 'ASC')
            ->get();

        // los departamentos que faltan por aprobarse su presupuesto
        if(sizeof($listaDepa) > 0){
            return ['success' => 2, 'lista' => $listaDepa];
        }


        //---------------------------------------------------------------

        $infoArrayPresuUnidad = P_PresupUnidad::where('id_anio', $request->anio)->get();

        $pilaArrayPresuUnidad = array();

        foreach ($infoArrayPresuUnidad as $p){
            array_push($pilaArrayPresuUnidad, $p->id);
        }

        // como todos están aprobados, obtener sus montos

        $porciones = explode(",", $request->objetos);
        $arrayObj = ObjEspecifico::whereIn('id', $porciones)->get();

        // se mostrará un listado select con el código y el monto

        $total = 0;

        foreach ($arrayObj as $dd){

            // por cada id objeto obtener suma de dinero de los presupuestos de año seleccionado

            $infoPresuUniDeta = DB::table('p_presup_unidad_detalle AS pre')
                ->join('p_materiales AS pm', 'pre.id_material', '=', 'pm.id')
                ->select('pm.id_objespecifico', 'pre.cantidad', 'pre.precio', 'pre.periodo')
                ->whereIn('pre.id_presup_unidad', $pilaArrayPresuUnidad)
                ->where('pm.id_objespecifico', $dd->id) // todos los materiales con este id obj específico
                ->get();

            // dinero total por código
            $multiplicado = 0;

            foreach ($infoPresuUniDeta as $infodd){
                $multiplicado += ($infodd->cantidad * $infodd->precio) * $infodd->periodo;
            }

            $total += $multiplicado;
            $multiplicado = number_format((float)$multiplicado, 2, '.', ',');
            $dd->unido = $dd->codigo . " - " . $dd->nombre . "   $" . $multiplicado;
        }

        $total = number_format((float)$total, 2, '.', ',');

        return ['success' => 3, 'lista' => $arrayObj, 'total' => $total];
    }


    public function nuevoRegistroBolson(Request $request){

        $rules = array(
            'anio' => 'required',
            'fecha' => 'required',
            'nombre' => 'required',
            'fuenter' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {


        // bolsón para este año ya está creado
        if(Bolson::where('id_anio', $request->anio)->first()){
            return ['success' => 1];
        }

        // verificar que todos presupuesto este aprobados
        $depar = P_Departamento::all();
        $pilaIDDepartamento = array();

        foreach ($depar as $de){

            if($pre = P_PresupUnidad::where('id_anio', $request->anio)
                ->where('id_departamento', $de->id)->first()){

                // estados
                // 0: default
                // 1: listo para revisión
                // 2: aprobados

                if($pre->id_estado == 0 || $pre->id_estado == 1){
                    array_push($pilaIDDepartamento, $de->id);
                }

            }else{
                // no está creado aun
                array_push($pilaIDDepartamento, $de->id);
            }
        }

        $listaDepa = P_Departamento::whereIn('id', $pilaIDDepartamento)
            ->orderBy('nombre', 'ASC')
            ->get();

        // los departamentos que faltan por aprobarse su presupuesto
        if(sizeof($listaDepa) > 0){
            return ['success' => 2, 'lista' => $listaDepa];
        }

        //-----------------------------------------------------------------------------

        $infoArrayPresuUnidad = P_PresupUnidad::where('id_anio', $request->anio)->get();

        $pilaArrayPresuUnidad = array();

        foreach ($infoArrayPresuUnidad as $p){
            array_push($pilaArrayPresuUnidad, $p->id);
        }

        // obtener dinero según cuentas obj específico

        $porciones = explode(",", $request->objetos);
        $arrayObj = ObjEspecifico::whereIn('id', $porciones)->get();

        // se mostrará un listado select con el código y el monto

        $totalSaldoInicial = 0;

        foreach ($arrayObj as $dd){

            // por cada id objeto obtener suma de dinero de los presupuestos de año seleccionado

            $infoPresuUniDeta = DB::table('p_presup_unidad_detalle AS pre')
                ->join('p_materiales AS pm', 'pre.id_material', '=', 'pm.id')
                ->select('pm.id_objespecifico', 'pre.cantidad', 'pre.precio', 'pre.periodo')
                ->whereIn('pre.id_presup_unidad',$pilaArrayPresuUnidad)
                ->where('pm.id_objespecifico', $dd->id) // todos los materiales con este id obj específico
                ->get();

            // dinero total por código
            $multiplicado = 0;

            foreach ($infoPresuUniDeta as $infodd){
                $multiplicado += ($infodd->cantidad * $infodd->precio) * $infodd->periodo;
            }

            $totalSaldoInicial += $multiplicado;
        }

        $or = new Bolson();
        $or->id_anio = $request->anio;
        $or->nombre = $request->nombre;
        $or->id_fuenter = $request->fuenter;
        $or->fecha = $request->fecha;
        $or->monto_inicial = $totalSaldoInicial;
        $or->save();

        // guardar detalle bolsón de cada obj específico

            foreach ($arrayObj as $dd){

                $bolsonDeta = new BolsonDetalle();
                $bolsonDeta->id_bolson = $or->id;
                $bolsonDeta->id_objespecifico = $dd->id;
                $bolsonDeta->save();
            }

        DB::commit();
        return ['success' => 3];

        }catch(\Throwable $e){
            Log::info('error: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function indexDetalleBolson($id){
        return view('backend.admin.proyectos.bolson.registro.vistadetallebolson', compact('id'));
    }

    public function tablaDetalleBolson($id){

        $listado = BolsonDetalle::where('id_bolson', $id)->get();

        $infoBolson = Bolson::where('id', $id)->first();

        $infoArrayPresuUnidad = P_PresupUnidad::where('id_anio', $infoBolson->id_anio)->get();

        $pilaArrayPresuUnidad = array();

        foreach ($infoArrayPresuUnidad as $p){
            array_push($pilaArrayPresuUnidad, $p->id);
        }

        foreach ($listado as $dd){

            $infoObjeto = ObjEspecifico::where('id', $dd->id_objespecifico)->first();
            $dd->codigo = $infoObjeto->codigo;
            $dd->objeto = $infoObjeto->nombre;

            $infoPresuUniDeta = DB::table('p_presup_unidad_detalle AS pre')
                ->join('p_materiales AS pm', 'pre.id_material', '=', 'pm.id')
                ->select('pm.id_objespecifico', 'pre.cantidad', 'pre.precio', 'pre.periodo')
                ->whereIn('pre.id_presup_unidad',$pilaArrayPresuUnidad)
                ->where('pm.id_objespecifico', $infoObjeto->id) // todos los materiales con este id obj específico
                ->get();

            // dinero total por código
            $multiplicado = 0;

            foreach ($infoPresuUniDeta as $infodd){
                $multiplicado += ($infodd->cantidad * $infodd->precio) * $infodd->periodo;
            }

            $dd->monto = number_format((float)$multiplicado, 2, '.', ',');
        }

        return view('backend.admin.proyectos.bolson.registro.tabladetallesbolson', compact('listado'));
    }


    public function retornarFuenteRecursosActivos(Request $request){

        $rules = array(
            'id' => 'required', // ID DE FUENTE DE FINANCIAMIENTO
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        if(FuenteFinanciamiento::where('id', $request->id)->first()){

            $lista = FuenteRecursos::where('id_fuentef', $request->id)
                ->where('activo', 1)
                ->get();

            foreach ($lista as $dd){
                $anios = P_AnioPresupuesto::where('id', $dd->id_p_anio)->first();
                $dd->unido = $dd->codigo . ' - ' . $dd->nombre . ' (' . $anios->nombre . ')';
            }

            return ['success' => 1, 'lista' => $lista];
        }else{

            return ['success' => 2];
        }
    }



}

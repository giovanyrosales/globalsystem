<?php

namespace App\Http\Controllers\Backend\Bolson;

use App\Http\Controllers\Controller;
use App\Models\Bolson;
use App\Models\Cuenta;
use App\Models\MovimientoBolson;
use App\Models\ObjEspecifico;
use App\Models\P_AnioPresupuesto;
use App\Models\P_Departamento;
use App\Models\P_PresupUnidad;
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
        $listaAniosBolson = Bolson::all();

        $pilaArrayAnio = array();

        foreach ($listaAniosBolson as $p){
            array_push($pilaArrayAnio, $p->id_anio);
        }

        $listadoanios = P_AnioPresupuesto::whereNotIn('id', $pilaArrayAnio)->get();

        $arrayobj = ObjEspecifico::orderBy('codigo', 'ASC')->get();

        return view('backend.admin.proyectos.bolson.registro.vistabolson', compact('listadoanios', 'arrayobj'));
    }

    // retorna tabla con lista de bolsones
    public function tablaBolson(){

        $lista = Bolson::orderBy('fecha')->get();

        foreach ($lista as $dd){

            $infoCuenta = Cuenta::where('id', $dd->id_cuenta)->first();
            $dd->cuenta = $infoCuenta->nombre;

            $dd->montoini = number_format((float)$dd->montoini, 2, '.', ',');
        }

        return view('backend.admin.proyectos.bolson.registro.tablabolson', compact('lista'));
    }


    public function verificarSaldosObjetos(Request $request){

        // verificar que no haya bolsón con este año
        if(Bolson::where('id_anio', $request->anio)->first()){
            return ['success' => 1];
        }


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
            'fecha' => 'required',
            'nombre' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

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




        // obtener dinero según cuentas obj específico

        $porciones = explode(",", $request->objetos);
        $arrayObj = ObjEspecifico::whereIn('id', $porciones)->get();

        // se mostrará un listado select con el código y el monto

        $total = 0;

        foreach ($arrayObj as $dd){

            // por cada id objeto obtener suma de dinero de los presupuestos de año seleccionado

            $infoPresuUniDeta = DB::table('p_presup_unidad_detalle AS pre')
                ->join('p_materiales AS pm', 'pre.id_material', '=', 'pm.id')
                ->select('pm.id_objespecifico', 'pre.cantidad', 'pre.precio', 'pre.periodo')
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


        formData.append('anio', anio);
        formData.append('fecha', fecha);
        formData.append('nombre', nombre);
        formData.append('numero', numero);
        formData.append('objeto', selected);

        $or = new Bolson();
        $or->nombre = $request->nombre;
        $or->numero = $request->numero;
        $or->fecha = $request->fecha;
        $or->montoini = $request->monto;
        $or->saldo = 0;
        $or->estado = 0;
        if($or->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionBolson(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Bolson::where('id', $request->id)->first()){

            $info = Cuenta::where('id', $lista->id_cuenta)->first();

            return ['success' => 1, 'info' => $lista, 'cuenta' => $info->nombre];
        }else{
            return ['success' => 2];
        }
    }

    public function editarRegistro(Request $request){
        $rules = array(
            'fecha' => 'required',
            'nombre' => 'required',
            'id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        if(Bolson::where('id', $request->id)->first()){

            Bolson::where('id', $request->id)->update([
                'id_cuenta' => $request->idcuenta,
                'nombre' => $request->nombre,
                'numero' => $request->numero,
                'fecha' => $request->fecha,
                'montoini' => $request->monto,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


    //**** MOVIMIENTO DE CUENTA ****

    public function indexMovimiento(){

        $proyecto = Proyecto::orderBy('nombre')->get();
        $bolson = Bolson::orderBy('nombre')->get();
        $tipomovi = TipoMovimiento::orderBy('nombre')->get();

        return view('Backend.Admin.Bolson.Cuenta.Movimiento.vistaMoviBolson', compact('proyecto',
        'bolson', 'tipomovi'));
    }

    public function tablaMovimiento(){

        $movi = MovimientoBolson::orderBy('fecha')->get();

       foreach ($movi as $dd){

           $infoPro = Proyecto::where('id', $dd->proyecto_id)->first();
           $infoBolson = Bolson::where('id', $dd->bolson_id)->first();

           $dd->fecha = date("d-m-Y", strtotime($dd->fecha));
           $dd->proyecto = $infoPro->nombre;
           $dd->bolson = $infoBolson->nombre;
       }

        return view('Backend.Admin.Bolson.Cuenta.Movimiento.tablaMoviBolson', compact('movi'));
    }


    public function nuevoMovimiento(Request $request){

        $rules = array(
            'fecha' => 'required',
            'proyecto' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        $or = new MovimientoBolson();
        $or->bolson_id = $request->bolson;
        $or->tipomovi_id = $request->movimiento;
        $or->proyecto_id = $request->proyecto;
        $or->aumenta = $request->aumenta;
        $or->disminuye = $request->disminuye;
        $or->fecha = $request->fecha;
        if($or->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionMovimiento(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = MovimientoBolson::where('id', $request->id)->first()){

            $infoBolson = Bolson::orderBy('nombre')->get();
            $infoProyecto = Proyecto::orderBy('nombre')->get();
            $infoTipo = TipoMovimiento::orderBy('nombre')->get();

            return ['success' => 1, 'info' => $lista, 'bolson' => $infoBolson,
                'idbolson' => $lista->bolson_id, 'proyecto' => $infoProyecto,
                'idproyecto' => $lista->proyecto_id, 'movimiento' => $infoTipo,
                'idmovi' => $lista->tipomovi_id];
        }else{
            return ['success' => 2];
        }
    }

    public function editarMovimiento(Request $request){
        $rules = array(
            'fecha' => 'required',
            'id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return ['success' => 0];
        }

        if(MovimientoBolson::where('id', $request->id)->first()){

            MovimientoBolson::where('id', $request->id)->update([
                'bolson_id' => $request->bolsonid,
                'tipomovi_id' => $request->movimientoid,
                'proyecto_id' => $request->proyectoid,
                'aumenta' => $request->aumenta,
                'disminuye' => $request->disminuye,
                'fecha' => $request->fecha,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

}

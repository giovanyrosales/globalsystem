<?php

namespace App\Http\Controllers\Backend\Uaciunidades\Requerimientos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//Estos metodos aun no estan funcionales porque son para los requerimientos que ingresa ruby desde la vista del proyecto individual, y tengo q hacerlos desde la vistad de cada unidad.
class RequerimientoController extends Controller
{
    public function tablaDepartamentoListaRequisicion($id){
        $listaRequisicion = Requisicion::where('id_proyecto', $id)
            ->orderBy('fecha', 'ASC')
            ->get();

        $numero = 0;
        foreach ($listaRequisicion as $ll){
            $numero += 1;
            $ll->numero = $numero;
        }



        return view('Backend.Admin.Proyectos.Requisicion.tablaRequisicion', compact('listaRequisicion'));
    }
    public function nuevoRequisicion(Request $request){

        $rules = array(
            'fecha' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $r = new Requisicion();
            $r->id_proyecto = $request->id;
            $r->destino = $request->destino;
            $r->fecha = $request->fecha;
            $r->necesidad = $request->necesidad;
            $r->estado = 0; // 0- no autorizado 1- autorizado
            $r->save();

            for ($i = 0; $i < count($request->cantidad); $i++) {

                $infom = CatalogoMateriales::where('id', $request->datainfo[$i])->first();
                $infoobj = ObjEspecifico::where('id', $infom->id_objespecifico)->first();

                // verificar el presupuesto detalle para el obj especifico de este material
                // obtener el saldo inicial - total de salidas y esto dara cuanto tengo en caja

                $infoP = Presupuesto::where('proyecto_id', $request->id)
                    ->where('objespeci_id', $infom->id_objespecifico)
                    ->first();

                $salidaDetalle = PresupuestoDetalle::where('presupuesto_id', $infoP->id)
                    ->where('tipo', 0) // salida
                    ->sum('dinero');

                $entradaDetalle = PresupuestoDetalle::where('presupuesto_id', $infoP->id)
                    ->where('tipo', 1) // entrada
                    ->sum('dinero');

                // esto es lo que hay de saldo restante en detalle para el obj especi.
                $saldoRestante = $infoP->saldo_inicial - ($salidaDetalle - $entradaDetalle);

                // verificar cantidad * dinero del material nuevo
                $saldoMaterial = $request->cantidad[$i] * $infom->pu;

                // verificar si alcanza el saldo para guardar la cotizacion
                if($saldoRestante < $saldoMaterial){
                    // retornar que no alcanza el saldo

                    $saldoRestante = number_format((float)$saldoRestante, 2, '.', ',');

                    return ['success' => 3, 'fila' => $i,
                        'obj' => $infoobj->codigo, 'disponible' => $saldoRestante];
                }else{

                    // si hay saldo para este material
                    // guardar detalle cotizacion e ingresar una salida de dinero

                    $rDetalle = new RequisicionDetalle();
                    $rDetalle->requisicion_id = $r->id;
                    $rDetalle->material_id = $request->datainfo[$i];
                    $rDetalle->cantidad = $request->cantidad[$i];
                    $rDetalle->estado = 0;
                    $rDetalle->save();

                    $rSalida = new PresupuestoDetalle();
                    $rSalida->presupuesto_id = $infoP->id;
                    $rSalida->tipo = 0; // salida
                    $rSalida->dinero = $saldoMaterial;
                    $rSalida->save();
                }
            }

            $contador = RequisicionDetalle::where('requisicion_id', $r->id)->count();
            $contador = $contador + 1;

            DB::commit();
            return ['success' => 1, 'contador' => $contador];

        }catch(\Throwable $e){
            //Log::info('eerror' . $e);
            DB::rollback();
            return ['success' => 2];
        }
    }

    function informacionRequisicion(Request $request){
        $rules = array(
            'id' => 'required', // id fila requisicion
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){
            return ['success' => 0];
        }

        if($info = Requisicion::where('id', $request->id)->first()){

            $detalle = RequisicionDetalle::where('requisicion_id', $request->id)
                ->orderBy('id', 'ASC')->get();

            foreach ($detalle as $dd){

                $datos = CatalogoMateriales::where('id', $dd->material_id)->first();
                $dd->descripcion = $datos->nombre;
            }

            return ['success' => 1, 'info' => $info, 'detalle' => $detalle];
        }
        return ['success' => 2];
    }

    public function editarRequisicion(Request $request){

        DB::beginTransaction();

        try {

            // actualizar registros requisicion
            Requisicion::where('id', $request->idrequisicion)->update([
                'destino' => $request->destino,
                'fecha' => $request->fecha,
                'necesidad' => $request->necesidad,
            ]);

            if($request->hayregistro == 1){

                // agregar id a pila
                $pila = array();
                for ($i = 0; $i < count($request->idarray); $i++) {
                    // Los id que sean 0, seran nuevos registros
                    if($request->idarray[$i] != 0) {
                        array_push($pila, $request->idarray[$i]);
                    }
                }

                // borrar todos los registros
                // primero obtener solo la lista de requisicon obtenido de la fila
                // y no quiero que borre los que si vamos a actualizar con los ID
                RequisicionDetalle::where('requisicion_id', $request->idrequisicion)
                    ->whereNotIn('id', $pila)
                    ->delete();

                // actualizar registros
                for ($i = 0; $i < count($request->cantidad); $i++) {
                    if($request->idarray[$i] != 0){
                        RequisicionDetalle::where('id', $request->idarray[$i])->update([
                            'cantidad' => $request->cantidad[$i],
                        ]);
                    }
                }

                // hoy registrar los nuevos registros
                for ($i = 0; $i < count($request->cantidad); $i++) {
                    if($request->idarray[$i] == 0){
                        $rDetalle = new RequisicionDetalle();
                        $rDetalle->requisicion_id = $request->idrequisicion;
                        $rDetalle->cantidad = $request->cantidad[$i];
                        $rDetalle->material_id = $request->datainfo[$i];
                        $rDetalle->estado = 0;
                        $rDetalle->save();
                    }
                }

                DB::commit();
                return ['success' => 1];
            }else{
                // borrar registros detalle
                // solo si viene vacio el array
                if($request->cantidad == null){
                    RequisicionDetalle::where('requisicion_id', $request->idrequisicion)->delete();
                }

                DB::commit();
                return ['success' => 1];
            }
        }catch(\Throwable $e){
            DB::rollback();
            return ['success' => 2];
        }
    }
}

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

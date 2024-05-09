<?php

namespace App\Http\Controllers\Backend\Configuracion\Referencias;

use App\Http\Controllers\Controller;
use App\Models\Referencias;
use App\Models\SecretariaDespacho;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ReferenciasController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }



    public function indexReferencia(){

        return view('backend.admin.configuraciones.referencias.vistareferencias');
    }


    public function tablaReferencia(){

        $lista = Referencias::orderBy('nombre')->get();

        return view('backend.admin.configuraciones.referencias.tablareferencias', compact('lista'));
    }


    public function nuevaReferencia(Request $request){

        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new Referencias();
        $dato->nombre = $request->nombre;
        $dato->save();

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }

    }


    public function informacionReferencia(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Referencias::where('id', $request->id)->first()){


            return ['success' => 1, 'lista' => $lista];
        }else{
            return ['success' => 2];
        }
    }


    public function editarReferencia(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        Referencias::where('id', $request->id)->update([
            'nombre' => $request->nombre,
        ]);


        return ['success' => 1];
    }




    public function indexSecreDespacho(){

        $fecha = Carbon::now('America/El_Salvador')->toDateString();;

        return view('backend.admin.secredespacho.despacho.vistadespacho', compact('fecha'));
    }



    public function tablaSecreDespacho(){

        $listado = SecretariaDespacho::orderBy('fecha', 'DESC')->get();

        foreach ($listado as $dato){

            $dato->fechaFormat = date("d-m-Y", strtotime($dato->fecha));

            $tiposoli = "";

            if($dato->tiposolicitud == 1){
                $tiposoli = "Vivienda Completa";
            }
            else if($dato->tiposolicitud == 2){
                $tiposoli = "Solo Vivienda";
            }
            else if($dato->tiposolicitud == 3){
                $tiposoli = "Materiales de Construcción";
            }
            else if($dato->tiposolicitud == 4){
                $tiposoli = "Viveres";
            }
            else if($dato->tiposolicitud == 5){
                $tiposoli = "Construcción";
            }

            $dato->tiposoli = $tiposoli;
        }

        return view('backend.admin.secredespacho.despacho.tabladespacho', compact('listado'));
    }


    public function guardarSecreDespacho(Request $request){

        $regla = array(
            'fecha' => 'required',
            'nombre' => 'required',
        );

        // telefono, direccion, editor, tiposolicitud

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();

        try {

            $dato = new SecretariaDespacho();
            $dato->nombre = $request->nombre;
            $dato->fecha = $request->fecha;
            $dato->telefono = $request->telefono;
            $dato->direccion = $request->direccion;
            $dato->descripcion = $request->editor;
            $dato->tiposolicitud = $request->tiposolicitud;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        }catch(\Throwable $e){

            DB::rollback();
            return ['success' => 99];
        }

    }



    public function borrarSecreDespacho(Request $request){

        $regla = array(
            'id' => 'required',
        );

        // telefono, direccion, editor

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(SecretariaDespacho::where('id', $request->id)->first()){
            SecretariaDespacho::where('id', $request->id)->delete();
        }

        return ['success' => 1];
    }



    public function informacionSecreDespacho(Request $request){

        $regla = array(
            'id' => 'required',
        );

        // telefono, direccion, editor

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($info = SecretariaDespacho::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $info];
        }

        return ['success' => 2];
    }



    public function editarSecreDespacho(Request $request){

        $regla = array(
            'fecha' => 'required',
            'nombre' => 'required',
        );

        // telefono, direccion, editor, tiposolicitud

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();

        try {

            SecretariaDespacho::where('id', $request->id)->update([
                'fecha' => $request->fecha,
                'nombre' => $request->nombre,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'descripcion' => $request->editor,
                'tiposolicitud' => $request->tiposolicitud
            ]);

            DB::commit();
            return ['success' => 1];
        }catch(\Throwable $e){
            Log::info('err: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }








}

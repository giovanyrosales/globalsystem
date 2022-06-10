<?php

namespace App\Http\Controllers\Backend\Configuraciones;

use App\Http\Controllers\Controller;
use App\Models\Proveedores;
use App\Models\Rubro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProveedoresController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){

        return view('Backend.Admin.Configuraciones.Proveedores.vistaProveedor');
    }

    public function tabla(){
        $lista = Proveedores::orderBy('nombre', 'ASC')->get();

        return view('Backend.Admin.Configuraciones.Proveedores.tablaProveedor', compact('lista'));
    }

    public function nuevoProveedor(Request $request){

        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new Proveedores();
        $dato->nombre = $request->nombre;
        $dato->telefono = $request->telefono;
        $dato->nit = $request->nit;
        $dato->nrc = $request->nrc;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // informacion
    public function informacionProveedor(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Proveedores::where('id', $request->id)->first()){

            return ['success' => 1, 'lista' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar
    public function editarProveedor(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Proveedores::where('id', $request->id)->first()){

            Proveedores::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'telefono' => $request->telefono,
                'nit' => $request->nit,
                'nrc' => $request->nrc
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


    // **************** RUBRO ****************

    public function indexRubro(){
        return view('Backend.Admin.Configuraciones.Rubro.vistaRubro');
    }

    public function tablaRubro(){
        $lista = Rubro::orderBy('nombre')->get();
        return view('Backend.Admin.Configuraciones.Rubro.tablaRubro', compact('lista'));
    }

    public function nuevaRubro(Request $request){

        $regla = array(
            'nombre' => 'required',
            'numero' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}


        $dato = new Rubro();
        $dato->nombre = $request->nombre;
        $dato->codigo = $request->numero;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // informacion
    public function informacionRubro(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Rubro::where('id', $request->id)->first()){

            return ['success' => 1, 'rubro' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar
    public function editarRubro(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
            'numero' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Rubro::where('id', $request->id)->first()){

            Rubro::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'codigo' => $request->numero
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }



}

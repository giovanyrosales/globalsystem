<?php

namespace App\Http\Controllers\Backend\Configuraciones;

use App\Http\Controllers\Controller;
use App\Models\Administradores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdministradoresController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){

        return view('backend.admin.configuraciones.administradores.vistaadministradores');
    }

    public function tabla(){
        $lista = Administradores::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.configuraciones.administradores.tablaadministradores', compact('lista'));
    }

    public function nuevoAdministrador(Request $request){

        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new Administradores();
        $dato->nombre = $request->nombre;
        $dato->telefono = $request->telefono;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // informacion
    public function informacionAdministrador(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Administradores::where('id', $request->id)->first()){

            return ['success' => 1, 'lista' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar
    public function editarAdministrador(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Administradores::where('id', $request->id)->first()){

            Administradores::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'telefono' => $request->telefono,

            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

}

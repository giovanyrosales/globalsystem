<?php

namespace App\Http\Controllers\Backend\Configuraciones;

use App\Http\Controllers\Controller;
use App\Models\Administradores;
use App\Models\InformacionGeneral;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdministradoresController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    // retorna vista con los nombres de administradores
    public function indexVistaAdministradores()
    {

        return view('backend.admin.proyectos.configuraciones.administradores.vistaadministradores');
    }

    // retorna tabla con los nombres de administradores
    public function tablaVistaAdministradores()
    {
        $lista = Administradores::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.proyectos.configuraciones.administradores.tablaadministradores', compact('lista'));
    }

    // registra nuevo administrador de proyectos
    public function nuevoAdministrador(Request $request)
    {

        $regla = array(
            'nombre' => 'required',
            'cargo' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        $dato = new Administradores();
        $dato->nombre = $request->nombre;
        $dato->telefono = $request->telefono;
        $dato->cargo = $request->cargo;

        if ($dato->save()) {
            return ['success' => 1];
        } else {
            return ['success' => 2];
        }
    }

    // obtener información de administrador de proyecto
    public function informacionAdministrador(Request $request)
    {
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if ($lista = Administradores::where('id', $request->id)->first()) {

            return ['success' => 1, 'lista' => $lista];
        } else {
            return ['success' => 2];
        }
    }

    // editar datos de administrador de proyecto
    public function editarAdministrador(Request $request)
    {

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        if (Administradores::where('id', $request->id)->first()) {

            Administradores::where('id', $request->id)->update([
                'nombre' => $request->nombre,
                'telefono' => $request->telefono,
                'cargo' => $request->cargo
            ]);

            return ['success' => 1];
        } else {
            return ['success' => 2];
        }
    }

    // obtener información de un imprevisto de proyecto
    public function informacionImprevistoProyecto(Request $request){


        if ($info = InformacionGeneral::where('id', 1)->first()) {

            $imprevisto = $info->imprevisto_modificable;
            $herramienta = $info->porcentaje_herramienta;

            return ['success' => 1, 'imprevisto' =>$imprevisto,
                'herramienta' => $herramienta];
        } else {
            return ['success' => 2];
        }
    }

    // editar imprevisto de proyecto
    public function editarImprevistoProyecto(Request $request){

        $regla = array(
            'imprevisto' => 'required',
            'herramienta' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()) {
            return ['success' => 0];
        }

        InformacionGeneral::where('id', 1)->update([
            'imprevisto_modificable' => $request->imprevisto,
            'porcentaje_herramienta' => $request->herramienta
        ]);

        return ['success' => 1];
    }

}

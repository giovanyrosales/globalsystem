<?php

namespace App\Http\Controllers\Backend\Configuraciones;

use App\Exports\ExportarConsolidadoExcel;
use App\Exports\ExportarPorUnidadesExcel;
use App\Exports\ExportarTotalesExcel;
use App\Http\Controllers\Controller;
use App\Models\CatalogoMateriales;
use App\Models\Clasificaciones;
use App\Models\Cuenta;
use App\Models\ObjEspecifico;
use App\Models\P_AnioPresupuesto;
use App\Models\P_Departamento;
use App\Models\P_Estado;
use App\Models\P_Materiales;
use App\Models\P_MaterialesDetalle;
use App\Models\P_PresupUnidad;
use App\Models\P_PresupUnidadDetalle;
use App\Models\P_UnidadMedida;
use App\Models\P_UsuarioDepartamento;
use App\Models\Proveedores;
use App\Models\Rubro;
use App\Models\UnidadMedida;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ProveedoresController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    // retorna vista con los proveedores para cotizaciones
    public function indexVistaProveedor(){
        return view('backend.admin.proyectos.configuraciones.proveedores.vistaproveedor');
    }

    // retorna tabla con los proveedores para cotizaciones
    public function tablaVistaProveedor(){
        $lista = Proveedores::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.proyectos.configuraciones.proveedores.tablaproveedor', compact('lista'));
    }

    // registra nuevo proveedor
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
        $dato->nombre_comercial = $request->comercial;
        $dato->dui = $request->dui;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // obtener información de un proveedor
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

    // edita la información de proveedor
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
                'nrc' => $request->nrc,
                'nombre_comercial' => $request->comercial,
                'dui' => $request->dui,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


    // **************** RUBRO ****************

    // retorna vista de rubros
    public function indexRubro(){
        return view('backend.admin.configuraciones.rubro.vistarubro');
    }


    public function tablaRubro(){
        $lista = Rubro::orderBy('nombre')->get();
        return view('backend.admin.configuraciones.rubro.tablarubro', compact('lista'));
    }

    // registra un nuevo rubro
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

    // obtener información de un rubro
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

    // editar un rubro
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

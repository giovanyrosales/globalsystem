<?php

namespace App\Http\Controllers\Controles;

use App\Http\Controllers\Controller;
use App\Models\P_Departamento;
use App\Models\P_UsuarioDepartamento;
use Illuminate\Support\Facades\Auth;

class ControlController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    // verifica que usuario inicio sesión y redirecciona a su vista según ROL
    public function indexRedireccionamiento(){

        $user = Auth::user();

        // ADMINISTRADOR SISTEMA
        if($user->hasRole('admin')){
            $ruta = 'admin.roles.index';
        }

        // UACI
        else  if($user->hasRole('uaci')){
            $ruta = 'admin.estadisticas.index';
        }

        //PRESUPUESTO
        else  if($user->hasRole('presupuesto')){
            $ruta = 'admin.usuario.departamento.vista.index';
        }

        //INGENIERIA
        else  if($user->hasRole('formulador')){
            $ruta = 'admin.estadisticas.index';
        }

        // ADMINISTRADOR QUE HACE REQUERIMIENTOS
        else  if($user->hasRole('administrador')){
            $ruta = 'admin.estadisticas.index';
        }

        // JEFE UACI
        else  if($user->hasRole('jefeuaci')){
            $ruta = 'admin.estadisticas.index';
        }
         // UACI UNIDAD
         else  if($user->hasRole('uaciunidad')){
            $ruta = 'admin.estadisticas.index';
        }
         // UNIDAD
         else  if($user->hasRole('unidad')){
            $ruta = 'p.admin.crear.presupuesto.index';
         }
         // SECRETARIA
         else  if($user->hasRole('secretaria')){
             $ruta = 'admin.estadisticas.index';
         }

         // CONSOLIDADOR
         else  if($user->hasRole('consolidador')){
             $ruta = 'requerimientos.pendientes.consolidadoras';
         }

         // SECRETARIA DESPACHO
         else  if($user->hasRole('despacho')){
             $ruta = 'sidebar.secretaria.despacho';
         }


         // RRHH ACTUALIZACION DATOS PERSONAL
         else  if($user->hasRole('rrhh.datos')){
             $ruta = 'sidebar.rrhh.actualizacion.datos';
         }


         // SINDICATURA
         else  if($user->hasRole('sindico')){
             $ruta = 'admin.sindico.registro.index';
         }




        else{
            // no tiene ningun permiso de vista, redirigir a pantalla sin permisos
            $ruta = 'no.permisos.index';
        }

        $titulo = "Alcaldía de Metapán";
        if($infoUsuario = P_UsuarioDepartamento::where('id_usuario', $user->id)->first()){
            $infoDepartamento = P_Departamento::where('id', $infoUsuario->id_departamento)->first();
            $titulo = $titulo . " - " . $infoDepartamento->nombre;
        }

        return view('backend.index', compact( 'ruta', 'user', 'titulo'));
    }

    // redirecciona a vista sin permisos
    public function indexSinPermiso(){
        return view('errors.403');
    }
}

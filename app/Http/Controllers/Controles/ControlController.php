<?php

namespace App\Http\Controllers\Controles;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ControlController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

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
        else  if($user->hasRole('sidebar.estadisticas')){
            $ruta = 'admin.estadisticas.index';
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
            $ruta = 'admin.estadisticas.index';
        }
        else{
            // no tiene ningun permiso de vista, redirigir a pantalla sin permisos
            $ruta = 'no.permisos.index';
        }

        return view('backend.index', compact( 'ruta', 'user'));
    }

    public function indexSinPermiso(){
        return view('errors.403');
    }
}

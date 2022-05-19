<?php

namespace App\Http\Controllers\Controles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ControlController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function indexRedireccionamiento(){

        $user = Auth::user();

        // ADMINISTRADOR
        if($user->hasPermissionTo('sidebar.roles.y.permisos')){
            $ruta = 'admin.estadisticas.index';
        }

        // UACI
        else  if($user->hasPermissionTo('sidebar.estadisticas')){
            $ruta = 'admin.estadisticas.index';
        }

        //PRESUPUESTO
        else  if($user->hasPermissionTo('sidebar.estadisticas')){
            $ruta = 'admin.estadisticas.index';
        }

        //INGENIERIA
        else  if($user->hasPermissionTo('sidebar.estadisticas')){
            $ruta = 'admin.estadisticas.index';
        }

        // JEFE UACI
        else  if($user->hasPermissionTo('sidebar.estadisticas')){
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

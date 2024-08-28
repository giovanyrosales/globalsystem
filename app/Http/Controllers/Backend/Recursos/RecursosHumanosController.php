<?php

namespace App\Http\Controllers\Backend\Recursos;

use App\Http\Controllers\Controller;
use App\Models\RRHHcargo;
use App\Models\RRHHempleados;
use App\Models\RRHHenfermedades;
use App\Models\RRHHunidad;
use Illuminate\Http\Request;

class RecursosHumanosController extends Controller
{
    // SERA PUBLICO PARA INGRESAR DATOS DE RECURSOS HUMANOS

    public function vistaIngresoDatos(){

        $listaEmpleados = RRHHempleados::orderBy('nombre', 'ASC')->get();
        $listaCargos = RRHHcargo::orderBy('nombre', 'ASC')->get();
        $listaUnidad = RRHHunidad::orderBy('nombre', 'ASC')->get();
        $listaEnfermedad = RRHHenfermedades::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.rrhhdatos.vistahojaactualizacion', compact('listaEmpleados',
        'listaCargos', 'listaUnidad', 'listaEnfermedad'));
    }


    public function guardarIngresoDatos(Request  $request){

        return ['success' => 1];
    }
}

<?php

namespace App\Http\Controllers\Backend\Configuracion\Estadisticas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EstadisticasController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    // vista para estadísticas
    public function indexEstadisticas(){
        return view('backend.admin.estadisticas.vistaestadisticas');
    }
}

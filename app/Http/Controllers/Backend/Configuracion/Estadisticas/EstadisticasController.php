<?php

namespace App\Http\Controllers\Backend\Configuracion\Estadisticas;

use App\Http\Controllers\Controller;
use App\Models\P_Materiales;
use App\Models\P_PresupUnidadDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

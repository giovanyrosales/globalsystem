<?php

namespace App\Http\Controllers\Backend\Bodega;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\bodega_material;
use App\Models\bodega_salida;
use App\Models\bodega_entrada;
use App\Models\bodega_detalle_entrada;
use App\Models\bodega_detalle_salida;
use Illuminate\Support\Facades\Validator;

class BSolicitudesController extends Controller
{
    // retorna vista de crear solicitud de la seccion de bodega en cada unidad solicitante
    public function indexBodegaSolicitud(){
        $unidadmedida = UnidadMedida::orderBy('medida', 'ASC')->get();
        $objespecifico = ObjEspecifico::orderBy('codigo', 'ASC')->get();

        return view('backend.admin.bodega.vistamateriales', compact('unidadmedida', 'objespecifico'));
    }
}

<?php

namespace App\Http\Controllers\Backend\Configuracion\Consolidador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConsolidadorController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }




}

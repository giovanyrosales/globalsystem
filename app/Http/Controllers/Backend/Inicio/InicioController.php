<?php

namespace App\Http\Controllers\Backend\Inicio;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InicioController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        return view('Backend.Admin.Inicio.vistaInicio');
    }





}

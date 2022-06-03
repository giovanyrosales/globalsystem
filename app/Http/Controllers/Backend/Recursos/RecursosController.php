<?php

namespace App\Http\Controllers\Backend\Recursos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RecursosController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){

        return view('Backend.Admin.Recursos.vistaRecursosHumanos');
    }
}

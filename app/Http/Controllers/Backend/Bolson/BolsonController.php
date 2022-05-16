<?php

namespace App\Http\Controllers\Backend\Bolson;

use App\Http\Controllers\Controller;
use App\Models\Bolson;
use App\Models\Cuenta;
use App\Models\MovimientoBolson;
use App\Models\Proyecto;
use App\Models\TipoMovimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BolsonController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function indexCuenta(){
        return view('backend.admin.bolson.cuenta.vistacuentabolson');
    }

    public function tablaCuenta(){

        $cuenta = Bolson::orderBy('fecha')->get();

        foreach ($cuenta as $dd){

            $infoCuenta = Cuenta::where('id', $dd->id_cuenta)->first();
            $dd->cuenta = $infoCuenta->nombre;
            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));
            $dd->montoini = number_format((float)$dd->montoini, 2, '.', '');
            $dd->saldo = number_format((float)$dd->saldo, 2, '.', '');
        }

        return view('backend.admin.bolson.cuenta.tablacuentabolson', compact('cuenta'));
    }

    public function buscarNombreCuenta(Request $request){

        if($request->get('query')){
            $query = $request->get('query');
            $data = Cuenta::where('nombre', 'LIKE', "%{$query}%")->take(25)->get();

            $output = '<ul class="dropdown-menu" style="display:block; position:relative;">';
            $tiene = true;
            foreach($data as $row){

                // si solo hay 1 fila, No mostrara el hr, salto de linea
                if(count($data) == 1){
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValor('.$row->id.')" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->nombre . ' - ' .$row->codigo .'</a></li>
                ';
                    }
                }

                else{
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValor('.$row->id.')" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->nombre . ' - ' .$row->codigo .'</a></li>
                   <hr>
                ';
                    }
                }
            }
            $output .= '</ul>';
            if($tiene){
                $output = '';
            }
            echo $output;
        }
    }

    public function buscarNombreCuentaEditar(Request $request){

        if($request->get('query')){
            $query = $request->get('query');
            $data = Cuenta::where('nombre', 'LIKE', "%{$query}%")->take(25)->get();

            $output = '<ul class="dropdown-menu" style="display:block; position:relative;">';
            $tiene = true;
            foreach($data as $row){

                // si solo hay 1 fila, No mostrara el hr, salto de linea
                if(count($data) == 1){
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValorEditar('.$row->id.')" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->nombre . ' - ' .$row->codigo .'</a></li>
                ';
                    }
                }

                else{
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValorEditar('.$row->id.')" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->nombre . ' - ' .$row->codigo .'</a></li>
                   <hr>
                ';
                    }
                }
            }
            $output .= '</ul>';
            if($tiene){
                $output = '';
            }
            echo $output;
        }
    }

    public function nuevoRegistro(Request $request){

        $rules = array(
            'fecha' => 'required',
            'nombre' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        $or = new Bolson();
        $or->id_cuenta = $request->idcuenta;
        $or->nombre = $request->nombre;
        $or->numero = $request->numero;
        $or->fecha = $request->fecha;
        $or->montoini = $request->monto;
        $or->saldo = 0;
        $or->estado = 0;
        if($or->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionBolson(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Bolson::where('id', $request->id)->first()){

            $info = Cuenta::where('id', $lista->id_cuenta)->first();

            return ['success' => 1, 'info' => $lista, 'cuenta' => $info->nombre];
        }else{
            return ['success' => 2];
        }
    }

    public function editarRegistro(Request $request){
        $rules = array(
            'fecha' => 'required',
            'nombre' => 'required',
            'id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        if(Bolson::where('id', $request->id)->first()){

            Bolson::where('id', $request->id)->update([
                'id_cuenta' => $request->idcuenta,
                'nombre' => $request->nombre,
                'numero' => $request->numero,
                'fecha' => $request->fecha,
                'montoini' => $request->monto,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }


    //**** MOVIMIENTO DE CUENTA ****

    public function indexMovimiento(){

        $proyecto = Proyecto::orderBy('nombre')->get();
        $bolson = Bolson::orderBy('nombre')->get();
        $tipomovi = TipoMovimiento::orderBy('nombre')->get();

        return view('backend.admin.bolson.cuenta.movimiento.vistamovibolson', compact('proyecto',
        'bolson', 'tipomovi'));
    }

    public function tablaMovimiento(){

        $movi = MovimientoBolson::orderBy('fecha')->get();

       foreach ($movi as $dd){

           $infoPro = Proyecto::where('id', $dd->proyecto_id)->first();
           $infoBolson = Bolson::where('id', $dd->bolson_id)->first();

           $dd->fecha = date("d-m-Y", strtotime($dd->fecha));
           $dd->proyecto = $infoPro->nombre;
           $dd->bolson = $infoBolson->nombre;
       }

        return view('backend.admin.bolson.cuenta.movimiento.tablamovibolson', compact('movi'));
    }


    public function nuevoMovimiento(Request $request){

        $rules = array(
            'fecha' => 'required',
            'proyecto' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        $or = new MovimientoBolson();
        $or->bolson_id = $request->bolson;
        $or->tipomovi_id = $request->movimiento;
        $or->proyecto_id = $request->proyecto;
        $or->aumenta = $request->aumenta;
        $or->disminuye = $request->disminuye;
        $or->fecha = $request->fecha;
        if($or->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionMovimiento(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = MovimientoBolson::where('id', $request->id)->first()){

            $infoBolson = Bolson::orderBy('nombre')->get();
            $infoProyecto = Proyecto::orderBy('nombre')->get();
            $infoTipo = TipoMovimiento::orderBy('nombre')->get();

            return ['success' => 1, 'info' => $lista, 'bolson' => $infoBolson,
                'idbolson' => $lista->bolson_id, 'proyecto' => $infoProyecto,
                'idproyecto' => $lista->proyecto_id, 'movimiento' => $infoTipo,
                'idmovi' => $lista->tipomovi_id];
        }else{
            return ['success' => 2];
        }
    }

    public function editarMovimiento(Request $request){
        $rules = array(
            'fecha' => 'required',
            'id' => 'required'
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return ['success' => 0];
        }

        if(MovimientoBolson::where('id', $request->id)->first()){

            MovimientoBolson::where('id', $request->id)->update([
                'bolson_id' => $request->bolsonid,
                'tipomovi_id' => $request->movimientoid,
                'proyecto_id' => $request->proyectoid,
                'aumenta' => $request->aumenta,
                'disminuye' => $request->disminuye,
                'fecha' => $request->fecha,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }



}

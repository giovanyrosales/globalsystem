<?php

namespace App\Http\Controllers\Backend\Configuraciones;

use App\Http\Controllers\Controller;
use App\Models\AreaGestion;
use App\Models\LineaTrabajo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LineaTrabajoController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    // retorna vista con las líneas de trabajo
    public function indexLineaTrabajo(){

        $area = AreaGestion::orderBy('codigo', 'ASC')->get();

        return view('backend.admin.proyectos.configuraciones.lineatrabajo.vistalineadetrabajo', compact('area'));
    }

    // retorna tabla con las líneas de trabajo
    public function tablaLineaTrabajo(){
        $lista = LineaTrabajo::orderBy('codigo', 'ASC')->get();

        foreach ($lista as $ll){

            $info = AreaGestion::where('id', $ll->id_areagestion)->first();
            $ll->area = $info->codigo . " " . $info->nombre;
        }

        return view('backend.admin.proyectos.configuraciones.lineatrabajo.tablalineadetrabajo', compact('lista'));
    }

    // registrar nueva línea de trabajo
    public function nuevaLineaTrabajo(Request $request){

        $regla = array(
            'codigo' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new LineaTrabajo();
        $dato->codigo = $request->codigo;
        $dato->nombre = $request->nombre;
        $dato->id_areagestion = $request->area;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // obtener información de línea de trabajo
    public function informacionLineaTrabajo(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = LineaTrabajo::where('id', $request->id)->first()){

            $arrayarea = AreaGestion::orderBy('id', 'ASC')->get();

            return ['success' => 1, 'linea' => $lista, 'arrayarea' => $arrayarea];
        }else{
            return ['success' => 2];
        }
    }

    // editar línea de trabajo
    public function editarLineaTrabajo(Request $request){

        $regla = array(
            'id' => 'required',
            'codigo' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(LineaTrabajo::where('id', $request->id)->first()){

            LineaTrabajo::where('id', $request->id)->update([
                'codigo' => $request->codigo,
                'nombre' => $request->nombre,
                'id_areagestion' => $request->area
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

}

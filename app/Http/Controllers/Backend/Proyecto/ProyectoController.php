<?php

namespace App\Http\Controllers\Backend\Proyecto;

use App\Http\Controllers\Controller;
use App\Models\AreaGestion;
use App\Models\Bitacora;
use App\Models\BitacoraDetalle;
use App\Models\Bolson;
use App\Models\EstadoProyecto;
use App\Models\FuenteFinanciamiento;
use App\Models\FuenteRecursos;
use App\Models\LineaTrabajo;
use App\Models\Naturaleza;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ProyectoController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){

        $arrayNaturaleza = Naturaleza::orderBy('nombre')->get();
        $arrayAreaGestion = AreaGestion::orderBy('codigo')->get();
        $arrayLineaTrabajo = LineaTrabajo::orderBy('codigo')->get();
        $arrayFuenteFinanciamiento = FuenteFinanciamiento::orderBy('codigo')->get();
        $arrayFuenteRecursos = FuenteRecursos::orderBy('codigo')->get();

        return view('backend.admin.proyectos.nuevoproyecto', compact('arrayNaturaleza',
        'arrayAreaGestion', 'arrayLineaTrabajo', 'arrayFuenteFinanciamiento', 'arrayFuenteRecursos'));
    }

    public function nuevoProyecto(Request $request){

        if(Proyecto::where('codigo', $request->codigo)->first()){
            return ['success' => 1];
        }

        if($request->hasFile('documento')) {

            $cadena = Str::random(15);
            $tiempo = microtime();
            $union = $cadena . $tiempo;
            $nombre = str_replace(' ', '_', $union);

            $extension = '.' . $request->documento->getClientOriginalExtension();
            $nomDocumento = $nombre . strtolower($extension);
            $avatar = $request->file('documento');
            $archivo = Storage::disk('archivos')->put($nomDocumento, \File::get($avatar));

            if($archivo){

                $p = new Proyecto();
                $p->codigo = $request->codigo;
                $p->nombre = $request->nombre;
                $p->ubicacion = $request->ubicacion;
                $p->id_naturaleza = $request->naturaleza;
                $p->id_areagestion = $request->areagestion;
                $p->id_linea = $request->linea;
                $p->id_fuentef = $request->fuentef;
                $p->id_fuenter = $request->fuenter;
                $p->contraparte = $request->contraparte;
                $p->codcontable = $request->codcontable;
                $p->fechaini = $request->fechainicio;
                $p->acuerdoapertura = $nomDocumento;
                $p->ejecutor = $request->ejecutor;
                $p->formulador = $request->formulador;
                $p->supervisor = $request->supervisor;
                $p->encargado = $request->encargado;
                $p->fecha = Carbon::now('America/El_Salvador');

                if($p->save()){
                    return ['success' => 2];
                }else{
                    return ['success' => 3];
                }
            }
            else{
                return ['success' => 3];
            }
        }else{
            $p = new Proyecto();
            $p->codigo = $request->codigo;
            $p->nombre = $request->nombre;
            $p->ubicacion = $request->ubicacion;
            $p->id_naturaleza = $request->naturaleza;
            $p->id_areagestion = $request->areagestion;
            $p->id_linea = $request->linea;
            $p->id_fuentef = $request->fuentef;
            $p->id_fuenter = $request->fuenter;
            $p->contraparte = $request->contraparte;
            $p->codcontable = $request->codcontable;
            $p->fechaini = $request->fechainicio;
            $p->ejecutor = $request->ejecutor;
            $p->formulador = $request->formulador;
            $p->supervisor = $request->supervisor;
            $p->encargado = $request->encargado;
            $p->fecha = Carbon::now('America/El_Salvador');
            $p->monto = 0;

            if($p->save()){
                return ['success' => 2];
            }else{
                return ['success' => 3];
            }
        }
    }

    public function indexProyectoLista(){

        return view('backend.admin.proyectos.listaproyecto');
    }

    public function tablaProyectoLista(){

        $lista = Proyecto::orderBy('fecha')->get();

        foreach ($lista as $ll){
            if($ll->fechaini != null) {
                $ll->fechaini = date("d-m-Y", strtotime($ll->fechaini));
            }
        }

        return view('backend.admin.proyectos.tablalistaproyecto', compact('lista'));
    }

    public function informacionProyecto(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($info = Proyecto::where('id', $request->id)->first()){

            $arrayNaturaleza = Naturaleza::orderBy('nombre')->get();
            $arrayAreaGestion = AreaGestion::orderBy('codigo')->get();
            $arrayLineaTrabajo = LineaTrabajo::orderBy('codigo')->get();
            $arrayFuenteFinanciamiento = FuenteFinanciamiento::orderBy('codigo')->get();
            $arrayFuenteRecursos = FuenteRecursos::orderBy('codigo')->get();
            $arrayBolson = Bolson::orderBy('nombre')->get();
            $arrayEstado = EstadoProyecto::orderBy('nombre')->get();

            // evitar null
            foreach ($arrayAreaGestion as $ll){
                if($ll->nombre == null){
                    $ll->nombre = '';
                }
            }

            foreach ($arrayLineaTrabajo as $ll){
                if($ll->nombre == null){
                    $ll->nombre = '';
                }
            }

            foreach ($arrayFuenteFinanciamiento as $ll){
                if($ll->nombre == null){
                    $ll->nombre = '';
                }
            }

            foreach ($arrayFuenteRecursos as $ll){
                if($ll->nombre == null){
                    $ll->nombre = '';
                }
            }

            return ['success' => 1, 'info' => $info, 'arrayNaturaleza' => $arrayNaturaleza,
                'arrayAreaGestion' => $arrayAreaGestion, 'arrayLineaTrabajo' => $arrayLineaTrabajo,
                'arrayFuenteFinanciamiento' => $arrayFuenteFinanciamiento,
                'arrayFuenteRecursos' => $arrayFuenteRecursos, 'arrayBolson' => $arrayBolson,
                'arrayEstado' => $arrayEstado];
        }else{
            return ['success' => 2];
        }
    }

    public function editarProyecto(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        if (Proyecto::where('codigo', $request->codigo)
            ->where('id', '!=', $request->id)
            ->first()) {
            return ['success' => 1];
        }

        if ($request->hasFile('documento')) {

            $cadena = Str::random(15);
            $tiempo = microtime();
            $union = $cadena . $tiempo;
            $nombre = str_replace(' ', '_', $union);

            $extension = '.' . $request->documento->getClientOriginalExtension();
            $nomDocumento = $nombre . strtolower($extension);
            $avatar = $request->file('documento');
            $archivo = Storage::disk('archivos')->put($nomDocumento, \File::get($avatar));

            if ($archivo) {

                $info = Proyecto::where('id', $request->id)->first();
                $documentoOld = $info->acuerdoapertura;

                Proyecto::where('id', $request->id)->update([
                    'codigo' => $request->codigo,
                    'nombre' => $request->nombre,
                    'ubicacion' => $request->ubicacion,
                    'id_naturaleza' => $request->naturaleza,
                    'id_areagestion' => $request->areagestion,
                    'id_linea' => $request->linea,
                    'id_fuentef' => $request->fuentef,
                    'id_fuenter' => $request->fuenter,
                    'contraparte' => $request->contraparte,
                    'codcontable' => $request->codcontable,
                    'fechaini' => $request->fechainicio,
                    'acuerdoapertura' => $nomDocumento,
                    'ejecutor' => $request->ejecutor,
                    'formulador' => $request->formulador,
                    'supervisor' => $request->supervisor,
                    'encargado' => $request->encargado,
                    'id_bolson' => $request->bolson,
                    'monto' => $request->monto,
                    'id_estado' => $request->estado,
                ]);

                // borrar documento anterior
                if (Storage::disk('archivos')->exists($documentoOld)) {
                    Storage::disk('archivos')->delete($documentoOld);
                }

                return ['success' => 2];
            } else {
                return ['success' => 3];
            }
        } else {
            Proyecto::where('id', $request->id)->update([
                'codigo' => $request->codigo,
                'nombre' => $request->nombre,
                'ubicacion' => $request->ubicacion,
                'id_naturaleza' => $request->naturaleza,
                'id_areagestion' => $request->areagestion,
                'id_linea' => $request->linea,
                'id_fuentef' => $request->fuentef,
                'id_fuenter' => $request->fuenter,
                'contraparte' => $request->contraparte,
                'codcontable' => $request->codcontable,
                'fechaini' => $request->fechainicio,
                'ejecutor' => $request->ejecutor,
                'formulador' => $request->formulador,
                'supervisor' => $request->supervisor,
                'encargado' => $request->encargado,
                'id_bolson' => $request->bolson,
                'monto' => $request->monto,
                'id_estado' => $request->estado,
            ]);

            return ['success' => 2];
        }
    }

    public function indexProyectoVista($id){
        $proyecto = Proyecto::where('id', $id)->first();
        return view('backend.admin.proyectos.vistaproyecto', compact('proyecto', 'id'));
    }

    public function tablaProyectoListaBitacora($id){

        $listaBitacora = Bitacora::where('id_proyecto', $id)
            ->orderBy('fecha')
            ->get();

        return view('backend.admin.proyectos.bitacoras.tablabitacoras', compact('listaBitacora'));
    }

    public function registrarBitacora(Request $request){

        $regla = array(
            'id' => 'required', // id de proyecto
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        DB::beginTransaction();

        try {

            $numero = Bitacora::where('id_proyecto', $request->id)->count();
            if($numero == null){
                $numero = 0;
            }

            if ($request->hasFile('documento')) {

                $cadena = Str::random(15);
                $tiempo = microtime();
                $union = $cadena . $tiempo;
                $nombre = str_replace(' ', '_', $union);

                $extension = '.' . $request->documento->getClientOriginalExtension();
                $nomDocumento = $nombre . strtolower($extension);
                $avatar = $request->file('documento');
                $archivo = Storage::disk('archivos')->put($nomDocumento, \File::get($avatar));

               if($archivo){

                   $b = new Bitacora();
                   $b->id_proyecto = $request->id;
                   $b->numero = $numero + 1;
                   $b->fecha = $request->fecha;
                   $b->observaciones = $request->observaciones;
                   $b->save();

                   $d = new BitacoraDetalle();
                   $d->id_bitacora = $b->id;
                   $d->nombre = $request->nombredocumento;
                   $d->documento = $nomDocumento;
                   $d->save();

                   DB::commit();
                   return ['success' => 1];
               }else{
                   return ['success' => 2];
               }
            }
            else{
                $b = new Bitacora();
                $b->id_proyecto = $request->id;
                $b->numero = $numero + 1;
                $b->fecha = $request->fecha;
                $b->observaciones = $request->observaciones;
                $b->save();

                if($request->nombredocumento != null){
                    $d = new BitacoraDetalle();
                    $d->id_bitacora = $b->id;
                    $d->nombre = $request->nombredocumento;
                    $d->save();
                }

                DB::commit();
                return ['success' => 1];
            }

        }catch(\Throwable $e){
            DB::rollback();
            return ['success' => 2];
        }
    }

    public function borrarBitacora(Request $request){
        $regla = array(
            'id' => 'required', // id de proyecto
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        if(Bitacora::where('id', $request->id)->first()){

            // obtener listado
            $lista = BitacoraDetalle::where('id_bitacora', $request->id)->get();

            // borrar cada documento primero si tiene
            foreach ($lista as $ll){
                if (Storage::disk('archivos')->exists($ll->documento)) {
                    Storage::disk('archivos')->delete($ll->documento);
                }
            }

            // borrar listado detalle
            BitacoraDetalle::where('id_bitacora', $request->id)->delete();
            Bitacora::where('id', $request->id)->delete();

            return ['success' => 1];
        }else{
            // siempre regresar 1
            return ['success' => 1];
        }
    }

    public function informacionBitacora(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Bitacora::where('id', $request->id)->first()){

            return ['success' => 1, 'bitacora' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarBitacora(Request $request){

        $regla = array(
            'id' => 'required',
            'fecha' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Bitacora::where('id', $request->id)->first()){

            Bitacora::where('id', $request->id)->update([
                'fecha' => $request->fecha,
                'observaciones' => $request->observaciones
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function vistaBitacoraDetalle($id){ // id de bitacora
        return view('backend.admin.proyectos.bitacoras.vistabitacoradetalle', compact('id'));
    }

    public function tablaBitacoraDetalle($id){ // id de bitacora
        $lista = BitacoraDetalle::where('id_bitacora', $id)->orderBy('id')->get();
        return view('backend.admin.proyectos.bitacoras.tablabitacoradetalle', compact('lista'));
    }

    public function descargarBitacoraDoc($id){ // id de bitacora

        $url = BitacoraDetalle::where('id', $id)->pluck('documento')->first();

        $pathToFile = "storage/archivos/".$url;

        $extension = pathinfo(($pathToFile), PATHINFO_EXTENSION);

        $nombre = "Doc." . $extension;

        return response()->download($pathToFile, $nombre);
    }

}

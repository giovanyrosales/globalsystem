<?php

namespace App\Http\Controllers\Backend\Proyecto;

use App\Http\Controllers\Controller;
use App\Models\AreaGestion;
use App\Models\Bitacora;
use App\Models\BitacoraDetalle;
use App\Models\Bolson;
use App\Models\CatalogoMateriales;
use App\Models\Cotizacion;
use App\Models\CotizacionDetalle;
use App\Models\EstadoProyecto;
use App\Models\FuenteFinanciamiento;
use App\Models\FuenteRecursos;
use App\Models\LineaTrabajo;
use App\Models\Naturaleza;
use App\Models\Partida;
use App\Models\Proveedores;
use App\Models\Proyecto;
use App\Models\Requisicion;
use App\Models\RequisicionDetalle;
use App\Models\UnidadMedida;
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

    // vista para agregar nuevo proyecto
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

        $conteo = Requisicion::where('id_proyecto', $id)->count();
        if($conteo == null){
            $conteo = 1;
        }else{
            $conteo += 1;
        }

        $conteoPartida = Partida::where('proyecto_id', $id)->count();
        if($conteoPartida == null){
            $conteoPartida = 1;
        }else{
            $conteoPartida += 1;
        }

        return view('backend.admin.proyectos.vistaproyecto', compact('proyecto', 'id',
            'conteo', 'conteoPartida'));
    }

    public function tablaProyectoListaBitacora($id){

        $listaBitacora = Bitacora::where('id_proyecto', $id)
            ->orderBy('fecha')
            ->get();

        $numero = 0;
        foreach ($listaBitacora as $ll){
            $numero += 1;
            $ll->numero = $numero;
        }

        return view('backend.admin.proyectos.bitacoras.tablabitacoras', compact('listaBitacora'));
    }


    // registrar nueva bitacora
    public function registrarBitacora(Request $request){

        $regla = array(
            'id' => 'required', // id de proyecto
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        DB::beginTransaction();

        try {

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
                $b->fecha = $request->fecha;
                $b->observaciones = $request->observaciones;
                $b->save();

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
            'id' => 'required', // id bitacora
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

    // descargar imagen de bitacora detalle
    public function descargarBitacoraDoc($id){ // id de bitacora

        $url = BitacoraDetalle::where('id', $id)->pluck('documento')->first();

        $pathToFile = "storage/archivos/".$url;

        $extension = pathinfo(($pathToFile), PATHINFO_EXTENSION);

        $nombre = "Doc." . $extension;

        return response()->download($pathToFile, $nombre);
    }

    // borrar una bitacora detalle
    public function borrarBitacoraDetalle(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($info = BitacoraDetalle::where('id', $request->id)->first()){

            $doc = $info->documento;

            BitacoraDetalle::where('id', $request->id)->delete();

          if (Storage::disk('archivos')->exists($doc)) {
              Storage::disk('archivos')->delete($doc);
          }

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // agregar nueva bitacora detalle
    public function nuevoBitacoraDetalle(Request $request){
        $regla = array(
            'id' => 'required', // id de proyecto
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        $numero = Bitacora::where('id_proyecto', $request->id)->count();
        if($numero == null){
            $numero = 0;
        }

        $cadena = Str::random(15);
        $tiempo = microtime();
        $union = $cadena . $tiempo;
        $nombre = str_replace(' ', '_', $union);

        $extension = '.' . $request->documento->getClientOriginalExtension();
        $nomDocumento = $nombre . strtolower($extension);
        $avatar = $request->file('documento');
        $archivo = Storage::disk('archivos')->put($nomDocumento, \File::get($avatar));

        if($archivo){
            $d = new BitacoraDetalle();
            $d->id_bitacora = $request->id;
            $d->nombre = $request->nombredocumento;
            $d->documento = $nomDocumento;
            $d->save();

            DB::commit();
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function tablaProyectoListaRequisicion($id){
        $listaRequisicion = Requisicion::where('id_proyecto', $id)
            ->orderBy('fecha', 'ASC')
            ->get();

        $numero = 0;
        foreach ($listaRequisicion as $ll){
            $numero += 1;
            $ll->numero = $numero;
        }

        return view('backend.admin.proyectos.requisicion.tablarequisicion', compact('listaRequisicion'));
    }


    public function nuevoRequisicion(Request $request){

        $rules = array(
            'fecha' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $r = new Requisicion();
            $r->id_proyecto = $request->id;
            $r->destino = $request->destino;
            $r->fecha = $request->fecha;
            $r->necesidad = $request->necesidad;
            $r->estado = 0; // 0- no autorizado 1- autorizado
            $r->save();

            $contador = $request->contador + 1;

            if($request->hayregistro == 1){

                if($request->cantidad != null) {
                    for ($i = 0; $i < count($request->cantidad); $i++) {

                        $rDetalle = new RequisicionDetalle();
                        $rDetalle->requisicion_id = $r->id;
                        $rDetalle->material_id = $request->datainfo[$i];
                        $rDetalle->cantidad = $request->cantidad[$i];
                        $rDetalle->estado = 0;
                        $rDetalle->save();
                    }
                }

                DB::commit();
                return ['success' => 1, 'contador' => $contador];
            }else{
                DB::commit();
                return ['success' => 1, 'contador' => $contador];
            }

        }catch(\Throwable $e){
            DB::rollback();
            return ['success' => 2];
        }
    }

    function informacionRequisicion(Request $request){
        $rules = array(
            'id' => 'required', // id fila requisicion
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){
            return ['success' => 0];
        }

        if($info = Requisicion::where('id', $request->id)->first()){

            $detalle = RequisicionDetalle::where('requisicion_id', $request->id)
                ->orderBy('id', 'ASC')->get();

            foreach ($detalle as $dd){

                $datos = CatalogoMateriales::where('id', $dd->material_id)->first();
                $dd->descripcion = $datos->nombre;
            }

            return ['success' => 1, 'info' => $info, 'detalle' => $detalle];
        }
        return ['success' => 2];
    }

    public function editarRequisicion(Request $request){

        DB::beginTransaction();

        try {

            // actualizar registros requisicion
            Requisicion::where('id', $request->idrequisicion)->update([
                'destino' => $request->destino,
                'fecha' => $request->fecha,
                'necesidad' => $request->necesidad,
            ]);

            if($request->hayregistro == 1){

                // agregar id a pila
                $pila = array();
                for ($i = 0; $i < count($request->idarray); $i++) {
                    // Los id que sean 0, seran nuevos registros
                    if($request->idarray[$i] != 0) {
                        array_push($pila, $request->idarray[$i]);
                    }
                }

                // borrar todos los registros
                // primero obtener solo la lista de requisicon obtenido de la fila
                // y no quiero que borre los que si vamos a actualizar con los ID
                RequisicionDetalle::where('requisicion_id', $request->idrequisicion)
                    ->whereNotIn('id', $pila)
                    ->delete();

                // actualizar registros
                for ($i = 0; $i < count($request->cantidad); $i++) {
                    if($request->idarray[$i] != 0){
                        RequisicionDetalle::where('id', $request->idarray[$i])->update([
                            'cantidad' => $request->cantidad[$i],
                        ]);
                    }
                }

                // hoy registrar los nuevos registros
                for ($i = 0; $i < count($request->cantidad); $i++) {
                    if($request->idarray[$i] == 0){
                        $rDetalle = new RequisicionDetalle();
                        $rDetalle->requisicion_id = $request->idrequisicion;
                        $rDetalle->cantidad = $request->cantidad[$i];
                        $rDetalle->material_id = $request->datainfo[$i];
                        $rDetalle->estado = 0;
                        $rDetalle->save();
                    }
                }

                DB::commit();
                return ['success' => 1];
            }else{
                // borrar registros detalle
                // solo si viene vacio el array
                if($request->cantidad == null){
                    RequisicionDetalle::where('requisicion_id', $request->idrequisicion)->delete();
                }

                DB::commit();
                return ['success' => 1];
            }
        }catch(\Throwable $e){
            DB::rollback();
            return ['success' => 2];
        }
    }

    // vista cotizacion
    public function indexCotizacion($id){ // id requisicion
        $requisicion = Requisicion::where('id', $id)->first();
        $proveedores = Proveedores::orderBy('nombre')->get();

        $requisicionDetalle = RequisicionDetalle::where('requisicion_id', $requisicion->id)
            ->where('estado', 0)
            ->get();

        foreach ($requisicionDetalle as $dd){
            $descripcion = CatalogoMateriales::where('id', $dd->material_id)->first();
            $dd->descripcion = $descripcion->nombre;
        }

        return view('backend.admin.proyectos.cotizacion.vistacotizacion', compact('requisicion', 'proveedores',
            'requisicionDetalle', 'id'));
    }

    public function obtenerListaCotizaciones(Request $request){

        // obtener lista de detalle requisicion por array ID
        $lista = RequisicionDetalle::whereIn('id', $request->lista)->get();

        foreach ($lista as $dd){

            $info = CatalogoMateriales::where('id', $dd->material_id)->first();
            $unidad = UnidadMedida::where('id', $info->id_unidadmedida)->first();

            $dd->descripcion = $info->nombre;
            $dd->medida = $unidad->medida;
        }

        return ['success' => 1, 'lista' => $lista];
    }

    public function buscadorMaterial(Request $request){

        if($request->get('query')){
            $query = $request->get('query');
            $data = CatalogoMateriales::where('nombre', 'LIKE', "%{$query}%")->take(25)->get();

            foreach ($data as $dd){
                $info = UnidadMedida::where('id', $dd->id_unidadmedida)->first();
                $dd->medida = $info->medida;
            }

            $output = '<ul class="dropdown-menu buscador" style="display:block; position:relative;">';
            $tiene = true;
            foreach($data as $row){

                // si solo hay 1 fila, No mostrara el hr, salto de linea
                if(count($data) == 1){
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValor(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->nombre . ' - ' .$row->medida .'</a></li>
                ';
                    }
                }

                else{
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValor(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->nombre . ' - ' .$row->medida .'</a></li>
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

    // utilizado para un usuario tipo ingenieria
    public function buscadorMaterialPresupuesto(Request $request){

        if($request->get('query')){
            $query = $request->get('query');
            $data = CatalogoMateriales::where('nombre', 'LIKE', "%{$query}%")->take(25)->get();

            foreach ($data as $dd){
                $info = UnidadMedida::where('id', $dd->id_unidadmedida)->first();
                $dd->medida = $info->medida;
            }

            $output = '<ul class="dropdown-menu buscador" style="display:block; position:relative;">';
            $tiene = true;
            foreach($data as $row){

                // si solo hay 1 fila, No mostrara el hr, salto de linea
                if(count($data) == 1){
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValorPresupuesto(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->nombre . ' - ' .$row->medida .'</a></li>
                ';
                    }
                }

                else{
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValorPresupuesto(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->nombre . ' - ' .$row->medida .'</a></li>
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

    public function nuevaCotizacion(Request $request){

        $rules = array(
            'fecha' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $r = new Cotizacion();
            $r->proveedor_id = $request->proveedor;
            $r->requisicion_id = $request->id;
            $r->fecha = $request->fecha;
            $r->estado = 0; // aprobada o no aprobada por jefa uaci
            $r->save();

            for ($i = 0; $i < count($request->precio); $i++) {

                // obtener cantidad
                $info = RequisicionDetalle::where('id', $request->idarray[$i])->first();

                $rDetalle = new CotizacionDetalle();
                $rDetalle->cotizacion_id = $r->id;
                $rDetalle->material_id = $info->material_id;
                $rDetalle->cantidad = $info->cantidad;
                $rDetalle->precio_u = $request->precio[$i];
                $rDetalle->cod_presup = $request->codigo[$i];
                $rDetalle->estado = 0;
                $rDetalle->save();
            }

            DB::commit();
            return ['success' => 1];
        }catch(\Throwable $e){
            DB::rollback();
            return ['success' => 2];
        }
    }

    // *** INGENIERIA ***
    public function tablaProyectoListaPresupuesto($id){

        $partida = Partida::where('proyecto_id', $id)
            ->orderBy('id', 'ASC')
            ->get();

        return view('backend.admin.proyectos.tablalistapresupuesto', compact('partida'));
    }

    public function agregarPresupuesto(Request $request){

        return ['success' => 1];
    }


}

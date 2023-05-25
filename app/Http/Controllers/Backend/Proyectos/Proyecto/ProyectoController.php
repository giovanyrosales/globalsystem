<?php

namespace App\Http\Controllers\Backend\Proyectos\Proyecto;

use App\Http\Controllers\Controller;
use App\Models\AreaGestion;
use App\Models\Bitacora;
use App\Models\BitacoraDetalle;
use App\Models\Bolson;
use App\Models\CatalogoMateriales;
use App\Models\Cotizacion;
use App\Models\CotizacionDetalle;
use App\Models\CuentaProy;
use App\Models\CuentaproyPartidaAdicional;
use App\Models\CuentaProyRetenido;
use App\Models\EstadoProyecto;
use App\Models\FuenteFinanciamiento;
use App\Models\FuenteRecursos;
use App\Models\InformacionGeneral;
use App\Models\LineaTrabajo;
use App\Models\MoviCuentaProy;
use App\Models\Naturaleza;
use App\Models\ObjEspecifico;
use App\Models\Orden;
use App\Models\P_Materiales;
use App\Models\P_PresupUnidadDetalle;
use App\Models\Partida;
use App\Models\PartidaAdicional;
use App\Models\PartidaAdicionalContenedor;
use App\Models\PartidaDetalle;
use App\Models\Proveedores;
use App\Models\Proyecto;
use App\Models\Requisicion;
use App\Models\RequisicionDetalle;
use App\Models\TipoPartida;
use App\Models\UnidadMedida;
use App\Models\UsuarioFormulador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function indexNuevoProyecto(){

        $arrayNaturaleza = Naturaleza::orderBy('nombre')->get();
        $arrayAreaGestion = AreaGestion::orderBy('codigo')->get();
        $arrayLineaTrabajo = LineaTrabajo::orderBy('codigo')->get();
        $arrayFuenteFinanciamiento = FuenteFinanciamiento::orderBy('codigo')->get();
        $arrayFuenteRecursos = FuenteRecursos::orderBy('codigo')->get();

        $formuladores = DB::table('usuario_formulador AS pud')
            ->join('usuario AS u', 'pud.id_usuario', '=', 'u.id')
            ->select('u.id', 'u.nombre')
            ->orderBy('u.nombre')
            ->get();

        return view('backend.admin.proyectos.nuevo.vistanuevoproyecto', compact('arrayNaturaleza',
            'arrayAreaGestion', 'arrayLineaTrabajo', 'arrayFuenteFinanciamiento', 'arrayFuenteRecursos',
        'formuladores'));
    }

    // registra un nuevo proyecto
    public function nuevoProyecto(Request $request){

        if(Proyecto::where('codigo', $request->codigo)->first()){
            return ['success' => 1];
        }

        $p = new Proyecto();
        $p->id_linea = $request->linea;
        $p->id_fuentef = $request->fuentef;
        $p->id_fuenter = $request->fuenter;
        $p->id_areagestion = $request->areagestion;
        $p->id_naturaleza = $request->naturaleza;
        $p->id_estado = null;
        $p->id_bolson = null;
        $p->codigo = $request->codigo;
        $p->nombre = $request->nombre;
        $p->ubicacion = $request->ubicacion;
        $p->contraparte = $request->contraparte;
        $p->fechaini = null;
        $p->fechafin = null;
        $p->fecha = Carbon::now('America/El_Salvador');
        $p->ejecutor = $request->ejecutor;
        $p->id_formulador = $request->formulador;
        $p->supervisor = $request->supervisor;
        $p->encargado = $request->encargado;
        $p->codcontable = $request->codcontable;
        $p->acuerdoapertura = null;
        $p->acuerdocierre = null;
        $p->imprevisto_fijo = 0; // este sera usado cuando sea aprobado presupuesto
        $p->porcentaje_herra_fijo = 0; // sera seteado al aprobar partida
        $p->presu_aprobado = 0;
        $p->fecha_aprobado = null;
        $p->permiso = 0;
        $p->permiso_partida_adic = 0;
        $p->monto = 0; // cuando presu es aprobado, esto es el monto de partida
        $p->monto_finalizado = 0; // es lo que sobra cuando proyecto es finalizado
        $p->porcentaje_obra = 20; // porcentaje obra adicional por defecto

        if($p->save()){
            return ['success' => 2];
        }else{
            return ['success' => 99];
        }
    }

    // retorna vista de todos los proyectos
    public function indexProyectoLista(){
        return view('backend.admin.proyectos.listaproyectos.vistalistaproyecto');
    }

    // retorna tabla con todos los proyectos creados
    public function tablaProyectoLista(){

        $lista = Proyecto::orderBy('fecha')->get();

        foreach ($lista as $ll){
            if($ll->fechaini != null) {
                $ll->fechaini = date("d-m-Y", strtotime($ll->fechaini));
            }

            if($ll->presu_aprobado == 2){
                $ll->montopartida = '$' . number_format((float)$ll->monto, 2, '.', ',');
            }else{
                $ll->montopartida = "Pendiente";
            }

            // proyecto finalizado
            if($ll->id_estado == 4){
                $ll->montosobrante = '$' . number_format((float)$ll->monto_finalizado, 2, '.', ',');
            }else{
                $ll->montosobrante = "Pendiente";
            }

        }

        return view('backend.admin.proyectos.listaproyectos.tablalistaproyecto', compact('lista'));
    }

    // retorna información de un proyecto en específico
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
            $arrayEstado = EstadoProyecto::orderBy('nombre')->get();
            $arrayFormulador = DB::table('usuario_formulador AS pud')
                ->join('usuario AS u', 'pud.id_usuario', '=', 'u.id')
                ->select('u.id', 'u.nombre')
                ->orderBy('u.nombre')
                ->get();

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
                'arrayFuenteRecursos' => $arrayFuenteRecursos,
                'arrayEstado' => $arrayEstado, 'arrayFormulador' => $arrayFormulador];
        }else{
            return ['success' => 2];
        }
    }

    // edita la información de un proyecto
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


        DB::beginTransaction();

        try {

            $infoProyecto = Proyecto::where('id', $request->id)->first();

            // usuario logeado
            $user = Auth::user();

            // solo id formulador podra editar
            if($user->id != $infoProyecto->id_formulador){
                return ['success' => 4];
            }

            if($infoProyecto->id_estado == 3){
                // pausado

                $texto = "El estado del proyecto es Pausado";
                return ['success' => 3, 'mensaje' => $texto];
            }

            if($infoProyecto->id_estado == 4){
                // finalizado

                $texto = "El estado del proyecto es Finalizado";
                return ['success' => 3, 'mensaje' => $texto];
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

                    $documentoOld = $infoProyecto->acuerdoapertura;

                    $pro = Proyecto::find($request->id);
                    $pro->codigo = $request->codigo;
                    $pro->nombre = $request->nombre;
                    $pro->ubicacion = $request->ubicacion;
                    $pro->id_naturaleza = $request->naturaleza;
                    $pro->id_areagestion = $request->areagestion;
                    $pro->id_linea = $request->linea;
                    $pro->id_fuentef = $request->fuentef;
                    $pro->id_fuenter = $request->fuenter;
                    $pro->contraparte = $request->contraparte;
                    $pro->codcontable = $request->codcontable;
                    if($infoProyecto->presu_aprobado == 2){ // aprobado
                        $pro->fechaini = $request->fechainicio;
                        $pro->acuerdoapertura = $nomDocumento;
                    }
                    $pro->ejecutor = $request->ejecutor;
                    $pro->id_formulador = $request->idformulador;
                    $pro->supervisor = $request->supervisor;
                    $pro->encargado = $request->encargado;
                    $pro->save();

                    // borrar documento anterior
                    if (Storage::disk('archivos')->exists($documentoOld)) {
                        Storage::disk('archivos')->delete($documentoOld);
                    }

                    DB::commit();
                    return ['success' => 2];
                } else {
                    return ['success' => 99];
                }
            } else {

                $pro = Proyecto::find($request->id);
                $pro->codigo = $request->codigo;
                $pro->nombre = $request->nombre;
                $pro->ubicacion = $request->ubicacion;
                $pro->id_naturaleza = $request->naturaleza;
                $pro->id_areagestion = $request->areagestion;
                $pro->id_linea = $request->linea;
                $pro->id_fuentef = $request->fuentef;
                $pro->id_fuenter = $request->fuenter;
                $pro->contraparte = $request->contraparte;
                $pro->codcontable = $request->codcontable;
                if($infoProyecto->presu_aprobado == 2){ // aprobado
                    $pro->fechaini = $request->fechainicio;
                }
                $pro->ejecutor = $request->ejecutor;
                $pro->id_formulador = $request->idformulador;
                $pro->supervisor = $request->supervisor;
                $pro->encargado = $request->encargado;
                $pro->save();

                DB::commit();
                return ['success' => 2];
            }

        }catch(\Throwable $e){
            Log::info('ee' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // retorna vista con información del proyecto individual por ID
    public function indexProyectoVista($id){
        $proyecto = Proyecto::where('id', $id)->first();

        $conteo = Requisicion::where('id_proyecto', $id)->count();
        if($conteo == null){
            $conteo = 1;
        }else{
            $conteo += 1;
        }

        $conteoPartida = Partida::where('proyecto_id', $id)->count();
        if($conteoPartida == 0){
            $conteoPartida = 1;
        }else{
            $conteoPartida += 1;
        }

        $estado = $proyecto->presu_aprobado;

        $preaprobacion = '';
        if($proyecto->presu_aprobado == 2){
            $preaprobacion = "Presupuesto Aprobado   " . date("d-m-Y", strtotime($proyecto->fecha_aprobado));;
        }

        $tipospartida = TipoPartida::orderBy('id', 'ASC')->get();

        $boolPartidaAdicional = false;
        // para mostrar boton "materiales partida adicional" para que administradora de proyecto vea los materiales
        // que fueron aprobados en una partida adicional
        if(PartidaAdicionalContenedor::where('id_proyecto', $id)
            ->where('estado', 2) // contenedor aprobado
            ->first()){
            // con solo 1 que encuentre es suficiente
            $boolPartidaAdicional = true;
        }

        return view('backend.admin.proyectos.infoproyectoindividual.vistaproyecto', compact('proyecto', 'id',
            'conteo', 'conteoPartida', 'estado', 'preaprobacion', 'tipospartida', 'boolPartidaAdicional'));
    }

    // retorna vista de tabla de las bitácoras de un proyecto por ID
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

    // registra una nueva bitácora para proyecto ID
    public function registrarBitacora(Request $request){

        $regla = array(
            'id' => 'required', // id de proyecto
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        DB::beginTransaction();

        try {

            $infoProyecto = Proyecto::where('id', $request->id)->first();

            if($infoProyecto->id_estado == 3){
                // pausado

                $texto = "El estado del proyecto es Pausado";
                return ['success' => 1, 'mensaje' => $texto];
            }

            if($infoProyecto->id_estado == 4){
                // finalizado

                $texto = "El estado del proyecto es Finalizado";
                return ['success' => 1, 'mensaje' => $texto];
            }

            $user = Auth::user();

            // solo id formulador podra editar
            if($user->id != $infoProyecto->id_formulador){
                return ['success' => 3];
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
                    $b->fecha = $request->fecha;
                    $b->observaciones = $request->observaciones;
                    $b->save();

                    $d = new BitacoraDetalle();
                    $d->id_bitacora = $b->id;
                    $d->nombre = $request->nombredocumento;
                    $d->documento = $nomDocumento;
                    $d->save();

                    DB::commit();
                    return ['success' => 2];
                }else{
                    return ['success' => 99];
                }
            }
            else{
                $b = new Bitacora();
                $b->id_proyecto = $request->id;
                $b->fecha = $request->fecha;
                $b->observaciones = $request->observaciones;
                $b->save();

                DB::commit();
                return ['success' => 2];
            }

        }catch(\Throwable $e){
            DB::rollback();
            return ['success' => 99];
        }
    }

    // borrar una bitácora de proyecto ID
    public function borrarBitacora(Request $request){
        $regla = array(
            'id' => 'required', // id bitacora
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        if($infoBitacora = Bitacora::where('id', $request->id)->first()){

            $infoProyecto = Proyecto::where('id', $infoBitacora->id_proyecto)->first();

            if($infoProyecto->id_estado == 3){
                // pausado

                $texto = "El estado del proyecto es Pausado";
                return ['success' => 1, 'mensaje' => $texto];
            }

            if($infoProyecto->id_estado == 4){
                // finalizado

                $texto = "El estado del proyecto es Finalizado";
                return ['success' => 1, 'mensaje' => $texto];
            }

            $user = Auth::user();

            // solo id formulador podra editar
            if($user->id != $infoProyecto->id_formulador){
                return ['success' => 3];
            }


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

            return ['success' => 2];
        }else{

            return ['success' => 99];
        }
    }

    // información de una bitácora proyecto por ID
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

    // editar bitácora de un proyecto ID
    public function editarBitacora(Request $request){

        $regla = array(
            'id' => 'required',
            'fecha' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($infoBitacora = Bitacora::where('id', $request->id)->first()){

            $infoProyecto = Proyecto::where('id', $infoBitacora->id_proyecto)->first();

            if($infoProyecto->id_estado == 3){
                // pausado

                $texto = "El estado del proyecto es Pausado";
                return ['success' => 1, 'mensaje' => $texto];
            }

            if($infoProyecto->id_estado == 4){
                // finalizado

                $texto = "El estado del proyecto es Finalizado";
                return ['success' => 1, 'mensaje' => $texto];
            }

            Bitacora::where('id', $request->id)->update([
                'fecha' => $request->fecha,
                'observaciones' => $request->observaciones
            ]);

            return ['success' => 2];
        }else{
            return ['success' => 99];
        }
    }

    // pasa a otra vista donde esta el detalle de la bitácora de un proyecto por ID bitácora
    public function vistaBitacoraDetalle($id){ // id de bitacora
        return view('backend.admin.proyectos.bitacoras.vistabitacoradetalle', compact('id'));
    }

    // retorna tabla detalle de bitácora de un proyecto por ID bitácora
    public function tablaBitacoraDetalle($id){ // id de bitacora
        $lista = BitacoraDetalle::where('id_bitacora', $id)->orderBy('id')->get();
        return view('backend.admin.proyectos.bitacoras.tablabitacoradetalle', compact('lista'));
    }

    // descargar un documento de la bitácora por ID
    public function descargarBitacoraDoc($id){ // id de bitacora

        $url = BitacoraDetalle::where('id', $id)->pluck('documento')->first();

        $pathToFile = "storage/archivos/".$url;

        $extension = pathinfo(($pathToFile), PATHINFO_EXTENSION);

        $nombre = "Doc." . $extension;

        return response()->download($pathToFile, $nombre);
    }

    // borrar documento bitácora por ID
    public function borrarBitacoraDetalle(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($infoDetalle = BitacoraDetalle::where('id', $request->id)->first()){

            $infoBitacora = Bitacora::where('id', $infoDetalle->id_bitacora)->first();
            $infoProyecto = Proyecto::where('id', $infoBitacora->id_proyecto)->first();

            if($infoProyecto->id_estado == 3){
                // pausado

                $texto = "El estado del proyecto es Pausado";
                return ['success' => 1, 'mensaje' => $texto];
            }

            if($infoProyecto->id_estado == 4){
                // finalizado

                $texto = "El estado del proyecto es Finalizado";
                return ['success' => 1, 'mensaje' => $texto];
            }

            $user = Auth::user();

            // solo id formulador podra editar
            if($user->id != $infoProyecto->id_formulador){
                return ['success' => 3];
            }


            $doc = $infoDetalle->documento;

            BitacoraDetalle::where('id', $request->id)->delete();

            if (Storage::disk('archivos')->exists($doc)) {
                Storage::disk('archivos')->delete($doc);
            }

            return ['success' => 2];
        }else{
            return ['success' => 99];
        }
    }

    // registrar nuevo detalle a una bitácora por ID
    public function nuevoBitacoraDetalle(Request $request){
        $regla = array(
            'id' => 'required', // id bitacora
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}


        $infoBitacora = Bitacora::where('id', $request->id)->first();
        $infoProyecto = Proyecto::where('id', $infoBitacora->id_proyecto)->first();

        if($infoProyecto->id_estado == 3){
            // pausado

            $texto = "El estado del proyecto es Pausado";
            return ['success' => 1, 'mensaje' => $texto];
        }

        if($infoProyecto->id_estado == 4){
            // finalizado

            $texto = "El estado del proyecto es Finalizado";
            return ['success' => 1, 'mensaje' => $texto];
        }

        $user = Auth::user();

        // solo id formulador podra editar
        if($user->id != $infoProyecto->id_formulador){
            return ['success' => 3];
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
            return ['success' => 2];
        }else{
            return ['success' => 99];
        }
    }

    // retorna tabla con todas las requisiciones de un Proyecto ID
    public function tablaProyectoListaRequisicion($id){

        // listado de requisiciones
        $listaRequisicion = Requisicion::where('id_proyecto', $id)
            ->orderBy('fecha', 'ASC')
            ->get();

        $contador = 0;
        foreach ($listaRequisicion as $ll){
            $contador += 1;
            $ll->numero = $contador;
            $ll->fecha = date("d-m-Y", strtotime($ll->fecha));

            $infoEstado = "Editar";
            //------------------------------------------------------------------
            // SI HAY MATERIAL COTIZADO, NO PODRA BORRAR REQUISICIÓN YA
            $hayCotizacion = true;
            if(Cotizacion::where('requisicion_id', $ll->id)->first()){
                $hayCotizacion = false;
                $infoEstado = "Información"; // no tomar si esta default, aprobado, denegado
            }

            $ll->haycotizacion = $hayCotizacion;
            $ll->estado = $infoEstado;

        } // end foreach


        return view('backend.admin.proyectos.requisicion.tablarequisicioning', compact('listaRequisicion'));
    }

    function redondear_dos_decimal($valor) {
        $float_redondeado=round($valor * 100) / 100;
        return $float_redondeado;
    }

    // crea una nueva requisición
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

            // VERIFICAR ESTADOS DE PROYECTO
            if($infoPr = Proyecto::where('id', $request->id)->first()){

                if($infoPr->id_estado == 3){
                    // pausado

                    $texto = "El estado del proyecto es Pausado";
                    return ['success' => 3, 'mensaje' => $texto];
                }

                if($infoPr->id_estado == 4){
                    // finalizado

                    $texto = "El estado del proyecto es Finalizado";
                    return ['success' => 3, 'mensaje' => $texto];
                }
            }


            // primero se crea, y después verificamos
            $r = new Requisicion();
            $r->id_proyecto = $request->id;
            $r->destino = $request->destino;
            $r->fecha = $request->fecha;
            $r->necesidad = $request->necesidad;
            $r->save();

            for ($i = 0; $i < count($request->cantidad); $i++) {

                $infoCatalogo = CatalogoMateriales::where('id', $request->datainfo[$i])->first();

                // en esta parte ya debera tener objeto específico
                $infoObjeto = ObjEspecifico::where('id', $infoCatalogo->id_objespecifico)->first();
                $txtObjeto = $infoObjeto->codigo . " - " . $infoObjeto->nombre;

                // verificar el presupuesto detalle para el obj especifico de este material
                // obtener el saldo inicial - total de salidas y esto dara cuanto tengo en caja

                // como siempre busco material que estaban en el presupuesto, siempre encontrara
                // el proyecto ID y el ID de objeto específico
                $infoCuentaProy = CuentaProy::where('proyecto_id', $request->id)
                    ->where('objespeci_id', $infoCatalogo->id_objespecifico)
                    ->first();

                // CÁLCULOS

                $totalRestante = 0;
                $totalRetenido = 0;

                // movimiento de cuentas (sube y baja)
                $infoMoviCuentaProySube = MoviCuentaProy::where('id_cuentaproy_sube', $infoCuentaProy->id)
                    ->where('autorizado', 1) // autorizado por presupuesto
                    ->sum('dinero');

                $infoMoviCuentaProyBaja = MoviCuentaProy::where('id_cuentaproy_baja', $infoCuentaProy->id)
                    ->where('autorizado', 1) // autorizado por presupuesto
                    ->sum('dinero');

                $totalMoviCuenta = $infoMoviCuentaProySube - $infoMoviCuentaProyBaja;

                // obtener todas las salidas de material
                $arrayRestante = DB::table('cuentaproy_restante AS pd')
                    ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                    ->select('rd.cantidad', 'rd.dinero')
                    ->where('pd.id_cuentaproy', $infoCuentaProy->id)
                    ->where('rd.cancelado', 0)
                    ->get();

                foreach ($arrayRestante as $dd){
                    $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
                }

                $infoCuentaPartida = CuentaproyPartidaAdicional::where('id_proyecto', $infoCuentaProy->proyecto_id)->get();

                $sumaPartidaAdicional = 0;
                foreach ($infoCuentaPartida as $dd){
                    if($infoCuentaProy->objespeci_id == $dd->objespeci_id){
                        $sumaPartidaAdicional += $dd->monto;
                    }
                }

                // información de saldos retenidos
                $arrayRetenido = DB::table('cuentaproy_retenido AS psr')
                    ->join('requisicion_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                    ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                    ->where('psr.id_cuentaproy', $infoCuentaProy->id)
                    ->where('rd.cancelado', 0)
                    ->get();

                foreach ($arrayRetenido as $dd){
                    $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
                }

                //
                $sumaPartidaAdicional += $infoCuentaProy->saldo_inicial;

                // aquí se obtiene el Saldo Restante del código
                $totalRestanteSaldo = $totalMoviCuenta + ($sumaPartidaAdicional - $totalRestante);

                // Esto es lo que hay SALDO RESTANTE quitando el retenido
                $totalCalculado = $totalRestanteSaldo - $totalRetenido;


                // Verificar cantidad * dinero del material nuevo.
                // Este dinero se está solicitando para la fila.
                $saldoMaterial = $request->cantidad[$i] * $infoCatalogo->pu;

                if($this->redondear_dos_decimal($totalCalculado) < $this->redondear_dos_decimal($saldoMaterial)){

                    // retornar que no alcanza el saldo

                    // SALDO RESTANTE Y SALDO RETENIDO FORMATEADOS
                    $restanteFormat = number_format((float)$totalRestanteSaldo, 2, '.', ',');
                    $retenidoFormat = number_format((float)$totalRetenido, 2, '.', ',');

                    $saldoMaterial = number_format((float)$saldoMaterial, 2, '.', ',');

                    return ['success' => 1, 'fila' => $i,
                        'obj' => $txtObjeto,
                        'restanteFormat' => $restanteFormat,
                        'retenidoFormat' => $retenidoFormat,
                        'retenido' => $totalRetenido,
                        'solicita' => $saldoMaterial, // el usuario solicita este dinero
                    ];

                }else{

                    // si hay saldo para este material
                    // guardar detalle cotización e ingresar una salida de dinero

                    $rDetalle = new RequisicionDetalle();
                    $rDetalle->requisicion_id = $r->id;
                    $rDetalle->material_id = $request->datainfo[$i];
                    $rDetalle->cantidad = $request->cantidad[$i];
                    $rDetalle->estado = 0;
                    $rDetalle->dinero = $infoCatalogo->pu; // lo que vale el material en ese momento
                    $rDetalle->cancelado = 0;
                    $rDetalle->save();

                    // guardar el SALDO RETENIDO
                    $rRetenido = new CuentaProyRetenido();
                    $rRetenido->id_requi_detalle = $rDetalle->id;
                    $rRetenido->id_cuentaproy = $infoCuentaProy->id;

                    $rRetenido->save();
                }
            }

            $contador = RequisicionDetalle::where('requisicion_id', $r->id)->count();
            $contador = $contador + 1;

            DB::commit();
            return ['success' => 2, 'contador' => $contador];

        }catch(\Throwable $e){
            Log::info('ERROR ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // información de una requisición de proyecto
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
                ->orderBy('id', 'ASC')
                ->get();

            foreach ($detalle as $deta) {

                $multi = ($deta->cantidad * $deta->dinero);
                $multi = number_format((float)$multi, 2, '.', ',');
                $deta->multiplicado = $multi;

                $infoCatalogo = CatalogoMateriales::where('id', $deta->material_id)->first();

                //-------------------------------------------

                // VERIFICAR QUE ESTE MATERIAL ESTE COTIZADO. PARA NO BORRARLO O PARA CANCELAR SI YA NO LO VA A QUERER

                $infoCotiDetalle = CotizacionDetalle::where('id_requidetalle', $deta->id)->get();
                $pila = array();
                $haycoti = false;
                foreach ($infoCotiDetalle as $dd){
                    $haycoti = true;
                    array_push($pila, $dd->cotizacion_id);
                }

                // saber si ya fue aprobado alguna cotizacion o todas han sido denegadas
                $infoCoti = Cotizacion::whereIn('id', $pila)
                    ->where('estado', 1) // aprobados
                    ->count();

                if($infoCoti > 0){
                    // COTI APROBADA, NO PUEDE BORRAR
                    $infoEstado = 1;

                    // verificar si la orden de compra con esa cotización fue denegada, para cancelar

                    // todas las cotizaciones donde puede estar este MATERIAL DE REQUI DETALLE
                    $arrayCoti = Cotizacion::whereIn('id', $pila)->get();
                    $pilaCoti = array();
                    foreach ($arrayCoti as $dd){
                        array_push($pilaCoti, $dd->id);
                    }

                    // ver si existe al menos 1 orden
                    if(Orden::whereIn('cotizacion_id', $pilaCoti)->first()){
                        $conteoOrden = Orden::whereIn('cotizacion_id', $pilaCoti)
                            ->where('estado', 0) // APROBADA LA ORDEN
                            ->count();

                        if($conteoOrden > 0){
                            // material tiene una orden aprobada
                        }else{
                            // material tiene una orden denegada
                            $infoEstado = 2;
                        }
                    }
                }else{
                    // COTI DENEGADA, PUEDE CANCELAR MATERIAL
                    $infoEstado = 2;
                }

                $deta->cotizado = $infoEstado;
                $deta->haycoti = $haycoti;
                //-------------------------------------------------

                $infoObjeto = ObjEspecifico::where('id', $infoCatalogo->id_objespecifico)->first();
                $infoUnidad = UnidadMedida::where('id', $infoCatalogo->id_unidadmedida)->first();

                $unidoCodigo = $infoObjeto->codigo . " - " . $infoObjeto->nombre;
                $unidoNombre = $infoCatalogo->nombre . " - " . $infoUnidad->medida;

                $deta->descripcion = $unidoNombre;
                $deta->codigo = $unidoCodigo;


            }// end foreach

            // conocer si hay una cotizacion hecha, asi no puede editar detalles como fecha, destino, necesidad
            $btnEditar = false;
            if(Cotizacion::where('requisicion_id', $info->id)->first()){
                $btnEditar = true;
            }

            return ['success' => 1, 'info' => $info, 'detalle' => $detalle, 'btneditar' => $btnEditar];
        }
        return ['success' => 2];
    }

    // editar una requisición de proyecto
    public function editarRequisicion(Request $request){

        DB::beginTransaction();

        try {

            $infoRequisicion = Requisicion::where('id', $request->idrequisicion)->first();

            if($infoProyecto = Proyecto::where('id', $infoRequisicion->id_proyecto)->first()){

                if($infoProyecto->id_estado == 3){
                    // pausado

                    $texto = "El estado del proyecto es Pausado";
                    return ['success' => 3, 'mensaje' => $texto];
                }

                if($infoProyecto->id_estado == 4){
                    // finalizado

                    $texto = "El estado del proyecto es Finalizado";
                    return ['success' => 3, 'mensaje' => $texto];
                }
            }


            // ACTUALIZAR SOLAMENTE SI NO TIENE COTIZACIÓN
            if(!Cotizacion::where('requisicion_id', $request->idrequisicion)->first()){
                Requisicion::where('id', $request->idrequisicion)->update([
                    'destino' => $request->destino,
                    'fecha' => $request->fecha,
                    'necesidad' => $request->necesidad,
                ]);
            }

            // agregar id a pila
            $pila = array();
            for ($i = 0; $i < count($request->idarray); $i++) {
                // Los id que sean 0, seran nuevos registros
                if($request->idarray[$i] != 0) {
                    array_push($pila, $request->idarray[$i]);
                }
            }

            // OBTENER LOS ID REQUISICION DETALLE QUE SE VAN A BORRAR,
            // Y SOLO VERIFICAR QUE NO ESTE COTIZADO
            $infoRequiDetalle = RequisicionDetalle::where('requisicion_id', $request->idrequisicion)
                ->whereNotIn('id', $pila)
                ->get();

            $pilaBorrar = array();

            // ya con los id a borrar. verificar que no esten cotizados
            foreach ($infoRequiDetalle as $dd){
                array_push($pilaBorrar, $dd->id);
                if($dd->estado == 1){
                    // MATERIAL COTIZADO, RETORNAR

                    $infoCatalogo = CatalogoMateriales::where('id', $dd->material_id)->first();
                    return ['success' => 1, 'nombre' => $infoCatalogo->nombre];
                }
            }

            // YA SE PUEDE BORRAR SI HAY MATERIALES A BORRAR.
            // borrar de saldo retenido y de la requisicion detalle
            CuentaProyRetenido::whereIn('id_requi_detalle', $pilaBorrar)->delete();

            RequisicionDetalle::whereIn('id', $pilaBorrar)->delete();

            DB::commit();
            return ['success' => 2];

        }catch(\Throwable $e){

            DB::rollback();
            return ['success' => 99];
        }
    }

    // petición para cancelar un material de requisición
    public function cancelarMaterialRequisicion(Request $request){

        // ID REQUISICION DETALLE


        $infoRequiDetalle = RequisicionDetalle::where('id', $request->id)->first();
        $infoRequisicion = Requisicion::where('id', $infoRequiDetalle->requisicion_id)->first();
        $infoProyecto = Proyecto::where('id', $infoRequisicion->id_proyecto)->first();

        $user = Auth::user();

        // solo id formulador podra editar
        if($user->id != $infoProyecto->id_formulador){
            return ['success' => 3];
        }

        // verificar que este material no este cotizado con una autorizada.

        // obtener todas las cotizaciones id donde esté cotizado
        $lista = CotizacionDetalle::where('id_requidetalle', $request->id)->get();

        $pila = array();
        foreach ($lista as $dd){
            array_push($pila, $dd->cotizacion_id);
        }

        // saber si hay una cotización autorizada. CON ESTE MATERIAL
        // EL ESTADO 1, APROBADA, 2: DENEGADA
        // ES DECIR, MIENTRAS EL ESTADO DE LA COTIZACION ESTA EN DEFAULT Y APROBADO.
        // NO PODRA CANCELAR EL MATERIAL
        $conteo = Cotizacion::whereIn('id', $pila)
            ->whereIn('estado', [0, 1])
            ->count();

        if($conteo > 0){

            // si hay cotización, hoy verificar si orden de compra fue anulada
            $arrayCoti = Cotizacion::whereIn('id', $pila)->get();
            $pilaCoti = array();
            foreach ($arrayCoti as $dd){
                array_push($pilaCoti, $dd->id);
            }

            // ver si existe al menos 1 orden
            if(Orden::whereIn('cotizacion_id', $pilaCoti)->first()){
                $conteoOrden = Orden::whereIn('cotizacion_id', $pilaCoti)
                    ->where('estado', 0) // APROBADA LA ORDEN
                    ->count();

                if($conteoOrden > 0){
                    // material tiene una orden aprobada
                }else{
                    // material tiene una orden denegada

                    // SE PUEDE CANCELAR PORQUE TIENE UNA ORDEN DE COMPRA CANCELADA

                    RequisicionDetalle::where('id', $request->id)->update([
                        'cancelado' => 1,
                    ]);

                    return ['success' => 2];
                }
            }

            // MATERIAL FUE APROBADO O ---- ESPERANDO APROBACIÓN ----, NO SE PUEDE CANCELAR YA
            // solo para mostrar mensaje que la coti fue aprobada y no se puede borrar.
            $infoTipo = Cotizacion::whereIn('id', $pila)->where('estado', 1)->count();
            return ['success' => 1, 'tipo' => $infoTipo];
        }

        // SE PUEDE CANCELAR, PORQUE NINGUNA COTI ESTA APROBADA
        RequisicionDetalle::where('id', $request->id)->update([
            'cancelado' => 1,
        ]);

        return ['success' => 2];
    }

    // borrar una requi detalle, específicamente una Fila, ya que ya no se puede Editar
    public function borrarMaterialRequisicionFila(Request $request){
        DB::beginTransaction();

        try {

            // verificar si hay una cotización con este material

            if(CotizacionDetalle::where('id_requidetalle', $request->id)->first()){
                return ['success' => 1];
            }

            if(RequisicionDetalle::where('id', $request->id)->first()){
                CuentaProyRetenido::where('id_requi_detalle', $request->id)->delete();
                RequisicionDetalle::where('id', $request->id)->delete();
            }

            DB::commit();
            return ['success' => 2];
        }catch(\Throwable $e){
            Log::info('ee' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // borrar toda una requisición
    public function borrarRequisicion(Request $request){

        $regla = array(
            'id' => 'required', // id requisicion
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        if($infoRequisicion = Requisicion::where('id', $request->id)->first()){

            $infoProyecto = Proyecto::where('id', $infoRequisicion->id_proyecto)->first();

            if($infoProyecto->id_estado == 3){
                // pausado

                $texto = "El estado del proyecto es Pausado";
                return ['success' => 3, 'mensaje' => $texto];
            }

            if($infoProyecto->id_estado == 4){
                // finalizado

                $texto = "El estado del proyecto es Finalizado";
                return ['success' => 3, 'mensaje' => $texto];
            }

            // buscar si no hay ningún material ya cotizado
            if(Cotizacion::where('requisicion_id', $request->id)->first()){
                // SE ENCONTRO UN MATERIAL COTIZADO, RETORNAR.
                return ['success' => 1];
            }

            // obtener todos los ID REQUISICION DETALLE CON EL ID REQUISICION
            $arrayID = RequisicionDetalle::where('requisicion_id', $request->id)
                ->select('id')
                ->get();

            // LIBERAR SALDO RETENIDO
            CuentaProyRetenido::whereIn('id_requi_detalle', $arrayID)->delete();

            // borrar listado detalle
            RequisicionDetalle::where('requisicion_id', $request->id)->delete();

            // borrar requisicion
            Requisicion::where('id', $request->id)->delete();

            return ['success' => 2];
        }else{
            return ['success' => 99];
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

    // buscador de material para crear una partida
    public function buscadorMaterialPresupuesto(Request $request){

        if($request->get('query')){
            $query = $request->get('query');
            $data = CatalogoMateriales::where('nombre', 'LIKE', "%{$query}%")->get();

            foreach ($data as $dd){
                if($info = UnidadMedida::where('id', $dd->id_unidadmedida)->first()){
                    $dd->medida = "- " . $info->medida;
                }else{
                    $dd->medida = "";
                }
            }

            $output = '<ul class="dropdown-menu" style="display:block; position:relative;">';
            $tiene = true;
            foreach($data as $row){

                // si solo hay 1 fila, No mostrara el hr, salto de linea
                if(count($data) == 1){
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValorPresupuesto(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->nombre . ' ' .$row->medida .'</a></li>
                ';
                    }
                }

                else{
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValorPresupuesto(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->nombre . ' ' .$row->medida .'</a></li>
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

    // buscador de material para editar una partida
    public function buscadorMaterialPresupuestoEditar(Request $request){

        if($request->get('query')){
            $query = $request->get('query');
            $data = CatalogoMateriales::where('nombre', 'LIKE', "%{$query}%")->get();

            foreach ($data as $dd){
                $info = UnidadMedida::where('id', $dd->id_unidadmedida)->first();
                $dd->medida = $info->medida;
            }

            $output = '<ul class="dropdown-menu" style="display:block; position:relative;">';
            $tiene = true;
            foreach($data as $row){

                // si solo hay 1 fila, No mostrara el hr, salto de linea
                if(count($data) == 1){
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValorPresupuestoEditar(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->nombre . ' - ' .$row->medida .'</a></li>
                ';
                    }
                }

                else{
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValorPresupuestoEditar(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->nombre . ' - ' .$row->medida .'</a></li>
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


    // retorna tabla con las partidas de un proyecto por ID
    public function tablaProyectoListaPresupuesto($id){

        $partida = Partida::where('proyecto_id', $id)
            ->orderBy('id', 'ASC')
            ->get();

        $conteo = 0;
        foreach ($partida as $pp){
            $conteo = $conteo + 1;
            $pp->item = $conteo;

            $sumamonto = 0;

            $arrayPartidaDeta = PartidaDetalle::where('partida_id', $pp->id)->get();

            foreach ($arrayPartidaDeta as $dd){

                $infoMaterial = CatalogoMateriales::where('id', $dd->material_id)->first();

                if($dd->duplicado > 0){
                    $multi = ($dd->cantidad * $infoMaterial->pu) * $dd->duplicado;
                }else{
                    $multi = $dd->cantidad * $infoMaterial->pu;
                }

                $sumamonto += $multi;
            }

            $pp->montopartida = number_format((float)$sumamonto, 2, '.', ',');
        }

        $presuaprobado = 0;
        if($infoProyecto = Proyecto::where('id', $id)->first()) {
            $presuaprobado = $infoProyecto->presu_aprobado;
        }

        return view('backend.admin.proyectos.infoproyectoindividual.tablalistapresupuesto', compact('partida', 'presuaprobado'));
    }

    // registra una nueva partida a un proyecto por ID
    public function agregarPresupuestoPartida(Request $request){

        $rules = array(
            'nombrepartida' => 'required',
            'tipopartida' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $infoProyecto = Proyecto::where('id', $request->id)->first();
            //0: presupuesto en desarrollo
            //1: listo para revision
            //2: aprobado

            if ($infoProyecto->presu_aprobado == 1){
                return ['success' => 1];
            }

            if ($infoProyecto->presu_aprobado == 2){
                return ['success' => 2];
            }

            // usuario logeado
            $user = Auth::user();

            // solo id formulador podra editar
            if($user->id != $infoProyecto->id_formulador){
                return ['success' => 4];
            }

            $r = new Partida();
            $r->proyecto_id = $request->id;
            $r->nombre = $request->nombrepartida;
            $r->cantidadp = $request->cantidadpartida;
            $r->id_tipopartida = $request->tipopartida;
            $r->save();

            $conteoPartida = Partida::where('proyecto_id', $request->id)->count();
            if($conteoPartida == 0){
                $conteoPartida = 1;
            }else{
                $conteoPartida += 1;
            }

            // siempre habra registros

            if($request->cantidad != null) {
                for ($i = 0; $i < count($request->cantidad); $i++) {

                    $rDetalle = new PartidaDetalle();
                    $rDetalle->partida_id = $r->id;
                    $rDetalle->material_id = $request->datainfo[$i];
                    $rDetalle->cantidad = $request->cantidad[$i];
                    $rDetalle->estado = 0;
                    $rDetalle->duplicado = $request->duplicado[$i];
                    $rDetalle->save();
                }
            }

            DB::commit();
            return ['success' => 3, 'contador' => $conteoPartida];

        }catch(\Throwable $e){
            //Log::info('ee' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // obtiene información de la partida de un proyecto
    function informacionPresupuesto(Request $request){
        $rules = array(
            'id' => 'required', // id fila presupuesto (partida)
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){
            return ['success' => 0];
        }

        if($info = Partida::where('id', $request->id)->first()){

            $infoPro = Proyecto::where('id', $info->proyecto_id)->first();

            $presuaprobado = $infoPro->presu_aprobado;

            $detalle = PartidaDetalle::where('partida_id', $request->id)
                ->orderBy('id', 'ASC')
                ->get();

            foreach ($detalle as $dd){

                $datos = CatalogoMateriales::where('id', $dd->material_id)->first();
                if($infoUnidad = UnidadMedida::where('id', $datos->id_unidadmedida)->first()){
                    $dd->descripcion = $datos->nombre . " - " . $infoUnidad->medida;
                }else{
                    $dd->descripcion = $datos->nombre;
                }
            }

            return ['success' => 1, 'info' => $info, 'detalle' => $detalle, 'estado' => $presuaprobado];
        }
        return ['success' => 2];
    }

    // editar la información de una partida
    public function editarPresupuesto(Request $request){

        DB::beginTransaction();

        try {

            $infoPartida = Partida::where('id', $request->idpartida)->first();
            $infoProyecto = Proyecto::where('id', $infoPartida->proyecto_id)->first();

            // Modo revision
            if ($infoProyecto->presu_aprobado == 1) {
                return ['success' => 1];
            }

            // presupuesto aprobado
            if ($infoProyecto->presu_aprobado == 2) {
                return ['success' => 2];
            }

            $user = Auth::user();

            // solo id formulador podra editar
            if($user->id != $infoProyecto->id_formulador){
                return ['success' => 4];
            }

            // actualizar registros requisicion
            Partida::where('id', $request->idpartida)->update([
                'cantidadp' => $request->cantidadpartida,
                'nombre' => $request->nombrepartida,
                'id_tipopartida' => $request->tipopartida
            ]);

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
            PartidaDetalle::where('partida_id', $request->idpartida)
                ->whereNotIn('id', $pila)
                ->delete();

            // actualizar registros
            for ($i = 0; $i < count($request->cantidad); $i++) {
                if($request->idarray[$i] != 0){
                    PartidaDetalle::where('id', $request->idarray[$i])->update([
                        'cantidad' => $request->cantidad[$i],
                        'duplicado' => $request->duplicado[$i],
                    ]);
                }
            }

            // hoy registrar los nuevos registros
            for ($i = 0; $i < count($request->cantidad); $i++) {
                if($request->idarray[$i] == 0){
                    $rDetalle = new PartidaDetalle();
                    $rDetalle->partida_id = $request->idpartida;
                    $rDetalle->cantidad = $request->cantidad[$i];
                    $rDetalle->material_id = $request->datainfo[$i];
                    $rDetalle->estado = 0;
                    $rDetalle->duplicado = $request->duplicado[$i];
                    $rDetalle->save();
                }
            }

            DB::commit();
            return ['success' => 3];

        }catch(\Throwable $e){
            DB::rollback();
            return ['success' => 99];
        }
    }

    // borra una partida con todos los detalle
    public function borrarPresupuesto(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        DB::beginTransaction();

        try {

            $infoPartida = Partida::where('id', $request->id)->first();
            $infoProyecto = Proyecto::where('id', $infoPartida->proyecto_id)->first();

            if($infoProyecto->presu_aprobado == 1){ // en revisión
                return ['success' => 1];
            }

            if($infoProyecto->presu_aprobado == 2){ // aprobado
                return ['success' => 2];
            }

            $user = Auth::user();

            // solo id formulador podra editar
            if($user->id != $infoProyecto->id_formulador){
                return ['success' => 4];
            }

            // borrar listado
            PartidaDetalle::where('partida_id', $request->id)->delete();
            Partida::where('id', $request->id)->delete();

            $conteoPartida = Partida::where('proyecto_id', $infoPartida->proyecto_id)->count();
            if($conteoPartida == 0){
                $conteoPartida = 1;
            }else{
                $conteoPartida += 1;
            }

            DB::commit();
            return ['success' => 3, 'contador' => $conteoPartida];

        }catch(\Throwable $e){
            Log::info('error: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // verifica si partida mano de obra existe
    public function verificarPartidaManoObra(Request $request){

        // TIPO PARTIDA 3: Mano de obra (Por Administración)
        if(Partida::where('proyecto_id', $request->id)->where('id_tipopartida', 2)->first()){
            return ['success' => 1];
        }
        return ['success' => 2];
    }


    // retorna vista para MODAL, aquí se visualiza el presupuesto. aquí se visualiza botón para aprobar el presupuesto
    public function informacionPresupuestoParaAprobacion($id){

        // obtener todas los presupuesto por tipo partida
        // 1- Materiales
        // 2- Mano de obra (Por Administración)
        // 3- Alquiler de Maquinaria
        // 4- Transporte de Concreto Fresco

        // MATERIALES, ALQUILER DE MAQUINARIA, TRANSPORTE DE CONCRETO FRESCO
        $partida1 = Partida::where('proyecto_id', $id)
            ->whereIn('id_tipopartida', [1, 3, 4])
            ->orderBy('id', 'ASC')
            ->get();

        $infoPro = Proyecto::where('id', $id)->first();

        if ($infoFuenteR = FuenteRecursos::where('id', $infoPro->id_fuenter)->first()) {
            $fuenter = $infoFuenteR->nombre;
        } else {
            $fuenter = "";
        }

        $resultsBloque = array();
        $index = 0;
        $resultsBloque3 = array();
        $index3 = 0;

        // Fechas
        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $fecha = Carbon::parse(Carbon::now());
        $anio = Carbon::now()->format('Y');
        $mes = $meses[($fecha->format('n')) - 1] . " del " . $anio;

        $item = 0;
        $sumaMateriales = 0;

        foreach ($partida1 as $secciones) {
            array_push($resultsBloque, $secciones);
            $item = $item + 1;
            $secciones->item = $item;

            $detalle1 = PartidaDetalle::where('partida_id', $secciones->id)->get();

            $total = 0;

            foreach ($detalle1 as $lista) {

                $infomaterial = CatalogoMateriales::where('id', $lista->material_id)->first();

                $lista->objespecifico = $infomaterial->id_objespecifico;

                $medida = '';
                if($infomedida = UnidadMedida::where('id', $infomaterial->id_unidadmedida)->first()){
                    $medida = $infomedida->medida;
                }

                $lista->medida = $medida;

                if ($lista->duplicado > 0) {
                    $multi = ($lista->cantidad * $infomaterial->pu) * $lista->duplicado;
                    $lista->material = $infomaterial->nombre . " (" . $lista->duplicado . ")";
                } else {
                    $multi = $lista->cantidad * $infomaterial->pu;
                    $lista->material = $infomaterial->nombre;
                }

                $lista->cantidad = number_format((float)$lista->cantidad, 2, '.', ',');
                $lista->pu = "$" . number_format((float)$infomaterial->pu, 2, '.', ',');
                $lista->subtotal = "$" . number_format((float)$multi, 2, '.', ',');

                // se sumara solo materiales
                if($secciones->id_tipopartida == 1){
                    $sumaMateriales = $sumaMateriales + $multi;
                }

                $total = $total + $multi;
            }

            $secciones->total = "$" . number_format((float)$total, 2, '.', ',');

            $resultsBloque[$index]->bloque1 = $detalle1;
            $index++;
        }

        // 2- MANO DE OBRA POR ADMINISTRACION

        $manoobra = Partida::where('proyecto_id', $id)
            ->where('id_tipopartida', 2) // mano de obra
            ->orderBy('id', 'ASC')
            ->get();

        $totalManoObra = 0;

        foreach ($manoobra as $secciones3){
            array_push($resultsBloque3, $secciones3);
            $item = $item + 1;
            $secciones3->item = $item;


            $detalle3 = PartidaDetalle::where('partida_id', $secciones3->id)->get();

            $total3 = 0;

            foreach ($detalle3 as $lista){

                $infomaterial = CatalogoMateriales::where('id', $lista->material_id)->first();

                $objespecifico = null;
                if($infoobjeto = ObjEspecifico::where('id', $infomaterial->id_objespecifico)->first()){
                    $objespecifico = $infoobjeto->codigo;
                }

                $lista->objespecifico = $objespecifico;

                $medida = '';
                if($infomedida = UnidadMedida::where('id', $infomaterial->id_unidadmedida)->first()){
                    $medida = $infomedida->medida;
                }

                $lista->medida = $medida;

                if($lista->duplicado > 0){
                    $lista->material = $infomaterial->nombre . " (" . $lista->duplicado . ")";
                }else{
                    $lista->material = $infomaterial->nombre;
                }

                $multi = $lista->cantidad * $infomaterial->pu;
                if($lista->duplicado != 0){
                    $multi = $multi * $lista->duplicado;
                }

                $lista->cantidad = number_format((float)$lista->cantidad, 2, '.', ',');
                $lista->pu = "$" . number_format((float)$infomaterial->pu, 2, '.', ',');
                $lista->subtotal = "$" . number_format((float)$multi, 2, '.', ',');

                $totalManoObra = $totalManoObra + $multi;
                $total3 = $total3 + $multi;
            }

            $secciones3->total = "$" . number_format((float)$total3, 2, '.', ',');

            $resultsBloque3[$index3]->bloque3 = $detalle3;
            $index3++;
        }


        // 3- ALQUILER DE MAQUINARIA

        $alquilerMaquinaria = Partida::where('proyecto_id', $id)
            ->where('id_tipopartida', 3)
            ->get();

        $totalAlquilerMaquinaria = 0;

        foreach ($alquilerMaquinaria as $secciones3){

            $detalle4 = PartidaDetalle::where('partida_id', $secciones3->id)->get();

            foreach ($detalle4 as $lista){

                $infomaterial = CatalogoMateriales::where('id', $lista->material_id)->first();
                $lista->material = $infomaterial->nombre;
                $multi = $lista->cantidad * $infomaterial->pu;
                if ($lista->duplicado > 0) {
                    $multi = $multi * $lista->duplicado;
                }

                $totalAlquilerMaquinaria += $multi;
            }
        }


        // 4- TRANSPORTE CONCRETO FRESCO

        $trasportePesado = Partida::where('proyecto_id', $id)
            ->where('id_tipopartida', 4)
            ->get();

        $totalTransportePesado = 0;

        foreach ($trasportePesado as $secciones3){

            $detalle4 = PartidaDetalle::where('partida_id', $secciones3->id)->get();

            foreach ($detalle4 as $lista){

                $infomaterial = CatalogoMateriales::where('id', $lista->material_id)->first();
                $lista->material = $infomaterial->nombre;
                $multi = $lista->cantidad * $infomaterial->pu;
                if ($lista->duplicado > 0) {
                    $multi = $multi * $lista->duplicado;
                }

                $totalTransportePesado += $multi;
            }
        }

        $afp =  ($totalManoObra * 7.75) / 100;
        $isss = ($totalManoObra * 7.5) / 100;
        $insaforp = ($totalManoObra * 1) / 100;

        $totalDescuento = $afp + $isss + $insaforp;

        $afp = "$" . number_format((float)$afp, 2, '.', ',');
        $isss = "$" . number_format((float)$isss, 2, '.', ',');
        $insaforp = "$" . number_format((float)$insaforp, 2, '.', ',');

        $informacionGeneral = InformacionGeneral::where('id', 1)->first();

        // obtener porcentaje actual
        if($infoPro->presu_aprobado == 2){
            $porcientoHerramienta = $infoPro->porcentaje_herra_fijo;
        }else{
            $porcientoHerramienta = $informacionGeneral->porcentaje_herramienta;
        }


        $herramienta2Porciento = ($sumaMateriales * $porcientoHerramienta) / 100;

        // subtotal del presupuesto partida
        $subtotalPartida = ($sumaMateriales + $herramienta2Porciento + $totalManoObra + $totalDescuento
            + $totalAlquilerMaquinaria + $totalTransportePesado);

        // obtener el imprevisto actual
        if($infoPro->presu_aprobado == 2){
            $imprevistoActual = $infoPro->imprevisto_fijo;
        }else{
            $imprevistoActual = $informacionGeneral->imprevisto_modificable;
        }

        // imprevisto obtenido del proyecto
        $imprevisto = ($subtotalPartida * $imprevistoActual) / 100;

        // total de la partida final
        $totalPartidaFinal = $subtotalPartida + $imprevisto;

        $totalDescuento = "$" . number_format((float)$totalDescuento, 2, '.', ',');
        $sumaMateriales = "$" . number_format((float)$sumaMateriales, 2, '.', ',');
        $herramienta2Porciento = "$" . number_format((float)$herramienta2Porciento, 2, '.', ',');
        $totalManoObra = "$" . number_format((float)$totalManoObra, 2, '.', ',');

        $totalAlquilerMaquinaria = "$" . number_format((float)$totalAlquilerMaquinaria, 2, '.', ',');
        $totalTransportePesado = "$" . number_format((float)$totalTransportePesado, 2, '.', ',');
        $subtotalPartida = "$" . number_format((float)$subtotalPartida, 2, '.', ',');
        $imprevisto = "$" . number_format((float)$imprevisto, 2, '.', ',');
        $totalPartidaFinal = "$" . number_format((float)$totalPartidaFinal, 2, '.', ',');

        $preAprobado = $infoPro->presu_aprobado;

        return view('backend.admin.proyectos.modal.pdfpresupuesto', compact('partida1', 'porcientoHerramienta',
            'manoobra', 'mes', 'fuenter', 'afp', 'isss', 'insaforp', 'totalDescuento',
            'sumaMateriales', 'herramienta2Porciento', 'totalManoObra', 'totalAlquilerMaquinaria',
            'totalTransportePesado', 'subtotalPartida', 'imprevisto', 'totalPartidaFinal', 'preAprobado', 'imprevistoActual'));
    }

    // petición para aprobar el presupuesto y guardar las cuentas proyecto
    public function aprobarPresupuesto(Request $request){

        // id   (ID PROYECTO)
        // idbolson

        if($infoProyecto = Proyecto::where('id', $request->id)->first()){

            DB::beginTransaction();
            try {

                if ($infoProyecto->presu_aprobado == 0) {
                    // presupuesto cambio estado y está en desarrollo
                    return ['success' => 1];
                }

                if ($infoProyecto->presu_aprobado == 2) {
                    // presupuesto ya aprobado y no hacer nada
                    return ['success' => 2];
                }

                // MANO DE OBRA POR ADMINISTRACION, APARTE PARA CALCULAR ISSS, AFP, INSAFORP
                $manoobra = Partida::where('proyecto_id', $request->id)
                    ->where('id_tipopartida', 2)
                    ->orderBy('id', 'ASC')
                    ->get();

                // el presupuesto no tiene APORTE MANO DE OBRA (POR ADMINISTRACIÓN)
                if (sizeof($manoobra) <= 0) {
                    return ['success' => 3];
                }

                if(!Bolson::where('id', $request->idbolson)->first()){
                    // solo mostrar error, ya que no podría enviar bolsón sin ID
                    return ['success' => 99];
                }


                //-------------------------------------------------------------
                // OBTENER MONTO INICIAL DE PRESUPUESTO PROYECTO

                // 1- Materiales
                // 2- Mano de obra (Por Administración)
                // 3- Alquiler de Maquinaria
                // 4- Transporte de Concreto Fresco

                $partida1 = Partida::where('proyecto_id', $infoProyecto->id)
                    ->orderBy('id', 'ASC')
                    ->get();

                $pilaIdPartidas = array();

                foreach ($partida1 as $pp){
                    array_push($pilaIdPartidas, $pp->id);
                }

                $sumaMateriales = 0;

                // UNICAMENTE PARTIDA MATERIALES
                foreach ($partida1 as $secciones) {
                    $detalle1 = PartidaDetalle::where('partida_id', $secciones->id)->get();

                    foreach ($detalle1 as $lista) {

                        if($secciones->id_tipopartida == 1){

                            $infomaterial = CatalogoMateriales::where('id', $lista->material_id)->first();

                            if ($lista->duplicado > 0) {
                                $multi = ($lista->cantidad * $infomaterial->pu) * $lista->duplicado;
                            } else {
                                $multi = $lista->cantidad * $infomaterial->pu;
                            }

                            // se sumara solo materiales

                            $sumaMateriales = $sumaMateriales + $multi; // 17,237.52
                        }
                    }
                }

                // 2- MANO DE OBRA POR ADMINISTRACION

                $manoobra = Partida::where('proyecto_id', $infoProyecto->id)
                    ->where('id_tipopartida', 2)
                    ->orderBy('id', 'ASC')
                    ->get();

                $totalManoObra = 0;

                foreach ($manoobra as $secciones3) {

                    $detalle3 = PartidaDetalle::where('partida_id', $secciones3->id)->get();

                    foreach ($detalle3 as $lista) {

                        $infomaterial = CatalogoMateriales::where('id', $lista->material_id)->first();

                        if ($lista->duplicado != 0) {
                            $lista->material = $infomaterial->nombre . " (" . $lista->duplicado . ")";
                        } else {
                            $lista->material = $infomaterial->nombre;
                        }

                        $multi = $lista->cantidad * $infomaterial->pu;
                        if ($lista->duplicado > 0) {
                            $multi = $multi * $lista->duplicado;
                        }

                        $totalManoObra = $totalManoObra + $multi;
                    }
                }

                // 3- ALQUILER DE MAQUINARIA

                $alquilerMaquinaria = Partida::where('proyecto_id', $infoProyecto->id)
                    ->where('id_tipopartida', 3)
                    ->get();

                $totalAlquilerMaquinaria = 0;

                foreach ($alquilerMaquinaria as $secciones3) {

                    $detalle4 = PartidaDetalle::where('partida_id', $secciones3->id)->get();

                    foreach ($detalle4 as $lista) {

                        $infomaterial = CatalogoMateriales::where('id', $lista->material_id)->first();
                        $lista->material = $infomaterial->nombre;
                        $multi = $lista->cantidad * $infomaterial->pu;
                        if ($lista->duplicado > 0) {
                            $multi = $multi * $lista->duplicado;
                        }

                        $totalAlquilerMaquinaria += $multi;
                    }
                }

                // 4- TRANSPORTE CONCRETO FRESCO

                $trasportePesado = Partida::where('proyecto_id', $infoProyecto->id)
                    ->where('id_tipopartida', 4)
                    ->get();

                $totalTransportePesado = 0;

                foreach ($trasportePesado as $secciones3) {

                    $detalle4 = PartidaDetalle::where('partida_id', $secciones3->id)->get();

                    foreach ($detalle4 as $lista) {

                        $infomaterial = CatalogoMateriales::where('id', $lista->material_id)->first();
                        $lista->material = $infomaterial->nombre;
                        $multi = $lista->cantidad * $infomaterial->pu;
                        if ($lista->duplicado > 0) {
                            $multi = $multi * $lista->duplicado;
                        }

                        $totalTransportePesado += $multi;
                    }
                }

                $afp = ($totalManoObra * 7.75) / 100;
                $isss = ($totalManoObra * 7.5) / 100;
                $insaforp = ($totalManoObra * 1) / 100;

                $informacionGeneral = InformacionGeneral::where('id', 1)->first();

                $totalDescuento = ($afp + $isss + $insaforp);

                $herramientaXPorciento = ($sumaMateriales * $informacionGeneral->porcentaje_herramienta) / 100;

                // subtotal del presupuesto partida
                $subtotalPartida = ($sumaMateriales + $herramientaXPorciento + $totalManoObra + $totalDescuento
                    + $totalAlquilerMaquinaria + $totalTransportePesado);

                // obtener el imprevisto actual
                $imprevistoActual = $informacionGeneral->imprevisto_modificable;

                // imprevisto obtenido del proyecto
                $imprevisto = ($subtotalPartida * $imprevistoActual) / 100;

                $montoPartidaFinal = $subtotalPartida + $imprevisto;

                // verificar que haya saldo en bolsón

                // - verificar dinero restado a bolsón por presupuesto de proyecto
                // - verificar cuando proyecto finaliza y devuelve dinero a bolsón
                // hasta de las partidas adicionales

                //*****************

                // obtener cuanto dinero queda en bolsón, ya que muchos proyectos asignados a un bolsón

                $proyectoMontoBolson = Proyecto::where('id_bolson', $request->idbolson)->sum('monto');

                //*****************

                // obtener dinero descontado cuando una partida adicional está aprobada

                $partidaAdicionalMonto = PartidaAdicionalContenedor::where('id_proyecto', $request->id)
                    ->where('estado', 2) // partidas aprobadas
                    ->sum('monto_aprobado');

                //*****************

                // obtener monto de los proyectos que han finalizado

                $proyectoFinalizadoMonto = Proyecto::where('id_bolson', $request->idbolson)
                    ->where('id_estado', 4)
                    ->sum('monto_finalizado');

                //*****************

                // dinero inicial de bolsón

                $montoBolsonInicial = Bolson::where('id', $request->idbolson)->sum('monto_inicial');

                // montoPartidaFinal: es el monto de las partida actual que viene para ser aprobada
                // proyectoMontoBolson: es el monto de las partidas aprobadas de todos los proyectos a bolsón
                // partidaAdicionalMonto: es el monto de las partidas adicionales aprobadas
                // proyectoFinalizadoMonto: es el monto sobrante de un proyecto cuando se finaliza

                // restar a monto inicial y después sumarle
                $restaBolsonInicial = $montoBolsonInicial - ($proyectoMontoBolson + $partidaAdicionalMonto);

                // BOLSÓN ACTUAL LO QUE HAY $
                $restaBolsonInicial += $proyectoFinalizadoMonto;

                // me queda x de bolsón, no debe ser menor a lo solicitado
                if($this->redondear_dos_decimal($restaBolsonInicial) < $this->redondear_dos_decimal($montoPartidaFinal)){

                    $restaBolsonInicial = number_format((float)$restaBolsonInicial, 2, '.', ',');
                    $montoPartida = number_format((float)$montoPartidaFinal, 2, '.', ',');

                    return ['success' => 4, 'actual' => $restaBolsonInicial, 'requerido' => $montoPartida];
                }else{

                    // si hay fondos en bolsón

                    //-------------------------------------------------------------

                    //*************** OBTENER SALDO DE CADA CÓDIGO **********************/

                    // ** MATERIALES UNICAMENTE

                    // agrupando para obtener solo los ID de objetos específicos.
                    $detalles = DB::table('partida_detalle AS pd')
                        ->join('materiales AS m', 'pd.material_id', '=', 'm.id')
                        ->select('m.id_objespecifico')
                        ->whereIn('pd.partida_id', $pilaIdPartidas)
                        ->groupBy('m.id_objespecifico')
                        ->get();

                    // recorrer lista de objetos específicos
                    foreach ($detalles as $det){

                        // obtener lista de materiales
                        $info = DB::table('partida_detalle AS pd')
                            ->join('materiales AS m', 'pd.material_id', '=', 'm.id')
                            ->join('obj_especifico AS obj', 'm.id_objespecifico', '=', 'obj.id')
                            ->select('m.id_objespecifico', 'pd.cantidad', 'pd.duplicado', 'm.pu', 'obj.codigo')
                            ->where('m.id_objespecifico', $det->id_objespecifico)
                            ->get();

                        $suma = 0;

                        // obtener sumatoria de los materiales
                        foreach ($info as $dato) {
                            if ($dato->duplicado > 0) {
                                $suma = $suma + (($dato->cantidad * $dato->duplicado) * $dato->pu);
                            } else {
                                $suma = $suma + ($dato->cantidad * $dato->pu);
                            }
                        }

                        // GUARDAR REGISTRO
                        $presu = new CuentaProy();
                        $presu->proyecto_id = $request->id;
                        $presu->objespeci_id = $det->id_objespecifico;
                        $presu->saldo_inicial = $suma;
                        $presu->cuentaproy_part_adicional = null;
                        $presu->save();
                    }


                    //** VOY A COMPROBAR QUE NO SE REPITA NINGÚN OBJ ESPECIFICO */

                    // HERRAMIENTAS 2% E IMPREVISTO PARA CÓDIGOS FIJOS

                    if($cuentapr = CuentaProy::where('proyecto_id', $request->id)
                    ->where('objespeci_id', 37)->first()){

                        // el 0 debera ser la herramienta del 2%
                        $sumaher = $cuentapr->saldo_inicial + $herramientaXPorciento;

                        CuentaProy::where('id', $cuentapr->id)->update([
                            'saldo_inicial' => $sumaher
                        ]);
                    }else{
                        $presu = new CuentaProy();
                        $presu->proyecto_id = $request->id;
                        $presu->objespeci_id = 37; // HERRAMIENTAS, REPUESTOS Y ACCESORIOS
                        $presu->saldo_inicial = $herramientaXPorciento;
                        $presu->cuentaproy_part_adicional = null;
                        $presu->save();
                    }

                    // IMPREVISTO DEL X PORCIENTO

                    if($cuentapr = CuentaProy::where('proyecto_id', $request->id)
                        ->where('objespeci_id', 97)->first()){

                        // el 0 debera ser el imprevisto del x porciento
                        $sumaher = $cuentapr->saldo_inicial + $imprevisto;

                        CuentaProy::where('id', $cuentapr->id)->update([
                            'saldo_inicial' => $sumaher
                        ]);
                    }else{
                        $presu = new CuentaProy();
                        $presu->proyecto_id = $request->id;
                        $presu->objespeci_id = 97; // HERRAMIENTAS, REPUESTOS Y ACCESORIOS
                        $presu->saldo_inicial = $imprevisto;
                        $presu->cuentaproy_part_adicional = null;
                        $presu->save();
                    }

                    // APORTE PATRONAL DEL ISSS AFP INSAFORP

                    if($cuentapr = CuentaProy::where('proyecto_id', $request->id)
                        ->where('objespeci_id', 10)->first()){

                        $sumaher = $cuentapr->saldo_inicial + $imprevisto;

                        CuentaProy::where('id', $cuentapr->id)->update([
                            'saldo_inicial' => $sumaher
                        ]);
                    }else{
                        $presu = new CuentaProy();
                        $presu->proyecto_id = $request->id;
                        $presu->objespeci_id = 10; // código: 51402
                        $presu->saldo_inicial = ($isss + $insaforp);
                        $presu->cuentaproy_part_adicional = null;
                        $presu->save();
                    }

                    // APORTE AFP
                    if($cuentapr = CuentaProy::where('proyecto_id', $request->id)
                        ->where('objespeci_id', 13)->first()){

                        $sumaher = $cuentapr->saldo_inicial + $imprevisto;

                        CuentaProy::where('id', $cuentapr->id)->update([
                            'saldo_inicial' => $sumaher
                        ]);

                    }else{
                        $presu = new CuentaProy();
                        $presu->proyecto_id = $request->id;
                        $presu->objespeci_id = 13; // código: 51502
                        $presu->saldo_inicial = ($afp);
                        $presu->cuentaproy_part_adicional = null;
                        $presu->save();
                    }

                    // pasar a modo aprobado
                    Proyecto::where('id', $request->id)->update([
                        'fecha_aprobado' => Carbon::now('America/El_Salvador'),
                        'presu_aprobado' => 2,
                        'monto' => $montoPartidaFinal,
                        'id_bolson' => $request->idbolson,
                        'imprevisto_fijo' => $informacionGeneral->imprevisto_modificable,
                        'porcentaje_herra_fijo' => $informacionGeneral->porcentaje_herramienta
                    ]);

                    DB::commit();
                    return ['success' => 2];
                }

            }catch(\Throwable $e){
                Log::info('error: ' . $e);
                DB::rollback();
                return ['success' => 99];
            }
        }

        return ['success' => 99];
    }

    // mostrara vista para MODAL, donde esta el saldo inicial, el restante y el retenido.
    public function infoTablaSaldoProyecto($id){

        // presupuesto
        $presupuesto = DB::table('cuentaproy AS p')
            ->join('obj_especifico AS obj', 'p.objespeci_id', '=', 'obj.id')
            ->select('obj.nombre', 'obj.id AS idcodigo', 'obj.codigo', 'p.id', 'p.saldo_inicial')
            ->where('p.proyecto_id', $id)
            ->orderBy('obj.codigo', 'ASC')
            ->get();

        foreach ($presupuesto as $pp){

            // CÁLCULOS

            $totalRestante = 0;
            $totalRetenido = 0;

            // movimiento de cuentas SUBE
            $infoMoviCuentaProySube = MoviCuentaProy::where('id_cuentaproy_sube', $pp->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            // movimiento de cuentas BAJA
            $infoMoviCuentaProyBaja = MoviCuentaProy::where('id_cuentaproy_baja', $pp->id)
                ->where('autorizado', 1) // autorizado por presupuesto
                ->sum('dinero');

            $totalMoviCuenta = $infoMoviCuentaProySube - $infoMoviCuentaProyBaja;

            // obtener todas las salidas de material
            $arrayRestante = DB::table('cuentaproy_restante AS pd')
                ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero')
                ->where('pd.id_cuentaproy', $pp->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRestante as $dd){
                $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
            }

            $infoCuentaPartida = CuentaproyPartidaAdicional::where('id_proyecto', $id)->get();

            $sumaPartidaAdicional = 0;
            foreach ($infoCuentaPartida as $dd){
                if($pp->idcodigo == $dd->objespeci_id){
                    $sumaPartidaAdicional += $dd->monto;
                }
            }

            // información de saldos retenidos
            $arrayRetenido = DB::table('cuentaproy_retenido AS psr')
                ->join('requisicion_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('psr.id_cuentaproy', $pp->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($arrayRetenido as $dd){
                $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
            }

            // sumando partidas adicionales que coincidan con el obj específico + saldo inicial
            $sumaPartidaAdicional += $pp->saldo_inicial;

            // aquí se obtiene el Saldo Restante del código
            $totalRestanteSaldo = $totalMoviCuenta + ($sumaPartidaAdicional - $totalRestante);

            //$totalCalculado = $totalRestanteSaldo - $totalRetenido;

            $pp->saldo_inicial = number_format((float)$pp->saldo_inicial, 2, '.', ',');
            $pp->saldo_restante = number_format((float)$totalRestanteSaldo, 2, '.', ',');
            $pp->total_retenido = number_format((float)$totalRetenido, 2, '.', ',');

            $pp->saldoRestante = $this->redondear_dos_decimal($totalRestanteSaldo);
            $pp->totalRetenido = $this->redondear_dos_decimal($totalRetenido);
        }

        return view('backend.admin.proyectos.modal.modalsaldo', compact('presupuesto'));
    }


    // petición para cambiar estado de presupuesto, asi para que el encargado de Presupuesto lo apruebe
    public function cambiarEstadoPresupuesto(Request $request){


        // SE REQUIRE PARTIDA MANO DE OBRA POR ADMINISTRACION
        if(!Partida::where('proyecto_id', $request->id)
            ->where('id_tipopartida', 3)
            ->first()){
            return ['success' => 1];
        }

        // el presupuesto ya tiene estado 2: PRESUPUESTO APROBADO
        if(Proyecto::where('id', $request->id)
            ->where('presu_aprobado', 2)
            ->first()){
            return ['success' => 2];
        }

        // cambiar estado al 0 o 1
        Proyecto::where('id', $request->id)->update([
            'presu_aprobado' => $request->estado,
        ]);

        return ['success' => 3];
    }

    // buscar materiales de un determinado presupuesto de proyecto + partida adicional
    public function buscadorMaterialRequisicion(Request $request){

        if($request->get('query')){
            $query = $request->get('query');


            // ************* OBTENER TODOS LOS ID MATERIALES DE PRESUPUESTO *************

            //---------- PARTIDA PRESUPUESTO ----------
            $arrayPartidas = Partida::where('proyecto_id', $request->idpro)
                ->select('id')
                ->get();

            $arrayIdMateriales = DB::table('partida_detalle AS pd')
                ->join('materiales AS m', 'pd.material_id', '=', 'm.id')
                ->select('m.id')
                ->whereIn('pd.partida_id', $arrayPartidas)
                ->where('m.nombre', 'LIKE', "%{$query}%")
                ->groupBy('m.id')
                ->get();

            // ************* OBTENER TODOS LOS ID MATERIALES DE PARTIDA ADICIONAL *************

            $infoContenedor = PartidaAdicionalContenedor::where('id_proyecto', $request->idpro)
                ->select('id')
                ->where('estado', 2) // solo aprobados
                ->get();

            $arrayPartidaAdicional = PartidaAdicional::whereIn('id_partidaadic_conte', $infoContenedor)
                ->select('id')
                ->get();

            // array de materiales materiales adicionales
            $arrayIdMatePartidaAdic = DB::table('partida_adicional_detalle AS pd')
                ->join('materiales AS m', 'pd.id_material', '=', 'm.id')
                ->select('m.id')
                ->whereIn('pd.id_partida_adicional', $arrayPartidaAdicional)
                ->where('m.nombre', 'LIKE', "%{$query}%")
                ->groupBy('m.id')
                ->take(40)
                ->get();

            $arrayUnido = $arrayIdMateriales->merge($arrayIdMatePartidaAdic);

            $pilaIdMateriales = array();

            foreach ($arrayUnido as $dd){
                array_push($pilaIdMateriales, $dd->id);
            }

            // BÚSQUEDA DE INFORMACIÓN DE MATERIALES
            $listaMateriales = CatalogoMateriales::whereIn('id', $pilaIdMateriales)
                ->select('id')
                ->groupBy('id')
                ->get();

            // BÚSQUEDA

            $output = '<ul class="dropdown-menu" style="display:block; position:relative;">';
            $tiene = true;
            foreach($listaMateriales as $row){

                $infoMaterial = CatalogoMateriales::where('id', $row->id)->first();

                $infoUnidad = UnidadMedida::where('id', $infoMaterial->id_unidadmedida)->first();
                $row->unido = $infoMaterial->nombre . ' - ' . $infoUnidad->medida;

                // si solo hay 1 fila, No mostrara el hr, salto de linea
                if(count($arrayIdMateriales) == 1){
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValorRequisicion(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->unido.'</a></li>
                ';
                    }
                }

                else{
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValorRequisicion(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->unido.'</a></li>
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


    // busca materiales para crear una requisición, solo muestra materiales asignado a presupuesto de proyecto
    public function verCatalogoMaterialRequisicion($id){

        $listaPartidas = Partida::where('proyecto_id', $id)->get();
        $pilaIdPartidas = array();

        foreach ($listaPartidas as $dd){
            array_push($pilaIdPartidas, $dd->id);
        }

        // presupuesto
        $presupuesto = DB::table('partida_detalle AS p')
            ->join('materiales AS m', 'p.material_id', '=', 'm.id')
            ->select('m.nombre', 'm.id', 'm.id_objespecifico', 'm.id_unidadmedida')
            ->whereIn('p.partida_id', $pilaIdPartidas)
            ->groupBy('m.id')
            ->get();

        foreach ($presupuesto as $pp){

            $infoMedida = UnidadMedida::where('id', $pp->id_unidadmedida)->first();
            $pp->medida = $infoMedida->medida;

            $infoObjeto = ObjEspecifico::where('id', $pp->id_objespecifico)->first();

            $pp->objcodigo = $infoObjeto->codigo;
            $pp->objnombre = $infoObjeto->nombre;

            $infoCatalogo = CatalogoMateriales::where('id', $pp->id)->first();
            $pp->actual = '$' . number_format((float)$infoCatalogo->pu, 2, '.', ',');
        }

        $titulo = "Materiales del Presupuesto";

        return view('backend.admin.proyectos.modal.modalcatalogomaterial', compact('presupuesto', 'titulo'));
    }

    // mostrar modal de todos los materiales de partidas adicionales
    public function verCatalogoMaterialPartidaAdicional($id){

        $infoContenedor = PartidaAdicionalContenedor::where('id_proyecto', $id)
            ->where('estado', 2) // solo aprobados
            ->get();

        $pilaIdContenedor = array();

        foreach ($infoContenedor as $dd){
            array_push($pilaIdContenedor, $dd->id);
        }

        $listaPartidas = PartidaAdicional::whereIn('id_partidaadic_conte', $pilaIdContenedor)->get();
        $pilaIdPartida = array();

        foreach ($listaPartidas as $dd){
            array_push($pilaIdPartida, $dd->id);
        }

        // presupuesto
        $presupuesto = DB::table('partida_adicional_detalle AS p')
            ->join('materiales AS m', 'p.id_material', '=', 'm.id')
            ->select('m.nombre', 'm.id', 'm.id_objespecifico', 'm.id_unidadmedida')
            ->whereIn('p.id_partida_adicional', $pilaIdPartida)
            ->groupBy('m.id')
            ->get();

        foreach ($presupuesto as $pp){

            $infoMedida = UnidadMedida::where('id', $pp->id_unidadmedida)->first();
            $pp->medida = $infoMedida->medida;

            $infoObjeto = ObjEspecifico::where('id', $pp->id_objespecifico)->first();

            $pp->objcodigo = $infoObjeto->codigo;
            $pp->objnombre = $infoObjeto->nombre;

            $infoCatalogo = CatalogoMateriales::where('id', $pp->id)->first();
            $pp->actual = number_format((float)$infoCatalogo->pu, 2, '.', ',');
        }

        $titulo = "Materiales del Presupuesto Partida Adicionalx";

        return view('backend.admin.proyectos.modal.modalcatalogomaterial', compact('presupuesto', 'titulo'));
    }


    // información de un estado de proyecto
    public function informacionEstadoProyecto(Request $request){

        $arrayEstado = EstadoProyecto::orderBy('id', 'ASC')->get();
        $infoProyecto = Proyecto::where('id', $request->id)->first();

        $monto = number_format((float)$infoProyecto->monto, 2, '.', ',');

        $nombolson = '';
        if($infoBolson = Bolson::where('id', $infoProyecto->id_bolson)->first()){
            $nombolson = $infoBolson->nombre;
        }

        return ['success' => 1, 'info' => $infoProyecto, 'arrayEstado' => $arrayEstado, 'monto' => $monto,
            'nombolson' => $nombolson];
    }

    // editar estado de un proyecto
    public function editarEstadoProyecto(Request $request){

        $regla = array(
            'id' => 'required',
            'idestado' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        DB::beginTransaction();

        try {

            $infoProyecto = Proyecto::where('id', $request->id)->first();

            // ESTADOS
            // 1: priorizado: solo si presu no esta aprobado se puede volver a colocar
            // 2: iniciado: // siempre se puede volver a colocar
            // 3: en pausa: // siempre se puede volver a colocar
            // 4: finalizado: // solo 1 vez se puede colocar en el proyecto

            // cualquier intento de modificar si presupuesto esta finalizado, retornar error;
            if($infoProyecto->idestado == 4){
                $mensaje = "El Proyecto fue Finalizado";
                return ['success' => 1, 'titulo' => 'No Actualizado', 'mensaje' => $mensaje];
            }

            // PRIORIZADO
            if($request->idestado == 1){

                // puede cambiarse se está en modo desarrollo, el modo revisión
                // es solo por evitar alguna cosa rara
                if($infoProyecto->presu_aprobado == 0 || $infoProyecto->presu_aprobado == 1){

                    // puede actualizarse, a PRIORIZADO
                    Proyecto::where('id', $request->id)->update([
                        'id_estado' => 1,
                    ]);

                    DB::commit();
                    return ['success' => 2];
                }else{
                    // presupuesto ya aprobado, no puede volver a colocar estado priorizado
                    $mensaje = "El Presupuesto ya esta Aprobado";
                    return ['success' => 3, 'titulo' => 'No Actualizado', 'mensaje' => $mensaje];
                }
            }

            // INICIADO: puede crear requerimientos y necesita bolsón
            else if($request->idestado == 2){

                if($infoProyecto->presu_aprobado == 0 || $infoProyecto->presu_aprobado == 1){
                    // presupuesto no aprobado aun
                    $mensaje = "El Presupuesto No esta Aprobado";
                    return ['success' => 3, 'titulo' => 'No Actualizado', 'mensaje' => $mensaje];
                }else{
                    // presupuesto ya aprobado. Y verificar que haya bolsón.

                    if($infoProyecto->id_bolson == null){

                        // bolsón no encontrado
                        $mensaje = "Verificar Proyecto, no se encuentra Bolsón asignado";
                        return ['success' => 3, 'titulo' => 'No Actualizado', 'mensaje' => $mensaje];

                    }else{
                        // bolsón encontrado, se puede crear requerimientos

                        // puede actualizarse, a INICIADO
                        Proyecto::where('id', $request->id)->update([
                            'id_estado' => 2,
                        ]);

                        DB::commit();
                        return ['success' => 4];
                    }
                }
            }

            // EN PAUSA: no se puede crear requerimientos.
            else if($request->idestado == 3){

                if($infoProyecto->presu_aprobado == 0){
                    // presupuesto no aprobado aun
                    $mensaje = "El Presupuesto No esta Aprobado";
                    return ['success' => 3, 'titulo' => 'No Actualizado', 'mensaje' => $mensaje];
                }else{
                    // presupuesto ya aprobado. Y verificar que haya bolsón.

                    if($infoProyecto->id_bolson != null){

                        // se puede PAUSAR

                        Proyecto::where('id', $request->id)->update([
                            'id_estado' => 3,
                        ]);

                        DB::commit();
                        return ['success' => 5];

                    }else{
                        // esto fuera raro, ya que al aprobar presupuesto se debe seleccionar a una cuenta bolsón
                        $mensaje = "Verificar que bolsón exista en el Proyecto";
                        return ['success' => 3, 'titulo' => 'No Encontrado', 'mensaje' => $mensaje];
                    }
                }
            }
            // FINALIZADO: se devuelve dinero a bolsón
            else if($request->idestado == 4){

                if($infoProyecto->presu_aprobado == 0){
                    // presupuesto no aprobado aun
                    $mensaje = "El Presupuesto No esta Aprobado";
                    return ['success' => 3, 'titulo' => 'No Actualizado', 'mensaje' => $mensaje];
                }else{
                    // presupuesto ya aprobado. Y verificar que haya bolsón.

                    if($infoProyecto->id_bolson != null){


                        // dinero sobrante de proyecto finalizado
                        $dineroSobrante = 0;


                        // OBTENER SALDO RESTANTE
                        $presupuesto = DB::table('cuentaproy AS p')
                            ->join('obj_especifico AS obj', 'p.objespeci_id', '=', 'obj.id')
                            ->select('obj.nombre', 'obj.id AS idcodigo', 'obj.codigo', 'p.id', 'p.saldo_inicial')
                            ->where('p.proyecto_id', $infoProyecto->id)
                            ->orderBy('obj.codigo', 'ASC')
                            ->get();

                        foreach ($presupuesto as $pp){

                            // CÁLCULOS

                            $totalRestante = 0;

                            // movimiento de cuentas SUBE
                            $infoMoviCuentaProySube = MoviCuentaProy::where('id_cuentaproy_sube', $pp->id)
                                ->where('autorizado', 1) // autorizado por presupuesto
                                ->sum('dinero');

                            // movimiento de cuentas BAJA
                            $infoMoviCuentaProyBaja = MoviCuentaProy::where('id_cuentaproy_baja', $pp->id)
                                ->where('autorizado', 1) // autorizado por presupuesto
                                ->sum('dinero');

                            $totalMoviCuenta = $infoMoviCuentaProySube - $infoMoviCuentaProyBaja;

                            // obtener todas las salidas de material
                            $arrayRestante = DB::table('cuentaproy_restante AS pd')
                                ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                                ->select('rd.cantidad', 'rd.dinero')
                                ->where('pd.id_cuentaproy', $pp->id)
                                ->where('rd.cancelado', 0)
                                ->get();

                            foreach ($arrayRestante as $dd){
                                $totalRestante = $totalRestante + ($dd->cantidad * $dd->dinero);
                            }

                            $infoCuentaPartida = CuentaproyPartidaAdicional::where('id_proyecto', $infoProyecto->id)->get();

                            $sumaPartidaAdicional = 0;
                            foreach ($infoCuentaPartida as $dd){
                                if($pp->idcodigo == $dd->objespeci_id){
                                    $sumaPartidaAdicional += $dd->monto;
                                }
                            }

                            //* NOTA: SALDO RETENIDO NO SE TOMARA EN CUENTA, YA QUE CUANDO PROYECTO ES FINALIZADO
                            // TODOS LOS PROCESOS SE BLOQUEAN


                            // sumando partidas adicionales que coincidan con el obj específico + saldo inicial
                            $sumaPartidaAdicional += $pp->saldo_inicial;

                            // aquí se obtiene el Saldo Restante del código
                            $totalRestanteSaldo = $totalMoviCuenta + ($sumaPartidaAdicional - $totalRestante);

                            $dineroSobrante += $totalRestanteSaldo;
                        }

                        $dineroSobrante = $this->redondear_dos_decimal($dineroSobrante);

                        // se puede FINALIZAR proyecto
                        Proyecto::where('id', $request->id)->update([
                            'id_estado' => 4,
                            'monto_finalizado' => $dineroSobrante,
                            'fechafin' => Carbon::now('America/El_Salvador')
                        ]);

                        DB::commit();
                        return ['success' => 6];

                    }else{
                        // null, aunque cuando se aprueba el presupuesto se dice a que
                        // bolsón utilizar
                        $mensaje = "Verificar que bolsón exista en el Proyecto";
                        return ['success' => 3, 'titulo' => 'No Encontrado', 'mensaje' => $mensaje];
                    }
                }
            }else{
                // Error
                return ['success' => 99];
            }

        }catch(\Throwable $e){
            Log::info('ee ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // obtener todos los bolsones
    public function obtenerLosBolsones(Request $request){

        // OBTENER EL TOTAL DE LAS PARTIDAS. PARA MOSTRAR AL USUARIO CUANTO DINERO SE USARA Y
        // ELEGIR EL BOLSÓN

        // $request->id  ES EL ID DEL PROYECTO

        $montoPartida = $this->montoFinalPartidaProyecto($request->id);
        $montoPartida = "$" . number_format((float)$montoPartida, 2, '.', ',');

        $infoProyecto = Proyecto::where('id', $request->id)->first();

        $lista = Bolson::where('id_fuenter', $infoProyecto->id_fuenter)
        ->orderBy('nombre')
        ->get();

        return ['success' => 1, 'lista' => $lista, 'presupuesto' => $montoPartida];
    }

    // información de saldo de bolsón, ya con todos sus descuentos
    public function infoSaldoBolson(Request $request){

        $regla = array(
            'idbolson' => 'required',
            'idproyecto' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Bolson::where('id', $request->idbolson)->first()){

            DB::beginTransaction();

            try {

                // proyectoMontoBolson: es el monto de las partidas aprobadas de todos los proyectos a bolson
                // partidaAdicionalMonto: es el monto de las partidas adicionales aprobadas
                // proyectoFinalizadoMonto: es el monto sobrante de un proyecto cuando se finaliza

                $proyectoMontoBolson = Proyecto::where('id_bolson', $request->idbolson)
                    ->sum('monto');

                $partidaAdicionalMonto = PartidaAdicionalContenedor::where('id_proyecto', $request->idproyecto)
                    ->where('estado', 2) // partidas aprobadas
                    ->sum('monto_aprobado');

                $proyectoFinalizadoMonto = Proyecto::where('id_bolson', $request->idbolson)
                    ->where('id_estado', 4)
                    ->sum('monto_finalizado');

                $montoBolsonInicial = Bolson::where('id', $request->idbolson)->sum('monto_inicial');

                // vamos a restar
                $montoActual = $montoBolsonInicial - ($proyectoMontoBolson + $partidaAdicionalMonto);
                // vamos a sumar los sobrantes
                $montoActual += $proyectoFinalizadoMonto;

                $montoActual = "$" . number_format((float)$montoActual, 2, '.', ',');

                return ['success' => 1, 'monto' => $montoActual];
            } catch(\Throwable $e){
                Log::info('ee ' . $e);
            DB::rollback();
            return ['success' => 99];
            }
        }else{
            return ['success' => 2];
        }
    }


    // UTILIZADO SOLO PARA MOSTRAR EL MONTO FINAL DE UNA PARTIDA, AL
    // QUERER APROBAR UN PRESUPUESTO, AHI SE ELIGE EL BOLSÓN
    function montoFinalPartidaProyecto($id){

        // id es ID PROYECTO

        // 1- Materiales
        // 2- Mano de obra (Por Administración)
        // 3- Alquiler de Maquinaria
        // 4- Transporte de Concreto Fresco

        $partida1 = Partida::where('proyecto_id', $id)
            ->whereIn('id_tipopartida', [1, 3, 4])
            ->orderBy('id', 'ASC')
            ->get();

        $sumaMateriales = 0;

        foreach ($partida1 as $secciones) {

            $detalle1 = PartidaDetalle::where('partida_id', $secciones->id)->get();

            $total = 0;

            foreach ($detalle1 as $lista) {

                $infomaterial = CatalogoMateriales::where('id', $lista->material_id)->first();

                if ($lista->duplicado > 0) {
                    $multi = ($lista->cantidad * $infomaterial->pu) * $lista->duplicado;
                    $lista->material = $infomaterial->nombre . " (" . $lista->duplicado . ")";
                } else {
                    $multi = $lista->cantidad * $infomaterial->pu;
                    $lista->material = $infomaterial->nombre;
                }

                // se sumara solo materiales
                if($secciones->id_tipopartida == 1){
                    $sumaMateriales = $sumaMateriales + $multi;
                }

                $total = $total + $multi;
            }
        }

        // 2- MANO DE OBRA POR ADMINISTRACION

        $manoobra = Partida::where('proyecto_id', $id)
            ->where('id_tipopartida', 2)
            ->orderBy('id', 'ASC')
            ->get();

        $totalManoObra = 0;

        foreach ($manoobra as $secciones3) {

            $detalle3 = PartidaDetalle::where('partida_id', $secciones3->id)->get();

            $total3 = 0;

            foreach ($detalle3 as $lista) {

                $infomaterial = CatalogoMateriales::where('id', $lista->material_id)->first();

                if ($lista->duplicado != 0) {
                    $lista->material = $infomaterial->nombre . " (" . $lista->duplicado . ")";
                } else {
                    $lista->material = $infomaterial->nombre;
                }

                $multi = $lista->cantidad * $infomaterial->pu;
                if ($lista->duplicado > 0) {
                    $multi = $multi * $lista->duplicado;
                }

                $totalManoObra = $totalManoObra + $multi;
                $total3 = $total3 + $multi;
            }
        }


        // 3- ALQUILER DE MAQUINARIA

        $alquilerMaquinaria = Partida::where('proyecto_id', $id)
            ->where('id_tipopartida', 3)
            ->get();

        $totalAlquilerMaquinaria = 0;

        foreach ($alquilerMaquinaria as $secciones3) {

            $detalle4 = PartidaDetalle::where('partida_id', $secciones3->id)->get();

            foreach ($detalle4 as $lista) {

                $infomaterial = CatalogoMateriales::where('id', $lista->material_id)->first();
                $lista->material = $infomaterial->nombre;
                $multi = $lista->cantidad * $infomaterial->pu;
                if ($lista->duplicado > 0) {
                    $multi = $multi * $lista->duplicado;
                }

                $totalAlquilerMaquinaria += $multi;
            }
        }

        // 4- TRANSPORTE CONCRETO FRESCO

        $trasportePesado = Partida::where('proyecto_id', $id)
            ->where('id_tipopartida', 4)
            ->get();

        $totalTransportePesado = 0;

        foreach ($trasportePesado as $secciones3) {

            $detalle4 = PartidaDetalle::where('partida_id', $secciones3->id)->get();

            foreach ($detalle4 as $lista) {

                $infomaterial = CatalogoMateriales::where('id', $lista->material_id)->first();
                $lista->material = $infomaterial->nombre;
                $multi = $lista->cantidad * $infomaterial->pu;
                if ($lista->duplicado > 0) {
                    $multi = $multi * $lista->duplicado;
                }

                $totalTransportePesado += $multi;
            }
        }

        $afp = ($totalManoObra * 7.75) / 100;
        $isss = ($totalManoObra * 7.5) / 100;
        $insaforp = ($totalManoObra * 1) / 100;

        $informacionGeneral = InformacionGeneral::where('id', 1)->first();

        // obtener porcentaje actual

        $totalDescuento = ($afp + $isss + $insaforp);
        $herramientaXPorciento = ($sumaMateriales * $informacionGeneral->porcentaje_herramienta) / 100;

        // subtotal del presupuesto partida
        $subtotalPartida = ($sumaMateriales + $herramientaXPorciento + $totalManoObra + $totalDescuento
            + $totalAlquilerMaquinaria + $totalTransportePesado);

        // imprevisto obtenido del proyecto
        $imprevisto = ($subtotalPartida * $informacionGeneral->imprevisto_modificable) / 100;

        // total de la partida final
        $totalPartidaFinal = $subtotalPartida + $imprevisto;

        // total de la partida final
        return ($this->redondear_dos_decimal($totalPartidaFinal));
    }


    // obtener información del proyecto
    public function informacionProyectoIndividual(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($info = Proyecto::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $info];
        }else{
            return ['success' => 2];
        }
    }






}

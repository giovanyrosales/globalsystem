<?php

namespace App\Http\Controllers\Backend\Proyecto;

use App\Http\Controllers\Controller;
use App\Models\AreaGestion;
use App\Models\Bitacora;
use App\Models\BitacoraDetalle;
use App\Models\CatalogoMateriales;
use App\Models\Cotizacion;
use App\Models\CotizacionDetalle;
use App\Models\CuentaProy;
use App\Models\CuentaProyRetenido;
use App\Models\EstadoProyecto;
use App\Models\FuenteFinanciamiento;
use App\Models\FuenteRecursos;
use App\Models\LineaTrabajo;
use App\Models\MoviCuentaProy;
use App\Models\Naturaleza;
use App\Models\ObjEspecifico;
use App\Models\Partida;
use App\Models\PartidaDetalle;
use App\Models\Proveedores;
use App\Models\Proyecto;
use App\Models\Requisicion;
use App\Models\RequisicionDetalle;
use App\Models\TipoPartida;
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

        return view('backend.admin.proyectos.nuevo.vistanuevoproyecto', compact('arrayNaturaleza',
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
                $p->id_estado = 1; // priorizado
                $p->formulador = $request->formulador;
                $p->supervisor = $request->supervisor;
                $p->encargado = $request->encargado;
                $p->fecha = Carbon::now('America/El_Salvador');
                $p->presu_aprobado = 0;
                $p->fecha_aprobado = null;

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
            $p->id_estado = 1; // priorizado
            $p->formulador = $request->formulador;
            $p->supervisor = $request->supervisor;
            $p->encargado = $request->encargado;
            $p->fecha = Carbon::now('America/El_Salvador');
            $p->monto = 0;
            $p->presu_aprobado = 0;
            $p->fecha_aprobado = null;

            if($p->save()){
                return ['success' => 2];
            }else{
                return ['success' => 3];
            }
        }
    }

    public function indexProyectoLista(){
        return view('backend.admin.proyectos.listaproyectos.vistalistaproyecto');
    }

    public function tablaProyectoLista(){

        $lista = Proyecto::orderBy('fecha')->get();

        foreach ($lista as $ll){
            if($ll->fechaini != null) {
                $ll->fechaini = date("d-m-Y", strtotime($ll->fechaini));
            }

            if($ll->presu_aprobado == 0){
                $ll->estadoPresupuesto = false;
            }else{
                $ll->estadoPresupuesto = true;
            }
        }

        return view('backend.admin.proyectos.listaproyectos.tablalistaproyecto', compact('lista'));
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
                'arrayFuenteRecursos' => $arrayFuenteRecursos,
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

        return view('backend.admin.proyectos.vistaproyecto', compact('proyecto', 'id',
            'conteo', 'conteoPartida', 'estado', 'preaprobacion', 'tipospartida'));
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
        return view('Backend.Admin.Proyectos.Bitacoras.vistaBitacoraDetalle', compact('id'));
    }

    public function tablaBitacoraDetalle($id){ // id de bitacora
        $lista = BitacoraDetalle::where('id_bitacora', $id)->orderBy('id')->get();
        return view('Backend.Admin.Proyectos.Bitacoras.tablaBitacoraDetalle', compact('lista'));
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

    // NUEVO REQUERIMIENTO
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

                // verificar el presupuesto detalle para el obj especifico de este material
                // obtener el saldo inicial - total de salidas y esto dara cuanto tengo en caja

                // como siempre busco material que estaban en el presupuesto, siempre encontrara
                // el proyecto ID y el ID de objeto específico
                $infoPresupuesto = CuentaProy::where('proyecto_id', $request->id)
                    ->where('objespeci_id', $infoCatalogo->id_objespecifico)
                    ->first();

                $totalSalida = 0;
                $totalEntrada = 0;
                $totalRetenido = 0;

                //*********

                $infoSalidaDetalle = DB::table('cuentaproy_detalle AS pd')
                    ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                    ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                    ->where('pd.id_cuentaproy', $infoPresupuesto->id)
                    ->where('pd.tipo', 0) // salidas. la orden es valida
                    ->where('rd.cancelado', 0)
                    ->get();

                foreach ($infoSalidaDetalle as $dd){
                    $totalSalida = $totalSalida + ($dd->cantidad * $dd->dinero);
                }

                $infoEntradaDetalle = DB::table('cuentaproy_detalle AS pd')
                    ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                    ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                    ->where('pd.id_cuentaproy', $infoPresupuesto->id)
                    ->where('pd.tipo', 1) // entradas. la orden es cancelada
                    ->where('rd.cancelado', 0)
                    ->get();

                foreach ($infoEntradaDetalle as $dd){
                    $totalEntrada = $totalEntrada + ($dd->cantidad * $dd->dinero);
                }

                //********* end

                // OBTENER SALDO RESTANTE POR LOS MOVIMIENTO DE CUENTAS

                $moviSumaSaldo = MoviCuentaProy::where('id_cuentaproy', $infoPresupuesto->id)
                    ->sum('aumento');

                $moviRestaSaldo = MoviCuentaProy::where('id_cuentaproy', $infoPresupuesto->id)
                    ->sum('disminuye');

                $totalMoviCuenta = $moviSumaSaldo - $moviRestaSaldo;

                //INICIO DE VERIFICACIÓN DE CUANTO SALDO RESTANTE ME QUEDA

                // hoy obtener cuanto me queda por los movimientos de cuenta
                $saldoRestante = $totalMoviCuenta;

                // esto es lo que hay de SALDO RESTANTE PARA EL OBJETO ESPECÍFICO
                // total salida y entrada son de cuentaproy_detalle para calcular ordenes de compra valida o rechazada
                $saldoRestante += $infoPresupuesto->saldo_inicial - ($totalSalida - $totalEntrada);


                // obtener cuanto saldo retenido tengo para el objeto específico
                // y el dinero lo obtiene de LA REQUISICIÓN DETALLE

                $infoSaldoRetenido = DB::table('cuentaproy_retenido AS psr')
                    ->join('requisicion_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                    ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                    ->where('psr.id_cuentaproy', $infoPresupuesto->id) // con esto obtengo solo del obj específico
                    ->where('rd.cancelado', 0)
                    ->get();

                foreach ($infoSaldoRetenido as $dd){
                    $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
                }

                // verificar cantidad * dinero del material nuevo
                $saldoMaterial = $request->cantidad[$i] * $infoCatalogo->pu;

                $sumaSaldos = $saldoMaterial + $totalRetenido; //


                if($this->redondear_dos_decimal($saldoRestante) < $this->redondear_dos_decimal($sumaSaldos)){

                    // retornar que no alcanza el saldo

                    // SALDO RESTANTE Y SALDO RETENIDO FORMATEADOS
                    $saldoRestanteFormat = number_format((float)$saldoRestante, 2, '.', ',');
                    $saldoRetenidoFormat = number_format((float)$totalRetenido, 2, '.', ',');

                    $saldoMaterial = number_format((float)$saldoMaterial, 2, '.', ',');

                    return ['success' => 3, 'fila' => $i,
                        'obj' => $infoObjeto->codigo,
                        'disponibleFormat' => $saldoRestanteFormat, // esto va formateado
                        'retenidoFormat' => $saldoRetenidoFormat, // esto va formateado
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
                    $rDetalle->save();

                    // guardar el SALDO RETENIDO
                    $rRetenido = new CuentaProyRetenido();
                    $rRetenido->id_requi_detalle = $rDetalle->id;
                    $rRetenido->id_cuentaproy = $infoPresupuesto->id;

                    $rRetenido->save();
                }
            }

            $contador = RequisicionDetalle::where('requisicion_id', $r->id)->count();
            $contador = $contador + 1;

            DB::commit();
            return ['success' => 1, 'contador' => $contador];

        }catch(\Throwable $e){
            Log::info('ERROR ' . $e);
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

    public function editarRequisicion(Request $request){

        DB::beginTransaction();

        try {

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
            $infoRequi = RequisicionDetalle::where('requisicion_id', $request->idrequisicion)
                ->whereNotIn('id', $pila)
                ->get();

            $pilaBorrar = array();

            // ya con los id a borrar. verificar que no esten cotizados
            foreach ($infoRequi as $dd){
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

    public function cancelarMaterialRequisicion(Request $request){

        // verificar que este material no este cotizado con una autorizada.

        // obtener todas las cotizacion id donde este cotizado
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
            // MATERIAL FUE APROBADO O ESPERANDO APROBACIÓN, NO SE PUEDE CANCELAR YA
            return ['success' => 1];
        }

        // SE PUEDE CANCELAR, PORQUE NINGUNA COTI ESTA APROBADA
        RequisicionDetalle::where('id', $request->id)->update([
            'cancelado' => 1,
        ]);

        return ['success' => 2];
    }

    // BORRAR UNA REQUISICION COMPLETA, SOLO SINO HA SIDO COTIZADO NINGUN MATERIAL
    public function borrarRequisicion(Request $request){

        $regla = array(
            'id' => 'required', // id requisicion
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        if(Requisicion::where('id', $request->id)->first()){

            // buscar si no hay ningún material ya cotizado
            if(Cotizacion::where('id', $request->id)->first()){
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

        return view('Backend.Admin.Proyectos.Cotizacion.vistaCotizacion', compact('requisicion', 'proveedores',
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

    // MUESTRA MATERIALES SOLO DEL PRESUPUESTO DEL PROYECTO
    public function buscadorMaterialRequisicion(Request $request){

        if($request->get('query')){
            $query = $request->get('query');

            $listado = Partida::where('proyecto_id', $request->idpro)->get();

            $pila = array();

            foreach ($listado as $dd){
                array_push($pila, $dd->id);
            }

            $data = DB::table('partida_detalle AS pd')
                ->join('materiales AS m', 'pd.material_id', '=', 'm.id')
                ->select('m.id')
                ->whereIn('pd.partida_id', $pila)
                ->where('m.nombre', 'LIKE', "%{$query}%")
                ->groupBy('m.id')
                ->get();

            foreach ($data as $dd){

                $infoMaterial = CatalogoMateriales::where('id', $dd->id)->first();

                $medida = '';
                if($infoUnidad = UnidadMedida::where('id', $infoMaterial->id_unidadmedida)->first()){
                    $medida = $infoUnidad->medida;
                }

                if($medida === ''){
                    $dd->unido = $infoMaterial->nombre;
                }else{
                    $dd->unido = $infoMaterial->nombre . ' - ' . $medida;
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
                 <li onclick="modificarValor(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->unido.'</a></li>
                ';
                    }
                }

                else{
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValor(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->unido.'</a></li>
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

    // utilizado para un usuario tipo ingenieria al editar
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

  /*  public function nuevaCotizacion(Request $request){

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
    }*/

    // *** INGENIERIA ***
    public function tablaProyectoListaPresupuesto($id){

        $partida = Partida::where('proyecto_id', $id)
            ->orderBy('id', 'ASC')
            ->get();

        $conteo = 0;
        foreach ($partida as $pp){
           $conteo = $conteo + 1;
           $pp->item = $conteo;
        }

        $presuaprobado = 0;
        if($infoProyecto = Proyecto::where('id', $id)->first()) {
            $presuaprobado = $infoProyecto->presu_aprobado;
        }

        return view('Backend.Admin.Proyectos.tablaListaPresupuesto', compact('partida', 'presuaprobado'));
    }

    public function agregarPresupuestoPartida(Request $request){

        $rules = array(
            'nombrepartida' => 'required',
            'tipopartida' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ( $validator->fails()){
            return ['success' => 0];
        }

        if($infop = Proyecto::where('id', $request->id)->first()){
            //0: presupuesto en desarrollo
            //1: listo para revision
            //2: aprobado

            if ($infop->presu_aprobado == 1){
                return ['success' => 1];
            }

            if ($infop->presu_aprobado == 2){
                return ['success' => 2];
            }
        }

        DB::beginTransaction();

        try {

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
            DB::rollback();
            return ['success' => 4];
        }
    }

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

    public function editarPresupuesto(Request $request){

        DB::beginTransaction();

        try {

            if($infopa = Partida::where('id', $request->idpartida)->first()) {

                if ($proy = Proyecto::where('id', $infopa->proyecto_id)->first()) {

                    // Modo revision
                    if ($proy->presu_aprobado == 1) {
                        return ['success' => 1];
                    }

                    // presupuesto aprobado
                    if ($proy->presu_aprobado == 2) {
                        return ['success' => 2];
                    }
                }
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

    public function borrarPresupuesto(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){return ['success' => 0];}

        if($info = Partida::where('id', $request->id)->first()){

            if($pro = Proyecto::where('id', $info->proyecto_id)->first()){
                if($pro->presu_aprobado == 1){
                    return ['success' => 1];
                }
            }

            // borrar listado
            PartidaDetalle::where('partida_id', $request->id)->delete();
            Partida::where('id', $request->id)->delete();

            $conteoPartida = Partida::where('proyecto_id', $info->proyecto_id)->count();
            if($conteoPartida == 0){
                $conteoPartida = 1;
            }else{
                $conteoPartida += 1;
            }

            return ['success' => 2, 'contador' => $conteoPartida];
        }else{
            return ['success' => 3];
        }
    }

    // para crear pdf se debe verificar que exista esta partida
    public function verificarPartidaManoObra(Request $request){

        // TIPO PARTIDA 3: Mano de obra (Por Administración)
        if(Partida::where('proyecto_id', $request->id)->where('id_tipopartida', 3)->first()){
            return ['success' => 1];
        }
        return ['success' => 2];
    }


    // informacion para presupuesto para uaci
    public function informacionPresupuestoParaAprobacion($id){

        // obtener todas los presupuesto por tipo partida
        // 1- Materiales
        // 2- Herramientas (2% de Materiales)
        // 3- Mano de obra (Por Administración)
        // 4- Aporte Mano de Obra
        // 5- Alquiler de Maquinaria
        // 6- Transporte de Concreto Fresco

        // MATERIALES, HERRAMIENTAS (2% DE MATERIALES), ALQUILER DE MAQUINARIA
        $partida1 = Partida::where('proyecto_id', $id)
            ->whereIn('id_tipopartida', [1, 2, 5])
            ->orderBy('id', 'ASC')
            ->get();

        $infoPro = Proyecto::where('id', $id)->first();
        $nombrepro = $infoPro->nombre;
        $fuenter = "";

        if($infoFuenteR = FuenteRecursos::where('id', $infoPro->id_fuenter)->first()){
            $fuenter = $infoFuenteR->nombre;
        }

        $resultsBloque = array();
        $index = 0;
        $resultsBloque3 = array();
        $index3 = 0;

        // Fechas
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $fecha = Carbon::parse(Carbon::now());
        $anio = Carbon::now()->format('Y');
        $mes = $meses[($fecha->format('n')) - 1] . " del " . $anio;

        $item = 0;
        $sumaMateriales = 0;

        foreach ($partida1 as $secciones){
            array_push($resultsBloque, $secciones);
            $item = $item + 1;
            $secciones->item = $item;

            $secciones->cantidadp = number_format((float)$secciones->cantidadp, 2, '.', ',');

            $detalle1 = PartidaDetalle::where('partida_id', $secciones->id)->get();

            $total = 0;

            foreach ($detalle1 as $lista){

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

                if($lista->duplicado != 0){
                    $lista->material = $infomaterial->nombre . " (" . $lista->duplicado . ")";
                }else{
                    $lista->material = $infomaterial->nombre;
                }

                $multi = $lista->cantidad * $infomaterial->pu;

                $lista->cantidad = number_format((float)$lista->cantidad, 2, '.', ',');
                $lista->pu = "$" . number_format((float)$infomaterial->pu, 2, '.', ',');
                $lista->subtotal = "$" . number_format((float)$multi, 2, '.', ',');

                $total = $total + $multi;

                // suma de materiales
                if($secciones->id_tipopartida == 1){
                    $sumaMateriales = $sumaMateriales + $multi;
                }
            }

            $secciones->total = "$" . number_format((float)$total, 2, '.', ',');

            $resultsBloque[$index]->bloque1 = $detalle1;
            $index++;
        }

        $manoobra = Partida::where('proyecto_id', $id)
            ->where('id_tipopartida', 3)
            ->orderBy('id', 'ASC')
            ->get();

        $totalManoObra = 0;

        foreach ($manoobra as $secciones3){
            array_push($resultsBloque3, $secciones3);
            $item = $item + 1;
            $secciones3->item = $item;

            $secciones3->cantidadp = number_format((float)$secciones3->cantidadp, 2, '.', ',');

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

                if($lista->duplicado != 0){
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


        // APORTE DE MANO DE OBRA

        $aporteManoObra = Partida::where('proyecto_id', $id)
            ->where('id_tipopartida', 4)
            ->get();

        $totalAporteManoObra = 0;

        foreach ($aporteManoObra as $secciones3){

            $detalle4 = PartidaDetalle::where('partida_id', $secciones3->id)->get();

            foreach ($detalle4 as $lista){

                $infomaterial = CatalogoMateriales::where('id', $lista->material_id)->first();
                if($lista->duplicado != 0){
                    $lista->material = $infomaterial->nombre . " (" . $lista->duplicado . ")";
                }else{
                    $lista->material = $infomaterial->nombre;
                }

                $multi = $lista->cantidad * $infomaterial->pu;

                $totalAporteManoObra = $totalAporteManoObra + $multi;
            }
        }

        // ALQUILER DE MAQUINARIA

        $alquilerMaquinaria = Partida::where('proyecto_id', $id)
            ->where('id_tipopartida', 5)
            ->get();

        $totalAlquilerMaquinaria = 0;

        foreach ($alquilerMaquinaria as $secciones3){

            $detalle4 = PartidaDetalle::where('partida_id', $secciones3->id)->get();

            foreach ($detalle4 as $lista){

                $infomaterial = CatalogoMateriales::where('id', $lista->material_id)->first();
                $lista->material = $infomaterial->nombre;
                $multi = $lista->cantidad * $infomaterial->pu;

                $totalAlquilerMaquinaria = $totalAporteManoObra + $multi;
            }
        }


        // TRANSPORTE CONCRETO FRESCO

        $trasportePesado = Partida::where('proyecto_id', $id)
            ->where('id_tipopartida', 6)
            ->get();

        $totalTransportePesado = 0;

        foreach ($trasportePesado as $secciones3){

            $detalle4 = PartidaDetalle::where('partida_id', $secciones3->id)->get();

            foreach ($detalle4 as $lista){

                $infomaterial = CatalogoMateriales::where('id', $lista->material_id)->first();
                $lista->material = $infomaterial->nombre;
                $multi = $lista->cantidad * $infomaterial->pu;

                $totalTransportePesado = $totalAporteManoObra + $multi;
            }
        }

        $afp =  ($totalManoObra * 7.75) / 100;
        $isss = ($totalManoObra * 7.5) / 100;
        $insaforp = ($totalManoObra * 1) / 100;

        $totalDescuento = $afp + $isss + $insaforp;

        $afp = "$" . number_format((float)$afp, 2, '.', ',');
        $isss = "$" . number_format((float)$isss, 2, '.', ',');
        $insaforp = "$" . number_format((float)$insaforp, 2, '.', ',');

        $herramienta2Porciento = ($sumaMateriales * 2) / 100;

        // subtotal del presupuesto partida
        $subtotalPartida = ($sumaMateriales + $herramienta2Porciento + $totalManoObra + $totalDescuento
            + $totalAlquilerMaquinaria + $totalTransportePesado);


        // imprevisto del 5%
        $imprevisto = ($subtotalPartida * 5) / 100;

        // total de la partida final
        $totalPartidaFinal = $subtotalPartida + $imprevisto;

        $totalDescuento = "$" . number_format((float)$totalDescuento, 2, '.', ',');
        $sumaMateriales = "$" . number_format((float)$sumaMateriales, 2, '.', ',');
        $herramienta2Porciento = "$" . number_format((float)$herramienta2Porciento, 2, '.', ',');
        $totalManoObra = "$" . number_format((float)$totalManoObra, 2, '.', ',');

        $totalAporteManoObra = "$" . number_format((float)$totalAporteManoObra, 2, '.', ',');
        $totalAlquilerMaquinaria = "$" . number_format((float)$totalAlquilerMaquinaria, 2, '.', ',');
        $totalTransportePesado = "$" . number_format((float)$totalTransportePesado, 2, '.', ',');
        $subtotalPartida = "$" . number_format((float)$subtotalPartida, 2, '.', ',');
        $imprevisto = "$" . number_format((float)$imprevisto, 2, '.', ',');
        $totalPartidaFinal = "$" . number_format((float)$totalPartidaFinal, 2, '.', ',');

        $preAprobado = $infoPro->presu_aprobado;

        return view('backend.admin.proyectos.modal.pdfpresupuesto', compact('partida1',
            'manoobra', 'mes', 'fuenter', 'nombrepro', 'afp', 'isss', 'insaforp', 'totalDescuento',
            'sumaMateriales', 'herramienta2Porciento', 'totalManoObra', 'totalAporteManoObra', 'totalAlquilerMaquinaria',
            'totalTransportePesado', 'subtotalPartida', 'imprevisto', 'totalPartidaFinal', 'preAprobado'));
    }

    public function aprobarPresupuesto(Request $request){

        if($pro = Proyecto::where('id', $request->id)->first()){

                DB::beginTransaction();
                try {

                    if ($pro->presu_aprobado != 1) {
                        // presupuesto cambio estado y está en desarrollo
                        return ['success' => 1];
                    }

                    if ($pro->presu_aprobado === 2) {
                        // presupuesto ya aprobado y no hacer nada
                        return ['success' => 2];
                    }

                    // MANO DE OBRA POR ADMINISTRACION, APARTE PARA CALCULAR ISSS, AFP, INSAFORP
                    $manoobra = Partida::where('proyecto_id', $request->id)
                        ->where('id_tipopartida', 3)
                        ->orderBy('id', 'ASC')
                        ->get();

                    // el presupuesto no tiene APORTE MANO DE OBRA (POR ADMINISTRACIÓN)
                    if (sizeof($manoobra) <= 0) {
                        return ['success' => 3];
                    }

                    // pasar a modo aprobado
                    Proyecto::where('id', $request->id)->update([
                        'fecha_aprobado' => Carbon::now('America/El_Salvador'),
                        'presu_aprobado' => 2]);


                    $partidas = Partida::where('proyecto_id', $request->id)
                        ->orderBy('id')
                        ->get();

                    $pilaDetalle = array();

                    foreach ($partidas as $pp){
                        array_push($pilaDetalle, $pp->id);
                    }

                    // verificar cada detalle de la partida para ver si tiene objeto especifico
                    $todoDetalle = DB::table('partida_detalle AS p')
                        ->join('materiales AS m', 'p.material_id', '=', 'm.id')
                        ->select('m.id_objespecifico', 'm.nombre')
                        ->whereIn('p.partida_id', $pilaDetalle)
                        ->get();

                    $conteo = 0;
                    foreach ($todoDetalle as $tt){
                        if($tt->id_objespecifico == null){
                            $conteo = $conteo + 1;
                        }
                    }

                    if($conteo > 0){
                        return ['success' => 4, 'conteo' => $conteo];
                    }

                    $totalManoObra = 0;

                    // CALCULAR LA MANO DE OBRA (POR ADMINISTRACIÓN)
                    foreach ($manoobra as $secciones3) {
                        $detalle3 = PartidaDetalle::where('partida_id', $secciones3->id)->get();

                        foreach ($detalle3 as $lista) {
                            $infomaterial = CatalogoMateriales::where('id', $lista->material_id)->first();
                            $multi = $lista->cantidad * $infomaterial->pu;
                            if ($lista->duplicado != 0) {
                                $multi = $multi * $lista->duplicado;
                            }

                            $totalManoObra = $totalManoObra + $multi;
                        }
                    }

                    // cálculos
                    $afp = ($totalManoObra * 7.75) / 100;
                    $isss = ($totalManoObra * 7.5) / 100;
                    $insaforp = ($totalManoObra * 1) / 100;

                    $pila = array();

                    foreach ($partidas as $dd){
                        array_push($pila, $dd->id);
                    }

                    // agrupando para obtener solo los ID de objetos específicos.
                    $detalles = DB::table('partida_detalle AS pd')
                        ->join('materiales AS m', 'pd.material_id', '=', 'm.id')
                        ->select('m.id_objespecifico')
                        ->whereIn('pd.partida_id', $pila)
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

                           // SUMA DE ISSS Y AFP
                           // LOS CÓDIGOS FIJOS
                           //51402: POR REMUNERACIONES EVENTUALES
                           if($dato->codigo == 51402){
                               $suma = $isss + $insaforp;
                               break;
                           }
                           // 51502: POR REMUNERACIONES EVENTUALES (igual al de arriba)
                           else if($dato->codigo == 51502){
                               $suma = $afp;
                               break;
                           }

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
                       $presu->save();
                   }

                    DB::commit();
                    return ['success' => 2];
                }catch(\Throwable $e){
                    Log::info('error: ' . $e);
                    DB::rollback();
                    return ['success' => 99];
                }
        }

        return ['success' => 99];
    }

    // mostrara saldo restante calculado.
    public function infoTablaSaldoProyecto($id){

        // presupuesto
        $presupuesto = DB::table('cuentaproy AS p')
            ->join('obj_especifico AS obj', 'p.objespeci_id', '=', 'obj.id')
            ->select('obj.nombre', 'obj.id AS idcodigo', 'obj.codigo', 'p.id', 'p.saldo_inicial')
            ->where('p.proyecto_id', $id)
            ->get();

        foreach ($presupuesto as $pp){

            $totalSalida = 0;
            $totalEntrada = 0;
            $totalRetenido = 0;

            // SUMA Y RESTA DE MOVIMIENTO DE CÓDIGOS
            // suma de saldo
             $moviSumaSaldo = MoviCuentaProy::where('id_cuentaproy', $pp->id)
                 ->sum('aumento');

            $moviRestaSaldo = MoviCuentaProy::where('id_cuentaproy', $pp->id)
                ->sum('disminuye');

            $totalMoviCuenta = $moviSumaSaldo - $moviRestaSaldo;

            // POR AQUI SE VALIDARA SI NO FUE CANCELADO LA ORDEN DE COMPRA, YA QUE AHI SE CREA EL PRESU_DETALLE.
            // Y SI ES CANCELADO SE CAMBIA UN ESTADO Y DEJAR DE SER VALIDO PARA VERIFICAR
            $infoSalidaDetalle = DB::table('cuentaproy_detalle AS pd')
                ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('pd.id_cuentaproy', $pp->id)
                ->where('pd.tipo', 0) // salidas. la orden es valida
                ->where('rd.cancelado', 0) // requi material no puede ser cancelado porque ya hay orden de compra
                ->get();

            foreach ($infoSalidaDetalle as $dd){
                $totalSalida = $totalSalida + ($dd->cantidad * $dd->dinero);
            }

            $infoEntradaDetalle = DB::table('cuentaproy_detalle AS pd')
                ->join('requisicion_detalle AS rd', 'pd.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero', 'rd.cancelado')
                ->where('pd.id_cuentaproy', $pp->id)
                ->where('pd.tipo', 1) // entradas. la orden fue cancelada
                ->where('rd.cancelado', 0) // requi material no puede ser cancelado porque ya hay orden de compra
                ->get();

            foreach ($infoEntradaDetalle as $dd){
                $totalEntrada = $totalEntrada + ($dd->cantidad * $dd->dinero);
            }

            // SALDOS RETENIDOS

            $infoSaldoRetenido = DB::table('cuentaproy_retenido AS psr')
                ->join('requisicion_detalle AS rd', 'psr.id_requi_detalle', '=', 'rd.id')
                ->select('rd.cantidad', 'rd.dinero')
                ->where('psr.id_cuentaproy', $pp->id)
                ->where('rd.cancelado', 0)
                ->get();

            foreach ($infoSaldoRetenido as $dd){
                $totalRetenido = $totalRetenido + ($dd->cantidad * $dd->dinero);
            }

            // SUMAR LOS MOVIMIENTOS DE CUENTA
            $totalRestante =  $totalMoviCuenta;
            $totalRestante += $pp->saldo_inicial - ($totalSalida - $totalEntrada);

            $pp->saldo_inicial = number_format((float)$pp->saldo_inicial, 2, '.', ',');
            $pp->saldo_restante = number_format((float)$totalRestante, 2, '.', ',');
            $pp->total_retenido = number_format((float)$totalRetenido, 2, '.', ',');

            $pp->saldoRestante = $this->redondear_dos_decimal($totalRestante);
            $pp->totalRetenido = $this->redondear_dos_decimal($totalRetenido);
        }

        return view('backend.admin.proyectos.modal.modalsaldo', compact('presupuesto'));
    }


    // cambiar estado de presupuesto ingenieria para que lo apruebe uaci
    public function cambiarEstadoPresupuesto(Request $request){


        // SE REQUIRE PARTIDA MANO DE OBRA POR ADMINISTRACION
        if(!Partida::where('proyecto_id', $request->id)->where('id_tipopartida', 3)->first()){
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


    public function verCatalogoMaterialRequisicion($id){

        $lista = Partida::where('proyecto_id', $id)->get();
        $pila = array();

        foreach ($lista as $dd){
            array_push($pila, $dd->id);
        }

        // presupuesto
        $presupuesto = DB::table('partida_detalle AS p')
            ->join('materiales AS m', 'p.material_id', '=', 'm.id')
            ->select('m.nombre', 'm.id', 'p.cantidad', 'm.id_objespecifico', 'm.id_unidadmedida')
            ->whereIn('p.partida_id', $pila)
            ->orderBy('m.nombre', 'ASC')
            ->get();

        foreach ($presupuesto as $pp){

            $medida = '';
            if($info = UnidadMedida::where('id', $pp->id_unidadmedida)->first()){
                $medida = $info->medida;
            }
            $pp->medida = $medida;

            $infoObjeto = ObjEspecifico::where('id', $pp->id_objespecifico)->first();

            $pp->objcodigo = $infoObjeto->codigo;
            $pp->objnombre = $infoObjeto->nombre;

            $infoCatalogo = CatalogoMateriales::where('id', $pp->id)->first();
            $pp->actual = number_format((float)$infoCatalogo->pu, 2, '.', ',');
        }

        return view('backend.admin.proyectos.modal.modalcatalogomaterial', compact('presupuesto'));
    }


}

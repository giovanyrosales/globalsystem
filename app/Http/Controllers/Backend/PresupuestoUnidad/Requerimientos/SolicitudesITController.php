<?php

namespace App\Http\Controllers\Backend\PresupuestoUnidad\Requerimientos;

use App\Http\Controllers\Controller;
use App\Models\InformacionGeneral;
use App\Models\ObjEspecifico;
use App\Models\P_AnioPresupuesto;
use App\Models\P_Departamento;
use App\Models\P_Materiales;
use App\Models\P_UsuarioDepartamento;
use App\Models\SolicitudITDatos;
use App\Models\SolicitudITDatosTabla;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SolicitudesITController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indexSolicitudesIT()
    {
        $anios = P_AnioPresupuesto::orderBy('nombre', 'DESC')->get();
        return view('backend.admin.solicitudesit.bloquefecha.vistafechasolicitudit', compact('anios'));
    }

    public function indexListadoSolicitudesIT($idanio)
    {

        // obtener usuario
        $user = Auth::user();
        if ($infoDepartamento = P_UsuarioDepartamento::where('id_usuario', $user->id)->first()) {
            $idUsuario = $infoDepartamento->id_usuario;
            $idDepartamento = $infoDepartamento->id_departamento;

            $infoAnio = P_AnioPresupuesto::where('id', $idanio)->first();
            $anio = $infoAnio->nombre;

            $listado = null;
            $haydatos = 0;

            //** FECHA MAXIMO PARA PODER EDITAR **/
            $infoGeneral = InformacionGeneral::where('id', 1)->first();
            $fechaActual = Carbon::now('America/El_Salvador')->startOfDay();
            $otraFecha = Carbon::createFromFormat('Y-m-d', $infoGeneral->fecha_it);
            $fechaLimite = date("d-m-Y", strtotime($infoGeneral->fecha_it));

            $puedeActualizar = 0;
            if ($fechaActual->greaterThan($otraFecha)) {
                // SUPERO LA FECHA LIMITE
                $puedeActualizar = 1;
            }


            if ($fila = SolicitudITDatos::where('id_anio', $idanio)->where('id_departamento', $idDepartamento)->first()) {

                $listado = SolicitudITDatosTabla::where('id_solicitudit_datos', $fila->id)
                    ->orderBY('nombre', 'ASC')
                    ->get();

                $conteo = 0;
                foreach ($listado as $item) {
                    $conteo++;

                    $item->conteo = $conteo;
                }

                if ($listado->isNotEmpty()) {
                    $haydatos = 1;
                }
            }


            // EJEMPLO DE LISTADO INFORMATICO

            $arrayInformatico = P_Materiales::whereIn('id_objespecifico', [34, 81])
                ->orderBy('descripcion')
                ->get();

            $conteoEquipo = 0;
            foreach ($arrayInformatico as $dato) {
                $conteoEquipo++;
                $infoObj = ObjEspecifico::where('id', $dato->id_objespecifico)->first();
                $dato->codigo = $infoObj->codigo;
                $dato->conteo = $conteoEquipo;
            }


            return view('backend.admin.solicitudesit.vistatabla.vistalistadosolicitudesit',
                compact('idanio', 'anio', 'listado', 'haydatos', 'puedeActualizar', 'fechaLimite',
                    'arrayInformatico'));

        } else {
            return "Usuario no tiene asignado un Departamento";
        }
    }


    public function guardarDatosSolicitudesIT(Request $request)
    {

        $user = Auth::user();
        if ($infoDepartamento = P_UsuarioDepartamento::where('id_usuario', $user->id)->first()) {
            $idUsuario = $infoDepartamento->id_usuario;
            $idDepartamento = $infoDepartamento->id_departamento;
            $idAnio = $request->idanio;

            //** FECHA MAXIMO PARA PODER EDITAR **/
            $infoGeneral = InformacionGeneral::where('id', 1)->first();
            $fechaActual = Carbon::now('America/El_Salvador')->startOfDay();
            $otraFecha = Carbon::createFromFormat('Y-m-d', $infoGeneral->fecha_it);

            if ($fechaActual->greaterThan($otraFecha)) {
                // FECHA LIMITE SUPERADA PARA PODER EDITAR
                return ['success' => 1];
            }

            DB::beginTransaction();

            try {

                if ($filaSoli = SolicitudITDatos::where('id_anio', $idAnio)
                    ->where('id_departamento', $idDepartamento)
                    ->first()) {

                    // YA HAY REGISTRO GUARDADO, SOLO MODIFICAR LA TABLA
                    // BORRAR TODOS LOS REGISTROS Y GUARDAR LOS NUEVOS

                    SolicitudITDatosTabla::where('id_solicitudit_datos', $filaSoli->id)->delete();

                    $datosContenedor = json_decode($request->contenedorArray, true);

                    if (!empty($datosContenedor)) {
                        foreach ($datosContenedor as $filaArray) {

                            $detalle = new SolicitudITDatosTabla();
                            $detalle->id_solicitudit_datos = $filaSoli->id;
                            $detalle->nombre = $filaArray['infoNombre'];
                            $detalle->cantidad = $filaArray['infoCantidad'];
                            $detalle->save();
                        }
                    }
                } else {

                    // NO HABIA REGISTROS AUN, SE GUARDARA COMO NUEVO
                    $registro = new SolicitudITDatos();
                    $registro->fecha = $fechaActual;
                    $registro->id_anio = $idAnio;
                    $registro->id_departamento = $idDepartamento;
                    $registro->save();

                    $datosContenedor = json_decode($request->contenedorArray, true);

                    if (!empty($datosContenedor)) {
                        foreach ($datosContenedor as $filaArray) {

                            $detalle = new SolicitudITDatosTabla();
                            $detalle->id_solicitudit_datos = $registro->id;
                            $detalle->nombre = $filaArray['infoNombre'];
                            $detalle->cantidad = $filaArray['infoCantidad'];
                            $detalle->save();
                        }
                    }
                }

                DB::commit();
                return ['success' => 2];

            } catch (\Throwable $e) {
                Log::info('ee ' . $e);
                DB::rollback();
                return ['success' => 99];
            }
        } else {
            // error usuario no encontrado
            return ['success' => 99];
        }
    }


    // *** ADMINISTRACION ****

    function indexSolicitudesITControl()
    {

        $anios = P_AnioPresupuesto::orderBy('nombre', 'DESC')->get();

        $infoGenera = InformacionGeneral::where('id', 1)->first();
        $fechaLimite = $infoGenera->fecha_it;

        return view('backend.admin.solicitudesit.administrar.vistaadministrarsolicitudit',
            compact('anios', 'fechaLimite'));
    }


    // OBTENER LISTADO DE UNIDADES QUE YA SOLICITARON POR X AÑO
    function listadoSolicitudeITBloqueFecha(Request $request)
    {

        $listado = DB::table('solicitudit_datos AS so')
            ->join('p_departamento AS depa', 'so.id_departamento', '=', 'depa.id')
            ->select('so.id', 'depa.nombre')
            ->where('so.id_anio', $request->idanio)
            ->get();

        return ['success' => 1, 'listado' => $listado];
    }


    public function indexSolicitudTablaFinal($idfila)
    {

        if ($infoSoli = SolicitudITDatos::where('id', $idfila)->first()) {

            $infoAnio = P_AnioPresupuesto::where('id', $infoSoli->id_anio)->first();
            $anioActual = $infoAnio->nombre;

            $infoDepa = P_Departamento::where('id', $infoSoli->id_departamento)->first();
            $departamento = $infoDepa->nombre;

            $conteo = 0;
            $haydatos = 0;
            $arrayDatos = SolicitudITDatosTabla::where('id_solicitudit_datos', $idfila)
                ->orderBY('nombre', 'ASC')
                ->get();

            foreach ($arrayDatos as $item) {
                $conteo++;
                $haydatos = 1;
                $item->conteo = $conteo;
            }

            return view('backend.admin.solicitudesit.administrar.vistasolicitudittablafinal',
                compact('departamento', 'anioActual', 'arrayDatos', 'haydatos'));
        } else {
            // datos no encontrados
            return "Datos no encontrados";
        }
    }


    public function guardarFechaLimiteSolicitudIT(Request $request)
    {

        InformacionGeneral::where('id', 1)->update([
            'fecha_it' => $request->fecha,
        ]);

        return ['success' => 1];
    }


    public function actualizarTabla(Request $request){

        DB::beginTransaction();

        try {

            DB::table('p_materiales AS pm')
                ->join('tablatemporal AS tt', 'pm.id', '=', 'tt.id')
                ->update([
                    'pm.costo' => DB::raw('tt.costo')
                ]);

            DB::commit();
            return ['success' => 1];
        } catch (\Throwable $e) {
            Log::info('error: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }




}

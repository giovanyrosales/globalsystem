<?php

namespace App\Http\Controllers\Backend\Sindico;

use App\Http\Controllers\Controller;
use App\Models\Adescos;
use App\Models\SindicoEstado;
use App\Models\SindicoInmueble;
use App\Models\SindicoRegistro;
use App\Models\SindicoTipoDeligencia;
use App\Models\SindicoTipoSolicitud;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SindicoController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function indexEstado(){
        return view('backend.admin.sindico.estados.vistaestado');
    }

    public function tablaEstado(){

        $listado = SindicoEstado::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.sindico.estados.tablaestado', compact('listado'));
    }

    public function nuevoEstado(Request $request){

        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();
        try {

            $registro = new SindicoEstado();
            $registro->nombre = $request->nombre;
            $registro->save();

            DB::commit();
            return ['success' => 1];

        }catch(\Throwable $e){
            Log::info('error: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // informacion
    public function informacionEstado(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = SindicoEstado::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar
    public function actualizarEstado(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(SindicoEstado::where('id', $request->id)->first()){

            SindicoEstado::where('id', $request->id)->update([
                'nombre' => $request->nombre,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }



    //*********************** TIPO DE SOLICITUD ***************************************


    public function indexTipoSolicitud(){
        return view('backend.admin.sindico.tiposolicitud.vistatiposolicitud');
    }

    public function tablaTipoSolicitud(){

        $listado = SindicoTipoSolicitud::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.sindico.tiposolicitud.tablatiposolicitud', compact('listado'));
    }

    public function nuevoTipoSolicitud(Request $request){

        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();
        try {

            $registro = new SindicoTipoSolicitud();
            $registro->nombre = $request->nombre;
            $registro->save();

            DB::commit();
            return ['success' => 1];

        }catch(\Throwable $e){
            Log::info('error: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // informacion
    public function informacionTipoSolicitud(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = SindicoTipoSolicitud::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar
    public function actualizarTipoSolicitud(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(SindicoTipoSolicitud::where('id', $request->id)->first()){

            SindicoTipoSolicitud::where('id', $request->id)->update([
                'nombre' => $request->nombre,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }






    //*********************** TIPO DE SOLICITUD ***************************************


    public function indexInmueble(){
        return view('backend.admin.sindico.inmueble.vistaestadoinmueble');
    }

    public function tablaInmueble(){

        $listado = SindicoInmueble::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.sindico.inmueble.tablaestadoinmueble', compact('listado'));
    }

    public function nuevoInmueble(Request $request){

        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();
        try {

            $registro = new SindicoInmueble();
            $registro->nombre = $request->nombre;
            $registro->save();

            DB::commit();
            return ['success' => 1];

        }catch(\Throwable $e){
            Log::info('error: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // informacion
    public function informacionInmueble(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = SindicoInmueble::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar
    public function actualizarInmueble(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(SindicoInmueble::where('id', $request->id)->first()){

            SindicoInmueble::where('id', $request->id)->update([
                'nombre' => $request->nombre,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }








//*********************** TIPO DE DELIGENCIA ***************************************


    public function indexTipoDeligencia(){
        return view('backend.admin.sindico.tipodeligencia.vistatipodeligencia');
    }

    public function tablaTipoDeligencia(){

        $listado = SindicoTipoDeligencia::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.sindico.tipodeligencia.tablatipodeligencia', compact('listado'));
    }

    public function nuevoTipoDeligencia(Request $request){

        $regla = array(
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();
        try {

            $registro = new SindicoTipoDeligencia();
            $registro->nombre = $request->nombre;
            $registro->save();

            DB::commit();
            return ['success' => 1];

        }catch(\Throwable $e){
            Log::info('error: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    // informacion
    public function informacionTipoDeligencia(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = SindicoTipoDeligencia::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    // editar
    public function actualizarTipoDeligencia(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(SindicoTipoDeligencia::where('id', $request->id)->first()){

            SindicoTipoDeligencia::where('id', $request->id)->update([
                'nombre' => $request->nombre,
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }







    // ****** REGISTRO DE DATOS *****


    public function indexRegistroDatos(){

        $arrayTipoSoli = SindicoTipoSolicitud::orderBy('nombre', 'ASC')->get();

        $contador = 1;
        foreach ($arrayTipoSoli as $dato){
            $dato->nombre = $contador . "- " . $dato->nombre;
            $contador++;
        }

        $arrayEstados = SindicoEstado::orderBy('nombre', 'ASC')->get();
        $arrayTipoDeligencia = SindicoTipoDeligencia::orderBy('nombre', 'ASC')->get();
        $arrayAdesco = Adescos::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.sindico.registro.vistaregistro',
            compact('arrayTipoSoli', 'arrayEstados',
                 'arrayTipoDeligencia', 'arrayAdesco'));
    }



    public function registroDatosSindicatura(Request $request)
    {
        $tipoSolicitud = $request->tipoSolicitud;
        $registro = new SindicoRegistro();
        DB::beginTransaction();

        try {

            $fecha = Carbon::now('America/El_Salvador')->toDateString();
            $registro->fecha_general = $fecha;

            if($tipoSolicitud == 1){

               $registro->id_tiposolicitud = 1;
               $registro->fecha_reunion = $request->fechaReunion;
               $registro->asesoria = $request->asesoria;
               $registro->id_estado = $request->estado;
               $registro->fecha_informe = $request->fechaInforme;
               $registro->save();
               DB::commit();
               return ['success' => 1];
            }
            else if($tipoSolicitud == 2){

                $registro->id_tiposolicitud = 2;
                $registro->fecha_inspeccion = $request->fechaInscripcion;
                $registro->ubicacion = $request->ubicacion;
                $registro->zonas_pendientes = $request->zonaPendientes;
                $registro->save();
                DB::commit();
                return ['success' => 1];
            }
            else if($tipoSolicitud == 3){

                $registro->id_tiposolicitud = 3;
                $registro->matricula = $request->matricula;
                $registro->fecha_reunion = $request->fechaInicio;
                $registro->id_estado = $request->estado;
                $registro->fecha_legalizacion = $request->fechaLegalizacion;
                $registro->inmueble = $request->inmueble;
                $registro->save();
                DB::commit();
                return ['success' => 1];
            }
            else if($tipoSolicitud == 4){

                $registro->id_tiposolicitud = 4;
                $registro->inmueble = $request->inmueble;
                $registro->fecha_legalizacion = $request->fechaRealizacion;
                $registro->realizado_por = $request->realizadoPor;
                $registro->monto = $request->monto;
                $registro->save();
                DB::commit();
                return ['success' => 1];
            }
            else if($tipoSolicitud == 5){ // DILIGENCIA DE JURISDICCION VOLUNTARIA

                $registro->id_tiposolicitud = 5;
                $registro->id_tipodeligencia = $request->tipoDeligencia;
                $registro->fecha_recepcion = $request->fechaRecepcion;
                $registro->nombre_solicitante = $request->nombreSolicitante;
                $registro->dui_solicitante = $request->duiSolicitante;
                $registro->fecha_revision = $request->fechaRevision;
                $registro->observacion = $request->observacion;
                $registro->fecha_emision_diligencia = $request->fechaEmision;
                $registro->fecha_entrega = $request->fechaEntrega;
                $registro->recibe = $request->recibe;
                $registro->nombre = $request->nombre;
                $registro->dui = $request->dui;
                $registro->save();
                DB::commit();
                return ['success' => 1];
            }
            else if($tipoSolicitud == 6){ // SOLICITUDES DE ADESCO

                $registro->id_tiposolicitud = 6;
                $registro->id_adesco = $request->adesco;
                $registro->id_estado = $request->estado;
                $registro->fecha_finalizacion = $request->fechaFinalizacion;
                $registro->observacion = $request->observacion;
                $registro->save();
                DB::commit();
                return ['success' => 1];

            }
            else if($tipoSolicitud == 7){ // INSPECCION DE INMUEBLE

                $registro->id_tiposolicitud = 7;
                $registro->id_tipodeligencia = $request->tipoDiligencia;
                $registro->fecha_recepcion = $request->fechaRecepcion;
                $registro->nombre = $request->nombre;
                $registro->dui = $request->dui;
                $registro->fecha_inspeccion = $request->fechaInspeccion;
                $registro->nombre_tecnico = $request->nombreTecnico;
                $registro->resultado = $request->resultadoInspeccion;
                $registro->fecha_emision_diligencia = $request->fechaEmision;
                $registro->fecha_entrega = $request->fechaDiligencia;
                $registro->save();
                DB::commit();
                return ['success' => 1];
            }
            else if($tipoSolicitud == 8){

                $registro->id_tiposolicitud = 8;
                $registro->fecha_recepcion = $request->fechaRecepcion;
                $registro->nombre_tecnico = $request->nombreEncargado;
                $registro->informe_meses = $request->informeMeses;
                $registro->monto = $request->monto;
                $registro->observacion = $request->observacion;
                $registro->save();
                DB::commit();
                return ['success' => 1];
            }
            else if($tipoSolicitud == 9){

                $registro->id_tiposolicitud = 9;
                $registro->fecha_recepcion = $request->fechaRecepcion;
                $registro->asesoria = $request->encargadoRemitir;
                $registro->numero_empresas = $request->numeroEmpresa;
                $registro->numero_inmuebles = $request->numeroInmueble;
                $registro->monto = $request->montoTotal;
                $registro->save();
                DB::commit();
                return ['success' => 1];
            }
            else if($tipoSolicitud == 10){

                $registro->id_tiposolicitud = 10;
                $registro->fecha_revision = $request->fechaRevision;
                $registro->total_doc = $request->totalDocumentos;
                $registro->total_doc_aprobados = $request->totalDocumentosApro;
                $registro->save();
                DB::commit();
                return ['success' => 1];
            }
            else{
                return ['success' => 0];
            }
        } catch (\Throwable $e) {
            Log::info('ee ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }




    public function indexTodosRegistros()
    {
        $listado = SindicoTipoSolicitud::orderBy('nombre', 'asc')->get();
        $contador = 1;
        foreach ($listado as $dato){
            $dato->nombre = $contador . "- " . $dato->nombre;
            $contador++;
        }

        return view('backend.admin.sindico.registro.todos.vistaregistrotodos', compact('listado'));
    }


    public function tablaTodosRegistros($id)
    {
        $listado = SindicoRegistro::where('id_tiposolicitud', $id)
            ->orderBy('fecha_general', 'DESC')
            ->get();

        foreach ($listado as $registro) {

            $dato = SindicoTipoSolicitud::where('id', $registro->id_tiposolicitud)->first();
            $registro->solicitud = $dato->nombre;
            $registro->fecha_general = date("d-m-Y", strtotime($registro->fecha_general));

            if($registro->fecha_inspeccion != null){
                $registro->fecha_inspeccion = date("d-m-Y", strtotime($registro->fecha_inspeccion));
            }

            if($registro->fecha_emision_diligencia != null){
                $registro->fecha_emision_diligencia = date("d-m-Y", strtotime($registro->fecha_emision_diligencia));
            }

            if($registro->fecha_entrega != null){
                $registro->fecha_entrega = date("d-m-Y", strtotime($registro->fecha_entrega));
            }


        }

        if($id == 5){ // DILIGENCIA DE JURISDICCION VOLUNTARIA
            return view('backend.admin.sindico.registro.todos.bloque.tablabloque5', compact('listado'));
        }
        else if($id == 7){ // INSPECCION DE INMUEBLE
            return view('backend.admin.sindico.registro.todos.bloque.tablabloque7', compact('listado'));
        }

        return view('backend.admin.sindico.registro.todos.tablaregistrotodos', compact('listado'));
    }


    public function borrarDatosSindicatura(Request $request)
    {
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        SindicoRegistro::where('id', $request->id)->delete();

        return ['success' => 1];
    }



    public function informacionDatosSindicatura(Request $request)
    {

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $info = SindicoRegistro::where('id', $request->id)->first();

        $arrayEstado = SindicoEstado::orderBy('nombre', 'ASC')->get();
        $arrayDiligencia = SindicoTipoDeligencia::orderBy('nombre', 'ASC')->get();
        $arrayAdesco = Adescos::orderBy('nombre', 'ASC')->get();

        return ['success' => 1, 'info' => $info, 'arrayEstado' => $arrayEstado,
            'arrayDiligencia' => $arrayDiligencia, 'arrayAdesco' => $arrayAdesco];
    }


    public function editarDatosSindicatura(Request $request)
    {
        $regla = array(
            'idGlobal' => 'required',
            'idTipoVista' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $idTipoVista = $request->idTipoVista;
        $idGlobal = $request->idGlobal;

        if($idTipoVista == 1){
            SindicoRegistro::where('id', $idGlobal)->update([
                'fecha_general' => $request->fechaRegistro,
                'fecha_reunion' => $request->fechaReunion,
                'asesoria' => $request->asesoria,
                'fecha_informe' => $request->fechaInforme,
                'id_estado' => $request->estado,
            ]);
        }
        else if($idTipoVista == 2){

            SindicoRegistro::where('id', $idGlobal)->update([
                'fecha_general' => $request->fechaRegistro,
                'fecha_inspeccion' => $request->fechaInscripcion,
                'ubicacion' => $request->ubicacion,
                'zonas_pendientes' => $request->zonasPendientes,
            ]);
        }
        else if($idTipoVista == 3){

            SindicoRegistro::where('id', $idGlobal)->update([
                'fecha_general' => $request->fechaRegistro,
                'matricula' => $request->matricula,
                'fecha_reunion' => $request->fechaInicio,
                'id_estado' => $request->estado,
                'fecha_legalizacion' => $request->fechaLegalizacion,
                'inmueble' => $request->inmueble,
            ]);
        }
        else if($idTipoVista == 4){

            SindicoRegistro::where('id', $idGlobal)->update([
                'fecha_general' => $request->fechaRegistro,
                'inmueble' => $request->inmueble,
                'fecha_legalizacion' => $request->fechaRealizacion,
                'realizado_por' => $request->realizadoPor,
                'monto' => $request->monto,
            ]);
        }
        else if($idTipoVista == 5){

            SindicoRegistro::where('id', $idGlobal)->update([
                'fecha_general' => $request->fechaRegistro,
                'id_tipodeligencia' => $request->tipoDeligencia,
                'fecha_recepcion' => $request->fechaRecepcion,
                'nombre_solicitante' => $request->nombreSolicitante,
                'dui_solicitante' => $request->duiSolicitante,
                'fecha_revision' => $request->fechaRevision,
                'observacion' => $request->observacion,
                'fecha_emision_diligencia' => $request->fechaEmision,
                'fecha_entrega' => $request->fechaEntrega,
                'recibe' => $request->recibe,
                'nombre' => $request->nombre,
                'dui' => $request->dui,
            ]);
        }
        else if($idTipoVista == 6){

            SindicoRegistro::where('id', $idGlobal)->update([
                'fecha_general' => $request->fechaRegistro,
                'id_adesco' => $request->adesco,
                'id_estado' => $request->estado,
                'fecha_finalizacion' => $request->fechaFinalizacion,
                'observacion' => $request->observacion,
            ]);
        }
        else if($idTipoVista == 7){

            SindicoRegistro::where('id', $idGlobal)->update([
                'fecha_general' => $request->fechaRegistro,
                'id_tipodeligencia' => $request->tipoDiligencia,
                'fecha_recepcion' => $request->fechaRecepcion,
                'nombre' => $request->nombre,
                'dui' => $request->dui,
                'fecha_inspeccion' => $request->fechaInspeccion,
                'nombre_tecnico' => $request->nombreTecnico,
                'resultado' => $request->resultadoInspeccion,
                'fecha_emision_diligencia' => $request->fechaEmision,
                'fecha_entrega' => $request->fechaDiligencia,
            ]);
        }
        else if($idTipoVista == 8){

            SindicoRegistro::where('id', $idGlobal)->update([
                'fecha_general' => $request->fechaRegistro,
                'fecha_recepcion' => $request->fechaRecepcion,
                'nombre_tecnico' => $request->nombreEncargado,
                'informe_meses' => $request->informeMeses,
                'monto' => $request->monto,
                'observacion' => $request->observaciones,
            ]);
        }
        else if($idTipoVista == 9){

            SindicoRegistro::where('id', $idGlobal)->update([
                'fecha_general' => $request->fechaRegistro,
                'fecha_recepcion' => $request->fechaRecepcion,
                'asesoria' => $request->encargadoRemitir,
                'numero_empresas' => $request->numeroEmpresa,
                'numero_inmuebles' => $request->numeroInmueble,
                'monto' => $request->montoTotal,
            ]);
        }
        else if($idTipoVista == 10){

            SindicoRegistro::where('id', $idGlobal)->update([
                'fecha_general' => $request->fechaRegistro,
                'fecha_revision' => $request->fechaRevision,
                'total_doc' => $request->totalDoc,
                'total_doc_aprobados' => $request->totalDocApro,
            ]);
        }

        return ['success' => 1];
    }

}

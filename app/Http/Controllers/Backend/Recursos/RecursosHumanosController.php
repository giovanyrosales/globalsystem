<?php

namespace App\Http\Controllers\Backend\Recursos;

use App\Http\Controllers\Controller;
use App\Models\RRHHcargo;
use App\Models\RRHHDatos;
use App\Models\RRHHDatosTabla;
use App\Models\RRHHempleados;
use App\Models\RRHHenfermedades;
use App\Models\RRHHunidad;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecursosHumanosController extends Controller
{
    // SERA PUBLICO PARA INGRESAR DATOS DE RECURSOS HUMANOS

    public function vistaIngresoDatos(){

        $listaEmpleados = RRHHempleados::orderBy('nombre', 'ASC')->get();
        $listaCargos = RRHHcargo::orderBy('nombre', 'ASC')->get();
        $listaUnidad = RRHHunidad::orderBy('nombre', 'ASC')->get();
        $listaEnfermedad = RRHHenfermedades::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.rrhhdatos.vistahojaactualizacion', compact('listaEmpleados',
        'listaCargos', 'listaUnidad', 'listaEnfermedad'));
    }


    public function guardarIngresoDatos(Request  $request){

        DB::beginTransaction();

        try {

            $registro = new RRHHDatos();

            $registro->fecha = Carbon::now('America/El_Salvador');

            if($request->empleadoCheck == 1){
                $registro->nombre = $request->nombreNuevo;
                $registro->id_empleado = null;
            }else{
                $registro->id_empleado = $request->selectNombre;
                $registro->nombre = null;
            }

            $registro->check_empleado = $request->empleadoCheck;

            $registro->id_cargo = $request->selectCargos;
            $registro->id_unidad = $request->selectUnidad;
            $registro->dui = $request->dui;
            $registro->nit = $request->nit;
            $registro->fecha_nacimiento = $request->fechaNacimiento;
            $registro->lugar_nacimiento = $request->lugarNacimiento;
            $registro->select_academico = $request->selectAcademica;
            $registro->profesion = $request->profesion;
            $registro->direccion_actual = $request->direccionActual;
            $registro->celular = $request->celular;
            $registro->emergencia_llamar = $request->emergenciasLlamar;
            $registro->celular_emergencia = $request->celularEmergencia;


            if($request->enfermedadCheck == 1){
                $registro->enfermedad_nuevo = $request->enfermedadNuevo;
                $registro->id_enfermedad = null;
            }else{
                $registro->id_enfermedad = $request->selectEnfermedad;
                $registro->enfermedad_nuevo = null;
            }

            $registro->enfermedad_check = $request->enfermedadCheck;
            $registro->save();



            $datosContenedor = json_decode($request->contenedorArray, true);


            foreach ($datosContenedor as $filaArray) {

                $detalle = new RRHHDatosTabla();
                $detalle->id_datos = $registro->id;
                $detalle->nombre = $filaArray['infoNombre'];
                $detalle->parentesco = $filaArray['infoParentesco'];
                $detalle->porcentaje = $filaArray['infoPorcentaje'];
                $detalle->save();
            }

            DB::commit();
            return ['success' => 1];

        } catch (\Throwable $e) {
            Log::info('ee ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }
}

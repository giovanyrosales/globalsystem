<?php

namespace App\Http\Controllers\Backend\Configuracion\Referencias;

use App\Http\Controllers\Controller;
use App\Models\Referencias;
use App\Models\RRHHcargo;
use App\Models\RRHHDatos;
use App\Models\RRHHDatosTabla;
use App\Models\RRHHempleados;
use App\Models\RRHHenfermedades;
use App\Models\RRHHunidad;
use App\Models\SecretariaDespacho;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ReferenciasController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    public function indexReferencia(){
        return view('backend.admin.configuraciones.referencias.vistareferencias');
    }


    public function tablaReferencia(){
        $lista = Referencias::orderBy('nombre')->get();
        return view('backend.admin.configuraciones.referencias.tablareferencias', compact('lista'));
    }


    public function nuevaReferencia(Request $request){

        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new Referencias();
        $dato->nombre = $request->nombre;
        $dato->save();

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }

    }


    public function informacionReferencia(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Referencias::where('id', $request->id)->first()){


            return ['success' => 1, 'lista' => $lista];
        }else{
            return ['success' => 2];
        }
    }


    public function editarReferencia(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        Referencias::where('id', $request->id)->update([
            'nombre' => $request->nombre,
        ]);


        return ['success' => 1];
    }




    public function indexSecreDespacho(){

        $fecha = Carbon::now('America/El_Salvador')->toDateString();;

        return view('backend.admin.secredespacho.despacho.vistadespacho', compact('fecha'));
    }



    public function tablaSecreDespacho(){

        $listado = SecretariaDespacho::orderBy('fecha', 'DESC')->get();

        foreach ($listado as $dato){

            $dato->fechaFormat = date("d-m-Y", strtotime($dato->fecha));

            $tiposoli = "";

            if($dato->tiposolicitud == 1){
                $tiposoli = "Vivienda Completa";
            }
            else if($dato->tiposolicitud == 2){
                $tiposoli = "Solo Vivienda";
            }
            else if($dato->tiposolicitud == 3){
                $tiposoli = "Materiales de Construcción";
            }
            else if($dato->tiposolicitud == 4){
                $tiposoli = "Viveres";
            }
            else if($dato->tiposolicitud == 5){
                $tiposoli = "Construcción";
            }
            else if($dato->tiposolicitud == 6){
                $tiposoli = "Proyecto";
            }
            else if($dato->tiposolicitud == 7){
                $tiposoli = "Afectaciones de la Vista";
            }


            $dato->tiposoli = $tiposoli;
        }

        return view('backend.admin.secredespacho.despacho.tabladespacho', compact('listado'));
    }


    public function guardarSecreDespacho(Request $request){

        $regla = array(
            'fecha' => 'required',
            'nombre' => 'required',
        );

        // telefono, direccion, editor, tiposolicitud

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();

        try {

            $dato = new SecretariaDespacho();
            $dato->nombre = $request->nombre;
            $dato->dui = $request->dui;
            $dato->fecha = $request->fecha;
            $dato->telefono = $request->telefono;
            $dato->direccion = $request->direccion;
            $dato->descripcion = $request->editor;
            $dato->tiposolicitud = $request->tiposolicitud;
            $dato->save();

            DB::commit();
            return ['success' => 1];
        }catch(\Throwable $e){

            DB::rollback();
            return ['success' => 99];
        }

    }



    public function borrarSecreDespacho(Request $request){

        $regla = array(
            'id' => 'required',
        );

        // telefono, direccion, editor

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(SecretariaDespacho::where('id', $request->id)->first()){
            SecretariaDespacho::where('id', $request->id)->delete();
        }

        return ['success' => 1];
    }



    public function informacionSecreDespacho(Request $request){

        $regla = array(
            'id' => 'required',
        );

        // telefono, direccion, editor

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($info = SecretariaDespacho::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $info];
        }

        return ['success' => 2];
    }



    public function editarSecreDespacho(Request $request){

        $regla = array(
            'fecha' => 'required',
            'nombre' => 'required',
        );

        // telefono, direccion, editor, tiposolicitud

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();

        try {

            SecretariaDespacho::where('id', $request->id)->update([
                'fecha' => $request->fecha,
                'nombre' => $request->nombre,
                'dui' => $request->dui,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'descripcion' => $request->editor,
                'tiposolicitud' => $request->tiposolicitud
            ]);

            DB::commit();
            return ['success' => 1];
        }catch(\Throwable $e){
            Log::info('err: ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }




    public function indexReportes(){

        return view('backend.admin.secredespacho.reportes.vistareportedespacho');
    }


    public function reporteDespachoSecretaria($desde, $hasta, $tipo){

        $solicitud = "";

        if($tipo == 1){
            $solicitud = "Vivienda Completa";
        }
        else if($tipo == 2){
            $solicitud = "Solo Vivienda";
        }
        else if($tipo == 3){
            $solicitud = "Materiales de Construcción";
        }
        else if($tipo == 4){
            $solicitud = "Viveres";
        }
        if($tipo == 5){
            $solicitud = "Construcción";
        }
        if($tipo == 6){
            $solicitud = "Proyecto";
        }
        if($tipo == 7){
            $solicitud = "Afectaciones de la Vista";
        }


        $start = Carbon::parse($desde)->startOfDay();
        $end = Carbon::parse($hasta)->endOfDay();

        $desdeFormat = date("d-m-Y", strtotime($desde));
        $hastaFormat = date("d-m-Y", strtotime($hasta));


        $arrayDespacho = SecretariaDespacho::where('tiposolicitud', $tipo)
            ->whereBetween('fecha', [$start, $end])
            ->orderBy('fecha', 'ASC')
            ->get();

        $vuelta = 0;
        foreach ($arrayDespacho as $dato){
            $vuelta = $vuelta + 1;
            $dato->vuelta = $vuelta;

            $fechaFormat = date("d-m-Y", strtotime($dato->fecha));

            $dato->fechaFormat = $fechaFormat;
        }


        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);

        $mpdf->SetTitle('Listado Solicitudes');



        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/logonuevo.png';

        $tabla = "<div class='contenedorp'>
            <img id='logo' src='$logoalcaldia' style='width: 150px !important;'>
            <p id='titulo'>Distrito de Metapán<br>

            Solicitud: $solicitud <br> <br>

            Fecha:  $desdeFormat - $hastaFormat</p>
            </div>";


            foreach ($arrayDespacho as $dato){

                if($dato->vuelta > 1){
                    $tabla .= "<hr>";
                }

                $tabla .= "<table width='100%' id='tablaFor'>
                <tbody>";

                $tabla .= "<tr>
                    <td style='font-weight: bold; width: 11%; font-size: 14px'>Fecha.</td>
                    <td style='font-weight: bold; width: 11%; font-size: 14px'>Nombre</td>
                    <td style='font-weight: bold; width: 12%; font-size: 14px'>Teléfono</td>
                    <td style='font-weight: bold; width: 12%; font-size: 14px'>Tipo Solicitud</td>
            <tr>";

                $tabla .= "<tr>
                <td>$dato->fechaFormat</td>
                <td>$dato->nombre</td>
                <td>$dato->telefono</td>
                <td>$solicitud</td>
                </tr>";

                $tabla .= "</tbody></table>";

                $tabla .= "<div style='margin-top: 5px'>
                <p><strong>Dirección: $dato->direccion</strong><br>
                </div>";

                    $tabla .= "<div style='margin-top: 5px'>
                <p><strong>Descripción: $dato->descripcion</strong><br>
                </div>";


            }


        $stylesheet = file_get_contents('css/csspresupuesto.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla,2);

        $mpdf->Output();
    }



    //*************** RECURSOS HUMANOS PARA ACTUALIZACION DE DATOS DE PERSONAL ****************

    public function indexRRHHDatosHoja(){

        return view('backend.admin.rrhhdatos.privado.vistalistadorrhhdatos');
    }

    public function tablaRRHHDatosHoja(){

        $listado = RRHHDatos::orderBy('fecha', 'DESC')->get();

        foreach ($listado as $dato){

            $dato->fechahora = date("d-m-Y h:i A", strtotime($dato->fecha));

            if($dato->check_empleado == 1){
                $nombreFormat = $dato->nombre;
            }else{
                $infoEmpleado = RRHHempleados::where('id', $dato->id_empleado)->first();
                $nombreFormat = $infoEmpleado->nombre;
            }

            $dato->nombreFormat = $nombreFormat;

            $infoCargo = RRHHcargo::where('id', $dato->id_cargo)->first();
            $infoUnidad = RRHHunidad::where('id', $dato->id_unidad)->first();

            $dato->cargo = $infoCargo->nombre;
            $dato->unidad = $infoUnidad->nombre;

            $dato->edad = Carbon::parse($dato->fecha_nacimiento)->age;
        }

        return view('backend.admin.rrhhdatos.privado.tablalistadorrhhdatos', compact('listado'));
    }



    public function RRHHDatosReporte($id){

       $infoDatos = RRHHDatos::where('id', $id)->first();
       $arrayTabla = RRHHDatosTabla::where('id_datos', $id)->get();

        if($infoDatos->check_empleado == 1){
            $nombreFormat = $infoDatos->nombre;
        }else{
            $infoEmpleado = RRHHempleados::where('id', $infoDatos->id_empleado)->first();
            $nombreFormat = $infoEmpleado->nombre;
        }

        $infoCargo = RRHHcargo::where('id', $infoDatos->id_cargo)->first();

        $infoUnidad = RRHHunidad::where('id', $infoDatos->id_unidad)->first();

        if (empty($infoDatos->nit)) {
            $nit = $infoDatos->dui;
        }else{
            $nit = $infoDatos->nit;
        }

        $fechaServer = date("d-m-Y", strtotime($infoDatos->fecha));
        $fechaNacimiento = date("d-m-Y", strtotime($infoDatos->fecha_nacimiento));


        $edad = Carbon::parse($infoDatos->fecha_nacimiento)->age;

        if($infoDatos->select_academico == 1){
            $academico = "BASICO";
        }else if($infoDatos->select_academico == 2){
            $academico = "MEDIO";
        } else if($infoDatos->select_academico == 3){
            $academico = "SUPERIOR";
        }else{
            $academico = "";
        }

        if($infoDatos->enfermedad_check == 1){
            $enfermedad = $infoDatos->enfermedad_nuevo;
        }else{
            $infoEnfe = RRHHenfermedades::where('id', $infoDatos->id_enfermedad)->first();
            $enfermedad = $infoEnfe->nombre;
        }





       //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
       $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);

       $mpdf->SetTitle('Listado ');


       // mostrar errores
       $mpdf->showImageErrors = false;

       $logoalcaldia = 'images/logonuevo.png';

       $tabla = "<p class='fechaTop'>Fecha: <span style='font-weight: normal'>$fechaServer</span></p>";

       $tabla .= "<div class='contenedorp'>
            <img id='logo' src='$logoalcaldia' style='width: 150px !important;'>
            <p id='titulo' >ALCALDIA MUNICIPAL DE SANTA ANA NORTE<br>
                DISTRITO DE METAPÁN </p>
            </div>";


       $tabla .= "<p class='subtitulo1'>HOJA DE ACTUALIZACIÓN DE DATOS DE PERSONAL</p>";

        $tabla .= "<table width='80%' id='tablaFor'>
                <tbody>";

        $tabla .= "<tr>
                <td style='text-align: left !important;' colspan='2'>
                    <span class='trFila'>NOMBRE DEL EMPLEADO: </span>
                    <span class='trFila2'>$nombreFormat</span>
                </td>
            </tr>";


        $tabla .= "<tr>
            <td class='trFila' style='width: 50%; text-align: left;'>
                <strong class='trFila'>CARGO:</strong>
                <strong style='font-weight: normal;'>$infoCargo->nombre</strong>
            </td>

            <td class='trFila2' style='width: 20%; text-align: left;'>
                    <strong class='trFila'>DUI: </strong>
                    <strong class='trFila2' style='font-weight: normal !important;'>$infoDatos->dui</strong>
            </td>

           </tr>";


        $tabla .= "<tr>
            <td class='trFila' style='width: 50%; text-align: left;'>
                <strong class='trFila'>UNIDAD:</strong>
                <strong style='font-weight: normal;'>$infoUnidad->nombre</strong>
            </td>

            <td class='trFila2' style='width: 20%; text-align: left;'>
                    <strong class='trFila'>NIT: </strong>
                    <strong class='trFila2' style='font-weight: normal !important;'>$nit</strong>
            </td>

           </tr>";

        $tabla .= "</tbody></table>";


        $tabla .= "<p class='subtitulo1'>INFORMACIÓN PARTICULAR</p>";



        $tabla .= "<table width='80%' id='tablaFor'><tbody>
            <tr>
                <td style='text-align: left; border: 1px solid black; padding: 8px; width: 80%'>
                    <strong class='estilo1'>FECHA Y LUGAR DE NACIMIENTO:</strong>
                    <strong class='estilo2'>$fechaNacimiento $infoDatos->lugar_nacimiento</strong>
                </td>
                <td style=' text-align: left; border: 1px solid black; padding: 8px;'>
                        <strong style='font-weight: bold'>EDAD: </strong>
                        <strong style='font-weight: normal'>$edad</strong>
                </td>
            </tr>
           </tbody></table>";


        $tabla .= "<table width='80%' id='tablaFor2'><tbody>
            <tr>
                <td style='text-align: left;  padding: 8px; width: 45%'>
                    <strong class='estilo1'>NIVEL ACADÉMICO: </strong>
                    <strong class='estilo2'>$academico</strong>
                </td>
                <td style=' text-align: left;  padding: 8px;'>
                        <strong style='font-weight: bold'>PROFESIÓN: </strong>
                        <strong style='font-weight: normal'>$infoDatos->profesion</strong>
                </td>
               </tr>

                <tr>
                <td colspan='2' style='text-align: left;  padding: 8px;'>
                    <strong class='estilo1'>DIRECCIÓN ACTUAL: </strong>
                    <strong style='font-weight: normal'>$infoDatos->direccion_actual</strong>
                </td>
               </tr>

                <tr>
                <td colspan='2' style='text-align: left; padding: 8px;'>
                    <strong class='estilo1'>CELULAR: </strong>
                    <strong style='font-weight: normal'>$infoDatos->celular</strong>
                </td>
               </tr>
           </tbody></table>";


        $tabla .= "<table width='80%' id='tablaFor2'><tbody>
            <tr>
                <td style='text-align: left;  padding: 8px; width: 65%'>
                    <strong class='estilo1'>EN EMERGENCIAS LLAMAR A: </strong>
                    <strong class='estilo2'>$infoDatos->emergencia_llamar</strong>
                </td>
                <td style=' text-align: left;  padding: 8px;'>
                        <strong style='font-weight: bold'>CELULAR: </strong>
                        <strong style='font-weight: normal'>$infoDatos->celular</strong>
                </td>
               </tr>


               <tr>
                <td colspan='2' style='text-align: left;  padding: 8px;'>
                    <strong class='estilo1'>¿PADECE ALGUNA ENFERMEDAD CRÓNICA O EXISTE ALGUNA CONDICIÓN FÍSICA QUE LE AFECTE? </strong>
                    <strong class='estilo2'>$enfermedad</strong>
                </td>
               </tr>

           </tbody></table>";

        $tabla .= "<p class='subtitulo1' style='margin-top: 40px'>DATOS BENEFICIARIOS</p>";


        $tabla .= "<table width='80%' id='tablaFor'><tbody>";

        $tabla .= "<tr>
                        <td class='tdBold' style='width: 8%'>N°</td>
                        <td class='tdBold' style='width: 35%'>NOMBRE</td>
                        <td class='tdBold' style='width: 30%'>PARENTESCO</td>
                         <td class='tdBold' style='width: 8%'>%</td>
                    </tr>
                    ";

        $conteo = 0;

            foreach ($arrayTabla as $info){
                $conteo++;

                $tabla .= "<tr>
                        <td class='tdNormal' style='width: 8%'>$conteo</td>
                        <td class='tdNormal' style='width: 35%'>$info->nombre</td>
                        <td class='tdNormal' style='width: 30%'>$info->parentesco</td>
                         <td class='tdNormal' style='width: 8%'>$info->porcentaje</td>
                    </tr>
                    ";
            }

        $tabla .="</tbody></table>";


        $stylesheet = file_get_contents('css/cssrrhh.css');
        $mpdf->WriteHTML($stylesheet,1);

       // $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla,2);

        $mpdf->Output();
    }


    public function RRHHDatosBorrar(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        DB::beginTransaction();

        try {

            // borrar tabla
            RRHHDatosTabla::where('id_datos', $request->id)->delete();
            // borrar fila
            RRHHDatos::where('id', $request->id)->delete();


            DB::commit();
            return ['success' => 1];

        } catch (\Throwable $e) {
            Log::info('ee ' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }


}

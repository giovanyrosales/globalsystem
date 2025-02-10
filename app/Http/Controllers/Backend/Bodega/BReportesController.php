<?php

namespace App\Http\Controllers\Backend\Bodega;

use App\Http\Controllers\Controller;
use App\Models\BodegaEntradas;
use App\Models\BodegaEntradasDetalle;
use App\Models\BodegaExtras;
use App\Models\BodegaMateriales;
use App\Models\BodegaSalida;
use App\Models\BodegaSalidaDetalle;
use App\Models\BodegaSolicitud;
use App\Models\BodegaSolicitudDetalle;
use App\Models\BodegaUsuarioObjEspecifico;
use App\Models\ObjEspecifico;
use App\Models\P_Departamento;
use App\Models\P_UnidadMedida;
use App\Models\P_UsuarioDepartamento;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BReportesController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    public function reporteSolicitudCompleta($idsolicitud)
    {
        if($infoSolicitud = BodegaSolicitud::where('id', $idsolicitud)->first()){
            $fechaFormat = date("d-m-Y", strtotime($infoSolicitud->fecha));


            $nombreBodeguero = "";
            if($infoEntrDeta = BodegaUsuarioObjEspecifico::where('id_objespecifico', $infoSolicitud->id_objespecifico)->first()){
                $infoUs = Usuario::where('id', $infoEntrDeta->id_usuario)->first();
                $nombreBodeguero = $infoUs->nombre;
            }


            $arrayDetalle = BodegaSolicitudDetalle::where('id_bodesolicitud', $idsolicitud)->get();
            $infoExtra = BodegaExtras::where('id', 1)->first();
            $infoUsuario = Usuario::where('id', $infoSolicitud->id_usuario)->first();
            $nombreDepartamento = "";
            if($infoUD = P_UsuarioDepartamento::where('id_usuario', $infoSolicitud->id_usuario)->first()){
                $infoDepa = P_Departamento::where('id', $infoUD->id_departamento)->first();
                $nombreDepartamento = $infoDepa->nombre;
            }

            //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
            $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);

            $mpdf->SetTitle('Solicitudes');

            // mostrar errores
            $mpdf->showImageErrors = false;

            $logoalcaldia = 'images/gobiernologo.jpg';
            $logosantaana = 'images/logo.png';

            $tabla = "
            <table style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <!-- Logo izquierdo -->
                    <td style='width: 15%; text-align: left;'>
                        <img src='$logosantaana' alt='Santa Ana Norte' style='max-width: 100px; height: auto;'>
                    </td>
                    <!-- Texto centrado -->
                    <td style='width: 60%; text-align: center;'>
                        <h1 style='font-size: 16px; margin: 0; color: #003366; text-transform: uppercase;'>ALCALDÍA MUNICIPAL DE SANTA ANA NORTE</h1>
                        <h2 style='font-size: 14px; margin: 0; color: #003366; text-transform: uppercase;'>UNIDAD DE PROVEEDURÍA Y BODEGA</h2>
                    </td>
                    <!-- Logo derecho -->
                    <td style='width: 10%; text-align: right;'>
                        <img src='$logoalcaldia' alt='Gobierno de El Salvador' style='max-width: 60px; height: auto;'>
                    </td>
                </tr>
            </table>
            <hr style='border: none; border-top: 2px solid #003366; margin: 0;'>
            ";

            $tabla .= "
            <div style='text-align: center; margin-top: 20px;'>
                <h1 style='font-size: 16px; margin: 0; color: #000;'>SOLICITUD DE INSUMOS EN BODEGA</h1>
                <p style='font-size: 14px; margin: 5px 0; color: #000;'>N° DE SOLICITUD: $idsolicitud</p>
            </div>
            <div style='text-align: right; margin-top: 10px; padding-right: 80px;'>
            <p style='font-size: 14px; margin: 0; color: #000;'>Fecha: $fechaFormat</p>
            </div>
          ";


            $tabla .= "
            <div style='text-align: left; margin-top: 20px; font-family: \"Times New Roman\", Times, serif;'>
                <p style='font-size: 14px; margin: 0; color: #000;'>$infoExtra->nombre_encargado</p>
                <p style='font-size: 14px; margin: 5px 0; color: #000;'>Santa Ana Norte</p>
            </div>
               <div style='text-align: left; margin-top: 20px; font-family: \"Times New Roman\", Times, serif;'>
                <p style='font-size: 14px; margin: 5px 0; color: #000;'>Por medio de la presente, me permito solicitar la entrega de los siguientes materiales,
                los cuales son necesarios para el desarrollo de nuestras actividades dentro de la municipalidad: </p>

                   <p style='font-size: 14px; margin: 5px 0; color: #000;'>
                        <strong>Descripción general:</strong> (ej. Materiales de oficina, papelería, productos de limpieza, etc)
                    </p>
                    <p style='font-size: 14px; margin: 5px 0; color: #000;'>
                    <strong>NOTA: el número y estado de la solicitud será completado por la unidad de proveeduría y bodega.</strong>
                </p>
            </div>
            ";


            $tabla .= "


           <div style='text-align: left; margin-top: 20px; font-family: \"Times New Roman\", Times, serif;'>
                <p style='font-size: 14px; margin: 5px 0; color: #000;'>
                    Quedo a la espera de su confirmación sobre la disponibilidad y fecha de entrega.
                </p>
           </div>
           <br>
        ";


            $tabla .= "<table id='tablaFor' style='width: 100%; border-collapse: collapse;'>
    <tbody>
        <tr>
            <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold; border: 1px solid black;'>N°</th>
            <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold; border: 1px solid black;'>Descripción</th>
            <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold; border: 1px solid black;'>Unidad de medida</th>
            <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold; border: 1px solid black;'>Cantidad solicitada</th>
        </tr>
        ";

            $contaFila = 0;
            foreach ($arrayDetalle as $fila){
                $contaFila++;

                $infoUnidad = P_UnidadMedida::where('id', $fila->id_unidad)->first();

                $tabla .= "<tr>
                    <td style='text-align: center; font-size:13px; border: 1px solid black;'>$contaFila</td>
                    <td style='text-align: center; font-size:13px; border: 1px solid black;'>$fila->nombre</td>
                    <td style='text-align: center; font-size:13px; border: 1px solid black;'>$infoUnidad->nombre</td>
                    <td style='text-align: center; font-size:13px; border: 1px solid black;'>$fila->cantidad</td>
                </tr> ";
            }


            $tabla .= "</tbody></table>";




            $tabla .= "
<table style='width: 100%; margin-top: 30px; font-family: \"Times New Roman\", Times, serif; font-size: 14px; color: #000;'>
    <!-- Fila para los títulos -->
    <tr>
        <td style='width: 50%; text-align: left; padding-bottom: 15px;'>
            <p style='margin: 0; font-weight: bold; margin-left: 15px'>Solicitado por:</p>
        </td>
        <td style='width: 50%; text-align: right; padding-bottom: 15px;'>
            <p style='margin: 0; font-weight: bold; margin-right: 15px'>Autorizado por:</p>
        </td>
    </tr>
    <!-- Fila para los contenidos -->
    <tr>
        <td style='width: 50%; text-align: center; padding: 20px;'>
            <p style='margin: 10px 0;'>f.____________________________</p>
            <p style='margin: 10px 0;'>$infoUsuario->nombre</p>
            <p style='margin: 10px 0;'>Jefe de unidad</p>
            <p style='margin: 10px 0;'>$nombreDepartamento</p>
        </td>
        <td style='width: 50%; text-align: center; padding: 20px;'>
            <p style='margin: 10px 0;'>f.____________________________</p>
            <p style='margin: 10px 0;'>$infoExtra->nombre_gerente</p>
            <p style='margin: 10px 0;'>$infoExtra->nombre_gerente_cargo</p>
            <p style='margin: 10px 0;'></p>
        </td>
    </tr>
</table>
";

                    $tabla .= "
        <table style='width: 100%; margin-top: 30px; font-family: \"Times New Roman\", Times, serif; font-size: 14px; color: #000;'>
            <!-- Fila para los títulos -->
            <tr>
                <td style='width: 50%; text-align: center; padding-bottom: 15px;'>
                    <p style='margin: 0; font-weight: bold; margin-left: 15px'>Recibido por:</p>
                </td>

            </tr>
            <!-- Fila para los contenidos -->
            <tr>
                <td style='width: 50%; text-align: center; padding: 20px;'>
                    <p style='margin: 10px 0;'>f.____________________________</p>
                    <p style='margin: 10px 0;'>$nombreBodeguero</p>
                    <p style='margin: 10px 0;'>Encargado de proveeduría y bodega</p>
                    <p style='margin: 10px 0;'>Fecha: </p>
                    <p style='margin: 10px 0;'>Hora: </p>
                </td>
            </tr>
        </table>
        ";

            $stylesheet = file_get_contents('css/cssbodega.css');
            $mpdf->WriteHTML($stylesheet,1);

            $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
            $mpdf->WriteHTML($tabla,2);

            $mpdf->Output();
        }else{
            return "Solicitud no encontrado";
        }
    }

    public function vistaConfigurarNombreReporte()
    {
        $info = BodegaExtras::where('id', 1)->first();
        return view('backend.admin.bodega.extras.vistaextrareporte', compact('info'));
    }


    public function actualizarConfigurarNombreReporte(Request $request)
    {
        BodegaExtras::where('id', 1)->update([
            'nombre_gerente' => $request->nombreGerente,
            'nombre_gerente_cargo' => $request->nombreGerenteCargo,
        ]);

        return ['success' => 1];
    }



    public function reporteEncargadoBodegaCompleta($idsolicitud)
    {
        if($infoSolicitud = BodegaSolicitud::where('id', $idsolicitud)->first()){
            $fechaFormat = date("d-m-Y", strtotime(Carbon::now('America/El_Salvador')));

            $arrayDetalle = BodegaSolicitudDetalle::where('id_bodesolicitud', $idsolicitud)
                ->where('cantidad_entregada', '>', 0)
                ->orderBy('id', 'ASC')
                ->get();

            $userAutenticado = Auth::user();
            $infoUserLogin = Usuario::where('id', $userAutenticado->id)->first();

            $infoExtra = BodegaExtras::where('id', 1)->first();
            $infoUsuario = Usuario::where('id', $infoSolicitud->id_usuario)->first();
            $nombreDepartamento = "";
            if($infoUD = P_UsuarioDepartamento::where('id_usuario', $infoSolicitud->id_usuario)->first()){
                $infoDepa = P_Departamento::where('id', $infoUD->id_departamento)->first();
                $nombreDepartamento = $infoDepa->nombre;
            }

            //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
            $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);

            $mpdf->SetTitle('Solicitudes');

            // mostrar errores
            $mpdf->showImageErrors = false;

            $logoalcaldia = 'images/gobiernologo.jpg';
            $logosantaana = 'images/logo.png';

            $tabla = "
            <table style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <!-- Logo izquierdo -->
                    <td style='width: 15%; text-align: left;'>
                        <img src='$logosantaana' alt='Santa Ana Norte' style='max-width: 100px; height: auto;'>
                    </td>
                    <!-- Texto centrado -->
                    <td style='width: 60%; text-align: center;'>
                        <h1 style='font-size: 16px; margin: 0; color: #003366; text-transform: uppercase;'>ALCALDÍA MUNICIPAL DE SANTA ANA NORTE</h1>
                        <h2 style='font-size: 14px; margin: 0; color: #003366; text-transform: uppercase;'>UNIDAD DE PROVEEDURÍA Y BODEGA</h2>
                    </td>
                    <!-- Logo derecho -->
                    <td style='width: 10%; text-align: right;'>
                        <img src='$logoalcaldia' alt='Gobierno de El Salvador' style='max-width: 60px; height: auto;'>
                    </td>
                </tr>
            </table>
            <hr style='border: none; border-top: 2px solid #003366; margin: 0;'>
            ";

            $tabla .= "
            <div style='text-align: center; margin-top: 20px;'>
                <h1 style='font-size: 16px; margin: 0; color: #000;'>ENTREGA DE INSUMOS DE BODEGA</h1>
                <p style='font-size: 14px; margin: 5px 0; color: #000;'>N° DE SOLICITUD: $idsolicitud</p>
            </div>
            <div style='text-align: right; margin-top: 10px; padding-right: 80px;'>
            <p style='font-size: 14px; margin: 0; color: #000;'>Fecha: $fechaFormat</p>
        </div>
      ";


            $tabla .= "
            <div style='text-align: left; margin-top: 20px; font-family: \"Times New Roman\", Times, serif;'>
                <p style='font-size: 14px; margin: 0; color: #000;'>$infoUserLogin->nombre</p>
                <p style='font-size: 14px; margin: 5px 0; color: #000;'>Encargado de proveeduría y bodega</p>
                <p style='font-size: 14px; margin: 5px 0; color: #000;'>Santa Ana Norte</p>
            </div>
               <div style='text-align: left; margin-top: 20px; font-family: \"Times New Roman\", Times, serif;'>
                <p style='font-size: 14px; margin: 5px 0; color: #000;'>Por medio de la presente, me permito solicitar la entrega de los siguientes materiales,
                los cuales son necesarios para el desarrollo de nuestras actividades dentro de la municipalidad: </p>

                   <p style='font-size: 14px; margin: 5px 0; color: #000;'>
                        <strong>Descripción general:</strong> (ej. Materiales de oficina, papelería, productos de limpieza, etc)
                    </p>
                    <p style='font-size: 14px; margin: 5px 0; color: #000;'>
                    <strong>NOTA: el número y estado de la solicitud será completado por la unidad de proveeduría y bodega.</strong>
                </p>
            </div>
            ";


            $tabla .= "


           <div style='text-align: left; margin-top: 20px; font-family: \"Times New Roman\", Times, serif;'>
                <p style='font-size: 14px; margin: 5px 0; color: #000;'>
                    Quedo a la espera de su confirmación sobre la disponibilidad y fecha de entrega.
                </p>
           </div>
           <br>
        ";


            $tabla .= "<table id='tablaFor' style='width: 100%; border-collapse: collapse;'>
    <tbody>
        <tr>
            <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold; border: 1px solid black; width: 3%'>N°</th>
            <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold; border: 1px solid black;'>Descripción</th>
            <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold; border: 1px solid black; width: 4%'>U. Medida</th>
            <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold; border: 1px solid black; width: 4%'>C. Entregada</th>
            <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold; border: 1px solid black;'>Estado</th>
        </tr>
        ";

            $contaFila = 0;
            foreach ($arrayDetalle as $fila){
                $contaFila++;

                $nombreProducto = "";
                $nombreUnidad = "";
                if($infoBode = BodegaMateriales::where('id', $fila->id_referencia)->first()){
                    $nombreProducto = $infoBode->nombre;

                    $infoUnidad = P_UnidadMedida::where('id', $infoBode->id_unidadmedida)->first();
                    $nombreUnidad = $infoUnidad->nombre;
                }

                if($fila->estado == 1){
                    $nombreEstado = "Pendiente";
                }else if($fila->estado == 2){
                    $nombreEstado = "Entregado";
                }else if($fila->estado == 3){
                    $nombreEstado = "Entregado/Parcial";
                }else{
                    $nombreEstado = "Denegado";
                }

                $tabla .= "<tr>
                    <td style='text-align: center; font-size:13px; border: 1px solid black;'>$contaFila</td>
                    <td style='text-align: center; font-size:13px; border: 1px solid black;'>$nombreProducto</td>
                    <td style='text-align: center; font-size:13px; border: 1px solid black;'>$nombreUnidad</td>
                    <td style='text-align: center; font-size:13px; border: 1px solid black;'>$fila->cantidad_entregada</td>
                      <td style='text-align: center; font-size:13px; border: 1px solid black;'>$nombreEstado</td>
                </tr> ";
            }


            $tabla .= "</tbody></table>";



            $tabla .= "
<table style='width: 100%; margin-top: 30px; font-family: \"Times New Roman\", Times, serif; font-size: 14px; color: #000;'>
    <!-- Fila para los títulos -->
    <tr>
        <td style='width: 50%; text-align: left; padding-bottom: 50px;'>
            <p style='margin: 0; font-weight: bold; margin-left: 15px'>Solicitado por:</p>
        </td>
        <td style='width: 50%; text-align: right; padding-bottom: 50px;'>
            <p style='margin: 0; font-weight: bold; margin-right: 15px'>Entregado por:</p>
        </td>
    </tr>
    <!-- Fila para los contenidos -->
    <tr>
        <td style='width: 50%; text-align: center; padding: 20px;'>
            <p style='margin: 10px 0;'>f.____________________________</p>
            <p style='margin: 10px 0;'>$infoUsuario->nombre</p>
            <p style='margin: 10px 0;'>Jefe de unidad</p>
            <p style='margin: 10px 0;'>$nombreDepartamento</p>
        </td>
        <td style='width: 50%; text-align: center; padding: 20px;'>
            <p style='margin: 10px 0;'>f.____________________________</p>
            <p style='margin: 10px 0;'>$infoUserLogin->nombre</p>
            <p style='margin: 10px 0;'>Encargado de Proveeduría y Bodega</p>
            <p style='margin: 10px 0;'></p>
        </td>
    </tr>
</table>
";


            $stylesheet = file_get_contents('css/cssbodega.css');
            $mpdf->WriteHTML($stylesheet,1);

            $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
            $mpdf->WriteHTML($tabla,2);

            $mpdf->Output();
        }else{
            return "Solicitud no encontrado";
        }
    }


    public function reporteEncargadoBodegaItem($idsalidaDeta)
    {
        if($infoSalidaDetalle = BodegaSalidaDetalle::where('id', $idsalidaDeta)->first()){
            $fechaFormat = date("d-m-Y", strtotime(Carbon::now('America/El_Salvador')));


            $infoBodeSoliDetalle = BodegaSolicitudDetalle::where('id', $infoSalidaDetalle->id_solidetalle)->first();
            $infoSolicitud = BodegaSolicitud::where('id', $infoBodeSoliDetalle->id_bodesolicitud)->first();
            $infoUsuario = Usuario::where('id', $infoSolicitud->id_usuario)->first();

            $infoEntradaDeta = BodegaEntradasDetalle::where('id', $infoSalidaDetalle->id_entradadetalle)->first();

            $nombreDepartamento = "";
            if($infoUD = P_UsuarioDepartamento::where('id_usuario', $infoSolicitud->id_usuario)->first()){
                $infoDepa = P_Departamento::where('id', $infoUD->id_departamento)->first();
                $nombreDepartamento = $infoDepa->nombre;
            }


            $userAutenticado = Auth::user();
            $infoUserLogin = Usuario::where('id', $userAutenticado->id)->first();


            //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
            $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);

            $mpdf->SetTitle('Solicitud');

            // mostrar errores
            $mpdf->showImageErrors = false;

            $logoalcaldia = 'images/gobiernologo.jpg';
            $logosantaana = 'images/logo.png';

            $tabla = "
            <table style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <!-- Logo izquierdo -->
                    <td style='width: 15%; text-align: left;'>
                        <img src='$logosantaana' alt='Santa Ana Norte' style='max-width: 100px; height: auto;'>
                    </td>
                    <!-- Texto centrado -->
                    <td style='width: 60%; text-align: center;'>
                        <h1 style='font-size: 16px; margin: 0; color: #003366; text-transform: uppercase;'>ALCALDÍA MUNICIPAL DE SANTA ANA NORTE</h1>
                        <h2 style='font-size: 14px; margin: 0; color: #003366; text-transform: uppercase;'>UNIDAD DE PROVEEDURÍA Y BODEGA</h2>
                    </td>
                    <!-- Logo derecho -->
                    <td style='width: 10%; text-align: right;'>
                        <img src='$logoalcaldia' alt='Gobierno de El Salvador' style='max-width: 60px; height: auto;'>
                    </td>
                </tr>
            </table>
            <hr style='border: none; border-top: 2px solid #003366; margin: 0;'>
            ";

            $tabla .= "
            <div style='text-align: center; margin-top: 20px;'>
                <h1 style='font-size: 16px; margin: 0; color: #000;'>ENTREGA DE INSUMOS DE BODEGA</h1>
                <p style='font-size: 14px; margin: 5px 0; color: #000;'>N° DE SOLICITUD: $infoSolicitud->id</p>
            </div>
            <div style='text-align: right; margin-top: 10px; padding-right: 80px;'>
            <p style='font-size: 14px; margin: 0; color: #000;'>Fecha: $fechaFormat</p>
        </div>
      ";


            $tabla .= "
            <div style='text-align: left; margin-top: 20px; font-family: \"Times New Roman\", Times, serif;'>
                <p style='font-size: 14px; margin: 0; color: #000;'>$infoUserLogin->nombre</p>
                <p style='font-size: 14px; margin: 5px 0; color: #000;'>Encargado de proveeduría y bodega</p>
                <p style='font-size: 14px; margin: 5px 0; color: #000;'>Santa Ana Norte</p>
            </div>
               <div style='text-align: left; margin-top: 20px; font-family: \"Times New Roman\", Times, serif;'>
                <p style='font-size: 14px; margin: 5px 0; color: #000;'>Por medio de la presente, me permito solicitar la entrega de los siguientes materiales,
                los cuales son necesarios para el desarrollo de nuestras actividades dentro de la municipalidad: </p>

                   <p style='font-size: 14px; margin: 5px 0; color: #000;'>
                        <strong>Descripción general:</strong> (ej. Materiales de oficina, papelería, productos de limpieza, etc)
                    </p>
                    <p style='font-size: 14px; margin: 5px 0; color: #000;'>
                    <strong>NOTA: el número y estado de la solicitud será completado por la unidad de proveeduría y bodega.</strong>
                </p>
            </div>
            ";


            $tabla .= "


           <div style='text-align: left; margin-top: 20px; font-family: \"Times New Roman\", Times, serif;'>
                <p style='font-size: 14px; margin: 5px 0; color: #000;'>
                    Quedo a la espera de su confirmación sobre la disponibilidad y fecha de entrega.
                </p>
           </div>
           <br>
        ";


            $tabla .= "<table id='tablaFor' style='width: 100%; border-collapse: collapse;'>
    <tbody>
        <tr>
            <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold; border: 1px solid black;'>Descripción</th>
            <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold; border: 1px solid black; width: 4%'>U. Medida</th>
            <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold; border: 1px solid black; width: 4%'>C. Entregada</th>
            <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold; border: 1px solid black;'>Estado</th>
        </tr>
        ";

                $nombreProducto = "";
                $nombreUnidad = "";

                if($infoBode = BodegaMateriales::where('id', $infoEntradaDeta->id_material)->first()){
                    $nombreProducto = $infoBode->nombre;

                    $infoUnidad = P_UnidadMedida::where('id', $infoBode->id_unidadmedida)->first();
                    $nombreUnidad = $infoUnidad->nombre;
                }

                if($infoBodeSoliDetalle->estado == 1){
                    $nombreEstado = "Pendiente";
                }else if($infoBodeSoliDetalle->estado == 2){
                    $nombreEstado = "Entregado";
                }else if($infoBodeSoliDetalle->estado == 3){
                    $nombreEstado = "Entregado/Parcial";
                }else{
                    $nombreEstado = "Denegado";
                }

                $tabla .= "<tr>
                    <td style='text-align: center; font-size:13px; border: 1px solid black;'>$nombreProducto</td>
                    <td style='text-align: center; font-size:13px; border: 1px solid black;'>$nombreUnidad</td>
                    <td style='text-align: center; font-size:13px; border: 1px solid black;'>$infoSalidaDetalle->cantidad_salida</td>
                      <td style='text-align: center; font-size:13px; border: 1px solid black;'>$nombreEstado</td>
                </tr> ";


            $tabla .= "</tbody></table>";


            $tabla .= "
            <table style='width: 100%; margin-top: 30px; font-family: \"Times New Roman\", Times, serif; font-size: 14px; color: #000;'>
                <!-- Fila para los títulos -->
                <tr>
                    <td style='width: 50%; text-align: left; padding-bottom: 40px;'>
                        <p style='margin: 0; font-weight: bold;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Recibido por:</p>
                    </td>

                </tr>
                <!-- Fila para los contenidos -->
                <tr>
                    <td style='width: 50%; text-align: center; padding: 20px;'>
                        <p style='margin: 10px 0;'>f.____________________________</p>
                        <p style='margin: 10px 0;'>$infoUsuario->nombre</p>
                        <p style='margin: 10px 0;'>Jefe de unidad</p>
                          <p style='margin: 10px 0;'>$nombreDepartamento</p>
                        <p style='margin: 10px 0;'>Fecha: </p>
                        <p style='margin: 10px 0;'>Hora: </p>
                    </td>
                </tr>
            </table>
            ";

            $stylesheet = file_get_contents('css/cssbodega.css');
            $mpdf->WriteHTML($stylesheet,1);

            $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
            $mpdf->WriteHTML($tabla,2);

            $mpdf->Output();
        }else{
            return "Solicitud no encontrado";
        }
    }



    public function vistaReporteGenerales()
    {
        return view('backend.admin.bodega.reportes.general.vistareportegeneral');
    }


    public function generarPDFExistencias()
    {
        // OBTENER UNICAMENTE LOS MATERIALES ASOCIADOS A MIS CODIGOS

        $pilaObjEspeci = array();
        $pilaMaterialValido = array();
        $infoAuth = auth()->user();
        $arrayCodigo = BodegaUsuarioObjEspecifico::where('id_usuario', $infoAuth->id)->get();

        $totalCulumna = 0;

        foreach ($arrayCodigo as $fila) {
            array_push($pilaObjEspeci, $fila->id_objespecifico);
        }

        $arrayMisMaterieales = BodegaMateriales::whereIn('id_objespecifico', $pilaObjEspeci)->get();
        foreach ($arrayMisMaterieales as $fila) {
            array_push($pilaMaterialValido, $fila->id);
        }

        $arrayInfo = BodegaEntradasDetalle::whereIn('id_material', $pilaMaterialValido)
            ->whereColumn('cantidad_entregada', '<', 'cantidad')->get();

        foreach ($arrayInfo as $fila) {
            $infoEntrada = BodegaEntradas::where('id', $fila->id_entrada)->first();
            $infoMaterial = BodegaMateriales::where('id', $fila->id_material)->first();
            $infoObj = ObjEspecifico::where('id', $infoMaterial->id_objespecifico)->first();

            $fila->nombreMaterial = $infoMaterial->nombre;
            $fila->nombreCodigo = $infoObj->codigo;
            $fila->nombreObjeto = $infoObj->nombre;
            $fila->lote = $infoEntrada->lote;

            $resta = $fila->cantidad - $fila->cantidad_entregada;
            $fila->cantidadActual = $resta;

            // LO QUE HAY MULTIPLICADO POR EL PRECIO
            $multiplicado = $resta * $fila->precio;
            $totalCulumna += $multiplicado;

            $fila->multiplicado = "$" . number_format((float)$multiplicado, 2, '.', ',');
            $fila->precioFormat = "$" . number_format((float)$fila->precio, 2, '.', ',');
        }

        $totalCulumna = "$" . number_format((float)$totalCulumna, 2, '.', ',');

        $arrayDetalle = $arrayInfo->sortBy('nombreMaterial');
        $fechaFormat = date("d-m-Y", strtotime(Carbon::now('America/El_Salvador')));

        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);

        $mpdf->SetTitle('Existencias');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/gobiernologo.jpg';
        $logosantaana = 'images/logo.png';

        $tabla = "
            <table style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <!-- Logo izquierdo -->
                    <td style='width: 15%; text-align: left;'>
                        <img src='$logosantaana' alt='Santa Ana Norte' style='max-width: 100px; height: auto;'>
                    </td>
                    <!-- Texto centrado -->
                    <td style='width: 60%; text-align: center;'>
                        <h1 style='font-size: 16px; margin: 0; color: #003366; text-transform: uppercase;'>ALCALDÍA MUNICIPAL DE SANTA ANA NORTE</h1>
                        <h2 style='font-size: 14px; margin: 0; color: #003366; text-transform: uppercase;'>UNIDAD DE PROVEEDURÍA Y BODEGA</h2>
                    </td>
                    <!-- Logo derecho -->
                    <td style='width: 10%; text-align: right;'>
                        <img src='$logoalcaldia' alt='Gobierno de El Salvador' style='max-width: 60px; height: auto;'>
                    </td>
                </tr>
            </table>
            <hr style='border: none; border-top: 2px solid #003366; margin: 0;'>
            ";

        $tabla .= "
            <div style='text-align: center; margin-top: 20px;'>
                <h1 style='font-size: 16px; margin: 0; color: #000;'>EXISTENCIAS</h1>
            </div>
            <div style='text-align: left; margin-top: 10px;'>
            <p style='font-size: 14px; margin: 0; color: #000;'>Fecha: $fechaFormat</p>
        </div>
      ";

        $tabla .= "<table id='tablaFor' style='width: 100%; border-collapse: collapse; margin-top: 35px'>
        <tbody>
            <tr>
                <th style='text-align: center; font-size:13px; width: 8%; font-weight: bold; border: 1px solid black;'>Lote</th>
                <th style='text-align: center; font-size:13px; width: 12%; font-weight: bold; border: 1px solid black;'>Producto</th>
                <th style='text-align: center; font-size:13px; width: 8%; font-weight: bold; border: 1px solid black;'>Cantidad</th>
                <th style='text-align: center; font-size:13px; width: 8%; font-weight: bold; border: 1px solid black;'>Precio</th>
                <th style='text-align: center; font-size:13px; width: 8%; font-weight: bold; border: 1px solid black;'>Total</th>
                <th style='text-align: center; font-size:13px; width: 8%; font-weight: bold; border: 1px solid black;'>Obj. Específico</th>
            </tr>
        ";

        foreach ($arrayDetalle as $fila) {
            $tabla .= "<tr>
                    <td style='text-align: center; font-size:13px; border: 1px solid black;'>$fila->lote</td>
                    <td style='text-align: center; font-size:13px; border: 1px solid black;'>$fila->nombreMaterial</td>
                    <td style='text-align: center; font-size:13px; border: 1px solid black;'>$fila->cantidadActual</td>
                    <td style='text-align: center; font-size:13px; border: 1px solid black;'>$fila->precioFormat</td>
                    <td style='text-align: center; font-size:13px; border: 1px solid black;'>$fila->multiplicado</td>
                    <td style='text-align: center; font-size:13px; border: 1px solid black;'>$fila->nombreCodigo</td>
                </tr> ";
        }

        $tabla .= "<tr>
                    <td colspan='4' style='text-align: center; font-size:13px; border: 1px solid black;'><strong>Total</strong></td>
                    <td style='text-align: center; font-size:13px; border: 1px solid black;'>$totalCulumna</td>
                </tr> ";

        $tabla .= "</tbody></table>";

        $stylesheet = file_get_contents('css/cssbodega.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla,2);

        $mpdf->Output();
    }




    public function vistaReporteEntregadoUnidades()
    {
        $pilaObjEspeci = array();
        $pilaIDUsuarios = array();
        $infoAuth = auth()->user();
        $arrayCodigo = BodegaUsuarioObjEspecifico::where('id_usuario', $infoAuth->id)->get();

        foreach ($arrayCodigo as $fila) {
            array_push($pilaObjEspeci, $fila->id_objespecifico);
        }

        // ARRAY DE USUARIOS QUE HAN HECHO SOLICITUD
        $arraySoli = BodegaSolicitud::whereIn('id_objespecifico', $pilaObjEspeci)->get();
        foreach ($arraySoli as $fila) {
            array_push($pilaIDUsuarios, $fila->id_usuario);
        }

        $arrayUnidad = DB::table('p_usuario_departamento AS pud')
            ->join('p_departamento AS pd', 'pud.id_departamento', '=', 'pd.id')
            ->select('pd.nombre', 'pud.id_usuario AS id')
            ->whereIn('pud.id_usuario', $pilaIDUsuarios)
            ->orderBy('pd.nombre', 'ASC')
            ->get();

        return view('backend.admin.bodega.reportes.entregados.vistareporteentregados', compact('arrayUnidad'));
    }



    public function reporteUnidadPDFEntregas($idusuario, $desde, $hasta)
    {
        $start = Carbon::parse($desde)->startOfDay();
        $end = Carbon::parse($hasta)->endOfDay();

        $desdeFormat = date("d-m-Y", strtotime($desde));
        $hastaFormat = date("d-m-Y", strtotime($hasta));


        // ARRAY SOLICITUDES DEL USUARIO DE LA UNIDAD
        $arrayBodeSoli = BodegaSolicitud::where('id_usuario', $idusuario)->get();
        $pilaIdBodeSoli = array();

        foreach ($arrayBodeSoli as $fila) {
            array_push($pilaIdBodeSoli, $fila->id);
        }

        $pilaIDSalida = array();
        $arrayIDSalida = BodegaSalida::whereIn('id_solicitud', $pilaIdBodeSoli)
            ->whereBetween('fecha', [$start, $end])
            ->orderBy('fecha', 'ASC')
            ->get();

        foreach ($arrayIDSalida as $fila) {
            array_push($pilaIDSalida, $fila->id);
        }

        $arraySalidaDetalle = BodegaSalidaDetalle::whereIn('id_salida', $pilaIDSalida)->get();

        $columnaTotalMultiplicado = 0;
        foreach ($arraySalidaDetalle as $fila) {
            $infoEnDeta = BodegaEntradasDetalle::where('id', $fila->id_entradadetalle)->first();
            $infoMaterial = BodegaMateriales::where('id', $infoEnDeta->id_material)->first();

            $fila->nombreMaterial = $infoMaterial->nombre;
            $fila->precioFormat = "$" . $infoEnDeta->precio;

            // FECHA DE SALIDA
            $infoSalida = BodegaSalida::where('id', $fila->id_salida)->first();
            $fila->fechaFormat = date("d-m-Y", strtotime($infoSalida->fecha));

            // UNIDADMEDIDA
            $infoUnidad = P_UnidadMedida::where('id', $infoMaterial->id_unidadmedida)->first();
            $fila->unidadMedida = $infoUnidad->nombre;

            // TOTAL
            $multiplicado = $fila->cantidad_salida * $infoEnDeta->precio;
            $multiplicado = round($multiplicado, 2);
            $columnaTotalMultiplicado += $multiplicado;
            $fila->multiplicado = '$' . number_format((float)$multiplicado, 2, '.', ',');
        }

        $columnaTotalMultiplicado = round($columnaTotalMultiplicado, 2);
        $columnaTotalMultiplicado = '$' . number_format((float)$columnaTotalMultiplicado, 2, '.', ',');

        $nombreUnidad = "";
        if($dato = P_UsuarioDepartamento::where('id_usuario', $idusuario)->first()){
            $infoDepa = P_Departamento::where('id', $dato->id_departamento)->first();
            $nombreUnidad = $infoDepa->nombre;
        }


        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);

        $mpdf->SetTitle('Salidas');

        // mostrar errores
        $mpdf->showImageErrors = false;


        $logoalcaldia = 'images/gobiernologo.jpg';
        $logosantaana = 'images/logo.png';

        $tabla = "
            <table style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <!-- Logo izquierdo -->
                    <td style='width: 15%; text-align: left;'>
                        <img src='$logosantaana' alt='Santa Ana Norte' style='max-width: 100px; height: auto;'>
                    </td>
                    <!-- Texto centrado -->
                    <td style='width: 60%; text-align: center;'>
                        <h1 style='font-size: 16px; margin: 0; color: #003366; text-transform: uppercase;'>ALCALDÍA MUNICIPAL DE SANTA ANA NORTE</h1>
                        <h2 style='font-size: 14px; margin: 0; color: #003366; text-transform: uppercase;'>UNIDAD DE PROVEEDURÍA Y BODEGA</h2>
                    </td>
                    <!-- Logo derecho -->
                    <td style='width: 10%; text-align: right;'>
                        <img src='$logoalcaldia' alt='Gobierno de El Salvador' style='max-width: 60px; height: auto;'>
                    </td>
                </tr>
            </table>
            <hr style='border: none; border-top: 2px solid #003366; margin: 0;'>
            ";

        $tabla .= "
            <div style='text-align: center; margin-top: 20px;'>
                <h1 style='font-size: 16px; margin: 0; color: #000;'>ENTREGAS</h1>
                <p style='font-size: 14px; margin: 0; color: #000;'><strong>DESDE: $desdeFormat   HASTA: $hastaFormat</strong></p>
            </div>
            <div style='text-align: left; margin-top: 20px;'>
            <p style='font-size: 14px; margin: 0; color: #000;'><strong>UNIDAD</strong></p>
             <p style='font-size: 14px; color: #000; margin-top: 10px;'><strong>$nombreUnidad</strong></p>
        </div>
      ";

        // Encabezado de la tabla
        $tabla .= "<table width='100%' id='tablaFor'>
            <thead>
                <tr>
                    <th style='font-weight: bold; width: 6%; font-size: 11px; text-align: center;'>F. Salida</th>
                    <th style='font-weight: bold; width: 22%; font-size: 11px; text-align: center;'>Descripción</th>
                    <th style='font-weight: bold; width: 8%; font-size: 11px; text-align: center;'>U/M</th>
                    <th style='font-weight: bold; width: 8%; font-size: 11px; text-align: center;'>C. Entregada</th>
                    <th style='font-weight: bold; width: 8%; font-size: 11px; text-align: center;'>Precio</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>Total</th>
                </tr>
            </thead>
            <tbody>";

        foreach ($arraySalidaDetalle as $dato) {
            $tabla .= "<tr>
                <td style='font-size: 11px'>$dato->fechaFormat</td>
                <td style='font-size: 11px'>$dato->nombreMaterial</td>
                <td style='font-size: 11px'>$dato->unidadMedida</td>
                <td style='font-size: 11px'>$dato->cantidad_salida</td>
                <td style='font-size: 11px'>$dato->precioFormat</td>
                <td style='font-size: 11px'>$dato->multiplicado</td>
            </tr>";
        }

        $tabla .= "<tr>
                <td colspan='5' style='font-size: 11px'><strong>TOTAL</strong></td>
                <td style='font-size: 11px'><strong>$columnaTotalMultiplicado</strong></td>
            </tr>";

        $tabla .= "</tbody></table>";

        $stylesheet = file_get_contents('css/cssbodega.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla,2);

        $mpdf->Output();
    }



    public function reportePDFEntregasTotal($desde, $hasta)
    {
        $start = Carbon::parse($desde)->startOfDay();
        $end = Carbon::parse($hasta)->endOfDay();

        $desdeFormat = date("d-m-Y", strtotime($desde));
        $hastaFormat = date("d-m-Y", strtotime($hasta));
        $infoAuth = auth()->user();


        // TODAS LAS SALIDAS QUE HIZO MI USUARIO BODEGUERO
        $arrayBodegaSalida = BodegaSalida::where('id_usuario', $infoAuth->id)
            ->whereBetween('fecha', [$start, $end])
            ->orderBy('fecha', 'ASC')
            ->get();

        foreach ($arrayBodegaSalida as $fila){
            $infoBodeSoli = BodegaSolicitud::where('id', $fila->id_solicitud)->first();
            $fila->idusersolicito = $infoBodeSoli->id_usuario;
        }
        $pilaIdUserSolicito = array();
        foreach ($arrayBodegaSalida as $fila) {
            array_push($pilaIdUserSolicito, $fila->idusersolicito);
        }

        // TOTAL FINAL DE PAGINADO DE TODAS LAS UNIDADES
        $totalTodasLasUnidades = 0;

        $resultsBloque = array();
        $index = 0;

        // OBTENER LAS UNIDADES QUE SE ENCONTRO SUS ID
        $arrayUnidades = P_UsuarioDepartamento::whereIn('id_usuario', $pilaIdUserSolicito)->get();

        foreach ($arrayUnidades as $filaP) {
            array_push($resultsBloque, $filaP);

            $infoDepa = P_Departamento::where('id', $filaP->id_departamento)->first();
            $filaP->nombreDepartamento = $infoDepa->nombre;

            // POR CADA UNIDAD ENCONTRAR SUS SALIDAS

            $arrayBodeSoli = BodegaSolicitud::where('id_usuario', $filaP->id_usuario)->get();
            $pilaIdBodeSoli = array();

            foreach ($arrayBodeSoli as $fila) {
                array_push($pilaIdBodeSoli, $fila->id);
            }

            $pilaIDSalida = array();
            $arrayIDSalida = BodegaSalida::whereIn('id_solicitud', $pilaIdBodeSoli)
                ->whereBetween('fecha', [$start, $end])
                ->orderBy('fecha', 'ASC')
                ->get();

            foreach ($arrayIDSalida as $fila) {
                array_push($pilaIDSalida, $fila->id);
            }

            // AQUI SE OBTIENE TODAS LAS SALIDAS DE DEPARTAMENTO EN DEPARTAMENTO
            $arraySalidaDetalle = BodegaSalidaDetalle::whereIn('id_salida', $pilaIDSalida)->get();

            $columnaTotalMultiplicado = 0;
            foreach ($arraySalidaDetalle as $fila) {

                $infoEnDeta = BodegaEntradasDetalle::where('id', $fila->id_entradadetalle)->first();
                $infoMaterial = BodegaMateriales::where('id', $infoEnDeta->id_material)->first();

                $fila->nombreMaterial = $infoMaterial->nombre;
                $fila->precioFormat = "$" . $infoEnDeta->precio;

                // FECHA DE SALIDA
                $infoSalida = BodegaSalida::where('id', $fila->id_salida)->first();
                $fila->fechaFormat = date("d-m-Y", strtotime($infoSalida->fecha));

                // UNIDADMEDIDA
                $infoUnidad = P_UnidadMedida::where('id', $infoMaterial->id_unidadmedida)->first();
                $fila->unidadMedida = $infoUnidad->nombre;

                // TOTAL
                $multiplicado = $fila->cantidad_salida * $infoEnDeta->precio;
                $multiplicado = round($multiplicado, 2);
                $columnaTotalMultiplicado += $multiplicado;
                $fila->multiplicado = '$' . number_format((float)$multiplicado, 2, '.', ',');
            }

            $arraySalidaDetalleSORT = $arraySalidaDetalle->sortBy('fechaFormat');

            $totalTodasLasUnidades += $columnaTotalMultiplicado;

            $columnaTotalMultiplicado = round($columnaTotalMultiplicado, 2);
            $filaP->totalColumnaMultiplicado ='$' . number_format((float)$columnaTotalMultiplicado, 2, '.', ',');

            $resultsBloque[$index]->bloque = $arraySalidaDetalleSORT;
            $index++;
        }


        $totalTodasLasUnidades = round($totalTodasLasUnidades, 2);
        $totalTodasLasUnidades = '$' . number_format((float)$totalTodasLasUnidades, 2, '.', ',');


        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);

        $mpdf->SetTitle('Salidas');

        // mostrar errores
        $mpdf->showImageErrors = false;


        $logoalcaldia = 'images/gobiernologo.jpg';
        $logosantaana = 'images/logo.png';

        $tabla = "
            <table style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <!-- Logo izquierdo -->
                    <td style='width: 15%; text-align: left;'>
                        <img src='$logosantaana' alt='Santa Ana Norte' style='max-width: 100px; height: auto;'>
                    </td>
                    <!-- Texto centrado -->
                    <td style='width: 60%; text-align: center;'>
                        <h1 style='font-size: 16px; margin: 0; color: #003366; text-transform: uppercase;'>ALCALDÍA MUNICIPAL DE SANTA ANA NORTE</h1>
                        <h2 style='font-size: 14px; margin: 0; color: #003366; text-transform: uppercase;'>UNIDAD DE PROVEEDURÍA Y BODEGA</h2>
                    </td>
                    <!-- Logo derecho -->
                    <td style='width: 10%; text-align: right;'>
                        <img src='$logoalcaldia' alt='Gobierno de El Salvador' style='max-width: 60px; height: auto;'>
                    </td>
                </tr>
            </table>
            <hr style='border: none; border-top: 2px solid #003366; margin: 0;'>
            ";

        $tabla .= "
            <div style='text-align: center; margin-top: 20px;'>
                <h1 style='font-size: 16px; margin: 0; color: #000;'>ENTREGAS</h1>
                <p style='font-size: 14px; margin: 0; color: #000;'><strong>DESDE: $desdeFormat   HASTA: $hastaFormat</strong></p>
            </div>
      ";


        // COLOCAR CADA UNIDAD ENCONTRADA
        foreach ($arrayUnidades as $fila){

            if (empty($fila->bloque)) {
                continue; // Saltar esta iteración si el bloque está vacío
            }

            $tabla .= "
            <div style='text-align: left; margin-top: 20px;'>
             <p style='font-size: 14px; color: #000; margin-top: 10px;'><strong>$fila->nombreDepartamento</strong></p>
            </div>
          ";

            $tabla .= "<table width='100%' id='tablaFor'>
            <thead>
                <tr>
                    <th style='font-weight: bold; width: 6%; font-size: 11px; text-align: center;'>F. Salida</th>
                    <th style='font-weight: bold; width: 22%; font-size: 11px; text-align: center;'>Descripción</th>
                    <th style='font-weight: bold; width: 8%; font-size: 11px; text-align: center;'>U/M</th>
                    <th style='font-weight: bold; width: 8%; font-size: 11px; text-align: center;'>C. Entregada</th>
                    <th style='font-weight: bold; width: 8%; font-size: 11px; text-align: center;'>Precio</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>Total</th>
                </tr>
            </thead>
            <tbody>";

                foreach ($fila->bloque as $dato) {
                    $tabla .= "<tr>
                    <td style='font-size: 11px'>$dato->fechaFormat</td>
                    <td style='font-size: 11px'>$dato->nombreMaterial</td>
                    <td style='font-size: 11px'>$dato->unidadMedida</td>
                    <td style='font-size: 11px'>$dato->cantidad_salida</td>
                    <td style='font-size: 11px'>$dato->precioFormat</td>
                    <td style='font-size: 11px'>$dato->multiplicado</td>
                </tr>";
                }

            $tabla .= "<tr>
                <td colspan='5' style='font-size: 11px'><strong>TOTAL</strong></td>
                <td style='font-size: 11px'><strong>$fila->totalColumnaMultiplicado</strong></td>
            </tr>";

            $tabla .= "</tbody></table>";
        }

        $tabla .= "<br>";

        $tabla .= "<table width='100%' id='tablaFor'>
            <thead>
                <tr>
                    <th style='font-weight: bold; width: 6%; font-size: 11px; text-align: center;'>TOTAL</th>
                    <th style='font-weight: bold; width: 22%; font-size: 11px; text-align: center;'>$totalTodasLasUnidades</th>
                </tr>
            </thead>
            <tbody>";

        $tabla .= "</tbody></table>";




        $stylesheet = file_get_contents('css/cssbodega.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla,2);

        $mpdf->Output();
    }





}

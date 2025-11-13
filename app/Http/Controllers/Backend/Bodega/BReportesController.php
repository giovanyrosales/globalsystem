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
use App\Models\MoviCuentaProy;
use App\Models\ObjEspecifico;
use App\Models\P_Departamento;
use App\Models\P_PresupUnidad;
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


            // USUARIO QUE INICIO SESION


            $arrayDetalle = BodegaSolicitudDetalle::where('id_bodesolicitud', $idsolicitud)->get();
            $infoExtra = BodegaExtras::where('id', 1)->first();

            // USUARIO DE LA SOLICITUD
            $infoUsuarioSolicitud = Usuario::where('id', $infoSolicitud->id_usuario)->first();
            $nombreDepartamento = "";
            if($infoUD = P_UsuarioDepartamento::where('id_usuario', $infoSolicitud->id_usuario)->first()){
                $infoDepa = P_Departamento::where('id', $infoUD->id_departamento)->first();
                $nombreDepartamento = $infoDepa->nombre;
            }

            // NECESITO EL USUARIO A QUIEN VA DIRIGIDO ESTA SOLICITUD

            $dataEspecifico = BodegaUsuarioObjEspecifico::where('id_objespecifico', $infoSolicitud->id_objespecifico)->first();
            $infoUsuarioVaDirigido = Usuario::where('id', $dataEspecifico->id_usuario)->first();



            //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
            $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);

            $mpdf->SetTitle('Solicitud de Insumos');

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
                        <h2 style='font-size: 14px; margin: 0; color: #003366; text-transform: uppercase;'>$infoUsuarioVaDirigido->cargo2</h2>
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
                <p style='font-size: 16px; margin: 0; color: #000;'>N° DE SOLICITUD: _______________</p>
            </div>
            <div style='text-align: right; margin-top: 10px; padding-right: 80px;'>
            <p style='font-size: 14px; margin: 0; color: #000;'>Fecha: $fechaFormat</p>
            </div>
          ";

            $tabla .= "
            <div style='text-align: left; margin-top: 20px; font-family: \"Times New Roman\", Times, serif;'>
                <p style='font-size: 14px; margin: 0; color: #000;'>$infoUsuarioVaDirigido->nombre</p>
                <p style='font-size: 14px; margin: 0; color: #000;'>$infoUsuarioVaDirigido->cargo</p>
                <p style='font-size: 14px; margin: 5px 0; color: #000;'>Santa Ana Norte</p>
            </div>
            ";


            // TEXTO SEGUN USUARIO
            $textoDescripcion = "";
            if($infoUsuarioVaDirigido->id == 77){ // proveeduria y bodega
                $textoDescripcion = "materiales y/o papeleria";
            }else if($infoUsuarioVaDirigido->id == 78){ // informatica
                $textoDescripcion = "materiales informáticos";
            }else{ // activo
                $textoDescripcion = "materiales de oficina";
            }

            $tabla .= "
               <div style='text-align: left; margin-top: 10px; font-family: \"Times New Roman\", Times, serif;'>
                <p style='font-size: 15px; margin: 5px 0; color: #000;'>Por medio de la presente, me permito solicitar la entrega de los siguientes materiales,
                los cuales son necesarios para el desarrollo de nuestras actividades dentro de la municipalidad: </p>
                    <br>
                   <p style='font-size: 14px; margin: 5px 0; color: #000;'>
                        <strong>Descripción general:</strong> $textoDescripcion
                    </p>
                </p>
            </div>
            ";



            $tabla .= "<table id='tablaFor' style='width: 100%; border-collapse: collapse;'>
    <tbody>
        <tr>
            <th style='text-align: center; font-size:11px; width: 4%; font-weight: bold; border: 1px solid black;'>N°</th>
            <th style='text-align: center; font-size:11px; width: 12%; font-weight: bold; border: 1px solid black;'>Descripción (con especificaciones claras de lo que requieren)</th>
            <th style='text-align: center; font-size:11px; width: 9%; font-weight: bold; border: 1px solid black;'>Unidad de medida</th>
            <th style='text-align: center; font-size:11px; width: 10%; font-weight: bold; border: 1px solid black;'>Cantidad solicitada</th>
            <th style='text-align: center; font-size:11px; width: 8%; font-weight: bold; border: 1px solid black;'>Estado de la solicitud (pendiente o entregado)</th>
        </tr>
        ";

            $contaFila = 0;
            foreach ($arrayDetalle as $fila){
                $contaFila++;

                $infoUnidad = P_UnidadMedida::where('id', $fila->id_unidad)->first();

                $tabla .= "<tr>
                    <td style='text-align: center; font-size:12px; border: 1px solid black;'>$contaFila</td>
                    <td style='text-align: center; font-size:12px; border: 1px solid black;'>$fila->nombre</td>
                    <td style='text-align: center; font-size:12px; border: 1px solid black;'>$infoUnidad->nombre</td>
                    <td style='text-align: center; font-size:12px; border: 1px solid black;'>$fila->cantidad</td>
                     <td style='text-align: center; font-size:12px; border: 1px solid black;'></td>
                </tr> ";
            }


            $tabla .= "</tbody></table>";


            $texto = "NOTA: el número y estado de la solicitud será completado por " . $infoUsuarioVaDirigido->cargo;


            $tabla .= "
               <div style='text-align: left; margin-top: 20px; font-size: 14px; font-family: \"Times New Roman\", Times, serif;'>
                    <strong>$texto</strong>
                </p>
                <br>
                 <p style='font-size: 14px; margin: 5px 0; color: #000;'>
                Quedo a la espera de su confirmación sobre la disponibilidad y fecha de entrega.
                </p>
            </div>
            ";




            $tabla .= "
<table style='width: 100%; margin-top: 15px; font-family: \"Times New Roman\", Times, serif; font-size: 14px; color: #000;'>
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
            <p style='margin: 10px 0;'>$infoUsuarioSolicitud->nombre</p>
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
                    <p style='margin: 10px 0;'>$infoUsuarioVaDirigido->nombre</p>
                    <p style='margin: 10px 0;'>$infoUsuarioVaDirigido->cargo</p>
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
        if ($infoSolicitud = BodegaSolicitud::where('id', $idsolicitud)->first()) {

            // USUARIO QUE INICIO SESION
            $userAutenticado = Auth::user();
            $infoUserLogin = Usuario::where('id', $userAutenticado->id)->first();

            $infoExtra = BodegaExtras::where('id', 1)->first();

            // USUARIO DE LA SOLICITUD
            if ($infoUD = P_UsuarioDepartamento::where('id_usuario', $infoSolicitud->id_usuario)->first()) {
                $infoDepa = P_Departamento::where('id', $infoUD->id_departamento)->first();
            }

            $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
            $mpdf->SetTitle('Comprobante Entrega');

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
                        <h1 style='font-size: 15px; margin: 0; color: #003366; text-transform: uppercase;'>ALCALDÍA MUNICIPAL DE SANTA ANA NORTE</h1>
                        <h2 style='font-size: 13px; margin: 0; color: #003366; text-transform: uppercase;'>$infoUserLogin->cargo2</h2>
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
            <table width='100%' cellspacing='0' cellpadding='5' style='margin-top: 30px; border-collapse: collapse; border: none;'>
                <tr style='height: 20px;'>
                    <td colspan='2' style='text-align: center; font-size: 14px; font-style: italic; color: #000; padding: 1px; border: none;'>
                        <strong>COMPROBANTE DE ENTREGA</strong>
                    </td>
                </tr>
            </table>";


            $tabla .= '
            <div style="text-align: center; margin-top: 15px;">
                <p style="font-size: 14px; margin: 5px 0; color: #000;"><strong>REF. N° DE SOLICITUD: ' . $infoSolicitud->numero_solicitud . ' </strong></p>
            </div>';


            $tabla .= '<table style="width: 100%; margin-top: 0px; border-collapse: collapse;">
                <tr>
                    <td style="width: 60%; vertical-align: middle; text-align: center;">
                        <table cellpadding="0" cellspacing="0" style="display: inline-table; border-collapse: collapse;">
                            <tr>
                                <!-- Grupo PARCIAL -->
                                <td style="padding-right: 15px; text-align: center;">
                                    <table cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="font-size: 14px; padding-right: 5px; white-space: nowrap; text-align: center;"><strong>FORMA DE ENTREGA: PARCIAL</strong></td>
                                            <td style="width: 18px; height: 18px; border: 1px solid #000;"></td>
                                        </tr>
                                    </table>
                                </td>

                                <!-- Grupo TOTAL -->
                                <td style="text-align: center;">
                                    <table cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="font-size: 14px; padding-right: 5px; white-space: nowrap; text-align: center;"><strong>TOTAL</strong></td>
                                            <td style="width: 18px; height: 18px; border: 1px solid #000;"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>';


            $tabla .= "
                <div style='text-align: left; margin-top: 25px; font-family: \"Times New Roman\", Times, serif;'>
                    <p style='font-size: 14px; margin: 5px 0; color: #000; font-weight: bold;'>FECHA DE ENTREGA: ________________________________________________________________</p>
                    <p style='font-size: 14px; margin: 5px 0; color: #000; font-weight: bold;'>RECIBIDO POR: ______________________________________________________________________</p>
                    <p style='font-size: 14px; margin: 5px 0; color: #000; font-weight: bold;'>CARGO: ______________________________________________________________________________</p>
                    <p style='font-size: 14px; margin: 5px 0; color: #000; font-weight: bold;'>UNIDAD: _____________________________________________________________________________</p>
                </div>";


            $tabla .= "
                <div style='text-align: left; margin-top: 25px; font-family: \"Times New Roman\", Times, serif;'>
                    <p style='font-size: 14px; margin: 5px 0; color: #000;'><strong>ENTREGADO POR: </strong> $infoUserLogin->nombre</p>
                    <p style='font-size: 14px; margin: 5px 0; color: #000;'><strong>CARGO: </strong> $infoUserLogin->cargo</p>
                </div>";


            $tabla .= '
<table style="width: 100%; margin-top: 20px; border-collapse: collapse;">
    <!-- Fila de títulos separados -->
    <tr>
        <td style="width: 50%; border: none;"></td>
        <td style="width: 25%; border: 1px solid #000; text-align: center; padding: 8px;">
            FIRMAR
        </td>
        <td style="width: 25%; border: 1px solid #000; text-align: center; padding: 8px;">
            SELLO
        </td>
    </tr>

    <!-- Fila principal -->
    <tr>
        <td style="border: 1px solid #000; padding: 15px; vertical-align: top;">
            FIRMA DE QUIEN RECIBE:
        </td>
        <td style="border: 1px solid #000; text-align: center; padding: 15px;">

        </td>
        <td style="border: 1px solid #000; text-align: center; padding: 15px;">

        </td>
    </tr>

    <!-- Fila inferior -->
    <tr>
        <td style="border: 1px solid #000; padding: 15px; vertical-align: top;">
            FIRMA DE QUIEN ENTREGA:
        </td>
        <td style="border: 1px solid #000; padding: 15px;"></td>
        <td style="border: 1px solid #000; padding: 15px;"></td>
    </tr>
</table>
';


            $tabla .= "
    <div style='text-align: left; margin-top: 25px; font-family: \"Times New Roman\", Times, serif;'>
        <p style='font-size: 14px; margin: 5px 0; color: #000; line-height: 2;'>
            <strong>OBSERVACIONES:</strong><br>
            __________________________________________________________________________________________________________ <br>
            __________________________________________________________________________________________________________ <br>
            __________________________________________________________________________________________________________
        </p>
    </div>
    <br>
";


            $stylesheet = file_get_contents('css/cssbodega.css');
            $mpdf->WriteHTML($stylesheet, 1);

            //$mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
            $mpdf->WriteHTML($tabla, 2);

            $mpdf->Output();
        } else {
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

        $idusuario = Auth::id();
        $infoUsuario = Usuario::where('id', $idusuario)->first();

        $arrayCodigoPresupuestario = BodegaUsuarioObjEspecifico::where('id_usuario', $idusuario)->get();

        foreach ($arrayCodigoPresupuestario as $fila) {
            $infoObjeto = ObjEspecifico::where('id', $fila->id_objespecifico)->first();
            $fila->nombreObjeto = $infoObjeto->nombre;
            $fila->nombreCodigo = $infoObjeto->codigo;
        }


        $arrayProductos = BodegaMateriales::whereIn('tipo_bodega', [$infoUsuario->tipo_bodega])->get();

        foreach ($arrayProductos as $fila) {
            $infoObjeto = ObjEspecifico::where('id', $fila->id_objespecifico)->first();
            $fila->nombreObjeto = $infoObjeto->nombre;
            $fila->nombreCodigo = $infoObjeto->codigo;
        }


        // TODOS LOS LOTES REGISTRADOS - FILTRADOS POR EL USUARIO
        $arrayLotes = BodegaEntradas::where('id_usuario',$infoUsuario->id)
            ->orderBy('lote', 'ASC')
            ->get();

        $arrayUnidades = P_Departamento::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.bodega.reportes.general.vistareportegeneral',
            compact('arrayProductos', 'arrayLotes', 'arrayCodigoPresupuestario',
            'arrayUnidades'));
    }


    public function generarPDFExistencias()
    {
        // OBTENER UNICAMENTE LOS MATERIALES ASOCIADOS A MIS CODIGOS
        $pilaMaterialValido = array();
        $totalCulumna = 0;

        $idusuario = Auth::id();
        $infoUsuario = Usuario::where('id', $idusuario)->first();

        $arrayMisMaterieales = BodegaMateriales::whereIn('tipo_bodega', [$infoUsuario->tipo_bodega])->get();
        foreach ($arrayMisMaterieales as $fila) {
            array_push($pilaMaterialValido, $fila->id);
        }

        $arrayInfo = BodegaEntradasDetalle::whereIn('id_material', $pilaMaterialValido)
            ->whereColumn('cantidad_entregada', '<', 'cantidad')->get();

        foreach ($arrayInfo as $fila) {
            $infoEntrada = BodegaEntradas::where('id', $fila->id_entrada)->first();
            $infoMaterial = BodegaMateriales::where('id', $fila->id_material)->first();
            $infoObj = ObjEspecifico::where('id', $infoMaterial->id_objespecifico)->first();

            $infoUnidad = P_UnidadMedida::where('id', $infoMaterial->id_unidadmedida)->first();


            $fila->unidadMedida = $infoUnidad->nombre;

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
            $fila->precioFormat = "$" . number_format((float)$fila->precio, 4, '.', ',');
        }


        $totalCulumna = "$" . number_format((float)$totalCulumna, 2, '.', ',');

        $arrayDetalle = $arrayInfo->sortBy('nombreMaterial');
        $fechaFormat = date("d-m-Y", strtotime(Carbon::now('America/El_Salvador')));


        // USUARIO LOGEADO



        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);

        $mpdf->SetTitle('Existencias General');

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
                        <h2 style='font-size: 14px; margin: 0; color: #003366; text-transform: uppercase;'>$infoUsuario->cargo2</h2>
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
                <h1 style='font-size: 15px; margin: 0; color: #000;'>EXISTENCIAS</h1>
            </div>
            <div style='text-align: left; margin-top: 10px;'>
            <p style='font-size: 13px; margin: 0; color: #000;'>Fecha: $fechaFormat</p>
        </div>
      ";

        $tabla .= "<table id='tablaFor' style='width: 100%; border-collapse: collapse; margin-top: 35px'>
        <tbody>
            <tr>
                <th style='text-align: center; font-size:10px; width: 8%; font-weight: bold; border: 1px solid black;'>Lote</th>
                <th style='text-align: center; font-size:10px; width: 12%; font-weight: bold; border: 1px solid black;'>Producto</th>
                 <th style='text-align: center; font-size:10px; width: 12%; font-weight: bold; border: 1px solid black;'>U.M</th>
                <th style='text-align: center; font-size:10px; width: 10%; font-weight: bold; border: 1px solid black;'>Cantidad</th>
                <th style='text-align: center; font-size:10px; width: 8%; font-weight: bold; border: 1px solid black;'>Precio</th>
                <th style='text-align: center; font-size:10px; width: 8%; font-weight: bold; border: 1px solid black;'>Total</th>
                <th style='text-align: center; font-size:10px; width: 8%; font-weight: bold; border: 1px solid black;'>Obj. Específico</th>
            </tr>
        ";

        foreach ($arrayDetalle as $fila) {
            $tabla .= "<tr>
                    <td style='text-align: center; font-size:10px; border: 1px solid black;'>$fila->lote</td>
                    <td style='text-align: center; font-size:10px; border: 1px solid black;'>$fila->nombreMaterial</td>
                    <td style='text-align: center; font-size:10px; border: 1px solid black;'>$fila->unidadMedida</td>
                    <td style='text-align: center; font-size:10px; border: 1px solid black;'>$fila->cantidadActual</td>
                    <td style='text-align: center; font-size:10px; border: 1px solid black;'>$fila->precioFormat</td>
                    <td style='text-align: center; font-size:10px; border: 1px solid black;'>$fila->multiplicado</td>
                    <td style='text-align: center; font-size:10px; border: 1px solid black;'>$fila->nombreCodigo</td>
                </tr> ";
        }

        $tabla .= "<tr>
                    <td colspan='5' style='text-align: center; font-size:10px; border: 1px solid black;'><strong>Total</strong></td>
                    <td style='text-align: center; font-size:10px; border: 1px solid black;'>$totalCulumna</td>
                      <td style='text-align: center; font-size:10px; border: 1px solid black;'></td>
                </tr> ";

        $tabla .= "</tbody></table>";

        $stylesheet = file_get_contents('css/cssbodega.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla,2);

        $mpdf->Output();
    }

    // FECHAS EXISTENCIAS POR FECHAS
    public function generarPDFExistenciasFechas($desde, $hasta, $checkProductos, $arrayProductos) {

        $start = Carbon::parse($desde)->startOfDay();
        $end = Carbon::parse($hasta)->endOfDay();

        $desdeFormat = date("d/m/Y", strtotime($desde));
        $hastaFormat = date("d/m/Y", strtotime($hasta));


        // NECESITO TODOS LOS MATERIALES FILTRADOS

        $idusuario = Auth::id();
        $infoUsuario = Usuario::where('id', $idusuario)->first();


        // ARRAY DE PRODUCTOS

        if($checkProductos==1){ // TODOS LOS PRODUCTOS
            // OBTENEMOS TODOS LOS PRODUCTOS
            $arrayProductos = BodegaMateriales::whereIn('tipo_bodega', [$infoUsuario->tipo_bodega])
                ->orderBy('nombre', 'ASC')
                ->get();

        }else{ // SOLO SELECCIONADOS
            $porciones = explode("-", $arrayProductos);
            $arrayProductos = BodegaMateriales::whereIn('tipo_bodega', [$infoUsuario->tipo_bodega])
                ->whereIn('id', $porciones)
                ->orderBy('nombre', 'ASC')
                ->get();
        }

        $resultsBloque = array();
        $index = 0;
        $sumaFinalSaldoExisteciaActual = 0;

        foreach ($arrayProductos as $fila){
            array_push($resultsBloque, $fila);

            $infoUnidadMedida = P_UnidadMedida::where('id', $fila->id_unidadmedida)->first();
            $fila->unidadMedida = $infoUnidadMedida->nombre;

            // POR CADA PRODUCTO OBTENER TODOS LOS LOTES/ENTRADAS
            $arraySalidas = DB::table('bodega_salidas AS sa')
                ->join('bodega_salidas_detalle AS deta', 'deta.id_salida', '=', 'sa.id')
                ->join('bodega_entradas_detalle AS bodeentradeta', 'deta.id_entradadetalle', '=', 'bodeentradeta.id')
                ->select('sa.fecha', 'bodeentradeta.id_material', 'bodeentradeta.id_entrada')
                ->whereBetween('sa.fecha', [$start, $end])
                ->where('bodeentradeta.id_material', $fila->id)
                ->get();


            // OBTENER LOS LOTES INDIVIDUALES
            $pilaEntrada = array();
            foreach ($arraySalidas as $filaItem) {
                array_push($pilaEntrada, $filaItem->id_entrada);
            }

            $arrayEntradaDeta = BodegaEntradasDetalle::
                where('id_material', $fila->id)
                ->get();

            $columnaExistenciaActual = 0;
            $columnaExistenciaActualDinero = 0;
            $columnaExistenciaInicial = 0;
            $columnaSalidasTotales = 0;


            if($fila->id == 95){
               // return $arrayEntradaDeta;
            }

            foreach ($arrayEntradaDeta as $itemEntra) {
                $infoEntrada = BodegaEntradas::where('id', $itemEntra->id_entrada)->first();
                $itemEntra->lote = $infoEntrada->lote;

                $existencias = $itemEntra->cantidad - $itemEntra->cantidad_entregada;
                $itemEntra->existencias = $existencias;

                $columnaExistenciaInicial += $itemEntra->cantidad;
                $columnaSalidasTotales += $itemEntra->cantidad_entregada;
                $columnaExistenciaActual += $existencias;

                $multiplicado = $itemEntra->precio * $existencias;
                $columnaExistenciaActualDinero += $multiplicado;

                $sumaFinalSaldoExisteciaActual += $multiplicado;

                $itemEntra->saldoExistenciasDinero = "$" . number_format($multiplicado, 2, '.', ',');
                $itemEntra->precioFormat = "$" . number_format($itemEntra->precio, 2, '.', ',');
            }

            $fila->columnaExistenciaInicial = $columnaExistenciaInicial;
            $fila->columnaSalidasTotales = $columnaSalidasTotales;
            $fila->columnaExistenciaActual = $columnaExistenciaActual;
            $fila->columnaExistenciaActualDinero = "$" . number_format($columnaExistenciaActualDinero, 2, '.', ',');



            $resultsBloque[$index]->detalle = $arrayEntradaDeta;
            $index++;
        }

        $sumaFinalSaldoExisteciaActual = "$" . number_format($sumaFinalSaldoExisteciaActual, 2, '.', ',');


        //************************************************


        // INFORMACION GERENCIA
        $infoGerencia = BodegaExtras::where('id', 1)->first();


        $mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        //$mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);

        $mpdf->SetTitle('Reporte general de existencias');

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
                        <h2 style='font-size: 14px; margin: 0; color: #003366; text-transform: uppercase;'>$infoUsuario->cargo2</h2>
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
                <h1 style='font-size: 14px; margin: 0; color: #000;'>REPORTE GENERAL DE EXISTENCIAS</h1>
            </div>
            <div style='text-align: left; margin-top: 10px;'>
            <p style='font-size: 12px; margin: 0; color: #000;'><strong>PERÍODO: </strong>  $desdeFormat AL $hastaFormat</p>
        </div>
      ";


        // Encabezado de la tabla
        $tabla .= "<table width='100%' id='tablaFor' style='margin-top: 30px'>
            <thead>
                <tr>
                    <th style='font-weight: bold; width: 6%; font-size: 11px; text-align: center;'>CORRELATIVO</th>
                    <th style='font-weight: bold; width: 8%; font-size: 11px; text-align: center;'>CÓDIGO DEL PRODUCTO</th>
                    <th style='font-weight: bold; width: 8%; font-size: 11px; text-align: center;'>UNIDAD MEDIDA</th>
                    <th style='font-weight: bold; width: 8%; font-size: 11px; text-align: center;'>ARTÍCULO/DESCRIPCIÓN</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>LOTE</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>PRECIO UNITARIO</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>EXISTENCIA INICIAL</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>SALIDAS TOTALES</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>EXISTENCIA ACTUAL</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>SALDO (EXISTENCIA ACTUAL)</th>
                </tr>
            </thead>
            <tbody>";


        $correlativo = 0;
        foreach ($arrayProductos as $dato) {

            /*if (empty($dato->detalle) || count($dato->detalle) === 0) {
                continue;
            }*/

            $correlativo++;


            $tabla .= "<tr>
                    <td style='font-size: 11px; font-weight: bold'>$correlativo</td>
                    <td style='font-size: 11px; font-weight: bold'></td>
                    <td style='font-size: 11px; font-weight: bold'>$dato->unidadMedida</td>
                    <td style='font-size: 11px; font-weight: bold'>$dato->nombre</td>
                    <td style='font-size: 11px; font-weight: bold'></td>
                    <td style='font-size: 11px;font-weight: bold'></td>
                    <td style='font-size: 11px; font-weight: bold'></td>
                    <td style='font-size: 11px; font-weight: bold'></td>
                    <td style='font-size: 11px; font-weight: bold'></td>
                    <td style='font-size: 11px; font-weight: bold'></td>
                </tr>";


            foreach ($dato->detalle as $item) {
                $tabla .= "<tr>
                     <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'>$item->codigo_producto</td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'>$item->lote</td>
                    <td style='font-size: 11px'>$item->precioFormat</td>
                    <td style='font-size: 11px'>$item->cantidad</td>
                    <td style='font-size: 11px'>$item->cantidad_entregada</td>
                    <td style='font-size: 11px'>$item->existencias</td>
                    <td style='font-size: 11px'>$item->saldoExistenciasDinero</td>
                </tr>";

            } // END-FOREACH 2


            $tabla .= "<tr>
                    <td colspan='4' style='font-size: 11px; font-weight: bold'>EXISTENCIA TOTAL</td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px; font-weight: bold'>$dato->columnaExistenciaActual</td>
                    <td style='font-size: 11px; font-weight: bold'>$dato->columnaExistenciaActualDinero</td>

                </tr>";


        }// END-FOREACH



        $tabla .= "</tbody></table>";



        $tabla .= "<table width='100%' id='tablaFor' style='margin-top: 30px'>
            <thead>
                <tr>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>SALDO (EXISTENCIA ACTUAL)</th>
                </tr>
            </thead>
            <tbody>";




        $tabla .= "<tr>
                    <td style='font-size: 11px; font-weight: bold'>$sumaFinalSaldoExisteciaActual</td>

                </tr>";



        $tabla .= "</tbody></table>";



        $tabla .= "
            <div style='text-align: left; margin-top: 25px; font-family: \"Times New Roman\", Times, serif;'>
                <p style='font-size: 12px; margin: 5px 0; color: #000; line-height: 2;'>
                    <strong>OBSERVACIONES:</strong><br>
                    __________________________________________________________________________________________________________________ <br>
                    __________________________________________________________________________________________________________________ <br>
                    __________________________________________________________________________________________________________________
                </p>
            </div>
            <br>
        ";




        $tabla .= "
            <table style='width: 100%; margin-top: 20px; font-family: \"Times New Roman\", Times, serif; font-size: 14px; color: #000;'>
                <!-- Fila para los títulos -->
                <tr>
                    <td style='width: 50%; text-align: left; padding-bottom: 15px;'>
                        <p style='margin: 0; font-weight: bold; margin-left: 15px; font-size: 11px'>REPORTE GENERADO POR:</p>
                    </td>
                    <td style='width: 50%; padding-bottom: 15px; font-size: 11px; display: flex; flex-direction: column; align-items: flex-end; padding-right: 15px;'>
                        <p style='margin: 0; font-weight: bold;'>REVISADO POR:</p>
                    </td>
                </tr>
                <!-- Fila para los contenidos -->
                <tr>
                    <td style='width: 50%; text-align: left; padding: 20px;'>
                        <p style='margin: 10px 0;'>$infoUsuario->nombre</p>
                        <p style='margin: 10px 0;'>$infoUsuario->cargo</p>
                    </td>
                    <td style='width: 50%; padding: 20px; display: flex; flex-direction: column; align-items: flex-end; padding-right: 15px;'>
                        <p style='margin: 10px 0;'>$infoGerencia->nombre_gerente</p>
                        <p style='margin: 10px 0;'>$infoGerencia->nombre_gerente_cargo</p>
                    </td>
                </tr>
            </table>
        ";

        $stylesheet = file_get_contents('css/cssbodega.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla,2);

        $mpdf->Output();
    }





    // FECHAS EXISTENCIAS POR FECHAS
    public function generarPDFExistenciasFechasLotes($desde, $hasta, $arrayLotes) {

        $start = Carbon::parse($desde)->startOfDay();
        $end = Carbon::parse($hasta)->endOfDay();

        $desdeFormat = date("d/m/Y", strtotime($desde));
        $hastaFormat = date("d/m/Y", strtotime($hasta));


        // FILTRANDO SOLO OBJ ESPECIFICO DEL USUARIO

        $idusuario = Auth::id();
        $infoUsuario = Usuario::where('id', $idusuario)->first();



        // ESTO ES ID DE bodega_entradas
        $porciones = explode("-", $arrayLotes);
        $arrayBodegaEntradas = BodegaEntradas::whereIn('id', $porciones)->get();

        $pilaArrayBodegaEntradas = array();
        foreach ($arrayBodegaEntradas as $fila) {
            array_push($pilaArrayBodegaEntradas, $fila->id);
        }





// Clave compuesta material|lote normalizada para evitar "30" vs 30
        $makeKey = function ($idEntrada, $idMaterial) {
            return (int)$idEntrada . '|' . (int)$idMaterial;
        };

// 1) ENTRADAS por lote (precio unitario por lote)
        $entradasPorLote = DB::table('bodega_entradas_detalle AS bed')
            ->join('bodega_materiales AS bm', 'bed.id_material', '=', 'bm.id')
            ->select(
                'bed.id_entrada',
                'bed.id_material',
                'bm.id_objespecifico',
                'bm.nombre AS nombre_material',
                DB::raw('SUM(bed.cantidad) AS cantidad_entrada'),
                DB::raw('MAX(bed.precio) AS precio_unitario') // ajusta si tu campo se llama distinto
            )
            ->whereIn('bm.tipo_bodega', [$infoUsuario->tipo_bodega])
            ->whereIn('bed.id_entrada', $pilaArrayBodegaEntradas)
            ->groupBy('bed.id_entrada', 'bed.id_material', 'bm.id_objespecifico', 'bm.nombre')
            ->get();

        $entradasMap = [];
        foreach ($entradasPorLote as $e) {
            $entradasMap[$makeKey($e->id_entrada, $e->id_material)] = $e;
        }

// 2) SALIDAS previas al inicio (para existencia inicial)
        $salidasPrevias = DB::table('bodega_salidas AS bs')
            ->join('bodega_salidas_detalle AS bsd', 'bsd.id_salida', '=', 'bs.id')
            ->join('bodega_entradas_detalle AS bed', 'bsd.id_entradadetalle', '=', 'bed.id')
            ->join('bodega_materiales AS bm', 'bed.id_material', '=', 'bm.id')
            ->select(
                'bed.id_entrada',
                'bed.id_material',
                DB::raw('SUM(bsd.cantidad_salida) AS cantidad_previa')
            )
            ->whereIn('bm.tipo_bodega', [$infoUsuario->tipo_bodega])
            ->whereIn('bed.id_entrada', $pilaArrayBodegaEntradas)
            ->where('bs.fecha', '<', $start)
            ->groupBy('bed.id_entrada', 'bed.id_material')
            ->get();

        $previasMap = [];
        foreach ($salidasPrevias as $s) {
            $previasMap[$makeKey($s->id_entrada, $s->id_material)] = (float) $s->cantidad_previa;
        }

// 3) SALIDAS del período (para "salidas totales")
// Nota: LEFT JOIN a obj_especifico para no perder filas si no hay match
        $salidasPeriodo = DB::table('bodega_salidas AS bs')
            ->join('bodega_salidas_detalle AS bsd', 'bsd.id_salida', '=', 'bs.id')
            ->join('bodega_entradas_detalle AS bed', 'bsd.id_entradadetalle', '=', 'bed.id')
            ->join('bodega_materiales AS bm', 'bed.id_material', '=', 'bm.id')
            ->leftJoin('obj_especifico AS obj', 'bm.id_objespecifico', '=', 'obj.id') // opcional
            ->select(
                'bm.id_objespecifico',
                'bed.id_entrada',
                'bed.id_material',
                'obj.codigo AS codigo_obj',
                DB::raw('SUM(bsd.cantidad_salida) AS salidas_totales')
            )
            ->whereIn('bm.tipo_bodega', [$infoUsuario->tipo_bodega])
            ->whereIn('bed.id_entrada', $pilaArrayBodegaEntradas)
            ->whereBetween('bs.fecha', [$start, $end])
            ->groupBy('bm.id_objespecifico', 'bed.id_entrada', 'bed.id_material', 'obj.codigo')
            ->get();

    // Si no hay salidas en el período, retorna vacío (o ajusta a tu necesidad)
       /* if ($salidasPeriodo->isEmpty()) {
            return "vacio";
            return collect();
        }*/

    // 4) Combinar y calcular existencias/saldos por material+lote
        $items = [];
        foreach ($salidasPeriodo as $sp) {
            $key = $makeKey($sp->id_entrada, $sp->id_material);
            if (!isset($entradasMap[$key])) {
                // No hay entrada para ese lote/material → no podemos calcular existencia inicial
                continue;
            }

            $e            = $entradasMap[$key];
            $cantEntrada  = (float) $e->cantidad_entrada;
            $cantPrevias  = (float) ($previasMap[$key] ?? 0);
            $existInicial = max(0, $cantEntrada - $cantPrevias);
            $salidasTot   = (float) $sp->salidas_totales;
            $existActual  = max(0, $existInicial - $salidasTot);
            $precio       = (float) $e->precio_unitario;

            $items[] = [
                'id_objespecifico' => $e->id_objespecifico,
                'codigo'           => $sp->codigo_obj, // puede ser null si no hay match en obj_especifico

                'detalle_item' => [
                    'id_entrada'               => (int) $sp->id_entrada, // LOTE
                    'id_material'              => (int) $sp->id_material,
                    'nombre_material'          => $e->nombre_material,
                    'precio_unitario'          => $precio,
                    'existencia_inicial'       => $existInicial,
                    'salidas_totales'          => $salidasTot,
                    'saldo_salidas'            => round($salidasTot * $precio, 2),
                    'existencia_actual'        => $existActual,
                    'saldo_existencia_actual'  => round($existActual * $precio, 2),
                ],
            ];
        }

    // 5) Reagrupar por id_objespecifico, ordenar dentro de cada grupo y
    //    asignar correlativo 1..N por grupo (se reinicia en cada array)
        $resultado = collect($items)
            ->groupBy('id_objespecifico')
            ->map(function ($regs, $obj) {
                // (Opcional) si guardaste "codigo" en $items, tómalo del primero
                $codigo = null;
                $primero = $regs->first();
                if (is_array($primero) && array_key_exists('codigo', $primero)) {
                    $codigo = $primero['codigo'];
                } elseif (is_object($primero) && property_exists($primero, 'codigo')) {
                    $codigo = $primero->codigo;
                }

                // Orden dentro del grupo (ajústalo si prefieres otro criterio)
                $detalle = $regs->pluck('detalle_item')
                    ->sortBy(function ($it) {
                        $nom  = $it['nombre_material'];
                        $lote = str_pad((string)$it['id_entrada'], 10, '0', STR_PAD_LEFT);
                        return $nom . '|' . $lote;
                    })
                    ->values();

                // Correlativo por grupo (reinicia en 1)
                $i = 0;
                $detalle = $detalle->map(function ($item) use (&$i) {
                    $i++;
                    $item['correlativo'] = $i;   // 👈 sin correlativo_str
                    return $item;
                })->values();

                return [
                    'id_objespecifico' => $obj,
                    'codigo'           => $codigo,   // opcional
                    'detalle'          => $detalle,  // cada item trae su correlativo 1..N
                ];
            })
            ->values();

        //return $resultado;







        //************************************************


        // INFORMACION GERENCIA
        $infoGerencia = BodegaExtras::where('id', 1)->first();


       // $mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);

        $mpdf->SetTitle('Existencias General');

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
                        <h2 style='font-size: 14px; margin: 0; color: #003366; text-transform: uppercase;'>$infoUsuario->cargo2</h2>
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
            <table width='100%' style='border-collapse: collapse; margin-top: 20px;'>
                <tr style='background-color: #B0C4DE;'>
                    <td style='text-align: center; padding: 8px;'>
                        <h1 style='font-size: 14px; margin: 0; color: #000000;'>
                            REPORTE DE SALIDAS
                        </h1>
                    </td>
                </tr>
            </table>
            <div style='text-align: left; margin-top: 10px;'>
                <p style='font-size: 12px; margin: 0; color: #000;'>
                    PERÍODO: $desdeFormat AL $hastaFormat
                </p>
            </div>
        ";


        // Encabezado de la tabla
        $tabla .= "<table width='100%' id='tablaFor' style='margin-top: 30px'>
            <thead>
                <tr>
                    <th style='font-weight: bold; width: 6%; font-size: 11px; text-align: center;'>CORRELATIVO</th>
                    <th style='font-weight: bold; width: 8%; font-size: 11px; text-align: center;'>CÓDIGO PRESUPUESTARIO</th>
                    <th style='font-weight: bold; width: 8%; font-size: 11px; text-align: center;'>UNIDAD DE MEDIDA</th>
                    <th style='font-weight: bold; width: 8%; font-size: 11px; text-align: center;'>ARTÍCULO/DESCRIPCIÓN</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>LOTE</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>PRECIO UNITARIO</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>EXISTENCIA INICIAL</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>SALIDAS TOTALES</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>SALDO (SALIDAS)</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>EXISTENCIA ACTUAL</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>SALDO (EXISTENCIA ACTUAL)</th>
                </tr>
            </thead>
            <tbody>";


        foreach ($resultado as $dato) {

            $codigo = $dato['codigo'];
            $sumaSaldoSalidasColumna = 0;
            $sumaSaldoExistenciaActual = 0;

            foreach ($dato['detalle'] as $item) {

                $correlativo       = $item['correlativo'] ?? null;
                $lote              = $item['id_entrada'];
                $idMaterial        = $item['id_material'];
                $nombreMaterial    = $item['nombre_material'];
                $precio            = $item['precio_unitario'];
                $existInicial      = $item['existencia_inicial'];
                $salidasTotales    = $item['salidas_totales'];
                $saldoSalidas      = $item['saldo_salidas'];
                $existActual       = $item['existencia_actual'];
                $saldoExistActual  = $item['saldo_existencia_actual'];

                $sumaSaldoSalidasColumna += $saldoSalidas;
                $sumaSaldoExistenciaActual += $saldoExistActual;


                $precio = "$" . number_format((float)$precio, 2, '.', ',');
                $saldoSalidas = "$" . number_format((float)$saldoSalidas, 2, '.', ',');
                $saldoExistActual = "$" . number_format((float)$saldoExistActual, 2, '.', ',');

                $infoMaterial = BodegaMateriales::where('id', $idMaterial)->first();
                $infoUnidad = P_UnidadMedida::where('id', $infoMaterial->id_unidadmedida)->first();
                $nombreUnidadMedida = $infoUnidad->nombre;

                $infoEntrada = BodegaEntradas::where('id', $lote)->first();
                $nombreEntradaLote = $infoEntrada->nombre;

                $tabla .= "<tr>
                     <td style='font-size: 11px'>$correlativo</td>
                    <td style='font-size: 11px'>$codigo</td>
                    <td style='font-size: 11px'>$nombreUnidadMedida</td>
                    <td style='font-size: 11px'>$nombreMaterial</td>
                    <td style='font-size: 11px'>$nombreEntradaLote</td>
                    <td style='font-size: 11px'>$precio</td>
                    <td style='font-size: 11px'>$existInicial</td>
                    <td style='font-size: 11px'>$salidasTotales</td>
                    <td style='font-size: 11px'>$saldoSalidas</td>
                    <td style='font-size: 11px'>$existActual</td>
                    <td style='font-size: 11px; font-weight: bold'>$saldoExistActual</td>
                </tr>";

            } // END-FOREACH 2


            $sumaSaldoSalidasColumna = "$" . number_format((float)$sumaSaldoSalidasColumna, 2, '.', ',');
            $sumaSaldoExistenciaActual = "$" . number_format((float)$sumaSaldoExistenciaActual, 2, '.', ',');


            $tabla .= "<tr style='background-color: #F8CCA2'>
                    <td colspan='4' style='font-size: 11px; font-weight: bold'>TOTALES $codigo</td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px; font-weight: bold'>$sumaSaldoSalidasColumna</td>
                    <td style='font-size: 11px; font-weight: bold'></td>
                      <td style='font-size: 11px; font-weight: bold'>$sumaSaldoExistenciaActual</td>

                </tr>";


        }// END-FOREACH



        $tabla .= "</tbody></table>";



        $tabla .= "
            <table style='width: 100%; margin-top: 40px; font-family: \"Times New Roman\", Times, serif; font-size: 14px; color: #000;'>
                <!-- Fila para los títulos -->
                <tr>
                    <td style='width: 50%; text-align: left; padding-bottom: 15px;'>
                        <p style='margin: 0; font-weight: normal; margin-left: 15px; font-size: 11px'>ELABORADO POR:</p>
                    </td>
                    <td style='width: 50%; padding-bottom: 15px; font-size: 11px; display: flex; flex-direction: column; align-items: flex-end; padding-right: 15px;'>
                        <p style='margin: 0; font-weight: normal;'>REVISADO POR:</p>
                    </td>
                </tr>

            </table>
        ";



        $tabla .= "
            <table style='width: 100%; margin-top: 40px; font-family: \"Times New Roman\", Times, serif; font-size: 14px; color: #000;'>
                <!-- Fila para los títulos -->
            <tr>
                <!-- Fila para los contenidos -->
                    <td style='width: 50%; text-align: left; padding: 20px;'>
                        <p style='margin: 10px 0; font-size: 11px'>$infoUsuario->nombre</p>
                        <p style='margin: 10px 0; font-size: 11px'>$infoUsuario->cargo</p>
                    </td>
                    <td style='width: 50%; padding: 20px; display: flex; flex-direction: column; align-items: flex-end; padding-right: 15px;'>
                        <p style='margin: 10px 0; font-size: 11px'>$infoGerencia->nombre_gerente</p>
                        <p style='margin: 10px 0; font-size: 11px'>$infoGerencia->nombre_gerente_cargo</p>
                    </td>
                </tr>
            </table>
        ";


        $stylesheet = file_get_contents('css/cssbodega.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla,2);

        $mpdf->Output();
    }













    // FECHAS EXISTENCIAS POR FECHAS POR DESGLOSE CARDEX
    public function generarPDFExistenciasFechasDesglose($desde, $hasta, $idproducto) {

        $start = Carbon::parse($desde)->startOfDay();
        $end = Carbon::parse($hasta)->endOfDay();

        $desdeFormat = date("d/m/Y", strtotime($desde));
        $hastaFormat = date("d/m/Y", strtotime($hasta));

        $infoProducto = BodegaMateriales::where('id', $idproducto)->first();
        $infoUnidadMedida = P_UnidadMedida::where('id', $infoProducto->id_unidadmedida)->first();



        // POR CADA ENTRADA SERIA LA VUELTA, SIEMPRE SALDRAN AUNQUE NO HAYA SALIDAS
        $resultsBloque = array();
        $index = 0;

        $arrayEntradaDetalle = BodegaEntradasDetalle::where('id_material', $idproducto)->get();

        foreach ($arrayEntradaDetalle as $fila){
            array_push($resultsBloque, $fila);

            $saldoExistencia = $fila->cantidad - $fila->cantidad_entregada;
            $fila->existencia = $saldoExistencia;


            $infoEntradaBloque = BodegaEntradas::where('id', $fila->id_entrada)->first();
            $fila->loteProducto = $infoEntradaBloque->lote;


            // SUMATORIA COLUMNA
            $saldoTotalSalidas = 0;



            // HOY OBTENER TODAS LAS SALIDAS DE ESTA ENTRADA
            $arraySalidas = DB::table('bodega_salidas AS sa')
                ->join('bodega_salidas_detalle AS deta', 'deta.id_salida', '=', 'sa.id')
                ->join('bodega_entradas_detalle AS bodeentradeta', 'deta.id_entradadetalle', '=', 'bodeentradeta.id')
                ->select('sa.fecha', 'sa.id_solicitud', 'sa.observacion', 'sa.estado_salida',
                    'deta.id_solidetalle', 'deta.id_entradadetalle', 'deta.cantidad_salida', 'bodeentradeta.id_material',
                    'bodeentradeta.codigo_producto', 'bodeentradeta.cantidad', 'bodeentradeta.cantidad_entregada',
                    'bodeentradeta.id_entrada')
                ->whereBetween('sa.fecha', [$start, $end])
                ->where('deta.id_entradadetalle', $fila->id)
                ->get();

                foreach ($arraySalidas as $item){
                    $nombreDepSolicitud = "";
                    $numeroSolicitud = "";

                    $saldoTotalSalidas += $item->cantidad_salida;

                    // evitar salidas manuales
                    if($infoSolicitud = BodegaSolicitud::where('id', $item->id_solicitud)->first()){

                        $numeroSolicitud = $infoSolicitud->numero_solicitud;

                        // buscar departamento
                        if($infoUserDepa = P_UsuarioDepartamento::where('id_usuario', $infoSolicitud->id_usuario)->first()){
                            $infoDepa = P_Departamento::where('id', $infoUserDepa->id_departamento)->first();
                            $nombreDepSolicitud = $infoDepa->nombre;
                        }
                    }else{
                        // fue salida manual
                        if($item->estado_salida == 1){
                            $nombreDepSolicitud = "Salida sin Solicitud";
                        }else{
                            $nombreDepSolicitud = "Salida por Desperfecto";
                        }
                    }

                    $item->numeroSolicitud = $numeroSolicitud;
                    $item->nombreDepSolicitud = $nombreDepSolicitud;
                } // END-FOREACH SEGUNDO


                $fila->saldoTotalSalidas = $saldoTotalSalidas;

            $resultsBloque[$index]->detalle = $arraySalidas;
            $index++;
        }


        //************************************************


        $infoAuth = auth()->user();
        // USUARIO LOGEADO
        $infoUsuarioLogeado = Usuario::where('id', $infoAuth->id)->first();

        // INFORMACION GERENCIA
        $infoGerencia = BodegaExtras::where('id', 1)->first();




        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER', 'orientation' => 'L']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER', 'orientation' => 'L']);

        $mpdf->SetTitle('Existencias General - Desglose');

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
                        <h2 style='font-size: 14px; margin: 0; color: #003366; text-transform: uppercase;'>$infoUsuarioLogeado->cargo2</h2>
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
                <h1 style='font-size: 16px; margin: 0; color: #000;'>DESGLOSE DE MOVIMIENTOS DE INVENTARIO</h1>
            </div>
            <div style='text-align: left; margin-top: 10px;'>
            <p style='font-size: 14px; margin: 0; color: #000;'><strong>PERÍODO:</strong> $desdeFormat AL $hastaFormat</p>
        </div>
      ";


        $tabla .= "
            <div style='text-align: left; margin-top: 10px;'>
            <p style='font-size: 14px; margin: 0; color: #000;'><strong>Producto: </strong>$infoProducto->nombre</p>
            <p style='font-size: 14px; margin: 0; color: #000;'><strong>U. Medida: </strong>$infoUnidadMedida->nombre</p>
        </div>
      ";



        // Encabezado de la tabla
        $tabla .= "<table width='100%' id='tablaFor' style='margin-top: 30px'>
            <thead>
                <tr>
                    <th style='font-weight: bold; width: 8%; font-size: 11px; text-align: center;'>CÓDIGO DEL PRODUCTO</th>
                    <th style='font-weight: bold; width: 8%; font-size: 11px; text-align: center;'>SOLICITUD DE PEDIDO</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>ENTRADAS</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>N° DE SOLICITUD</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>SALIDAS</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>UNIDAD SOLICITANTE</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>EXISTENCIAS</th>
                </tr>
            </thead>
            <tbody>";


        foreach ($arrayEntradaDetalle as $dato) {
            $tabla .= "<tr>
                    <td style='font-size: 11px; font-weight: bold'>$dato->codigo_producto</td>
                    <td style='font-size: 11px; font-weight: bold'>$dato->loteProducto</td>
                    <td style='font-size: 11px; font-weight: bold'>$dato->cantidad</td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                     <td style='font-size: 11px'></td>
                </tr>";


            foreach ($dato->detalle as $item) {
                $tabla .= "<tr>
                     <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'>$item->numeroSolicitud</td>
                    <td style='font-size: 11px'>$item->cantidad_salida</td>
                    <td style='font-size: 11px'>$item->nombreDepSolicitud</td>
                    <td style='font-size: 11px'></td>
                </tr>";

            } // END-FOREACH 2

            $tabla .= "<tr>
                     <td colspan='2' style='font-size: 11px; font-weight: bold'>TOTALES</td>
                    <td style='font-size: 11px'></td>
                     <td style='font-size: 11px'></td>
                    <td style='font-size: 11px; font-weight: bold'>$dato->saldoTotalSalidas</td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px; font-weight: bold'>$dato->existencia</td>
                </tr>";

        }// END-FOREACH





        $tabla .= "</tbody></table>";





        $tabla .= "
            <table style='width: 100%; margin-top: 20px; font-family: \"Times New Roman\", Times, serif; font-size: 14px; color: #000;'>
                <!-- Fila para los títulos -->
                <tr>
                    <td style='width: 50%; text-align: left; padding-bottom: 15px;'>
                        <p style='margin: 0; font-weight: bold; margin-left: 15px; font-size: 11px'>REPORTE GENERADO POR:</p>
                    </td>
                    <td style='width: 50%; padding-bottom: 15px; font-size: 11px; display: flex; flex-direction: column; align-items: flex-end; padding-right: 15px;'>
                        <p style='margin: 0; font-weight: bold;'>REVISADO POR:</p>
                    </td>
                </tr>
                <!-- Fila para los contenidos -->
                <tr>
                    <td style='width: 50%; text-align: left; padding: 20px;'>
                        <p style='margin: 10px 0;'>$infoUsuarioLogeado->nombre</p>
                        <p style='margin: 10px 0;'>$infoUsuarioLogeado->cargo</p>
                    </td>
                    <td style='width: 50%; padding: 20px; display: flex; flex-direction: column; align-items: flex-end; padding-right: 15px;'>
                        <p style='margin: 10px 0;'>$infoGerencia->nombre_gerente</p>
                        <p style='margin: 10px 0;'>$infoGerencia->nombre_gerente_cargo</p>
                    </td>
                </tr>
            </table>
        ";



        $mpdf->setMargins(5, 5, 5);
        $stylesheet = file_get_contents('css/cssbodega.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla,2);

        $mpdf->Output();
    }







    // FECHAS EXISTENCIAS POR FECHAS POR DESGLOSE CARDEX
    public function generarPDFExistenciasFechasDesgloseTodos($desde, $hasta) {

        $start = Carbon::parse($desde)->startOfDay();
        $end = Carbon::parse($hasta)->endOfDay();

        $desdeFormat = date("d/m/Y", strtotime($desde));
        $hastaFormat = date("d/m/Y", strtotime($hasta));


        $idusuario = Auth::id();
        $infoUsuario = Usuario::where('id', $idusuario)->first();

        $arrayProductos = BodegaMateriales::whereIn('tipo_bodega', [$infoUsuario->tipo_bodega])
            ->orderBy('nombre', 'ASC')
            ->get();


        $pilaIdProductos = array();
        foreach ($arrayProductos as $fila) {
            array_push($pilaIdProductos, $fila->id);
        }

        $resultsBloque = array();
        $index = 0;

        $arrayEntradaDetalle = BodegaEntradasDetalle::whereIn('id_material', $pilaIdProductos)->get();

        foreach ($arrayEntradaDetalle as $fila){
            array_push($resultsBloque, $fila);


            $infoMateriales = BodegaMateriales::where('id', $fila->id_material)->first();
            $fila->nombreMaterial = $infoMateriales->nombre;

            $saldoExistencia = $fila->cantidad - $fila->cantidad_entregada;
            $fila->existencia = $saldoExistencia;

            $infoEntradaBloque = BodegaEntradas::where('id', $fila->id_entrada)->first();
            $fila->loteProducto = $infoEntradaBloque->lote;

            // SUMATORIA COLUMNA
            $saldoTotalSalidas = 0;

            // HOY OBTENER TODAS LAS SALIDAS DE ESTA ENTRADA
            $arraySalidas = DB::table('bodega_salidas AS sa')
                ->join('bodega_salidas_detalle AS deta', 'deta.id_salida', '=', 'sa.id')
                ->join('bodega_entradas_detalle AS bodeentradeta', 'deta.id_entradadetalle', '=', 'bodeentradeta.id')
                ->select('sa.fecha', 'sa.id_solicitud', 'sa.observacion', 'sa.estado_salida',
                    'deta.id_solidetalle', 'deta.id_entradadetalle', 'deta.cantidad_salida', 'bodeentradeta.id_material',
                    'bodeentradeta.codigo_producto', 'bodeentradeta.cantidad', 'bodeentradeta.cantidad_entregada',
                    'bodeentradeta.id_entrada')
                ->whereBetween('sa.fecha', [$start, $end])
                ->where('deta.id_entradadetalle', $fila->id)
                ->get();

            foreach ($arraySalidas as $item){
                $nombreDepSolicitud = "";
                $numeroSolicitud = "";

                $saldoTotalSalidas += $item->cantidad_salida;

                // evitar salidas manuales
                if($infoSolicitud = BodegaSolicitud::where('id', $item->id_solicitud)->first()){

                    $numeroSolicitud = $infoSolicitud->numero_solicitud;

                    // buscar departamento
                    if($infoUserDepa = P_UsuarioDepartamento::where('id_usuario', $infoSolicitud->id_usuario)->first()){
                        $infoDepa = P_Departamento::where('id', $infoUserDepa->id_departamento)->first();
                        $nombreDepSolicitud = $infoDepa->nombre;
                    }
                }else{
                    // fue salida manual
                    if($item->estado_salida == 1){
                        $nombreDepSolicitud = "Salida sin Solicitud";
                    }else{
                        $nombreDepSolicitud = "Salida por Desperfecto";
                    }
                }

                $item->numeroSolicitud = $numeroSolicitud;
                $item->nombreDepSolicitud = $nombreDepSolicitud;
            } // END-FOREACH SEGUNDO


            $fila->saldoTotalSalidas = $saldoTotalSalidas;

            $resultsBloque[$index]->detalle = $arraySalidas;
            $index++;
        }


        //************************************************


        // INFORMACION GERENCIA
        $infoGerencia = BodegaExtras::where('id', 1)->first();


        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER', 'orientation' => 'L']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER', 'orientation' => 'L']);

        $mpdf->SetTitle('Existencias General - Desglose');

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
                        <h2 style='font-size: 14px; margin: 0; color: #003366; text-transform: uppercase;'>$infoUsuario->cargo2</h2>
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
                <h1 style='font-size: 16px; margin: 0; color: #000;'>DESGLOSE DE MOVIMIENTOS DE INVENTARIO</h1>
            </div>
            <div style='text-align: left; margin-top: 10px;'>
            <p style='font-size: 14px; margin: 0; color: #000;'><strong>PERÍODO:</strong> $desdeFormat AL $hastaFormat</p>
        </div>
      ";



        // Encabezado de la tabla
        $tabla .= "<table width='100%' id='tablaFor' style='margin-top: 30px'>
            <thead>
                <tr>
                    <th style='font-weight: bold; width: 8%; font-size: 11px; text-align: center;'>CÓDIGO DEL PRODUCTO</th>
                    <th style='font-weight: bold; width: 8%; font-size: 11px; text-align: center;'>PRODUCTO</th>
                    <th style='font-weight: bold; width: 8%; font-size: 11px; text-align: center;'>LOTE</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>ENTRADAS</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>N° DE SOLICITUD</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>SALIDAS</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>UNIDAD SOLICITANTE</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>EXISTENCIAS</th>
                </tr>
            </thead>
            <tbody>";


        foreach ($arrayEntradaDetalle as $dato) {
            $tabla .= "<tr>
                    <td style='font-size: 11px; font-weight: bold'>$dato->codigo_producto</td>
                     <td style='font-size: 11px; font-weight: bold'>$dato->nombreMaterial</td>
                    <td style='font-size: 11px; font-weight: bold'>$dato->loteProducto</td>
                    <td style='font-size: 11px; font-weight: bold'>$dato->cantidad</td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                     <td style='font-size: 11px'></td>
                </tr>";


            foreach ($dato->detalle as $item) {
                $tabla .= "<tr>
                     <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                     <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'>$item->numeroSolicitud</td>
                    <td style='font-size: 11px'>$item->cantidad_salida</td>
                    <td style='font-size: 11px'>$item->nombreDepSolicitud</td>
                    <td style='font-size: 11px'></td>
                </tr>";

            } // END-FOREACH 2

            $tabla .= "<tr>
                     <td colspan='3' style='font-size: 11px; font-weight: bold'>TOTALES</td>
                    <td style='font-size: 11px'></td>
                     <td style='font-size: 11px'></td>
                    <td style='font-size: 11px; font-weight: bold'>$dato->saldoTotalSalidas</td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px; font-weight: bold'>$dato->existencia</td>
                </tr>";

        }// END-FOREACH





        $tabla .= "</tbody></table>";





        $tabla .= "
            <table style='width: 100%; margin-top: 20px; font-family: \"Times New Roman\", Times, serif; font-size: 14px; color: #000;'>
                <!-- Fila para los títulos -->
                <tr>
                    <td style='width: 50%; text-align: left; padding-bottom: 15px;'>
                        <p style='margin: 0; font-weight: bold; margin-left: 15px; font-size: 11px'>REPORTE GENERADO POR:</p>
                    </td>
                    <td style='width: 50%; padding-bottom: 15px; font-size: 11px; display: flex; flex-direction: column; align-items: flex-end; padding-right: 15px;'>
                        <p style='margin: 0; font-weight: bold;'>REVISADO POR:</p>
                    </td>
                </tr>
                <!-- Fila para los contenidos -->
                <tr>
                    <td style='width: 50%; text-align: left; padding: 20px;'>
                        <p style='margin: 10px 0;'>$infoUsuario->nombre</p>
                        <p style='margin: 10px 0;'>$infoUsuario->cargo</p>
                    </td>
                    <td style='width: 50%; padding: 20px; display: flex; flex-direction: column; align-items: flex-end; padding-right: 15px;'>
                        <p style='margin: 10px 0;'>$infoGerencia->nombre_gerente</p>
                        <p style='margin: 10px 0;'>$infoGerencia->nombre_gerente_cargo</p>
                    </td>
                </tr>
            </table>
        ";



        $mpdf->setMargins(5, 5, 5);
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

        // USUARIO LOGEADO
        $infoAuth = auth()->user();
        $infoUsuarioLogeado = Usuario::where('id', $infoAuth->id)->first();

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
            $columnaTotalMultiplicado += $multiplicado; // REDONDEADO A 2 DECIMALES


            $fila->multiplicado = '$' . number_format($multiplicado, 2, '.', ',');
        }


        $arraySalidaDetalle = $arraySalidaDetalle->sortBy('fechaFormat');


        // COLUMNA FINAL HASTA ABAJO
        $columnaTotalMultiplicado = round($columnaTotalMultiplicado, 2);
        $columnaTotalMultiplicado = '$' . number_format($columnaTotalMultiplicado, 2, '.', ',');



        $nombreUnidad = "";
        if($dato = P_UsuarioDepartamento::where('id_usuario', $idusuario)->first()){
            $infoDepa = P_Departamento::where('id', $dato->id_departamento)->first();
            $nombreUnidad = $infoDepa->nombre;
        }


        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);

        $mpdf->SetTitle('Entregas');

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
                        <h2 style='font-size: 14px; margin: 0; color: #003366; text-transform: uppercase;'>$infoUsuarioLogeado->cargo2</h2>
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
            <p style='font-size: 14px; margin: 0; color: #000;'><strong>UNIDAD: </strong>$nombreUnidad</p>
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

        $infoUsuarioLogeado = Usuario::where('id', $infoAuth->id)->first();

        // TODAS LAS SALIDAS QUE HIZO MI USUARIO BODEGUERO
        $arrayBodegaSalida = BodegaSalida::where('id_usuario', $infoAuth->id)
            ->whereBetween('fecha', [$start, $end])
            ->where('id_solicitud', '!=', null)
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

        $mpdf->SetTitle('Entregas');

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
                        <h2 style='font-size: 14px; margin: 0; color: #003366; text-transform: uppercase;'>$infoUsuarioLogeado->cargo2</h2>
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






    // REPORTE POR CODIGO PRESUPUESTARIO
    public function generarPDFCodigosPresupuestarios($desde, $hasta, $arrayCodigos) {

        $start = Carbon::parse($desde)->startOfDay();
        $end = Carbon::parse($hasta)->endOfDay();

        $desdeFormat = date("d/m/Y", strtotime($desde));
        $hastaFormat = date("d/m/Y", strtotime($hasta));



        $idusuario = Auth::id();
        $infoUsuarioLogeado = Usuario::where('id', $idusuario)->first();


        // ESTO ES ID DE bodega_entradas
        $porciones = explode("-", $arrayCodigos);


        $resultsBloque = array();
        $index = 0;

        // YA ESTA FILTRADO SOLO DEL USUARIO
        $arrayPorcionesObjEspecifico = BodegaUsuarioObjEspecifico::whereIn('id', $porciones)->get();

        $totalFinalSaldoSalidas = 0;
        $totalFinalSaldoExistenciaActual = 0;
        $correlativo = 0;

        foreach ($arrayPorcionesObjEspecifico as $filaItem) {

            array_push($resultsBloque, $filaItem);

            // OBTENER TODOS LOS MATERIALES ASOCIADOS A ESE OBJ ESPECIFICO, QUE OTRO USUARIO BODEGA
            // NO PUEDE TENER EL MISMO OBJ ESPECIFICO
            $arrayMaterialesMios = BodegaMateriales::where('id_objespecifico', $filaItem->id_objespecifico)
                ->where('tipo_bodega', $infoUsuarioLogeado->tipo_bodega)
                ->get();

            $infoObjeEspecifico = ObjEspecifico::where('id', $filaItem->id_objespecifico)->first();
            $filaItem->codigo = $infoObjeEspecifico->codigo;

            $pilaIdMaterialValidos = array();
            foreach ($arrayMaterialesMios as $filaid) {
                array_push($pilaIdMaterialValidos, $filaid->id);
            }

            // POR CADA OBJ ESPECIFICO OBTENER TODOS LOS PRODUCTOS SALIDAS
            $arraySalidas = DB::table('bodega_salidas AS sa')
                ->join('bodega_salidas_detalle AS deta', 'deta.id_salida', '=', 'sa.id')
                ->join('bodega_entradas_detalle AS bodeentradeta', 'deta.id_entradadetalle', '=', 'bodeentradeta.id')
                ->select('sa.fecha', 'bodeentradeta.id_material', 'bodeentradeta.id_entrada', 'deta.id_entradadetalle',
                        'bodeentradeta.precio', 'bodeentradeta.cantidad AS existenciaInicial', 'bodeentradeta.cantidad_entregada AS salidasTotales')
                ->whereBetween('sa.fecha', [$start, $end])
                ->whereIn('bodeentradeta.id_material', $pilaIdMaterialValidos)
                ->get();


            $totalPorBloqueSaldoSalidas = 0;
            $totalPorBloqueSaldoExistenciaActual = 0;

            foreach ($arraySalidas as $filaDeta) {
                $correlativo++;
                $filaDeta->correlativo = $correlativo;



                $filaDeta->codigo = $infoObjeEspecifico->codigo;

                $infoMaterial = BodegaMateriales::where('id', $filaDeta->id_material)->first();
                $infoUnidad = P_UnidadMedida::where('id', $infoMaterial->id_unidadmedida)->first();
                $infoEntrada = BodegaEntradas::where('id', $filaDeta->id_entrada)->first();

                $filaDeta->nombreMaterial = $infoMaterial->nombre;
                $filaDeta->nombreUnidad = $infoUnidad->nombre;
                $filaDeta->lote = $infoEntrada->lote;

                $saldoSalidas = $filaDeta->salidasTotales * $filaDeta->precio;
                $totalPorBloqueSaldoSalidas += $saldoSalidas;
                $filaDeta->saldoSalidas = '$' . number_format((float)$saldoSalidas, 2, '.', ',');

                $existenciaActual  = $filaDeta->existenciaInicial - $filaDeta->salidasTotales;
                $filaDeta->existenciaActual = $existenciaActual;
                $totalPorBloqueSaldoExistenciaActual += $existenciaActual;
                $saldoExistenciaActual = ($filaDeta->existenciaInicial - $filaDeta->salidasTotales) * $filaDeta->precio;

                $filaDeta->saldoExistenciaActual = '$' . number_format((float)$saldoExistenciaActual, 2, '.', ',');
                $filaDeta->precioFormat = '$' . number_format((float)$filaDeta->precio, 2, '.', ',');
            }


            $totalFinalSaldoExistenciaActual += $totalPorBloqueSaldoExistenciaActual;
            $filaItem->totalPorBloqueSaldoExistenciaActual = '$' . number_format((float)$totalPorBloqueSaldoExistenciaActual, 2, '.', ',');

            $totalFinalSaldoSalidas += $totalPorBloqueSaldoSalidas;
            $filaItem->totalPorBloqueSaldoSalidas = '$' . number_format((float)$totalPorBloqueSaldoSalidas, 2, '.', ',');

            $resultsBloque[$index]->detalle = $arraySalidas;
            $index++;
        }

        $totalFinalSaldoExistenciaActual = '$' . number_format((float)$totalFinalSaldoExistenciaActual, 2, '.', ',');
        $totalFinalSaldoSalidas = '$' . number_format((float)$totalFinalSaldoSalidas, 2, '.', ',');



        //************************************************


        // INFORMACION GERENCIA
        $infoGerencia = BodegaExtras::where('id', 1)->first();


        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);

        $mpdf->SetTitle('Reporte de Salidas');

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
                        <h2 style='font-size: 14px; margin: 0; color: #003366; text-transform: uppercase;'>$infoUsuarioLogeado->cargo2</h2>
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
                <h1 style='font-size: 16px; margin: 0; color: #000;'>REPORTE DE SALIDAS</h1>
            </div>
            <div style='text-align: left; margin-top: 10px;'>
            <p style='font-size: 12px; margin: 0; color: #000;'><strong>PERÍODO: </strong>  $desdeFormat <strong>AL</strong> $hastaFormat</p>
        </div>
      ";





        // Encabezado de la tabla
        $tabla .= "<table width='100%' id='tablaFor' style='margin-top: 30px'>
            <thead>
                <tr>
                    <th style='font-weight: bold; width: 6%; font-size: 10px; text-align: center;'>CORRELATIVO</th>
                    <th style='font-weight: bold; width: 8%; font-size: 11px; text-align: center;'>CÓDIGO PRESUPUESTARIO</th>
                    <th style='font-weight: bold; width: 8%; font-size: 11px; text-align: center;'>UNIDAD DE MEDIDA</th>
                    <th style='font-weight: bold; width: 8%; font-size: 11px; text-align: center;'>ARTÍCULO/DESCRIPCIÓN</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>LOTE</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>PRECIO UNITARIO</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>EXISTENCIA INICIAL</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>SALIDAS TOTALES</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center; background-color: #D6EAF8'>SALDO (SALIDAS)</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center;'>EXISTENCIA ACTUAL</th>
                    <th style='font-weight: bold; width: 10%; font-size: 11px; text-align: center; background-color: #D6EAF8'>SALDO (EXISTENCIA ACTUAL)</th>
                </tr>
            </thead>
            <tbody>";


        foreach ($arrayPorcionesObjEspecifico as $dato) {

            if (empty($dato->detalle) || count($dato->detalle) === 0) {
                continue;
            }

            foreach ($dato->detalle as $item) {
                $tabla .= "<tr>
                     <td style='font-size: 11px'>$item->correlativo</td>
                    <td style='font-size: 11px'>$item->codigo</td>
                    <td style='font-size: 11px'>$item->nombreUnidad</td>
                    <td style='font-size: 11px'>$item->nombreMaterial</td>
                    <td style='font-size: 11px'>$item->lote</td>
                    <td style='font-size: 11px'>$item->precioFormat</td>
                    <td style='font-size: 11px'>$item->existenciaInicial</td>
                    <td style='font-size: 11px'>$item->salidasTotales</td>
                    <td style='font-size: 11px; background-color: #D6EAF8'>$item->saldoSalidas</td>
                    <td style='font-size: 11px'>$item->existenciaActual</td>
                    <td style='font-size: 11px; background-color: #D6EAF8'>$item->saldoExistenciaActual</td>
                </tr>";

            } // END-FOREACH 2

            $tabla .= "<tr style='background-color: #F8CCA2'>
                    <td colspan='4' style='font-size: 11px; font-weight: bold'>TOTALES $dato->codigo </td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px; font-weight: bold'>$dato->totalPorBloqueSaldoSalidas</td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px; font-weight: bold'>$dato->totalPorBloqueSaldoExistenciaActual</td>
                </tr>";

        }// END-FOREACH


        $tabla .= "<tr>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px; background-color: #D6EAF8'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px; background-color: #D6EAF8'></td>
                </tr>";

        $tabla .= "<tr style='background-color: #F8CCA2'>
                    <td colspan='5' style='font-size: 11px; font-weight: bold'>SALDO TOTAL</td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px; font-weight: bold'>$totalFinalSaldoSalidas</td>
                    <td style='font-size: 11px'></td>
                    <td style='font-size: 11px; font-weight: bold'>$totalFinalSaldoExistenciaActual</td>
                </tr>";


        $tabla .= "</tbody></table>";




        $tabla .= "
               <br><br>
            <div style='text-align: left; margin-top: 25px; font-family: \"Times New Roman\", Times, serif;'>
                <p style='font-size: 13px; margin: 5px 0; color: #000; line-height: 2;'>
                    <strong>OBSERVACIONES:</strong><br>
                    __________________________________________________________________________________________________________________ <br>
                    __________________________________________________________________________________________________________________ <br>
                    __________________________________________________________________________________________________________________
                </p>
            </div>
            <br>
        ";




        $tabla .= "
            <br><br>
            <table style='width: 100%; margin-top: 20px; font-family: \"Times New Roman\", Times, serif; font-size: 14px; color: #000;'>
                <!-- Fila para los títulos -->
                <tr>
                    <td style='width: 50%; text-align: left; padding-bottom: 15px;'>
                        <p style='margin: 0; font-weight: bold; margin-left: 15px; font-size: 11px'>REPORTE GENERADO POR:</p>
                    </td>
                    <td style='width: 50%; padding-bottom: 15px; font-size: 11px; display: flex; flex-direction: column; align-items: flex-end; padding-right: 15px;'>
                        <p style='margin: 0; font-weight: bold;'>REVISADO POR:</p>
                    </td>
                </tr>

                <!-- Fila vacía para dar espacio -->
                <tr>
                    <td colspan='2' style='height: 30px;'></td>
                </tr>
                      <!-- Fila vacía para dar espacio -->
                <tr>
                    <td colspan='2' style='height: 30px;'></td>
                </tr>

      <!-- Fila vacía para dar espacio -->
                <tr>
                    <td colspan='2' style='height: 30px;'></td>
                </tr>


                <tr>
                    <td style='width: 50%; text-align: left; padding: 20px;'>
                        <p style='margin: 10px 0;'>$infoUsuarioLogeado->nombre</p>
                        <p style='margin: 10px 0;'>$infoUsuarioLogeado->cargo</p>
                    </td>
                    <td style='width: 50%; padding: 20px; display: flex; flex-direction: column; align-items: flex-end; padding-right: 15px;'>
                        <p style='margin: 10px 0;'>$infoGerencia->nombre_gerente</p>
                        <p style='margin: 10px 0;'>$infoGerencia->nombre_gerente_cargo</p>
                    </td>
                </tr>
            </table>
            ";



        $stylesheet = file_get_contents('css/cssbodega.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla,2);

        $mpdf->Output();
    }



    public function vistaReporteEntregas()
    {
        return view('backend.admin.bodega.reportes.entregables.vistareporteentregables');
    }



    public function reportePDFInicialFinal($desde, $hasta)
    {
        $start = Carbon::parse($desde)->startOfDay();
        $end   = Carbon::parse($hasta)->endOfDay();

        $desdeFormat = Carbon::parse($desde)->format('d/m/Y');
        $hastaFormat = Carbon::parse($hasta)->format('d/m/Y');

        $tipoBodega = Auth::user()->tipo_bodega; // coincide con bodega_materiales.tipo_bodega

        // ----- Consulta base (agrupado por producto, sin lotes separados) -----
        $rows = DB::select("
        WITH entradas AS (
            SELECT
                ed.id              AS id_entradadetalle,
                ed.id_material,
                ed.precio,
                ed.codigo_producto,
                ed.nombre_copia,
                e.lote             AS lote,
                ed.cantidad        AS cantidad_entrada,
                e.fecha            AS fecha_entrada
            FROM bodega_entradas_detalle ed
            JOIN bodega_entradas e ON e.id = ed.id_entrada
            WHERE EXISTS (
                SELECT 1
                FROM bodega_materiales bm
                WHERE bm.id = ed.id_material
                  AND bm.tipo_bodega = ?
            )
        ),
        salidas AS (
            SELECT
                sd.id_entradadetalle,
                sd.cantidad_salida,
                s.fecha AS fecha_salida
            FROM bodega_salidas_detalle sd
            JOIN bodega_salidas s ON s.id = sd.id_salida
        ),
        in_before AS (
            SELECT id_entradadetalle, SUM(cantidad_entrada) AS qty_in_before
            FROM entradas
            WHERE fecha_entrada < ?
            GROUP BY id_entradadetalle
        ),
        out_before AS (
            SELECT id_entradadetalle, SUM(cantidad_salida) AS qty_out_before
            FROM salidas
            WHERE fecha_salida < ?
            GROUP BY id_entradadetalle
        ),
        in_period AS (
            SELECT id_entradadetalle, SUM(cantidad_entrada) AS qty_in_period
            FROM entradas
            WHERE fecha_entrada >= ? AND fecha_entrada <= ?
            GROUP BY id_entradadetalle
        ),
        out_period AS (
            SELECT id_entradadetalle, SUM(cantidad_salida) AS qty_out_period
            FROM salidas
            WHERE fecha_salida >= ? AND fecha_salida <= ?
            GROUP BY id_entradadetalle
        ),
        base AS (
            SELECT
                en.id_entradadetalle,
                en.id_material,
                obj.codigo AS codigo,
                COALESCE(m.nombre, en.nombre_copia) AS descripcion,
                en.precio,

                -- Cantidades por cada entrada
                COALESCE(ib.qty_in_before, 0) - COALESCE(ob.qty_out_before, 0) AS saldo_inicial_cant,
                COALESCE(ip.qty_in_period,  0) AS entradas_mes_cant,
                COALESCE(op.qty_out_period, 0) AS salidas_mes_cant,
                (COALESCE(ib.qty_in_before, 0) - COALESCE(ob.qty_out_before, 0)
                 + COALESCE(ip.qty_in_period, 0)
                 - COALESCE(op.qty_out_period, 0)) AS saldo_final_cant,

                -- Dinero por cada entrada
                ((COALESCE(ib.qty_in_before, 0) - COALESCE(ob.qty_out_before, 0)) * en.precio) AS saldo_inicial_money,
                (COALESCE(ip.qty_in_period,  0) * en.precio) AS entradas_mes_money,
                (COALESCE(op.qty_out_period, 0) * en.precio) AS salidas_mes_money,
                ((COALESCE(ib.qty_in_before, 0) - COALESCE(ob.qty_out_before, 0)
                  + COALESCE(ip.qty_in_period, 0) - COALESCE(op.qty_out_period, 0)) * en.precio) AS saldo_final_money
            FROM entradas en
            LEFT JOIN bodega_materiales m ON m.id = en.id_material
            LEFT JOIN obj_especifico obj ON obj.id = m.id_objespecifico
            LEFT JOIN in_before  ib ON ib.id_entradadetalle  = en.id_entradadetalle
            LEFT JOIN out_before ob ON ob.id_entradadetalle  = en.id_entradadetalle
            LEFT JOIN in_period  ip ON ip.id_entradadetalle  = en.id_entradadetalle
            LEFT JOIN out_period op ON op.id_entradadetalle  = en.id_entradadetalle
        )

        -- AGRUPAMOS POR PRODUCTO (material + código + descripción + precio)
        SELECT
            b.id_material,
            b.codigo,
            b.descripcion,
            b.precio,

            SUM(b.saldo_inicial_cant)  AS saldo_inicial_cant,
            SUM(b.entradas_mes_cant)   AS entradas_mes_cant,
            SUM(b.salidas_mes_cant)    AS salidas_mes_cant,
            SUM(b.saldo_final_cant)    AS saldo_final_cant,

            SUM(b.saldo_inicial_money) AS saldo_inicial_money,
            SUM(b.entradas_mes_money)  AS entradas_mes_money,
            SUM(b.salidas_mes_money)   AS salidas_mes_money,
            SUM(b.saldo_final_money)   AS saldo_final_money

        FROM base b
        GROUP BY
            b.id_material,
            b.codigo,
            b.descripcion,
            b.precio

        ORDER BY b.codigo, b.descripcion
    ", [
            $tipoBodega,
            $start->toDateString(),
            $start->toDateString(),
            $start->toDateString(),
            $end->toDateString(),
            $start->toDateString(),
            $end->toDateString(),
        ]);

        // ----- Totales del período -----
        $totales = [
            'entradas_cant' => 0,
            'salidas_cant'  => 0,
            'final_cant'    => 0,
            'entradas_money'=> 0.0,
            'salidas_money' => 0.0,
            'final_money'   => 0.0,
            'inicial_cant'  => 0,
            'inicial_money' => 0.0,
        ];

        // 👉 Nuevo: sumatorias por código (sin descripción)
        $sumPorCodigo = [];

        foreach ($rows as $r) {
            // ======================
            // 1) TOTALES GENERALES
            // ======================
            $totales['inicial_cant']   += (int) ($r->saldo_inicial_cant ?? 0);
            $totales['entradas_cant']  += (int) ($r->entradas_mes_cant  ?? 0);
            $totales['salidas_cant']   += (int) ($r->salidas_mes_cant   ?? 0);
            $totales['final_cant']     += (int) ($r->saldo_final_cant   ?? 0);

            $totales['inicial_money']  += (float) ($r->saldo_inicial_money ?? 0);
            $totales['entradas_money'] += (float) ($r->entradas_mes_money  ?? 0);
            $totales['salidas_money']  += (float) ($r->salidas_mes_money   ?? 0);
            $totales['final_money']    += (float) ($r->saldo_final_money   ?? 0);

            // ======================
            // 2) SUMAS POR CÓDIGO
            // ======================
            $codigo = $r->codigo ?? 'SIN-CODIGO';

            if (!isset($sumPorCodigo[$codigo])) {
                $sumPorCodigo[$codigo] = [
                    'codigo'          => $codigo,
                    'inicial_cant'    => 0,
                    'entradas_cant'   => 0,
                    'salidas_cant'    => 0,
                    'final_cant'      => 0,
                    'inicial_money'   => 0.0,
                    'entradas_money'  => 0.0,
                    'salidas_money'   => 0.0,
                    'final_money'     => 0.0,
                ];
            }

            $sumPorCodigo[$codigo]['inicial_cant']   += (int) ($r->saldo_inicial_cant ?? 0);
            $sumPorCodigo[$codigo]['entradas_cant']  += (int) ($r->entradas_mes_cant  ?? 0);
            $sumPorCodigo[$codigo]['salidas_cant']   += (int) ($r->salidas_mes_cant   ?? 0);
            $sumPorCodigo[$codigo]['final_cant']     += (int) ($r->saldo_final_cant   ?? 0);

            $sumPorCodigo[$codigo]['inicial_money']  += (float) ($r->saldo_inicial_money ?? 0);
            $sumPorCodigo[$codigo]['entradas_money'] += (float) ($r->entradas_mes_money  ?? 0);
            $sumPorCodigo[$codigo]['salidas_money']  += (float) ($r->salidas_mes_money   ?? 0);
            $sumPorCodigo[$codigo]['final_money']    += (float) ($r->saldo_final_money   ?? 0);
        }

        // ========== Render PDF ==========
        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER-L']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        $mpdf->SetTitle('Reporte Mensual de Bodega');
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/gobiernologo.jpg';
        $logosantaana = 'images/logo.png';

        $encabezado = "
        <table width='100%' style='border-collapse:collapse; font-family: Arial, sans-serif;'>
            <tr>
                <td style='width:25%; border:0.8px solid #000; padding:6px 8px;'>
                    <table width='100%'>
                        <tr>
                            <td style='width:30%; text-align:left;'>
                                <img src='{$logoalcaldia}' style='height:38px'>
                            </td>
                            <td style='width:70%; text-align:left; color:#104e8c; font-size:13px; font-weight:bold; line-height:1.3;'>
                                SANTA ANA NORTE<br>EL SALVADOR
                            </td>
                        </tr>
                    </table>
                </td>
                <td style='width:50%; border-top:0.8px solid #000; border-bottom:0.8px solid #000; padding:6px 8px; text-align:center; font-size:15px; font-weight:bold;'>
                    CONTROL DE ENTRADAS/SALIDAS<br>
                </td>
                <td style='width:25%; border:0.8px solid #000; padding:0; vertical-align:top;'>
                    <table width='100%' style='font-size:10px;'>
                        <tr>
                            <td width='40%' style='border-right:0.8px solid #000; border-bottom:0.8px solid #000; padding:4px 6px;'><strong>Código:</strong></td>
                            <td width='60%' style='border-bottom:0.8px solid #000; padding:4px 6px; text-align:center;'></td>
                        </tr>
                        <tr>
                            <td style='border-right:0.8px solid #000; border-bottom:0.8px solid #000; padding:4px 6px;'><strong>Versión:</strong></td>
                            <td style='border-bottom:0.8px solid #000; padding:4px 6px; text-align:center;'>000</td>
                        </tr>
                        <tr>
                            <td style='border-right:0.8px solid #000; padding:4px 6px;'><strong>Fecha de vigencia:</strong></td>
                            <td style='padding:4px 6px; text-align:center;'></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br>
    ";

        $encabezado .= "<span style='font-weight:bold;'>Del {$desdeFormat} al {$hastaFormat} <br>";

        if (file_exists(public_path('css/cssbodega.css'))) {
            $stylesheet = file_get_contents(public_path('css/cssbodega.css'));
            $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
        }

        // Tabla detalle (SIN columna Lote)
        $html = $encabezado;
        $html .= "
        <table width='100%' border='1' cellspacing='0' cellpadding='4' style='border-collapse:collapse; font-size:11px; margin-top: 8px'>
            <thead style='background:#f2f4f8'>
                <tr>
                    <th>#</th>
                    <th>Código</th>
                    <th>Descripción / Nombre</th>
                    <th style='text-align:right'>Precio</th>
                    <th style='text-align:right'>INICIAL</th>
                    <th style='text-align:right'>ENTRADAS</th>
                    <th style='text-align:right'>SALIDAS</th>
                    <th style='text-align:right'>SALDO</th>
                    <th style='text-align:right'>$ INICIAL</th>
                    <th style='text-align:right'>$ ENTRADAS</th>
                    <th style='text-align:right'>$ SALIDAS</th>
                    <th style='text-align:right'>$ SALDO</th>
                </tr>
            </thead>
            <tbody>
    ";

        $i = 1;
        foreach ($rows as $r) {
            $desc = $r->descripcion;

            $html .= "
            <tr>
                <td>{$i}</td>
                <td>".e($r->codigo ?? '')."</td>
                <td>".e($desc)."</td>
                <td style='text-align:right'>".number_format($r->precio ?? 0, 4)."</td>
                <td style='text-align:right'>".number_format($r->saldo_inicial_cant ?? 0)."</td>
                <td style='text-align:right'>".number_format($r->entradas_mes_cant  ?? 0)."</td>
                <td style='text-align:right'>".number_format($r->salidas_mes_cant   ?? 0)."</td>
                <td style='text-align:right'>".number_format($r->saldo_final_cant   ?? 0)."</td>
                <td style='text-align:right'>".number_format($r->saldo_inicial_money ?? 0, 2)."</td>
                <td style='text-align:right'>".number_format($r->entradas_mes_money  ?? 0, 2)."</td>
                <td style='text-align:right'>".number_format($r->salidas_mes_money   ?? 0, 2)."</td>
                <td style='text-align:right'>".number_format($r->saldo_final_money   ?? 0, 2)."</td>
            </tr>
        ";
            $i++;
        }

        if (!$rows) {
            $html .= "<tr><td colspan='12' style='text-align:center; color:#888;'>Sin registros en el rango seleccionado.</td></tr>";
        }

        $html .= "
            </tbody>
            <tfoot>
                <tr style='font-weight:bold; background:#f9fafb'>
                    <td colspan='4' style='text-align:right'>Totales:</td>
                    <td style='text-align:right'>".number_format($totales['inicial_cant'])."</td>
                    <td style='text-align:right'>".number_format($totales['entradas_cant'])."</td>
                    <td style='text-align:right'>".number_format($totales['salidas_cant'])."</td>
                    <td style='text-align:right'>".number_format($totales['final_cant'])."</td>
                    <td style='text-align:right'>".number_format($totales['inicial_money'], 2)."</td>
                    <td style='text-align:right'>".number_format($totales['entradas_money'], 2)."</td>
                    <td style='text-align:right'>".number_format($totales['salidas_money'], 2)."</td>
                    <td style='text-align:right'>".number_format($totales['final_money'], 2)."</td>
                </tr>
            </tfoot>
        </table>
    ";

        // Resumen del período
        $html .= "
        <br>
        <table width='60%' border='1' cellspacing='0' cellpadding='6' style='border-collapse:collapse; font-size:12px'>
            <tr style='background:#eef3ff; font-weight:bold; text-align:center'>
                <td colspan='3'>Resumen del período {$desdeFormat} - {$hastaFormat}</td>
            </tr>
            <tr style='font-weight:bold; background:#f9fafb'>
                <td></td>
                <td style='text-align:right'>Cantidad</td>
                <td style='text-align:right'>Dinero ($)</td>
            </tr>
            <tr>
                <td>Ingresó (Entradas del mes)</td>
                <td style='text-align:right'>".number_format($totales['entradas_cant'])."</td>
                <td style='text-align:right'>".number_format($totales['entradas_money'], 2)."</td>
            </tr>
            <tr>
                <td>Salió (Salidas del mes)</td>
                <td style='text-align:right'>".number_format($totales['salidas_cant'])."</td>
                <td style='text-align:right'>".number_format($totales['salidas_money'], 2)."</td>
            </tr>
            <tr>
                <td>Disponible al cierre (Saldo final)</td>
                <td style='text-align:right'>".number_format($totales['final_cant'])."</td>
                <td style='text-align:right'>".number_format($totales['final_money'], 2)."</td>
            </tr>
        </table>
    ";

        // 👉 Cuadro adicional: sumatorias por código (sin descripción)
        if (!empty($sumPorCodigo)) {
            $html .= "
        <br><br>
        <table width='100%' border='1' cellspacing='0' cellpadding='4' style='border-collapse:collapse; font-size:11px'>
            <thead style='background:#f2f4f8'>
                <tr>
                    <th>#</th>
                    <th>Código</th>
                    <th style='text-align:right'>Saldo Inicial</th>
                    <th style='text-align:right'>ENTRADAS</th>
                    <th style='text-align:right'>SALIDAS</th>
                    <th style='text-align:right'>SALDO</th>
                    <th style='text-align:right'>$ INICIAL</th>
                    <th style='text-align:right'>$ ENTRADAS</th>
                    <th style='text-align:right'>$ SALIDAS</th>
                    <th style='text-align:right'>$ SALDO</th>
                </tr>
            </thead>
            <tbody>
        ";

            $j = 1;
            foreach ($sumPorCodigo as $cod => $s) {
                $html .= "
                <tr>
                    <td>{$j}</td>
                    <td>".e($s['codigo'])."</td>
                    <td style='text-align:right'>".number_format($s['inicial_cant'])."</td>
                    <td style='text-align:right'>".number_format($s['entradas_cant'])."</td>
                    <td style='text-align:right'>".number_format($s['salidas_cant'])."</td>
                    <td style='text-align:right'>".number_format($s['final_cant'])."</td>
                    <td style='text-align:right'>".number_format($s['inicial_money'], 2)."</td>
                    <td style='text-align:right'>".number_format($s['entradas_money'], 2)."</td>
                    <td style='text-align:right'>".number_format($s['salidas_money'], 2)."</td>
                    <td style='text-align:right'>".number_format($s['final_money'], 2)."</td>
                </tr>
            ";
                $j++;
            }

            $html .= "
            </tbody>
        </table>
        ";
        }

        $mpdf->setFooter("Página {PAGENO} de {nb}");
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
        $mpdf->Output(); // stream
    }























}

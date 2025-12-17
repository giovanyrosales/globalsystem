<?php

namespace App\Http\Controllers\Frontend\Login;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class LoginController extends Controller
{
    public function __construct(){
        $this->middleware('guest', ['except' => ['logout']]);
    }

    // retorna vista de login
    public function index(){
        return view('frontend.login.vistalogin');
    }

    // verificar usuario y contraseña para iniciar sesión
    public function login(Request $request){

        $rules = array(
            'usuario' => 'required',
            'password' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        // si ya habia iniciado sesión, redireccionar
        if (Auth::check()) {
            return ['success'=> 1, 'ruta'=> route('admin.panel')];
        }

        if($info = Usuario::where('usuario', $request->usuario)->first()){

            if($info->activo == 0){
                return ['success' => 5];
            }

            if(Auth::attempt(['usuario' => $request->usuario, 'password' => $request->password])) {

                return ['success'=> 1, 'ruta'=> route('admin.panel')];
            }else{
                return ['success' => 2]; // password incorrecta
            }
        }else{
            return ['success' => 3]; // usuario no encontrado
        }
    }

    // cerrar sesión
    public function logout(Request $request){
        Auth::logout();
        return redirect('/');
    }


    public function vistaSanciones()
    {
        return view('frontend.sanciones.vistacodigo');
    }


    public function pdfSanciones($idBuscado)
    {
        // 🔹 ID que querés buscar (puede venir por request)

        $path = public_path('excel/base.xlsx');
        $data = Excel::toArray([], $path);

        $empleado = null;

        // Recorremos filas (hoja 0)
        foreach ($data[0] as $index => $row) {

            // Saltar encabezado
            if ($index == 0) continue;

            if (trim($row[0]) == $idBuscado) {
                $empleado = [
                    'id'     => $row[0],
                    'nombre' => $row[1],
                    'dui'    => $row[2],
                    'unidad' => $row[3],
                    'cargo'  => $row[4],
                ];
                break;
            }
        }

        if (!$empleado) {
            return response()->json(['error' => 'Empleado no encontrado'], 404);
        }

        // ================= PDF ===================

        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(),
            'format' => 'LETTER',
            'margin_left'   => 25,
            'margin_right'  => 25,
            'margin_top'    => 25,
            'margin_bottom' => 25,
        ]);



        $mpdf->SetTitle('Carta de Uso de Uniforme');

        $logoalcaldia = 'images/gobiernologo.jpg';
        $logosantaana = 'images/logo.png';

        $html = "
<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12pt;
        line-height: 1.6;
    }

    .encabezado {
        font-weight: bold;
        margin-bottom: 30px; /* 👈 espacio después de Presente */
    }

    .parrafo {
        text-align: justify;
        margin-bottom: 15px;
    }

    .firma {
        position: absolute;
        bottom: 85px; /* 👈 controla qué tan abajo queda */
        left: 0;
        right: 0;
        text-align: center;
    }
</style>

<div class='encabezado'>
    Srs. Unidad de Talento Humano<br>
    Presente.-
</div>

<p class='parrafo'>
    Por medio de la presente, yo,
    <strong>{$empleado['nombre']}</strong>,
    con número de identificación
    <strong>{$empleado['dui']}</strong>,
    de la unidad de
    <strong>{$empleado['unidad']}</strong>,
    con el cargo de
    <strong>{$empleado['cargo']}</strong>,
    reconozco que he recibido y leído el reglamento de uso de uniformes.
</p>

<p class='parrafo'>
    Me comprometo a cumplir con las normativas establecidas respecto al uso adecuado del uniforme.
    Entiendo que el incumplimiento de estas normas puede resultar en sanciones disciplinarias,
    que incluyen:
</p>

<p class='parrafo'>
    <strong>Sanciones por faltas leves</strong><br>
    Descuento equivalente a una hora del salario ordinario.
</p>

<p class='parrafo'>
    <strong>Sanciones por faltas graves</strong><br>
    Descuento equivalente al día de descanso (séptimo).
</p>

<p class='parrafo'>
    Al firmar esta carta, acepto que soy responsable de seguir las directrices relacionadas con el uso
    del uniforme y que, en caso de no hacerlo, estaré sujeto/a a las sanciones correspondientes según lo
    estipulado en el reglamento del uso de uniformes.
</p>

<div class='firma'>
    _______________________________<br>
    <strong>{$empleado['nombre']}</strong>
</div>
";

        $mpdf->WriteHTML($html);
        // 🔥 ABRIR DIÁLOGO DE IMPRESIÓN AL CARGAR
        $mpdf->SetJS('this.print();');

        $mpdf->Output('carta.pdf', \Mpdf\Output\Destination::INLINE);
    }











}

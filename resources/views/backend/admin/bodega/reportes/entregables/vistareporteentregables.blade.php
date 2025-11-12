@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet"/>
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
@stop

<style>
    table {
        /*Ajustar tablas*/
        table-layout: fixed;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        font-size: 16px; /* Tamaño de texto más pequeño */
        text-align: left; /* Alineación del texto a la izquierda */
    }

    .checkbox {
        margin: 3; /* Elimina el margen para pegar el checkbox al texto */
        width: 15px; /* Tamaño pequeño para el checkbox */
        height: 15px; /* Ajusta la altura del checkbox */
        margin-right: 3px; /* Pega el checkbox al texto */
    }
</style>

<div id="divcontenedor" style="display: none">


    <section class="content" style="margin-top: 35px">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header" title="">
                    <h3 class="card-title">REPORTE POR CANTIDAD INICIAL Y FINAL</h3>
                </div>
                <div class="card-body">
                    <section class="content" style="margin-left: 30px">
                        <div class="container-fluid">
                            <div class="row">

                                <div class="form-group">
                                    <label>Desde</label>
                                    <input type="date" class="form-control" id="fecha-desdelote">
                                </div>

                                <div class="form-group" style="margin-left: 15px">
                                    <label>Hasta</label>
                                    <input type="date" class="form-control" id="fecha-hastalote">
                                </div>

                                <button type="button" onclick="pdfInicialFinal()" class="btn" style="margin-left: 15px; border-color: black; border-radius: 0.1px;">
                                    <img src="{{ asset('images/logopdf.png') }}" width="48px" height="55px">
                                    Generar PDF
                                </button>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>





</div>


@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

    function pdfInicialFinal(){
        var fechadesde = document.getElementById('fecha-desdelote').value;
        var fechahasta = document.getElementById('fecha-hastalote').value;

        if(fechadesde === ''){
            toastr.error('Fecha desde es requerido');
            return;
        }

        if(fechahasta === ''){
            toastr.error('Fecha hasta es requerido');
            return;
        }

        // Convertir a objetos Date para comparar
        let dateDesde = new Date(fechadesde);
        let dateHasta = new Date(fechahasta);

        if (dateHasta < dateDesde) {
            toastr.error('La Fecha Hasta no puede ser menor que la Fecha Desde');
            return;
        }

        window.open("{{ URL::to('admin/bodega/reportespdf/inicial/final') }}/" +
            fechadesde + "/" + fechahasta);
    }



    </script>

@endsection

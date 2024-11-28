@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
@stop


<div class="content-wrapper" id="divcc" style="display: none">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">

        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="callout callout-info">
                        <h5><i class="fas fa-info"></i> Generar Reporte de Solicitudes</h5>
                        <div class="card">
                            <form class="form-horizontal">
                                <div class="card-body">

                                    <div class="form-group row">
                                        <div class="col-sm-9 row">
                                            <div class="info-box shadow">
                                                <div class="info-box-content">

                                                    <div class="row">

                                                        <div class="form-group col-md-2" >
                                                            <label style="color: #686868">Desde: </label>
                                                            <input type="date" autocomplete="off" class="form-control" id="fecha-desde">
                                                        </div>

                                                        <div class="form-group col-md-2" >
                                                            <label style="color: #686868">Hasta: </label>
                                                            <input type="date" autocomplete="off" class="form-control" id="fecha-hasta">
                                                        </div>

                                                    </div>

                                                    <select class="form-control " id="tiposolicitud">
                                                        <option value="1">Vivienda Completa</option>
                                                        <option value="2">Solo Vivienda</option>
                                                        <option value="3">Materiales de Construcción</option>
                                                        <option value="4">Viveres</option>
                                                        <option value="5">Construcción</option>
                                                        <option value="6">Proyectos</option>
                                                        <option value="7">Afectaciones de la Vista</option>
                                                        <option value="8">Otros</option>
                                                    </select>


                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <button type="button" onclick="generarPdfMovimientos()" class="btn" style="margin-left: 15px; border-color: black; border-radius: 0.1px;">
                                            <img src="{{ asset('images/logopdf.png') }}" width="48px" height="55px">
                                            Generar PDF
                                        </button>

                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="callout callout-warning">
                        <h5><i class="fas fa-info"></i> Generar Reporte de transporte ()</h5>
                        <div class="card">
                            <form class="form-horizontal">
                                <div class="card-body">

                                    <div class="form-group row">
                                        <div class="col-sm-9 row">
                                            <div class="info-box shadow">
                                                <div class="info-box-content">
                                                    <div class="row">
                                                        <div class="form-group col-md-2" >
                                                            <label style="color: #686868">Desde: </label>
                                                            <input type="date" autocomplete="off" class="form-control" id="fecha-desdet">
                                                        </div>
                                                        <div class="form-group col-md-2" >
                                                            <label style="color: #686868">Hasta: </label>
                                                            <input type="date" autocomplete="off" class="form-control" id="fecha-hastat">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <button type="button" onclick="generarPdfTransporte()" class="btn" style="margin-left: 15px; border-color: black; border-radius: 0.1px;">
                                            <img src="{{ asset('images/logopdf.png') }}" width="48px" height="55px">
                                            Generar PDF
                                        </button>

                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>


</div>


@extends('backend.menus.footerjs')
@section('archivos-js')

    <script src="{{ asset('js/jquery.dataTables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            document.getElementById("divcc").style.display = "block";

            $('#select-unidad').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });
        });

    </script>

    <script>

        // reporte pdf generado por jefe de presupuesto para movimientos de cuenta
        function generarPdfMovimientos(){

            let fechaDesde = document.getElementById("fecha-desde").value;
            let fechaHasta = document.getElementById("fecha-hasta").value;
            let tipo = document.getElementById("tiposolicitud").value;

            if(fechaDesde === ''){
                toastr.error('Fecha Desde es requerido');
                return;
            }

            if(fechaHasta === ''){
                toastr.error('Fecha Hasta es requerido');
                return;
            }

            window.open("{{ URL::to('admin/reporte/despacho') }}/" + fechaDesde + "/" + fechaHasta + "/" + tipo);
        }
         // reporte pdf del transporte por rango de fechas
         function generarPdfTransporte(){

            let fechaDesde = document.getElementById("fecha-desdet").value;
            let fechaHasta = document.getElementById("fecha-hastat").value;

            if(fechaDesde === ''){
                toastr.error('Fecha Desde es requerido');
                return;
            }

            if(fechaHasta === ''){
                toastr.error('Fecha Hasta es requerido');
                return;
            }

            window.open("{{ URL::to('admin/reporte/despacho/transporte') }}/" + fechaDesde + "/" + fechaHasta);
            }

    </script>


@endsection

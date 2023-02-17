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
                        <h5><i class="fas fa-info"></i> Generar Reportes</h5>
                        <div class="card">
                            <form class="form-horizontal">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <div class="col-sm-5">
                                            <div class="info-box shadow">
                                                <span class="info-box-icon bg-transparent"><i class="far fa-calendar-alt"></i></span>
                                                <div class="info-box-content">
                                                    <label>Año de Presupuesto</label>
                                                    <select class="form-control" id="select-anio" style="width: 35%">
                                                        @foreach($anios as $item)
                                                            <option value="{{$item->id}}">{{$item->nombre}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <h5><i class="fas fa-file"></i> Generar Plan Anual de Compras</h5> <br>

                                    <div class="row">
                                        <button type="button" onclick="generarPdfPlanAnual()" class="btn" style="margin-left: 15px; border-color: black; border-radius: 0.1px;">
                                            <img src="{{ asset('images/logopdf.png') }}" width="55px" height="55px">
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


    <section class="content" id="divcontenedor" style="display: none">
        <div class="container-fluid">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Listado</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="tablaDatatable">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="modal fade" id="modalPendiente">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Presupuestos aun sin Aprobar</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="formulario">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <select class="form-control" id="select-departamento">
                                    </select>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

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


        function generarPdfPlanAnual(){
            var idanio = document.getElementById('select-anio').value;
            window.open("{{ URL::to('admin/p/generador/pdf/plan') }}/" + idanio);
        }


    </script>


@endsection

@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
@stop

<style>
    table{
        /*Ajustar tablas*/
        table-layout:fixed;
    }
</style>

<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">

            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Bodega</li>
                    <li class="breadcrumb-item active">Mis Solicitudes - Detalle</li>
                </ol>
            </div>
        </div>
    </section>


    @if($arrayReferencia->isNotEmpty())

    <section class="content">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">Materiales con Referencia</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">

                            <table id="tabla" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th style="width: 2%">#</th>
                                    <th style="width: 7%">Solicitado</th>
                                    <th style="width: 7%">Referencia</th>
                                    <th style="width: 2%">U/M</th>
                                    <th style="width: 2%">Cantidad Solicitado</th>
                                    <th style="width: 3%">Estado</th>
                                    <th style="width: 2%">Cantidad Entregada</th>
                                    <th style="width: 2%">Opciones</th>

                                </tr>
                                </thead>
                                <tbody>

                                @foreach($arrayReferencia as $fila)
                                    <tr>
                                        <td style="width: 2%">{{ $fila->numeralFila }}</td>
                                        <td style="width: 7%">{{ $fila->nombre }}</td>
                                        <td style="width: 7%">{{ $fila->nombreReferencia }}</td>
                                        <td style="width: 2%">{{ $fila->unidadMedida }}</td>
                                        <td style="width: 2%">{{ $fila->cantidad }}</td>
                                        <td style="width: 3%">
                                            @if($fila->estado == 1)
                                                <span class="badge bg-gray-dark">{{ $fila->nombreEstado }}</span>
                                            @elseif($fila->estado == 2)
                                                <span class="badge bg-success">{{ $fila->nombreEstado }}</span>
                                            @elseif($fila->estado == 3)
                                                <span class="badge bg-warning">{{ $fila->nombreEstado }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ $fila->nombreEstado }}</span>
                                            @endif
                                        </td>
                                        <td style="width: 2%">{{ $fila->cantidad_entregada }}</td>
                                        <td style="width: 2%">
                                            <button type="button" style="margin: 3px" class="btn btn-success btn-xs"
                                                    onclick="vistaPDF({{ $fila->id }})">
                                                <i class="fas fa-file-pdf" title="PDF"></i>&nbsp; PDF
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @endif




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

    <script type="text/javascript">
        $(document).ready(function(){

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function vistaPDF(id){
            // bodega_solicitud_detalle
            window.open("{{ URL::to('admin/bodega/reporte/encargadobodega/item') }}/" + id);
        }

    </script>


@endsection

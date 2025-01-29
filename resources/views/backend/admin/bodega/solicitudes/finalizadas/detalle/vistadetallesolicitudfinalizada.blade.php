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
                                    <th style="width: 2%">C. Solicitado</th>
                                    <th style="width: 3%">Estado</th>
                                    <th style="width: 2%">C. Entregada</th>
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


    <div class="modal fade" id="modalReportes">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Listado de Salidas</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-reportes">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-5">

                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label>Salidas Registradas</label>
                                            <select class="form-control" id="select-salidasregistro" >

                                            </select>
                                        </div>
                                    </div>


                                    <button type="button" onclick="generarPdfSalidas()" class="btn" style="margin-left: 15px; margin-top: 15px; border-color: black; border-radius: 0.1px;">
                                        <img src="{{ asset('images/logopdf.png') }}" width="48px" height="55px">
                                        Generar PDF
                                    </button>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
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

    <script type="text/javascript">
        $(document).ready(function(){

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function vistaPDF(id){
            // SE RECIBE ID: bodega_solicitud_detalle

            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post(url+'/bodega/infosalidas/bodegasolidetalle', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        document.getElementById("select-salidasregistro").options.length = 0;

                        $('#modalReportes').modal('show');

                        // EL ID SERA DE: bodega_salidas_detalle

                        $.each(response.data.arraySalidas, function( key, val ){
                            $('#select-salidasregistro').append('<option value="' + val.id + '">' + val.nombreCompleto + '</option>');
                        });
                    }
                    else {
                        toastr.error('Error al actualizar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error al actualizar');
                    closeLoading();
                });
        }

        function generarPdfSalidas(){

            var idSelect = document.getElementById('select-salidasregistro').value;

            if(idSelect === ''){
                toastr.error('Registro de Salida es requerido');
                return
            }

            // ID: bodega_salida_detalle
            window.open("{{ URL::to('admin/bodega/reporte/encargadobodega/item') }}/" + idSelect);

        }

    </script>


@endsection

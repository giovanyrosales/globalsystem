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
                    <h3 class="card-title">Pendientes de Referencia</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">

                            <table id="tabla" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th style="width: 8%">Nombre</th>
                                    <th style="width: 2%">U/M</th>
                                    <th style="width: 2%">Opciones</th>
                                </tr>
                                </thead>
                                <tbody>

                            @foreach($arrayReferencia as $fila)
                                <tr>
                                    <td style="width: 3%">{{ $fila->nombre }}</td>
                                    <td style="width: 2%">{{ $fila->unidadMedida }}</td>
                                    <td style="width: 2%">
                                        <button type="button" class="btn btn-success btn-xs"
                                                onclick="vistaAsignar({{ $fila->id }})">
                                            <i class="fas fa-plus" title="Referencia"></i>&nbsp; Referencia
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


    <section class="content">
        <div class="container-fluid">
            <div class="card card-gray-dark">
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


    <div class="modal fade" id="modalAgregar">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Asignación</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <input type="hidden" disabled class="form-control" id="id-referencia" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input type="text" disabled class="form-control" id="nombre-nuevo" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Unidad de Medida</label>
                                        <input type="text" disabled class="form-control" id="unidad-nuevo" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Asignar Material - Lote</label>
                                        <select class="form-control" id="select-materiallote">
                                            @foreach($arrayMateriales as $fila)
                                                <option value="{{ $fila->id }}">{{ $fila->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small" onclick="nuevo()">Guardar</button>
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
            let id = {{ $idsolicitud }};
            var ruta = "{{ URL::to('/admin/bodega/solicitudpendiente/detalle/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);

            $('#select-materiallote').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            let id = {{ $idsolicitud }};
            var ruta = "{{ URL::to('/admin/bodega/solicitudpendiente/detalle/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);
        }


        function vistaAsignar(id){
            openLoading();
            document.getElementById("formulario-nuevo").reset();

            axios.post(url+'/bodega/solicitudpendiente/infobodesolituddetalle',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalAgregar').modal('show');
                        $('#id-referencia').val(id);

                        $('#nombre-nuevo').val(response.data.info.nombre);
                        $('#unidad-nuevo').val(response.data.nombreUnidad);
                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }




    </script>


@endsection

@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
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
                    <li class="breadcrumb-item active">Solicitudes Pendientes</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">Listado Solicitudes Pendientes</h3>
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



    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nombre de Solicitud</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-editar">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <input type="hidden" id="id-editar">
                                    </div>

                                    <div class="form-group">
                                        <label>Número/Nombre de Solicitud</label>
                                        <input type="text" class="form-control" maxlength="50" id="nombre-solicitud-editar" autocomplete="off">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;"
                            class="button button-rounded button-pill button-small" onclick="editarSolicitud()">Guardar</button>
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

    <script type="text/javascript">
        $(document).ready(function(){

            var ruta = "{{ URL::to('/admin/bodega/solicitudpendiente/tabla') }}";
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/bodega/solicitudpendiente/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function vistaDetalle(idsolicitud){
            window.location.href="{{ url('/admin/bodega/solicitudpendiente/detalle/index') }}/" + idsolicitud;
        }

        function vistaEstado(id){
            Swal.fire({
                title: 'Finalizar Solicitud?',
                text: "",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    finalizar(id);
                }
            })
        }

        function finalizar(id){

            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post(url+'/bodega/solicitudpendiente/estadofinalizar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        pendiente()
                    }
                    else if(response.data.success === 2){
                        toastr.success('Actualizado correctamente');
                        recargar();
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

        function pendiente(){
            Swal.fire({
                title: 'Pendiente',
                text: "Se encontro un item que tiene estado Pendiente.",
                icon: 'info',
                showCancelButton: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                }
            })
        }

        function vistaPDF(id){
            window.open("{{ URL::to('admin/bodega/reporte/encargadobodega/completa') }}/" + id);
        }



        function vistaBorrar(id){
            Swal.fire({
                title: 'Borrar Solicitud',
                text: "No podra eliminarse si hay una salida",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Borrar'
            }).then((result) => {
                if (result.isConfirmed) {
                    borrarSolicitudCompleta(id)
                }
            })
        }

        function borrarSolicitudCompleta(id){
            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post(url+'/bodega/solicitud/eliminarcompleta', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.error('Se encontraron Salidas');
                    }
                    else if(response.data.success === 2){
                        toastr.success('Borrado');
                        recargar();
                    }
                    else {
                        toastr.error('Error al borrar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error al borrar');
                    closeLoading();
                });
        }

        // para cambio de solicitud
        function vistainfoNumeroSolicitud(id){
            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post(url+'/bodega/solicitud/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(id);
                        $('#nombre-solicitud-editar').val(response.data.info.numero_solicitud);

                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }


        function editarSolicitud(){
            var id = document.getElementById('id-editar').value;
            var nombre = document.getElementById('nombre-solicitud-editar').value;


            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);

            axios.post(url+'/bodega/solicitud/editardatos', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.success('Actualizado correctamente');
                        $('#modalEditar').modal('hide');
                        recargar();
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

    </script>


@endsection

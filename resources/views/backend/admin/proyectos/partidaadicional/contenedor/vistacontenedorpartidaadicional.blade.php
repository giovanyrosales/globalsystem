@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
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
        <div class="container-fluid">
            <div class="row mb-6">
                <div class="col-sm-5">
                    <h1>Solicitudes para Partidas Adicionales</h1>

                    <!-- Botón para crear solicitud de partida adicional -->
                    @can('boton.modal.crear.solicitud.partida.adicional')
                        <button type="button" style="margin-top: 15px" onclick="modalSolicitudPartidaAdicional()" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i>
                            Crear Solicitud Partida Adicional
                        </button>
                    @endcan

                </div>

            </div>
        </div>
    </section>

    <section class="content">
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


    <div class="modal fade" id="modalAgregar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nueva Solicitud de Partida Adicional</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Fecha de Solicitud</label>
                                            <input type="date" class="form-control" id="fecha-solicitud">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="crearSolicitudPartida()">Guardar</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalEstado">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Estado de Partida</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="formulario">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <input id="id-contenedor" type="hidden">

                                        <select class="form-control" id="select-estado">
                                            <option value="0">En Desarrollo</option>
                                            <option value="1">Listo para Revisión</option>
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarU" onclick="actualizarEstado()">Actualizar</button>
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
            let id = {{ $id }}; // id PROYECTO
            var ruta = "{{ URL::to('/admin/partida/adicional/contenedor/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function recargar(){
            let id = {{ $id }}; // id PROYECTO
            var ruta = "{{ URL::to('/admin/partida/adicional/contenedor/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);
        }


        function modalSolicitudPartidaAdicional(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function crearSolicitudPartida(){

            var fecha = document.getElementById('fecha-solicitud').value;

            if(fecha === ''){
                toastr.error('Fecha es Requerida');
                return;
            }

            let idpro = {{ $id }};

            let formData = new FormData();
            formData.append('idproyecto', idpro);
            formData.append('fecha', fecha);

            axios.post(url+'/partida/adicional/crear/solicitud', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        $('#modalAgregar').modal('hide');

                        recargar();

                        Swal.fire({
                            title: 'Solicitud Creada',
                            text: "Ya puede crear Partidas Adicionales",
                            icon: 'success',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }
                    else {
                        toastr.error('Error al registrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al registrar');
                    closeLoading();
                });
        }

        function infoBorrarContenedor(id){

            Swal.fire({
                title: 'Borrar Solicitud',
                text: "No se podrá eliminar si ya esta en modo Revisión o Aprobado",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                closeOnClickOutside: false,
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Borrar'
            }).then((result) => {
                if (result.isConfirmed) {
                    peticionBorrarContenedor(id);
                }
            });
        }

        function peticionBorrarContenedor(id){

            openLoading();

            axios.post(url+'/partida/adicional/borrar/contenedor', {
                'id' : id
            })
                .then((response) => {
                    closeLoading();

                    // esta en modo revisión
                    if(response.data.success === 1){

                        Swal.fire({
                            title: 'Estado Cambio',
                            text: "La solicitud de Partida esta en modo Revisión",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                               location.reload();
                            }
                        })
                    }

                    // esta aprobado
                    else if(response.data.success === 2){

                        Swal.fire({
                            title: 'Estado Cambio',
                            text: "La solicitud de Partida ya fue Aprobada",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    }
                    // borrado
                    else if(response.data.success === 3){
                        toastr.success('Solicitud Eliminada');
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


        function vistaPartidasAdicionales(id){
            // id Contenedor
            window.location.href="{{ url('/admin/partida/adicional/creacion/index') }}/" + id;
        }

        function infoPdf(id){
            // id Contenedor

            openLoading();

            axios.post(url+'/partida/adicional/comprobar/quehaya', {
                'id' : id
            })
                .then((response) => {
                    closeLoading();

                    // esta en modo revisión
                    if(response.data.success === 1){
                        window.open("{{ URL::to('admin/partida/adicional/verpdf') }}/" + id);

                    }else{
                        // la partida adicional esta aprobada

                        Swal.fire({
                            title: 'No Encontrada',
                            text: "No se encontró Partidas Adicionales",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }
                })
                .catch((error) => {
                    toastr.error('Error al buscar');
                    closeLoading();
                });
        }

        function vistaInformacionEstado(id){

            openLoading();

            axios.post(url+'/partida/adicio/contenedor/estado/informacion', {
                'id' : id
            })
                .then((response) => {
                    closeLoading();

                    // esta en modo revisión
                    if(response.data.success === 1){

                        $('#id-contenedor').val(id);

                        let estado = response.data.info.estado;

                        if(estado === 0){
                            $('#select-estado').prop('selectedIndex', 0).change();
                            $('#modalEstado').modal('show');
                        }
                        else if(estado === 1){
                            $('#select-estado').prop('selectedIndex', 1).change();
                            $('#modalEstado').modal('show');
                        }else{
                            // la partida adicional esta aprobada

                            Swal.fire({
                                title: 'Partida Aprobada',
                                text: "No se puede modificar el Estado",
                                icon: 'info',
                                showCancelButton: false,
                                allowOutsideClick: false,
                                confirmButtonColor: '#28a745',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Aceptar',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            })
                        }
                    }
                    else {
                        toastr.error('Error al buscar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al buscar');
                    closeLoading();
                });
        }

        function actualizarEstado(){

            openLoading();
            var estado = document.getElementById('select-estado').value;
            var id = document.getElementById('id-contenedor').value;

            let formData = new FormData();
            formData.append('estado', estado);
            formData.append('id', id);

            axios.post(url+'/partida/adicio/contenedor/estado/actualizar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        $('#modalEstado').modal('hide');
                        recargar();

                        Swal.fire({
                            title: 'No Actualizado',
                            text: "La Partida ya fue Aprobada",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }
                    else if(response.data.success === 2){
                        Swal.fire({
                            title: 'No Actualizado',
                            text: "No se encuentras Partidas Adicionales",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }

                    else if(response.data.success === 3){
                        toastr.success('Estado Actualizado');
                        $('#modalEstado').modal('hide');
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

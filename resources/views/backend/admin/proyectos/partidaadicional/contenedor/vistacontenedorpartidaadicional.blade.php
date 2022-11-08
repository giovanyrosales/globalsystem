@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
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
        <div class="container-fluid">
            <div class="row mb-6">
                <div class="col-sm-5">
                    <h1>Solicitudes para Partidas Adicionales</h1>

                    <!-- Botón para crear solicitud de partida adicional -->
                    @can('boton.modal.crear.solicitud.partida.adicional')
                        <button type="button" style="margin-top: 15px;font-weight: bold; background-color: #28a745; color: white !important;" onclick="modalSolicitudPartidaAdicional()" class="button button-3d button-rounded button-pill button-small">
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

    <!-- MODAL PARA AGREGAR NUEVO CONTENEDOR -->
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
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small" onclick="crearSolicitudPartida()">Guardar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- MODAL PARA EDITAR ESTADO DE CONTENEDOR -->
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
                                        <input id="id-estado" type="hidden">

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
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small" id="btnGuardarU" onclick="actualizarEstado()">Actualizar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PARA DIFERENTES OPCIONES -->
    <div class="modal fade" id="modalOpcion">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Opciones</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <!-- ID del contenedor -->
                                <input type="hidden" id="id-contenedor">

                                <!-- ver partidas adicionales -->
                                <div class="form-group">
                                    <button type="button" style=" width: 100%; font-weight: bold; font-size: 15px; background-color: #28a745; color: white !important;" class="button button-rounded button-pill" onclick="vistaPartidasAdicionales()">
                                        <i class="fas fa-list-alt" title="Partidas Adicionales"></i>&nbsp; Partidas Adicionales
                                    </button>
                                </div>

                                <!-- solo aparece el botón si no está aprobada la partida adicional -->
                                <div class="form-group" id="divModalEstadoContenedor">
                                    <button type="button" style=" width: 100%; font-weight: bold; font-size: 15px; background-color: #17a2b8; color: white !important;" class="button button-rounded button-pill" onclick="vistaInformacionEstado()">
                                        <i class="fas fa-check" title="Estado"></i>&nbsp; Estado
                                    </button>
                                </div>

                                <!-- sacar pdf -->
                                <div class="form-group" >
                                    <button type="button" style=" width: 100%; font-weight: bold; font-size: 15px; background-color: #17a2b8; color: white !important;" class="button button-rounded button-pill" onclick="infoPdf()">
                                        <i class="fas fa-file-pdf" title="PDF"></i>&nbsp; PDF
                                    </button>
                                </div>

                                @can('boton.borrar.contenedor.partida.adicional')
                                    <!-- solo autorizado podrá borrar contenedor de partidas adicionales -->
                                    <div class="form-group" id="divModalBorrarContenedor">
                                        <button type="button" style=" width: 100%; font-weight: bold; font-size: 15px; color: white !important;" class="button button-caution button-rounded button-pill" onclick="infoBorrarContenedor()">
                                            <i class="fas fa-trash" title="Borrar"></i>&nbsp; Borrar
                                        </button>
                                    </div>
                                @endcan

                            </div>
                        </div>
                    </div>

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

            openLoading();

            // id proyecto
            let idproyecto = {{ $id }};

            let formData = new FormData();
            formData.append('idproyecto', idproyecto);
            formData.append('fecha', fecha);

            axios.post(url+'/partida/adicional/crear/solicitud', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        let mensaje = response.data.mensaje;

                        Swal.fire({
                            title: 'Estado Proyecto',
                            html: mensaje,
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })

                    }
                    else if(response.data.success === 2) {
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

        function infoBorrarContenedor(){

            $('#modalOpcion').modal('hide');

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
                    peticionBorrarContenedor();
                }
            });
        }

        function peticionBorrarContenedor(){

            openLoading();
            let id = document.getElementById('id-contenedor').value;

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
                    else if(response.data.success === 4) {

                        let mensaje = response.data.mensaje;

                        Swal.fire({
                            title: 'Estado Proyecto',
                            html: mensaje,
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
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


        // vista de partidas adicionales
        function vistaPartidasAdicionales(){

            $('#modalOpcion').modal('hide');
            // id Contenedor
            var id = document.getElementById('id-contenedor').value;
            window.location.href="{{ url('/admin/partida/adicional/creacion/index') }}/" + id;
        }

        function infoPdf(){
            // id Contenedor
            var id = document.getElementById('id-contenedor').value;

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

        function vistaInformacionEstado(){
            $('#modalOpcion').modal('hide');

            var id = document.getElementById('id-contenedor').value;

            openLoading();

            axios.post(url+'/partida/adicio/contenedor/estado/informacion', {
                'id' : id
            })
                .then((response) => {
                    closeLoading();

                    // esta en modo revisión
                    if(response.data.success === 1){

                        $('#id-estado').val(id);

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
            var id = document.getElementById('id-estado').value;

            let formData = new FormData();
            formData.append('estado', estado);
            formData.append('id', id);

            axios.post(url+'/partida/adicio/contenedor/estado/actualizar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        let mensaje = response.data.mensaje;

                        Swal.fire({
                            title: 'Estado Proyecto',
                            html: mensaje,
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }
                    else if(response.data.success === 2){

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

                    else if(response.data.success === 3){

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
                    else if(response.data.success === 4){

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

        // ** MODAL DE OPCIONES **
        function modalOpciones(dato){

            // id contenedor
            $('#id-contenedor').val(dato.id);

            if (document.getElementById('divModalEstadoContenedor') !== null) {
                document.getElementById("divModalEstadoContenedor").style.display = "none";
            }

            if (document.getElementById('divModalBorrarContenedor') !== null) {
                document.getElementById("divModalBorrarContenedor").style.display = "none";
            }

            // OBTENER INFORMACIÓN DEL CONTENEDOR

            openLoading();
            axios.post(url+'/partida/adicio/contenedor/estado/informacion', {
                'id' : dato.id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        // estado de contenedor
                        let estado = response.data.info.estado;

                        if(estado !== 2){
                            // puede modificarse estado
                            if (document.getElementById('divModalEstadoContenedor') !== null) {
                                document.getElementById("divModalEstadoContenedor").style.display = "block";
                            }
                        }

                        // solo en modo desarrollo se puede borrar
                        if(estado === 0){
                            if (document.getElementById('divModalBorrarContenedor') !== null) {
                                document.getElementById("divModalBorrarContenedor").style.display = "block";
                            }
                        }

                        $('#modalOpcion').modal('show');
                    }
                    else {
                        toastr.error('Error al buscar información');
                    }

                })
                .catch((error) => {
                    toastr.error('Error al buscar información');
                    closeLoading();
                });
        }




    </script>

@endsection

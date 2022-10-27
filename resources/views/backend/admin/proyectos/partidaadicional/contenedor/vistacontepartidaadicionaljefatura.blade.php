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

                    <!-- Botón para dar permiso y crear x partidas adicionales. Para jefe presupuesto -->
                    @can('boton.autorizar.denegar.partida.adicional')
                        @if($infoPro->permiso_partida_adic == 1)
                            <button type="button" style="margin-top: 15px" onclick="modalPermisoDenegar()" class="btn btn-danger btn-sm">
                                <i class="fas fa-stop"></i>
                                Denegar Partidas Adicionales
                            </button>
                        @else
                            <button type="button" style="margin-top: 15px" onclick="modalPermisoAprobar()" class="btn btn-success btn-sm">
                                <i class="fas fa-check"></i>
                                Autorizar Partidas Adicionales
                            </button>
                        @endif
                    @endcan

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

    <div class="modal fade" id="modalEstado">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Estado de Partida Adicional</h4>
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
                                            <option value="2">Aprobar Partida</option>
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
            var ruta = "{{ URL::to('/admin/partida/adici/conte/jefatura/tabla') }}/" + id;
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

        function modalPermisoAprobar(){
            Swal.fire({
                title: 'Autorizar Partidas',
                text: "Se autorizara poder crear las partidas adicionales necesarias",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    autorizarPartida();
                }
            })
        }

        function autorizarPartida(){

            openLoading();

            // id proyecto
            let id = {{$id}};

            axios.post(url+'/partida/adicional/permiso/autorizar',{
                'id' : id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        Swal.fire({
                            title: 'Autorizado',
                            text: "Se podrá crear las partidas adicionales necesarias",
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            closeOnClickOutside: false,
                            allowOutsideClick: false,
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
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

        function modalPermisoDenegar(){
            Swal.fire({
                title: 'Denegar Partidas',
                text: "Se restringirá seguir creando partidas adicionales",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    denegarPartida();
                }
            })
        }

        function denegarPartida(){

            openLoading();

            // id proyecto
            let id = {{$id}};

            axios.post(url+'/partida/adicional/permiso/denegar', {
                'id' : id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        Swal.fire({
                            title: 'Autorizar Partidas',
                            text: "No se podrá crear partidas adicionales al Proyecto",
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            closeOnClickOutside: false,
                            allowOutsideClick: false,
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
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

        function vistaPartidasAdicionales(id){
            // id Contenedor
            window.location.href="{{ url('/admin/partida/adicional/creacion/index') }}/" + id;
        }

        // MODAL PARA REVISAR CUANDO MONTO TIENE LAS PARTIDAS Y ASIGNAR UN BOLSÓN
        function vistaInformacionEstado(id){

            openLoading();

            axios.post(url+'/partida/adicio/contenedor/estado/informacion', {
                'id' : id
            })
                .then((response) => {
                    closeLoading();

                    // está en modo revisión
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

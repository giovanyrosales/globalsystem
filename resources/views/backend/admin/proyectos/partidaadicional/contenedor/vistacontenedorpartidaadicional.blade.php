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
                    <h1>Partidas Adicionales</h1>
                    <button type="button" style="margin-top: 15px" onclick="verHistorico()" class="btn btn-primary btn-sm">
                        <i class="fas fa-list-alt"></i>
                        Histórico
                    </button>

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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nuevo Movimiento</h4>
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
                                        <input type="hidden" class="form-control" id="id-editar">
                                    </div>

                                    <label>Cuenta a Aumentar</label>
                                    <hr style="height:1px;border:none;color:#333;background-color:#333;">


                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Objeto Específico</label>
                                            <input type="text" disabled class="form-control" id="codigo">
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Cuenta</label>
                                            <input type="text" disabled class="form-control" id="cuenta">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Saldo Restante</label>
                                            <input type="text" disabled placeholder="0.00" class="form-control" id="saldo-restante">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Aumento de Saldo</label>
                                            <input type="text" class="form-control" placeholder="0.00" id="saldo-modificar">
                                        </div>
                                    </div>

                                    <hr style="height:1px;border:none;color:#333;background-color:#333;">

                                    <div class="form-group">
                                        <label>Obj. Específico a Modificar para Disminuir Saldo</label>
                                        <select class="form-control" id="select-cuentaproy" onchange="buscarSaldoRestante()" style="width: 100%">
                                        </select>
                                    </div>

                                    <div class="col-md-12 row">
                                        <div class="form-group col-md-6">
                                            <label>Fecha:</label>
                                            <input type="date" class="form-control" id="fecha-nuevo">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label style="font-weight: bold">Saldo Actual:</label>
                                            <input type="text" disabled class="form-control" id="restante">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="verificar()">Guardar</button>
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

        // historico de partidas adicionales
        function verHistorico(){
            let id = {{ $id }}; // ID PROYECTO


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
                            text: "No se podra crear partidas adicionales al Proyecto",
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









    </script>

@endsection

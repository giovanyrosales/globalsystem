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
        <div class="row">

            <button type="button" style="margin-left: 20px; font-weight: bold; background-color: #28a745; color: white !important;" onclick="modalAgregar()" class="button button-3d button-rounded button-pill button-small">
                <i class="fas fa-plus-square"></i>
                Nuevo Registro
            </button>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Listado de Unidades asignadas a Consolidador</h3>
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
                    <h4 class="modal-title">Nuevo Registro</h4>
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
                                        <label>Objeto Específico:</label>
                                        <select class="form-control" id="select-objespecifico">
                                            @foreach($arrayObjEspecifico as $sel)
                                                <option value="{{ $sel->id }}">{{ $sel->codigo }} - {{ $sel->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Usuarios:</label>
                                        <select class="form-control" id="select-usuario">
                                            @foreach($arrayUsuario as $sel)
                                                <option value="{{ $sel->id }}">{{ $sel->nombreUsuario }}</option>
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
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-3d button-rounded button-pill button-small" onclick="verificarGuardar()">Guardar</button>
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
            var ruta = "{{ URL::to('/admin/bodega/usuario/objespecifico/tabla') }}";
            $('#tablaDatatable').load(ruta);

            $('#select-usuario').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });

            $('#select-objespecifico').select2({
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
            var ruta = "{{ url('/admin/bodega/usuario/objespecifico/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }


        function modalAgregar(){
            //$('#select-depa').prop('selectedIndex', 0).change();
            $('#modalAgregar').modal('show');
        }

        function verificarGuardar(){

            var objeto = document.getElementById('select-objespecifico').value;
            var usuario = document.getElementById('select-usuario').value;

            if(objeto === ''){
                toastr.error('Objeto Específico es requerido');
                return;
            }

            if(usuario === ''){
                toastr.error('Usuario es requerido');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('objeto', objeto);
            formData.append('usuario', usuario);

            axios.post(url+'/bodega/usuario/objespecifico/registrar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        Swal.fire({
                            title: 'Registro Repetido',
                            text: "ya esta registrado el Objeto Específico",
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }
                    else if(response.data.success === 2){
                        toastr.success('Registrado correctamente');
                        $('#modalAgregar').modal('hide');
                        recargar();
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




        function informacionBorrar(id){

            Swal.fire({
                title: 'Borrar Registro',
                text: "",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Aceptar',
            }).then((result) => {
                if (result.isConfirmed) {
                    borrar(id);
                }
            })
        }

        function borrar(id){

            openLoading();
            axios.post(url+'/bodega/usuario/objespecifico/borrar',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Borrado correctamente');
                        recargar();
                    }else{
                        toastr.error('Error al borrar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error al borrar');
                });
        }





    </script>


@endsection

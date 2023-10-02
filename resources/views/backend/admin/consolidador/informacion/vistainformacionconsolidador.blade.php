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
                <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" onclick="modalAgregar()"
                        class="button button-3d button-rounded button-pill button-small">
                    <i class="fas fa-pencil-alt"></i>
                    Nuevo Registro
                </button>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Consolidador</li>
                    <li class="breadcrumb-item active">Listado de Información</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Listado de información para 1 Consolidador, para PDF orden de compra</h3>
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
                                        <label class="control-label">Usuario: </label>
                                        <select id="select-usuario" class="form-control">
                                            @foreach($arrayUsuarios as $item)
                                                <option value="{{$item->id}}">{{ $item->usuario }}</option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="form-group">
                                        <label class="control-label">Departamento: </label>
                                        <select id="select-departamento" class="form-control">
                                            @foreach($arrayDepartamentos as $item)
                                                <option value="{{$item->id}}">{{ $item->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Cargo</label>
                                        <input type="text" maxlength="100" class="form-control" id="cargo-nuevo" autocomplete="off">
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

    <!-- modal editar -->
    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Equipo</h4>
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
                                        <label style="color:#191818">Usuario</label>
                                        <br>
                                        <div>
                                            <select class="form-control" id="select-usuario-editar">
                                            </select>
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label style="color:#191818">Departamento</label>
                                        <br>
                                        <div>
                                            <select class="form-control" id="select-departamento-editar">
                                            </select>
                                        </div>
                                    </div>



                                    <div class="form-group">
                                        <label>Cargo</label>
                                        <input type="text" maxlength="100" class="form-control" id="cargo-editar" autocomplete="off">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small" onclick="editar()">Guardar</button>
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
            var ruta = "{{ URL::to('/admin/informacion/consolidador/tabla') }}";
            $('#tablaDatatable').load(ruta);


            $('#select-usuario').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });


            $('#select-departamento').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });


            $('#select-usuario-editar').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });


            $('#select-departamento-editar').select2({
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
            var ruta = "{{ url('/admin/informacion/consolidador/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function nuevo(){
            var usuario = document.getElementById('select-usuario').value;
            var departamento = document.getElementById('select-departamento').value;
            var cargo = document.getElementById('cargo-nuevo').value;

            if(usuario === ''){
                toastr.error('Usuario es requerido');
                return;
            }

            if(departamento === ''){
                toastr.error('Departamento es requerido');
                return;
            }

            if(cargo === ''){
                toastr.error('Cargo es requerido');
                return;
            }

            if(cargo.length > 100){
                toastr.error('Cargo máximo 100 caracteres');
                return;
            }


            openLoading();
            var formData = new FormData();
            formData.append('idusuario', usuario);
            formData.append('iddepartamento', departamento);
            formData.append('cargo', cargo);

            axios.post(url+'/informacion/consolidador/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.success('Usuario Repetido');
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

        function informacion(id){
            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post(url+'/informacion/consolidador/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(response.data.lista.id);

                        $('#cargo-editar').val(response.data.lista.cargo);


                        document.getElementById("select-usuario-editar").options.length = 0;
                        document.getElementById("select-departamento-editar").options.length = 0;

                        $.each(response.data.listausuario, function( key, val ){
                            if(response.data.lista.id_usuario == val.id){
                                $('#select-usuario-editar').append('<option value="' +val.id +'" selected="selected">'+val.nombre+'</option>');
                            }else{
                                $('#select-usuario-editar').append('<option value="' +val.id +'">'+val.nombre+'</option>');
                            }
                        });


                        $.each(response.data.listadepa, function( key, val ){
                            if(response.data.lista.id_departamento == val.id){
                                $('#select-departamento-editar').append('<option value="' +val.id +'" selected="selected">'+val.nombre+'</option>');
                            }else{
                                $('#select-departamento-editar').append('<option value="' +val.id +'">'+val.nombre+'</option>');
                            }
                        });


                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }

        function editar(){
            var id = document.getElementById('id-editar').value;
            var usuario = document.getElementById('select-usuario-editar').value;
            var departamento = document.getElementById('select-departamento-editar').value;
            var cargo = document.getElementById('cargo-editar').value;

            if(usuario === ''){
                toastr.error('Usuario es requerido');
                return;
            }

            if(departamento === ''){
                toastr.error('Departamento es requerido');
                return;
            }

            if(cargo === ''){
                toastr.error('Cargo es requerido');
                return;
            }

            if(cargo.length > 100){
                toastr.error('Cargo máximo 100 caracteres');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('idusuario', usuario);
            formData.append('iddepartamento', departamento);
            formData.append('cargo', cargo);

            axios.post(url+'/informacion/consolidador/actualizar', formData, {
            })
                .then((response) => {
                    closeLoading();


                    if(response.data.success === 1){
                        toastr.success('Usuario Repetido');
                    }

                    else if(response.data.success === 2){
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

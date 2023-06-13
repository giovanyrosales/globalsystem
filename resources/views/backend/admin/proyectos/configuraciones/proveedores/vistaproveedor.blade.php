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
                <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;"
                        onclick="modalAgregar()" class="button button-3d button-rounded button-pill button-small">
                    <i class="fas fa-pencil-alt"></i>
                    Nuevo Proveedor
                </button>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Proveedor</li>
                    <li class="breadcrumb-item active">Listado de Proveedores</li>
                </ol>
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
                    <h4 class="modal-title">Nuevo Proveedor</h4>
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
                                        <label>Nombre</label>
                                        <input type="text" maxlength="150" class="form-control" id="nombre-nuevo" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Teléfono</label>
                                        <input type="text" maxlength="20" class="form-control" id="telefono-nuevo" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>NIT</label>
                                        <input type="text" maxlength="25" class="form-control" id="nit-nuevo" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>NRC</label>
                                        <input type="text" maxlength="50" class="form-control" id="nrc-nuevo" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Nombre Comercial:</label>
                                        <input type="text" maxlength="100" class="form-control" id="comercial-nuevo" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>DUI:</label>
                                        <input type="text" maxlength="20" class="form-control" id="dui-nuevo" autocomplete="off">
                                    </div>
                                    <div class="form-group">
                                        <label>Correo:</label>
                                        <input type="text" maxlength="120" class="form-control" id="correo-nuevo" autocomplete="off">
                                    </div>
                                    <div class="form-group">
                                        <label>Dirección:</label>
                                        <input type="text" maxlength="350" class="form-control" id="direccion-nuevo" autocomplete="off">
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
                    <h4 class="modal-title">Editar Proveedor</h4>
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
                                        <label>Nombre</label>
                                        <input type="text" maxlength="150" class="form-control" id="nombre-editar" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Teléfono</label>
                                        <input type="text" maxlength="20" class="form-control" id="telefono-editar" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>NIT</label>
                                        <input type="text" maxlength="25" class="form-control" id="nit-editar" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>NRC</label>
                                        <input type="text" maxlength="50" class="form-control" id="nrc-editar" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Nombre Comercial</label>
                                        <input type="text" maxlength="100" class="form-control" id="comercial-editar" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>DUI</label>
                                        <input type="text" maxlength="20" class="form-control" id="dui-editar" autocomplete="off">
                                    </div>
                                    <div class="form-group">
                                        <label>Correo:</label>
                                        <input type="text" maxlength="120" class="form-control" id="correo-editar" autocomplete="off">
                                    </div>
                                    <div class="form-group">
                                        <label>Dirección:</label>
                                        <input type="text" maxlength="350" class="form-control" id="direccion-editar" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small"
                            onclick="editar()">Guardar</button>
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
            var ruta = "{{ URL::to('/admin/proveedores/tabla/index') }}";
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";

        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/proveedores/tabla/index') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function nuevo(){
            var nombre = document.getElementById('nombre-nuevo').value;
            var telefono = document.getElementById('telefono-nuevo').value;
            var nit = document.getElementById('nit-nuevo').value;
            var nrc = document.getElementById('nrc-nuevo').value;
            var comercial = document.getElementById('comercial-nuevo').value;
            var dui = document.getElementById('dui-nuevo').value;
            var correo = document.getElementById('correo-nuevo').value;
            var direccion = document.getElementById('direccion-nuevo').value;

            if(nombre === ''){
                toastr.error('Nombre es requerido');
                return;
            }

            if(nombre.length > 150){
                toastr.error('Nombre máximo 150 caracteres');
                return;
            }

            if(telefono.length > 20){
                toastr.error('Teléfono máximo 20 caracteres');
                return;
            }

            if(nit.length > 25){
                toastr.error('Nit máximo 25 caracteres');
                return;
            }

            if(nrc.length > 50){
                toastr.error('NRC máximo 50 caracteres');
                return;
            }

            if(comercial.length > 100){
                toastr.error('Nombre Comercial máximo 100 caracteres');
                return;
            }

            if(dui.length > 20){
                toastr.error('DUI máximo 20 caracteres');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('telefono', telefono);
            formData.append('nit', nit);
            formData.append('nrc', nrc);
            formData.append('comercial', comercial);
            formData.append('dui', dui);
            formData.append('correo', correo);
            formData.append('direccion', direccion);

            axios.post(url+'/proveedores/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
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

            axios.post(url+'/proveedores/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(response.data.lista.id);
                        $('#nombre-editar').val(response.data.lista.nombre);
                        $('#telefono-editar').val(response.data.lista.telefono);
                        $('#nit-editar').val(response.data.lista.nit);
                        $('#nrc-editar').val(response.data.lista.nrc);
                        $('#comercial-editar').val(response.data.lista.nombre_comercial);
                        $('#dui-editar').val(response.data.lista.dui);
                        $('#correo-editar').val(response.data.lista.correo);
                        $('#direccion-editar').val(response.data.lista.direccion);

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
            var nombre = document.getElementById('nombre-editar').value;
            var telefono = document.getElementById('telefono-editar').value;
            var nit = document.getElementById('nit-editar').value;
            var nrc = document.getElementById('nrc-editar').value;
            var comercial = document.getElementById('comercial-editar').value;
            var dui = document.getElementById('dui-editar').value;
            var correo = document.getElementById('correo-editar').value;
            var direccion = document.getElementById('direccion-editar').value;


            if(nombre === ''){
                toastr.error('Nombre es requerido');
                return;
            }

            if(nombre.length > 150){
                toastr.error('Nombre máximo 150 caracteres');
                return;
            }

            if(telefono.length > 20){
                toastr.error('Teléfono máximo 20 caracteres');
                return;
            }

            if(nit.length > 25){
                toastr.error('Nit máximo 25 caracteres');
                return;
            }

            if(nrc.length > 50){
                toastr.error('NRC máximo 50 caracteres');
                return;
            }

            if(comercial.length > 100){
                toastr.error('Nombre Comercial máximo 100 caracteres');
                return;
            }

            if(dui.length > 20){
                toastr.error('DUI máximo 20 caracteres');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('telefono', telefono);
            formData.append('nit', nit);
            formData.append('nrc', nrc);
            formData.append('comercial', comercial);
            formData.append('dui', dui);
            formData.append('correo', correo);
            formData.append('direccion', direccion);

            axios.post(url+'/proveedores/editar', formData, {
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

@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />

@stop

<style>
    .dataTables_wrapper .dataTables_info {
        float: left !important;
        text-align: left;
    }

    .dataTables_wrapper .dataTables_paginate {
        float: left !important;
        text-align: left;
        padding-left: 10px;
    }
</style>


<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <button type="button" style="margin: 10px" onclick="checkModificar()" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-square"></i>
                    Modificar Manual
                </button>

                <button type="button" style="margin: 10px" onclick="checkModificarTodos()" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-square"></i>
                    Modificar Todos
                </button>

            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Registros</li>
                    <li class="breadcrumb-item active">Listado Vencidas</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-red">
                <div class="card-header">
                        <h3 class="card-title">Listado VENCIDAS</h3>
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
                    <h4 class="modal-title">MODIFICAR ESTADO</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-estado">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <input type="hidden" id="id-editar">
                                    </div>

                                    <div class="form-group">
                                        <div class="d-flex align-items-center">
                                            <div class="form-check ml-3">
                                                <input class="form-check-input" type="checkbox" id="check-ucp">
                                                <label class="form-check-label" for="check-ucp">Entregada a UCP</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="d-flex align-items-center">
                                            <div class="form-check ml-3">
                                                <input class="form-check-input" type="checkbox" id="check-proveedor">
                                                <label class="form-check-label" for="check-proveedor">Entregada a PROVEEDOR</label>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small" onclick="actualizarEstado()">Actualizar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- UTILIZADO CUANDO SELECCIONAN CHECKBOX DE LA TABLA -->
    <div class="modal fade" id="modalCheckbox">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">MODIFICAR ESTADO</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-checkbox">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <input type="hidden" id="id-editar">
                                    </div>

                                    <div class="form-group">
                                        <div class="d-flex align-items-center">
                                            <div class="form-check ml-3">
                                                <input class="form-check-input" type="checkbox" id="check-ucp-box">
                                                <label class="form-check-label" for="check-ucp-box">Entregada a UCP</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="d-flex align-items-center">
                                            <div class="form-check ml-3">
                                                <input class="form-check-input" type="checkbox" id="check-proveedor-box">
                                                <label class="form-check-label" for="check-proveedor-box">Entregada a PROVEEDOR</label>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small" onclick="actualizarCheckBox()">Actualizar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- UTILIZADO CUANDO SELECCIONAN CHECKBOX DE LA TABLA -->
    <div class="modal fade" id="modalCheckboxTodos">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">MODIFICAR ESTADO</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-checkbox-todos">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <div class="d-flex align-items-center">
                                            <div class="form-check ml-3">
                                                <input class="form-check-input" type="checkbox" id="check-ucp-box-todos">
                                                <label class="form-check-label" for="check-ucp-box-todos">Entregada a UCP</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="d-flex align-items-center">
                                            <div class="form-check ml-3">
                                                <input class="form-check-input" type="checkbox" id="check-proveedor-box-todos">
                                                <label class="form-check-label" for="check-proveedor-box-todos">Entregada a PROVEEDOR</label>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small" onclick="actualizarCheckBoxTodos()">Actualizar Todos</button>
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

            var ruta = "{{ URL::to('/admin/tesoreria/listado-vencidas/tabla/index') }}";
            $('#tablaDatatable').load(ruta);


            document.getElementById("divcontenedor").style.display = "block";
        });


        function recargar(){
            var ruta = "{{ url('/admin/tesoreria/listado-vencidas/tabla/index') }}";
            $('#tablaDatatable').load(ruta);
        }

        function informacion(id){
            window.location.href="{{ url('/admin/tesoreria/listado/edicion/index') }}/"+id;
        }

        function infoBorrar(id){
            Swal.fire({
                title: 'BORRAR REGISTRO',
                text: "",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Borrar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    borrarRegistro(id)
                }
            })
        }

        // BORRAR LOTE DE ENTRADA COMPLETO
        function borrarRegistro(id){

            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post(url+'/tesoreria/registro/borrar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Borrado correctamente');
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


        function infoEstado(id){
            openLoading();
            document.getElementById("formulario-estado").reset();

            axios.post(url+'/tesoreria/registro/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(id);

                        let checkUcp = response.data.checkUcp
                        let checkProveedor = response.data.checkProveedor

                        document.getElementById('check-ucp').checked = checkUcp;
                        document.getElementById('check-proveedor').checked = checkProveedor;
                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }



        function actualizarEstado(){

            var id = document.getElementById('id-editar').value;
            var checkboxUcp = document.getElementById('check-ucp');
            var valorCheckboxUCP = checkboxUcp.checked ? 1 : 0;

            var checkboxProveedor = document.getElementById('check-proveedor');
            var valorCheckboxProveedor = checkboxProveedor.checked ? 1 : 0;

            if(valorCheckboxUCP === 0 && valorCheckboxProveedor === 0){
                toastr.error('Seleccionar una opción');
                return

            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('valorCheckboxUCP', valorCheckboxUCP);
            formData.append('valorCheckboxProveedor', valorCheckboxProveedor);

            axios.post(url+'/tesoreria/actualizar/estado', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        $('#modalEditar').modal('hide');
                        toastr.success('Actualizado correctamente');
                        recargar()
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




        function checkModificar(){
            var tableRows = document.querySelectorAll('#tabla tbody tr');

            var selected = [];

            if (tableRows.length === 0) {
                toastr.error('No hay registros seleccionados');
                return;
            }

            tableRows.forEach(function(row) {
                var checkbox = row.querySelector('.checkbox');
                if(checkbox != null) {
                    if (checkbox.checked) {
                        var dataInfo = row.getAttribute('data-info');
                        selected.push(dataInfo);
                    }
                }
            });

            if (selected.length <= 0) {
                toastr.error('No hay registros seleccionados');
                return;
            }

            // VALIDACION CORRECTA, ABRIR MODAL
            document.getElementById("formulario-checkbox").reset();
            $('#modalCheckbox').modal('show');
        }

        function checkModificarTodos(){

            // VALIDACION CORRECTA, ABRIR MODAL
            document.getElementById("formulario-checkbox-todos").reset();
            $('#modalCheckboxTodos').modal('show');
        }


        function actualizarCheckBox(){

            var tableRows = document.querySelectorAll('#tabla tbody tr');

            var selected = [];

            if (tableRows.length === 0) {
                toastr.error('No hay registros');
                return;
            }

            tableRows.forEach(function(row) {
                var checkbox = row.querySelector('.checkbox');
                if(checkbox != null) {
                    if (checkbox.checked) {
                        var dataInfo = row.getAttribute('data-info');
                        selected.push(dataInfo);
                    }
                }
            });

            if (selected.length <= 0) {
                toastr.error('Seleccionar Mínimo 1 Fila')
                return;
            }

            let listado = selected.toString();
            let reemplazo = listado.replace(/,/g, "-");


            var checkboxUcp = document.getElementById('check-ucp-box');
            var valorCheckboxUCP = checkboxUcp.checked ? 1 : 0;

            var checkboxProveedor = document.getElementById('check-proveedor-box');
            var valorCheckboxProveedor = checkboxProveedor.checked ? 1 : 0;

            if(valorCheckboxUCP === 0 && valorCheckboxProveedor === 0){
                toastr.error('Seleccionar una opción');
                return
            }

            openLoading();
            var formData = new FormData();
            formData.append('valorCheckboxUCP', valorCheckboxUCP);
            formData.append('valorCheckboxProveedor', valorCheckboxProveedor);
            formData.append('reemplazo', reemplazo);

            axios.post(url+'/tesoreria/actualizar/estado-checkbox', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        $('#modalCheckbox').modal('hide');
                        toastr.success('Actualizado correctamente');
                        recargar()
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


        function actualizarCheckBoxTodos(){

            var checkboxUcp = document.getElementById('check-ucp-box-todos');
            var valorCheckboxUCP = checkboxUcp.checked ? 1 : 0;

            var checkboxProveedor = document.getElementById('check-proveedor-box-todos');
            var valorCheckboxProveedor = checkboxProveedor.checked ? 1 : 0;

            if(valorCheckboxUCP === 0 && valorCheckboxProveedor === 0){
                toastr.error('Seleccionar una opción');
                return
            }

            openLoading();
            var formData = new FormData();
            formData.append('valorCheckboxUCP', valorCheckboxUCP);
            formData.append('valorCheckboxProveedor', valorCheckboxProveedor);

            axios.post(url+'/tesoreria/actualizar/estado-checkbox-todos', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        $('#modalCheckboxTodos').modal('hide');
                        toastr.success('Actualizado correctamente');
                        recargar()
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

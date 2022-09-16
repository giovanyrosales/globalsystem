@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />

@stop

<style>
    table{
        /*Ajustar tablas*/
        table-layout:fixed;
    }

    .dropdown-menu {
        max-height: 280px;
        overflow-y: auto;
        width: 100%;
    }
</style>

<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-3">
                    <h1>Creación de Cuentas Bolsón</h1>
                </div>
                <div class="col-sm-2">
                    <button type="button" onclick="abrirModalAgregar()" class="btn btn-success btn-sm">
                        <i class="fas fa-pencil-alt"></i>
                        Nuevo Cuenta Bolsón
                    </button>
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
                    <h4 class="modal-title">Nueva Cuenta</h4>
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
                                        <label>Nombre de la Cuenta *:</label>
                                        <div class="col-sm-8">
                                            <input name="nombre-cuenta-nuevo" id="nombre-cuenta-nuevo" placeholder="Buscar..." class="form-control" type="text" autocomplete="off">
                                            <div id="cuentaLista" style="position: absolute; z-index: 9;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nombre *:</label>
                                            <input type="text" class="form-control" maxlength="300" id="nombre-nuevo">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Número de Cuenta:</label>
                                            <input type="text" class="form-control" maxlength="100" id="numero-nuevo">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Mono Inicial:</label>
                                            <input type="number" class="form-control" id="monto-nuevo">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fecha *</label>
                                            <input type="date" class="form-control" id="fecha-nuevo">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="nuevo()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal editar -->
    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Clasificación</h4>
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
                                        <label>Nombre de la Cuenta *:</label>
                                        <div class="col-sm-8">
                                            <input name="nombre-cuenta-editar" id="nombre-cuenta-editar" placeholder="Buscar..." class="form-control" type="text" autocomplete="off">
                                            <div id="cuentaListaEditar" style="position: absolute; z-index: 9;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nombre *:</label>
                                            <input type="text" class="form-control" maxlength="300" id="nombre-editar">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Número de Cuenta:</label>
                                            <input type="text" class="form-control" maxlength="100" id="numero-editar">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Mono Inicial:</label>
                                            <input type="number" class="form-control" id="monto-editar">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fecha *</label>
                                            <input type="date" class="form-control" id="fecha-editar">
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="editar()">Guardar</button>
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
            var ruta = "{{ URL::to('/admin/bolson/cuenta/indextabla') }}";
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";

            window.idGlobalCuenta = 0;
            window.idGlobalCuentaEditar = 0;

            $('#nombre-cuenta-nuevo').keyup(function(){

                idGlobalCuenta = 0;

                var query = $(this).val();
                if(query != ''){
                    axios.post(url+'/bolson/buscar/cuenta', {
                        'query' : query
                    })
                        .then((response) => {

                            $('#cuentaLista').fadeIn();
                            $('#cuentaLista').html(response.data);

                            if(response.data == ''){
                                idGlobalCuenta = 0;
                            }
                        })
                        .catch((error) => {
                        });
                }else{
                    idGlobalCuenta = 0;
                }
            });

            $('#nombre-cuenta-editar').keyup(function(){

                idGlobalCuentaEditar = 0;

                var query = $(this).val();
                if(query != ''){
                    axios.post(url+'/bolson/buscar/cuenta-editar', {
                        'query' : query
                    })
                        .then((response) => {

                            $('#cuentaListaEditar').fadeIn();
                            $('#cuentaListaEditar').html(response.data);

                            if(response.data == ''){
                                idGlobalCuentaEditar = 0;
                            }
                        })
                        .catch((error) => {
                        });
                }else{
                    idGlobalCuentaEditar = 0;
                }
            });

            $(document).on('click', 'li', function(){
                $('#nombre-cuenta-nuevo').val($(this).text());
                $('#cuentaLista').fadeOut();

                $('#nombre-cuenta-editar').val($(this).text());
                $('#cuentaListaEditar').fadeOut();
            });

            $(document).click(function(){
                $('#cuentaLista').fadeOut();
                $('#cuentaListaEditar').fadeOut();
            });


        });
    </script>

    <script>

        function modificarValor(id) {
            idGlobalCuenta = id;
        }

        function modificarValorEditar(id) {
            idGlobalCuentaEditar = id;
        }

        function recargar(){
            var ruta = "{{ url('/admin/bolson/cuenta/indextabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function abrirModalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function nuevo(){
            var nombre = document.getElementById('nombre-nuevo').value;
            var numero = document.getElementById('numero-nuevo').value;
            var monto = document.getElementById('monto-nuevo').value;
            var fecha = document.getElementById('fecha-nuevo').value;

            if(idGlobalCuenta == 0){
                toastr.error('Se debe buscar nombre de la Cuenta');
                return;
            }

            if(nombre === ''){
                toastr.error('Nombre de Cuenta es Requerido');
                return;
            }

            if(nombre.length > 300){
                toastr.error('Máximo 300 caracteres para Nombre de Cuenta');
                return;
            }

            if(numero.length > 100){
                toastr.error('Máximo 100 caracteres para Número de Cuenta');
                return;
            }

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            if(monto.length > 0){
                if(!monto.match(reglaNumeroDecimal)) {
                    toastr.error('Monto debe ser decimal y no negativo');
                    return;
                }

                if(monto < 0){
                    toastr.error('Monto no debe ser negativo');
                    return;
                }

                if(monto.length > 10){
                    toastr.error('Monto debe tener máximo 10 caracteres');
                    return;
                }
            }else{
                monto = 0;
            }

            if(fecha === ''){
                toastr.error('Fecha es requerida');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('numero', numero);
            formData.append('monto', monto);
            formData.append('fecha', fecha);
            formData.append('idcuenta', idGlobalCuenta);

            axios.post(url+'/bolson/nuevo', formData, {
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

            idGlobalCuentaEditar = 0;

            axios.post(url+'/bolson/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');

                        $('#id-editar').val(id);
                        $('#nombre-cuenta-editar').val(response.data.cuenta);
                        $('#nombre-editar').val(response.data.info.nombre);
                        $('#numero-editar').val(response.data.info.numero);
                        $('#monto-editar').val(response.data.info.montoini);
                        $('#fecha-editar').val(response.data.info.fecha);
                        idGlobalCuentaEditar = response.data.info.id_cuenta;

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
            var numero = document.getElementById('numero-editar').value;
            var monto = document.getElementById('monto-editar').value;
            var fecha = document.getElementById('fecha-editar').value;

            if(idGlobalCuentaEditar == 0){
                toastr.error('Se debe buscar nombre de la Cuenta');
                return;
            }

            if(nombre === ''){
                toastr.error('Nombre de Cuenta es Requerido');
                return;
            }

            if(nombre.length > 300){
                toastr.error('Máximo 300 caracteres para Nombre de Cuenta');
                return;
            }

            if(numero.length > 100){
                toastr.error('Máximo 100 caracteres para Número de Cuenta');
                return;
            }

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            if(monto.length > 0){
                if(!monto.match(reglaNumeroDecimal)) {
                    toastr.error('Monto debe ser decimal y no negativo');
                    return;
                }

                if(monto < 0){
                    toastr.error('Monto no debe ser negativo');
                    return;
                }

                if(monto.length > 10){
                    toastr.error('Monto debe tener máximo 10 caracteres');
                    return;
                }
            }else{
                monto = 0;
            }

            if(fecha === ''){
                toastr.error('Fecha es requerida');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('numero', numero);
            formData.append('monto', monto);
            formData.append('fecha', fecha);
            formData.append('idcuenta', idGlobalCuentaEditar);

            axios.post(url+'/bolson/editar', formData, {
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

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
                    <h1>Cuenta Proyecto</h1>
                </div>
                <div class="col-sm-2">
                    <button type="button" onclick="abrirModalAgregar()" class="btn btn-success btn-sm">
                        <i class="fas fa-pencil-alt"></i>
                        Nueva Cuenta Proyecto
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
                    <h4 class="modal-title">Nueva Cuenta Proyecto</h4>
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
                                        <label>Proyecto *:</label>
                                        <select class="form-control" id="select-proyecto" style="width: 100%">
                                            @foreach($proyecto as $dd)
                                                <option value="{{ $dd->id }}"> {{ $dd->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Cuenta:</label>
                                        <select class="form-control" id="select-cuenta" style="width: 100%">
                                            @foreach($cuenta as $dd)
                                                <option value="{{ $dd->id }}"> {{ $dd->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Monto:</label>
                                            <input type="text" class="form-control" id="monto-nuevo">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Saldo:</label>
                                            <input type="text" class="form-control" id="saldo-nuevo">
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
                    <h4 class="modal-title">Editar Cuenta Proyecto</h4>
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
                                        <label>Proyecto:</label>
                                        <select class="form-control" id="select-proyecto-editar" style="width: 100%">
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Cuenta:</label>
                                        <select class="form-control" id="select-cuenta-editar" style="width: 100%">
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Monto:</label>
                                            <input type="text" class="form-control" id="monto-editar">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Saldo:</label>
                                            <input type="text" class="form-control" id="saldo-editar">
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
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            var ruta = "{{ URL::to('/admin/cuentaproy/cuenta/indextabla') }}";
            $('#tablaDatatable').load(ruta);

            $('#select-bolson').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            $('#select-proyecto').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            $('#select-cuenta').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            $('#select-cuenta-editar').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            $('#select-proyecto-editar').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            document.getElementById("divcontenedor").style.display = "block";

        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/cuentaproy/cuenta/indextabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function abrirModalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function nuevo(){
            var proyecto = document.getElementById('select-proyecto').value;
            var cuenta = document.getElementById('select-cuenta').value;

            var monto = document.getElementById('monto-nuevo').value;
            var saldo = document.getElementById('saldo-nuevo').value;

            if(proyecto === ''){
                toastr.error('Nombre de Proyecto es Requerido');
                return;
            }

            if(cuenta === ''){
                toastr.error('Cuenta es Requerido');
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

            if(saldo.length > 0){
                if(!saldo.match(reglaNumeroDecimal)) {
                    toastr.error('Saldo debe ser decimal y no negativo');
                    return;
                }

                if(saldo < 0){
                    toastr.error('Saldo no debe ser negativo');
                    return;
                }

                if(saldo.length > 10){
                    toastr.error('Saldo debe tener máximo 10 caracteres');
                    return;
                }
            }else{
                saldo = 0;
            }

            openLoading();
            var formData = new FormData();
            formData.append('proyecto', proyecto);
            formData.append('cuenta', cuenta);
            formData.append('saldo', saldo);
            formData.append('monto', monto);

            axios.post(url+'/cuentaproy/nuevo', formData, {
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

            axios.post(url+'/cuentaproy/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');

                        $('#id-editar').val(id);
                        $('#monto-editar').val(response.data.info.montoini);
                        $('#saldo-editar').val(response.data.info.saldo);

                        document.getElementById("select-proyecto-editar").options.length = 0;
                        document.getElementById("select-cuenta-editar").options.length = 0;

                        $.each(response.data.proyecto, function( key, val ){
                            if(response.data.idproyecto == val.id){
                                $('#select-proyecto-editar').append('<option value="' +val.id +'" selected="selected">'+ val.nombre +'</option>');
                            }else{
                                $('#select-proyecto-editar').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
                            }
                        });

                        $.each(response.data.cuenta, function( key, val ){
                            if(response.data.idcuenta == val.id){
                                $('#select-cuenta-editar').append('<option value="' +val.id +'" selected="selected">'+ val.nombre +'</option>');
                            }else{
                                $('#select-cuenta-editar').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
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
            var proyecto = document.getElementById('select-proyecto-editar').value;
            var cuenta = document.getElementById('select-cuenta-editar').value;

            var monto = document.getElementById('monto-editar').value;
            var saldo = document.getElementById('saldo-editar').value;

            if(proyecto === ''){
                toastr.error('Nombre de Proyecto es Requerido');
                return;
            }

            if(cuenta === ''){
                toastr.error('Cuenta es Requerido');
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

            if(saldo.length > 0){
                if(!saldo.match(reglaNumeroDecimal)) {
                    toastr.error('Saldo debe ser decimal y no negativo');
                    return;
                }

                if(saldo < 0){
                    toastr.error('Saldo no debe ser negativo');
                    return;
                }

                if(saldo.length > 10){
                    toastr.error('Saldo debe tener máximo 10 caracteres');
                    return;
                }
            }else{
                saldo = 0;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('proyecto', proyecto);
            formData.append('cuenta', cuenta);
            formData.append('saldo', saldo);
            formData.append('montoini', monto);

            axios.post(url+'/cuentaproy/editar', formData, {
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

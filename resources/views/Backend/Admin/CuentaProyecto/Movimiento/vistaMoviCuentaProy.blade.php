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
            <div class="row mb-6">
                <div class="col-sm-5">
                    <h1>Movimiento Cuenta de Proyecto</h1>
                    <button type="button" style="margin-top: 15px" onclick="abrirModalAgregar()" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus-square"></i>
                        Nuevo Movimiento de Cuenta
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
                                        <select class="form-control" id="select-proyecto" onchange="verificar()" style="width: 100%">
                                            <option value="">Seleccionar Proyecto</option>
                                            @foreach($proyecto as $dd)
                                                <option value="{{ $dd->id }}"> {{ $dd->nombre }} - {{ $dd->codigo }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Cuenta Proyecto *:</label>
                                        <select class="form-control" id="select-cuentaproy"  style="width: 100%">
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Aumenta:</label>
                                            <input type="text" class="form-control" id="aumenta-nuevo">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Disminuye:</label>
                                            <input type="text" class="form-control" id="disminuye-nuevo">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fecha:</label>
                                            <input type="date" class="form-control" id="fecha-nuevo">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 col-form-label">Reforma:</label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-file"></i></span>
                                                </div>
                                                <input type="file" style="color:#191818; width: 80%" id="documento" accept="image/jpeg, image/jpg, image/png, .pdf"/>
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
                                        <label>Proyecto *:</label>
                                        <select class="form-control" id="select-proyecto-editar" onchange="verificar()" style="width: 100%">
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Cuenta Proyecto *:</label>
                                        <select class="form-control" id="select-cuentaproy-editar"  style="width: 100%">
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Aumenta:</label>
                                            <input type="text" class="form-control" id="aumenta-editar">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Disminuye:</label>
                                            <input type="text" class="form-control" id="disminuye-editar">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fecha:</label>
                                            <input type="date" class="form-control" id="fecha-editar">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 col-form-label">Reforma:</label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-file"></i></span>
                                                </div>
                                                <input type="file" style="color:#191818; width: 80%" id="documento-editar" accept="image/jpeg, image/jpg, image/png, .pdf"/>
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
            var ruta = "{{ URL::to('/admin/movicuentaproy/tablamovicuentaproy') }}";
            $('#tablaDatatable').load(ruta);

            $('#select-proyecto').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            $('#select-cuentaproy').select2({
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

            $('#select-cuentaproy-editar').select2({
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
            var ruta = "{{ url('/admin/movicuentaproy/tablamovicuentaproy') }}";
            $('#tablaDatatable').load(ruta);
        }

        function verificar(){

            var id = document.getElementById('select-proyecto').value;

            if(id !== ''){
                openLoading();
                document.getElementById("formulario-editar").reset();

                axios.post(url+'/movicuentaproy/buscador',{
                    'id': id
                })
                    .then((response) => {
                        closeLoading();

                        if(response.data.success === 1){

                            document.getElementById("select-cuentaproy").options.length = 0;

                            $('#select-cuentaproy').append('<option value="" selected="selected">Seleccionar Cuenta</option>');
                            $.each(response.data.cuentaproy, function( key, val ){
                                $('#select-cuentaproy').append('<option value="' +val.id +'">'+ val.nomcuenta +'</option>');
                            });

                        }else{
                            toastr.error('Información no encontrada');
                        }
                    })
                    .catch((error) => {
                        closeLoading();
                        toastr.error('Información no encontrada');
                    });
            }else{
                document.getElementById("select-cuentaproy").options.length = 0;
            }
        }

        function abrirModalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function nuevo(){
            var proyecto = document.getElementById('select-proyecto').value;
            var cuenta = document.getElementById('select-cuentaproy').value;
            var documento = document.getElementById('documento');
            var aumenta = document.getElementById('aumenta-nuevo').value;
            var disminuye = document.getElementById('disminuye-nuevo').value;
            var fecha = document.getElementById('fecha-nuevo').value;

            if(proyecto === ''){
                toastr.error('Nombre de Proyecto es Requerido');
                return;
            }

            if(cuenta === ''){
                toastr.error('Cuenta Proyecto es Requerido');
                return;
            }

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            if(aumenta.length > 0){
                if(!aumenta.match(reglaNumeroDecimal)) {
                    toastr.error('Aumenta debe ser decimal y no negativo');
                    return;
                }

                if(aumenta < 0){
                    toastr.error('Aumenta no debe ser negativo');
                    return;
                }

                if(aumenta.length > 10){
                    toastr.error('Aumenta debe tener máximo 10 caracteres');
                    return;
                }
            }else{
                aumenta = 0;
            }

            if(disminuye.length > 0){
                if(!disminuye.match(reglaNumeroDecimal)) {
                    toastr.error('Disminuye debe ser decimal y no negativo');
                    return;
                }

                if(disminuye < 0){
                    toastr.error('Disminuye no debe ser negativo');
                    return;
                }

                if(disminuye.length > 10){
                    toastr.error('Disminuye debe tener máximo 10 caracteres');
                    return;
                }
            }else{
                disminuye = 0;
            }

            if(documento.files && documento.files[0]){ // si trae doc
                if (!documento.files[0].type.match('image/jpeg|image/jpeg|image/png|pdf')){
                    toastr.error('formato de documento permitido: .png .jpg .jpeg .pdf');
                    return;
                }
            }

            openLoading();
            var formData = new FormData();
            formData.append('proyecto', proyecto);
            formData.append('cuenta', cuenta);
            formData.append('aumenta', aumenta);
            formData.append('disminuye', disminuye);
            formData.append('fecha', fecha);
            formData.append('documento', documento.files[0]);

            axios.post(url+'/movicuentaproy/nuevo', formData, {
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

            axios.post(url+'/movicuentaproy/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');

                        $('#id-editar').val(id);
                        $('#aumenta-editar').val(response.data.info.aumenta);
                        $('#disminuye-editar').val(response.data.info.disminuye);
                        $('#fecha-editar').val(response.data.info.fecha);

                        document.getElementById("select-proyecto-editar").options.length = 0;
                        document.getElementById("select-cuentaproy-editar").options.length = 0;

                        $.each(response.data.proyecto, function( key, val ){
                            if(response.data.idproyecto == val.id){
                                $('#select-proyecto-editar').append('<option value="' +val.id +'" selected="selected">'+ val.nombrecod +'</option>');
                            }else{
                                $('#select-proyecto-editar').append('<option value="' +val.id +'">'+ val.nombrecod +'</option>');
                            }
                        });

                        $.each(response.data.cuentaproy, function( key, val ){
                            if(response.data.idcuentaproy == val.id){
                                $('#select-cuentaproy-editar').append('<option value="' +val.id +'" selected="selected">'+ val.nombrecod +'</option>');
                            }else{
                                $('#select-cuentaproy-editar').append('<option value="' +val.id +'">'+ val.nombrecod +'</option>');
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
            var cuenta = document.getElementById('select-cuentaproy-editar').value;
            var documento = document.getElementById('documento-editar');
            var aumenta = document.getElementById('aumenta-editar').value;
            var disminuye = document.getElementById('disminuye-editar').value;
            var fecha = document.getElementById('fecha-editar').value;

            if(proyecto === ''){
                toastr.error('Nombre de Proyecto es Requerido');
                return;
            }

            if(cuenta === ''){
                toastr.error('Cuenta Proyecto es Requerido');
                return;
            }

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            if(aumenta.length > 0){
                if(!aumenta.match(reglaNumeroDecimal)) {
                    toastr.error('Aumenta debe ser decimal y no negativo');
                    return;
                }

                if(aumenta < 0){
                    toastr.error('Aumenta no debe ser negativo');
                    return;
                }

                if(aumenta.length > 10){
                    toastr.error('Aumenta debe tener máximo 10 caracteres');
                    return;
                }
            }else{
                aumenta = 0;
            }

            if(disminuye.length > 0){
                if(!disminuye.match(reglaNumeroDecimal)) {
                    toastr.error('Disminuye debe ser decimal y no negativo');
                    return;
                }

                if(disminuye < 0){
                    toastr.error('Disminuye no debe ser negativo');
                    return;
                }

                if(disminuye.length > 10){
                    toastr.error('Disminuye debe tener máximo 10 caracteres');
                    return;
                }
            }else{
                disminuye = 0;
            }

            if(documento.files && documento.files[0]){ // si trae doc
                if (!documento.files[0].type.match('image/jpeg|image/jpeg|image/png|pdf')){
                    toastr.error('formato de documento permitido: .png .jpg .jpeg .pdf');
                    return;
                }
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('proyecto', proyecto);
            formData.append('cuenta', cuenta);
            formData.append('aumenta', aumenta);
            formData.append('disminuye', disminuye);
            formData.append('fecha', fecha);
            formData.append('documento', documento.files[0]);

            axios.post(url+'/movicuentaproy/editar', formData, {
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

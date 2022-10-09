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
            <div class="row mb-2">
                <div class="col-sm-3">
                    <h1>Movimiento Bolsón</h1>
                </div>
                <div class="col-sm-2">
                    <button type="button" onclick="abrirModalAgregar()" class="btn btn-success btn-sm">
                        <i class="fas fa-pencil-alt"></i>
                        Nuevo Movimiento
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
                                        <label>Bolsón *:</label>
                                        <select class="form-control" id="select-bolson" style="width: 100%">
                                            @foreach($bolson as $dd)
                                                <option value="{{ $dd->id }}"> {{ $dd->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Proyecto *:</label>
                                        <select class="form-control" id="select-proyecto" style="width: 100%">
                                            @foreach($proyecto as $dd)
                                                <option value="{{ $dd->id }}"> {{ $dd->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Tipo Movimiento:</label>
                                        <select class="form-control" id="select-movimiento" style="width: 100%">
                                            @foreach($tipomovi as $dd)
                                                <option value="{{ $dd->id }}"> {{ $dd->nombre }}</option>
                                            @endforeach
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
                    <h4 class="modal-title">Editar Movimiento</h4>
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
                                        <label>Bolsón:</label>
                                        <select class="form-control" id="select-bolson-editar" style="width: 100%">
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Proyecto:</label>
                                        <select class="form-control" id="select-proyecto-editar" style="width: 100%">
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Tipo Movimiento:</label>
                                        <select class="form-control" id="select-movimiento-editar" style="width: 100%">
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
            var ruta = "{{ URL::to('/admin/bolson/movimiento/tabla') }}";
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

            $('#select-movimiento').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            $('#select-bolson-editar').select2({
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

            $('#select-movimiento-editar').select2({
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
            var ruta = "{{ url('/admin/bolson/movimiento/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function abrirModalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function nuevo(){
            var proyecto = document.getElementById('select-proyecto').value;
            var bolson = document.getElementById('select-bolson').value;
            var movimiento = document.getElementById('select-movimiento').value;

            var aumenta = document.getElementById('aumenta-nuevo').value;
            var disminuye = document.getElementById('disminuye-nuevo').value;
            var fecha = document.getElementById('fecha-nuevo').value;

            if(proyecto === ''){
                toastr.error('Nombre de Proyecto es Requerido');
                return;
            }

            if(bolson === ''){
                toastr.error('Bolsón es Requerido');
                return;
            }

            if(movimiento === ''){
                toastr.error('Tipo Movimiento es Requerido');
                return;
            }

            if(fecha === ''){
                toastr.error('Fecha es Requerido');
                return;
            }

            var reglaNumeroDosDecimal = /^([0-9]+\.?[0-9]{0,2})$/;

            if(aumenta.length > 0){
                if(!aumenta.match(reglaNumeroDosDecimal)) {
                    toastr.error('Aumenta debe ser Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(aumenta < 0){
                    toastr.error('Aumenta no debe ser negativo');
                    return;
                }

                if(aumenta > 99000000){
                    toastr.error('Aumenta debe tener máximo 99 millones');
                    return;
                }
            }else{
                aumenta = 0;
            }

            if(disminuye.length > 0){
                if(!disminuye.match(reglaNumeroDosDecimal)) {
                    toastr.error('Disminuye debe ser Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(disminuye < 0){
                    toastr.error('Disminuye no debe ser negativo');
                    return;
                }

                if(disminuye > 99000000){
                    toastr.error('Disminuye debe tener máximo 99 millones');
                    return;
                }
            }else{
                disminuye = 0;
            }

            openLoading();
            var formData = new FormData();
            formData.append('proyecto', proyecto);
            formData.append('bolson', bolson);
            formData.append('movimiento', movimiento);
            formData.append('aumenta', aumenta);
            formData.append('disminuye', disminuye);
            formData.append('fecha', fecha);

            axios.post(url+'/bolson/movimiento/nuevo', formData, {
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

            axios.post(url+'/bolson/movimiento/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');

                        $('#id-editar').val(id);
                        $('#aumenta-editar').val(response.data.info.aumenta);
                        $('#disminuye-editar').val(response.data.info.disminuye);
                        $('#fecha-editar').val(response.data.info.fecha);56

                        document.getElementById("select-bolson-editar").options.length = 0;
                        document.getElementById("select-proyecto-editar").options.length = 0;
                        document.getElementById("select-movimiento-editar").options.length = 0;

                        $.each(response.data.bolson, function( key, val ){
                            if(response.data.idbolson == val.id){
                                $('#select-bolson-editar').append('<option value="' +val.id +'" selected="selected">'+ val.nombre +'</option>');
                            }else{
                                $('#select-bolson-editar').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
                            }
                        });

                        $.each(response.data.proyecto, function( key, val ){
                            if(response.data.idproyecto == val.id){
                                $('#select-proyecto-editar').append('<option value="' +val.id +'" selected="selected">'+ val.nombre +'</option>');
                            }else{
                                $('#select-proyecto-editar').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
                            }
                        });

                        $.each(response.data.movimiento, function( key, val ){
                            if(response.data.idmovi == val.id){
                                $('#select-movimiento-editar').append('<option value="' +val.id +'" selected="selected">'+ val.nombre +'</option>');
                            }else{
                                $('#select-movimiento-editar').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
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
            var bolson = document.getElementById('select-bolson-editar').value;
            var movimiento = document.getElementById('select-movimiento-editar').value;

            var aumenta = document.getElementById('aumenta-editar').value;
            var disminuye = document.getElementById('disminuye-editar').value;
            var fecha = document.getElementById('fecha-editar').value;

            if(proyecto === ''){
                toastr.error('Nombre de Proyecto es Requerido');
                return;
            }

            if(bolson === ''){
                toastr.error('Bolsón es Requerido');
                return;
            }

            if(movimiento === ''){
                toastr.error('Tipo Movimiento es Requerido');
                return;
            }

            if(fecha === ''){
                toastr.error('Fecha es Requerido');
                return;
            }

            var reglaNumeroDosDecimal = /^([0-9]+\.?[0-9]{0,2})$/;

            if(aumenta.length > 0){
                if(!aumenta.match(reglaNumeroDosDecimal)) {
                    toastr.error('Aumenta debe ser Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(aumenta < 0){
                    toastr.error('Aumenta no debe ser negativo');
                    return;
                }

                if(aumenta > 99000000){
                    toastr.error('Aumenta debe tener máximo 99 millones');
                    return;
                }
            }else{
                aumenta = 0;
            }

            if(disminuye.length > 0){
                if(!disminuye.match(reglaNumeroDosDecimal)) {
                    toastr.error('Disminuye debe ser Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(disminuye < 0){
                    toastr.error('Disminuye no debe ser negativo');
                    return;
                }

                if(disminuye > 99000000){
                    toastr.error('Disminuye debe tener máximo 99 millones');
                    return;
                }
            }else{
                disminuye = 0;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('proyectoid', proyecto);
            formData.append('bolsonid', bolson);
            formData.append('movimientoid', movimiento);
            formData.append('aumenta', aumenta);
            formData.append('disminuye', disminuye);
            formData.append('fecha', fecha);

            axios.post(url+'/bolson/movimiento/editar', formData, {
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

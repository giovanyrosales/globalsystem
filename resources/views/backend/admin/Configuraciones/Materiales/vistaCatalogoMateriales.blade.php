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
</style>

<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <button type="button" onclick="modalAgregar()" class="btn btn-success btn-sm">
                    <i class="fas fa-pencil-alt"></i>
                    Nuevo Material
                </button>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Materiales</li>
                    <li class="breadcrumb-item active">Catálogo de Materiales</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Listado Catálogo de Materiales</h3>
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nuevo Material</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Código:</label>
                                        <select style="width: 50%;" class="form-control" id="select-codigo-nuevo">
                                            <option value="" disabled selected>Seleccione una opción...</option>
                                            @foreach($lCodiEspec as $sel)
                                                <option value="{{ $sel->id }}">{{ $sel->codigo.' '.$sel->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Nombre *:</label>
                                        <input type="text" class="form-control" onpaste="contarcaracteresIngreso();" onkeyup="contarcaracteresIngreso();" maxlength="300" id="nombre-nuevo" placeholder="Nombre del material">
                                        <div id="res-caracter-nuevo" style="float: right">0/300</div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Unidad de Medida:</label>
                                        <br>
                                        <select width="60%"  class="form-control" id="select-unidad-nuevo">
                                            <option value="" disabled selected>Seleccione una opción...</option>
                                            @foreach($lUnidad as $sel)
                                                <option value="{{ $sel->id }}">{{ $sel->medida }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Precio Unitario *:</label>
                                        <input type="number" class="form-control" id="precio-nuevo" maxlength="10">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Clasificación *:</label>
                                        <select width="60%"  class="form-control" id="select-clasi-nuevo">
                                            <option value="" disabled selected>Seleccione una opción...</option>
                                            @foreach($lClasificacion as $sel)
                                                <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
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
                    <button type="button" class="btn btn-primary" onclick="verificarGuardar()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal editar -->
    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Material</h4>
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

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Código:</label>
                                                <select style="width: 50%;" class="form-control" id="select-codigo-editar">
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Nombre *:</label>
                                                <input type="text" class="form-control" onpaste="contarcaracteresEditar();" onkeyup="contarcaracteresEditar();" maxlength="300" id="nombre-editar" placeholder="Nombre del material">
                                                <div id="res-caracter-editar" style="float: right">0/300</div>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Unidad de Medida:</label>
                                                <br>
                                                <select width="60%"  class="form-control" id="select-unidad-editar">
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Precio Unitario *:</label>
                                                <input type="number" class="form-control" id="precio-editar" maxlength="10">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Clasificación *:</label>
                                                <select width="60%"  class="form-control" id="select-clasi-editar">
                                                </select>
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
                    <button type="button" class="btn btn-primary" onclick="verificarEditar()">Actualizar</button>
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
            var ruta = "{{ URL::to('/admin/catalogo/materiales/tabla/index') }}";
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";

        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/catalogo/materiales/tabla/index') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            document.getElementById('res-caracter-nuevo').innerHTML = '0/300 ';
            $('#modalAgregar').modal({backdrop: 'static', keyboard: false})
        }

        function verificarGuardar(){
            Swal.fire({
                title: 'Guardar Material?',
                text: "",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    nuevo();
                }
            })
        }

        function verificarEditar(){
            Swal.fire({
                title: 'Actualizar Material?',
                text: "",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    editar();
                }
            })
        }

        function nuevo(){

            var codigo = document.getElementById('select-codigo-nuevo').value; // nullable
            var nombre = document.getElementById('nombre-nuevo').value;
            var precio = document.getElementById('precio-nuevo').value;
            var unidad = document.getElementById('select-unidad-nuevo').value; // nullable
            var clasificacion = document.getElementById('select-clasi-nuevo').value; // nullable

            if(nombre === ''){
                toastr.error('Nombre es requerido');
                return;
            }

            if(nombre.length > 300){
                toastr.error('Nombre máximo 300 caracteres');
                return;
            }

            if(precio === ''){
                toastr.error('Precio es requerido');
                return;
            }

            if(clasificacion === ''){
                toastr.error('Clasificación es requerido');
                return;
            }

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            if (!precio.match(reglaNumeroDecimal)) {
                toastr.error('Precio debe ser número Decimal y no Negativo');
                return;
            }

            if (precio < 0) {
                toastr.error('Precio no permite números negativos');
                return;
            }

            if (precio.length > 10) {
                toastr.error('Precio máximo 10 dígitos de límite');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('codigo', codigo);
            formData.append('nombre', nombre);
            formData.append('precio', precio);
            formData.append('unidad', unidad);
            formData.append('clasificacion', clasificacion);

            axios.post(url+'/catalogo/materiales/nuevo', formData, {
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

            axios.post(url+'/catalogo/materiales/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal({backdrop: 'static', keyboard: false})

                        $('#id-editar').val(response.data.registro.id);
                        $('#nombre-editar').val(response.data.registro.nombre);
                        $('#precio-editar').val(response.data.registro.pu);

                        contarcaracteresEditar();

                        document.getElementById("select-codigo-editar").options.length = 0;
                        document.getElementById("select-unidad-editar").options.length = 0;
                        document.getElementById("select-clasi-editar").options.length = 0;

                        if(response.data.arraydatos['idcodigo'] == null){
                            $('#select-codigo-editar').append('<option value="">Seleccionar una opción</option>');
                        }

                        $.each(response.data.codigo, function( key, val ){
                            if(response.data.arraydatos['idcodigo'] == val.id){
                                $('#select-codigo-editar').append('<option value="' +val.id +'" selected="selected">'+val.codigo + ' ' + val.nombre +'</option>');
                            }else{
                                $('#select-codigo-editar').append('<option value="' +val.id +'">'+val.codigo + ' ' + val.nombre +'</option>');
                            }
                        });

                        if(response.data.arraydatos['idmedida'] == null){
                            $('#select-unidad-editar').append('<option value="">Seleccionar una opción</option>');
                        }

                        $.each(response.data.unidad, function( key, val ){
                            if(response.data.arraydatos['idmedida'] == val.id){
                                $('#select-unidad-editar').append('<option value="' +val.id +'" selected="selected">'+ val.medida +'</option>');
                            }else{
                                $('#select-unidad-editar').append('<option value="' +val.id +'">'+ val.medida +'</option>');
                            }
                        });

                        if(response.data.arraydatos['idclasifi'] == null){
                            $('#select-clasi-editar').append('<option value="">Seleccionar una opción</option>');
                        }

                        $.each(response.data.clasificacion, function( key, val ){
                            if(response.data.arraydatos['idclasifi'] == val.id){
                                $('#select-clasi-editar').append('<option value="' +val.id +'" selected="selected">'+ val.nombre +'</option>');
                            }else{
                                $('#select-clasi-editar').append('<option value="' +val.id +'">'+ val.nombre +'</option>');
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
            var codigo = document.getElementById('select-codigo-editar').value; // nullable
            var nombre = document.getElementById('nombre-editar').value;
            var precio = document.getElementById('precio-editar').value;
            var unidad = document.getElementById('select-unidad-editar').value; // nullable
            var clasificacion = document.getElementById('select-clasi-editar').value; // nullable

            if(nombre === ''){
                toastr.error('Nombre es requerido');
                return;
            }

            if(nombre.length > 300){
                toastr.error('Nombre máximo 300 caracteres');
                return;
            }

            if(precio === ''){
                toastr.error('Precio es requerido');
                return;
            }

            if(clasificacion === ''){
                toastr.error('Clasificación es requerido');
                return;
            }

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            if (!precio.match(reglaNumeroDecimal)) {
                toastr.error('Precio debe ser número Decimal y no Negativo');
                return;
            }

            if (precio < 0) {
                toastr.error('Precio no permite números negativos');
                return;
            }

            if (precio.length > 10) {
                toastr.error('Precio máximo 10 dígitos de límite');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('codigo', codigo);
            formData.append('nombre', nombre);
            formData.append('precio', precio);
            formData.append('unidad', unidad);
            formData.append('clasificacion', clasificacion);

            axios.post(url+'/catalogo/materiales/editar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Actualizado correctamente');
                        $('#modalEditar').modal('hide');
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

        function contarcaracteresIngreso(){
            setTimeout(function(){
                var valor = document.getElementById('nombre-nuevo');
                var cantidad = valor.value.length;
                document.getElementById('res-caracter-nuevo').innerHTML = cantidad + '/300 ';
            },10);
        }

        function contarcaracteresEditar(){
            setTimeout(function(){
                var valor = document.getElementById('nombre-editar');
                var cantidad = valor.value.length;
                document.getElementById('res-caracter-editar').innerHTML = cantidad + '/300 ';
            },10);
        }


    </script>


@endsection

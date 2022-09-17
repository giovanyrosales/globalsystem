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
        <div class="row mb-2">

            <div class="col-sm-6">
                @can('boton.solicitar.material.ing')
                <button type="button" onclick="modalAgregar()" class="btn btn-primary btn-sm">
                    <i class="fas fa-pencil-alt"></i>
                    Nueva Solicitud
                </button>
                @endcan
            </div>


            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Solicitudes</li>
                    <li class="breadcrumb-item active">Listado de Materiales Solicitados</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Solitud por Ingeniería</h3>
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
                    <h4 class="modal-title">Nuevo Material</h4>
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
                                        <input type="text" maxlength="300" class="form-control" id="nombre" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Unidad de Medida</label>
                                        <input type="text" maxlength="100" class="form-control" id="medida" autocomplete="off">
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


    <div class="modal fade" id="modalNuevoMaterial">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nuevo Material</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-editar">
                        <div class="card-body">

                            <div class="form-group">
                                <input type="hidden" id="id-editar">
                            </div>

                            <div class="form-group">
                                <label>Unidad de Medida Solicitada</label>
                                <input type="text" disabled class="form-control" id="medida-editar" autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label>Material Solicitado</label>
                                <input type="text" disabled class="form-control" id="material-solicitado" autocomplete="off">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Obj. Específico:</label>
                                        <select class="form-control" id="select-codigo-nuevo">
                                            <option value="" selected>Seleccione una opción...</option>
                                            @foreach($lObjEspeci as $sel)
                                                <option value="{{ $sel->id }}">{{ $sel->codigo}} - {{ $sel->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Clasificación:</label>
                                        <select class="form-control" id="select-clasi-nuevo">
                                            <option value="" disabled selected>Seleccione una opción...</option>
                                            @foreach($lClasificacion as $sel)
                                                <option value="{{ $sel->id }}">{{ $sel->codigo }} - {{ $sel->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Nombre *:</label>
                                <input type="text" class="form-control" autocomplete="off" onpaste="contarcaracteresIngreso();" onkeyup="contarcaracteresIngreso();" maxlength="300" id="nombre-nuevo" placeholder="Nombre del material">
                                <div id="res-caracter-nuevo" style="float: right">0/300</div>
                            </div>

                            <br>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Unidad de Medida:</label>
                                        <br>
                                        <select width="60%"  class="form-control" id="select-unidad-nuevo">
                                            <option value="" selected>Seleccione una opción...</option>
                                            @foreach($lUnidad as $sel)
                                                <option value="{{ $sel->id }}">{{ $sel->medida }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Precio Unitario *:</label>
                                        <input type="number" class="form-control" autocomplete="off" id="precio-nuevo" maxlength="10" placeholder="0.00">
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
            var ruta = "{{ URL::to('/admin/solicitud/material/ing/tabla') }}";
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";


            $('#select-codigo-nuevo').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            $('#select-clasi-nuevo').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

            $('#select-unidad-nuevo').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Busqueda no encontrada";
                    }
                },
            });

        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/solicitud/material/ing/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function nuevo(){
            var nombre = document.getElementById('nombre').value;
            var medida = document.getElementById('medida').value;

            if(nombre === ''){
                toastr.error('Nombre es requerido');
                return;
            }

            if(nombre.length > 300){
                toastr.error('Nombre máximo 300 caracteres');
                return;
            }

            if(medida === ''){
                toastr.error('Medida es requerido');
                return;
            }

            if(medida.length > 100){
                toastr.error('Medida máximo 100 caracteres');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('medida', medida);

            axios.post(url+'/solicitud/material/ing/nuevo', formData, {
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

        function informacionBorrar(id){

            Swal.fire({
                title: 'Borrar Solicitud?',
                text: "",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                   borrarSolicitud(id);
                }
            })
        }

        function informacionSoli(id){
            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post(url+'/solicitud/material/ing/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalNuevoMaterial').modal('show');
                        $('#id-editar').val(id);
                        $('#medida-editar').val(response.data.info.medida);
                        $('#material-solicitado').val(response.data.info.nombre);

                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }

        function borrarSolicitud(id){
            openLoading();

            axios.post(url+'/solicitud/material/ing/borrar',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Solicitud borrada');
                        recargar();
                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
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
                    nuevoMaterial();
                }
            })
        }

        function nuevoMaterial(){

            // id de material solicitado para borrarlo cuando se agregue al catalogo
            var id = document.getElementById('id-editar').value;
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
                toastr.error('Precio unitario es requerido');
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
            formData.append('objespecifico', codigo);
            formData.append('nombre', nombre);
            formData.append('precio', precio);
            formData.append('unidad', unidad);
            formData.append('clasificacion', clasificacion);
            formData.append('id', id);

            axios.post(url+'/solicitud/material/ing/agregar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Registrado correctamente');
                        $('#modalNuevoMaterial').modal('hide');
                        recargar();
                    }
                    else if(response.data.success === 3){
                        Swal.fire({
                            title: 'Material Repetido',
                            text: "El objeto específico, la clasificación, el nombre, y la unidad de medida están repetidos. Revisar Catálogo",
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
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

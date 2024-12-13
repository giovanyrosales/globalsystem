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
                    <li class="breadcrumb-item">Despacho</li>
                    <li class="breadcrumb-item active">Listado</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-blue">
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

    <div class="modal fade" id="modalAgregar" >
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Información</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo">
                        <div class="card-body">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Fecha</label>
                                    <input type="date" name="content" id="fecha-nuevo" value="{{ $fecha }}" class="form-control" maxlength="300">
                                </div>

                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" name="content" id="nombre-nuevo" class="form-control" maxlength="300">
                                </div>

                                <div class="form-group">
                                    <label>DUI</label>
                                    <input type="text" name="content" id="dui-nuevo" class="form-control" maxlength="12">
                                </div>

                                <div class="form-group">
                                    <label>Teléfono</label>
                                    <input type="text" name="content" id="telefono-nuevo" class="form-control" maxlength="300">
                                </div>

                                <div class="form-group">
                                    <label>Dirección</label>
                                    <input type="text" name="content" id="direccion-nuevo" class="form-control" maxlength="300">
                                </div>
                                <div class="form-group">
                                    <label>Tipo de Solicitud:</label>
                                    <div>
                                        <select class="form-control " id="tiposolicitud-nuevo">
                                            <option value="0">Seleccione una opción...</option>
                                            <option value="1">Vivienda Completa</option>
                                            <option value="2">Solo Vivienda</option>
                                            <option value="3">Materiales de Construcción</option>
                                            <option value="4">Viveres</option>
                                            <option value="5">Construcción</option>
                                            <option value="6">Proyecto</option>
                                            <option value="7">Afectaciones de la Vista</option>
                                            <option value="8">Otros</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" style="margin-top: 10px">
                                    <label>Solicitud</label>
                                    <textarea name="content" id="editor-nuevo" rows="12" cols="50"></textarea>
                                </div>



                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" onclick="AgregarNuevoRegistro()">Guardar</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalEditar" >
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Información (Ver)</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-editar">
                        <div class="card-body">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <input type="hidden" id="id-editar">
                                </div>

                                <div class="form-group">
                                    <label>Fecha</label>
                                    <input type="date" name="content" id="fecha-editar" class="form-control" maxlength="300">
                                </div>

                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" name="content" id="nombre-editar" class="form-control" maxlength="300">
                                </div>
                                <div class="form-group">
                                    <label>DUI</label>
                                    <input type="text" name="content" id="dui-editar" class="form-control" maxlength="12">
                                </div>

                                <div class="form-group">
                                    <label>Teléfono</label>
                                    <input type="text" name="content" id="telefono-editar" class="form-control" maxlength="300">
                                </div>

                                <div class="form-group">
                                    <label>Dirección</label>
                                    <input type="text" name="content" id="direccion-editar" class="form-control" maxlength="300">
                                </div>
                                <div class="form-group">
                                    <label>Tipo de Solicitud:</label>
                                    <div>
                                        <select class="form-control " id="tiposolicitud-editar">
                                            <option value="0">Seleccione una opción...</option>
                                            <option value="1">Vivienda Completa</option>
                                            <option value="2">Solo Vivienda</option>
                                            <option value="3">Materiales de Construcción</option>
                                            <option value="4">Viveres</option>
                                            <option value="5">Construcción</option>
                                            <option value="6">Proyecto</option>
                                            <option value="7">Afectaciones de la Vista</option>
                                            <option value="8">Otros</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" style="margin-top: 10px">
                                    <label>Solicitud</label>
                                    <textarea name="content" id="editor-editar" rows="12" cols="50"></textarea>
                                </div>



                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" onclick="editarRegistro()">Actualizar</button>
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
    <script src="{{ asset('plugins/ckeditor5v1/build/ckeditor.js') }}"></script>


    <script type="text/javascript">
        $(document).ready(function(){

            window.varGlobalEditorNuevo;
            window.varGlobalEditorEditar;


            ClassicEditor
                .create(document.querySelector('#editor-nuevo'), {
                    language: 'es',
                })
                .then(editor => {
                    varGlobalEditorNuevo = editor;
                })
                .catch(error => {

                });


            ClassicEditor
                .create(document.querySelector('#editor-editar'), {
                    language: 'es',
                })
                .then(editor => {
                    varGlobalEditorEditar = editor;
                })
                .catch(error => {

                });

            var ruta = "{{ URL::to('/admin/secretaria/despacho/tabla') }}";
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";

        });
    </script>

    <script>

        function recargar(){
            var ruta = "{{ url('/admin/secretaria/despacho/tabla') }}";
            $('#tablaDatatable').load(ruta);
        }

        function modalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
            $('#editor-nuevo').val('');
        }


        function AgregarNuevoRegistro(){

            var fecha = document.getElementById('fecha-nuevo').value;
            var nombre = document.getElementById('nombre-nuevo').value;
            var dui = document.getElementById('dui-nuevo').value;
            var telefono = document.getElementById('telefono-nuevo').value;
            var direccion = document.getElementById('direccion-nuevo').value;
            var tiposolicitud = document.getElementById('tiposolicitud-nuevo').value;
            if(fecha === ''){
                toastr.error('Fecha es requerido');
                return;
            }

            if(nombre === ''){
                toastr.error('Nombre es requerido');
                return;
            }

            if(tiposolicitud == '0'){
                toastr.error('Seleccionar Tipo de Solicitud');
                return;
            }

            const editorNuevo = varGlobalEditorNuevo.getData();

            openLoading();
            var formData = new FormData();
            formData.append('fecha', fecha);
            formData.append('nombre', nombre);
            formData.append('dui', dui);
            formData.append('telefono', telefono);
            formData.append('direccion', direccion);
            formData.append('editor', editorNuevo);
            formData.append('tiposolicitud', tiposolicitud);

            axios.post(url+'/secretaria/despacho/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        toastr.success('Guardado correctamente');
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


        function modalBorrar(id){

            Swal.fire({
                title: 'Borrar?',
                text: "",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    solicitudBorrar(id);
                }
            })
        }

        function solicitudBorrar(id){

            openLoading();

            axios.post(url+'/secretaria/despacho/borrar',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                       toastr.success('Borrado');
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


        function informacion(id){
            openLoading();
            document.getElementById("formulario-editar").reset();

            axios.post(url+'/secretaria/despacho/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditar').modal('show');
                        $('#id-editar').val(id);

                        $('#fecha-editar').val(response.data.info.fecha);
                        $('#nombre-editar').val(response.data.info.nombre);
                        $('#dui-editar').val(response.data.info.dui);
                        $('#telefono-editar').val(response.data.info.telefono);
                        $('#direccion-editar').val(response.data.info.direccion);

                        if(response.data.info.tiposolicitud == 1) {
                            moverSelect(1)
                        }else if (response.data.info.tiposolicitud == 2){
                            moverSelect(2)
                        }
                        else if (response.data.info.tiposolicitud == 3){
                            moverSelect(3)
                        }
                        else if (response.data.info.tiposolicitud == 4){
                            moverSelect(4)
                        }
                        else if (response.data.info.tiposolicitud == 5){
                            moverSelect(5)
                        }
                        else if (response.data.info.tiposolicitud == 6){
                            moverSelect(6)
                        }
                        else if (response.data.info.tiposolicitud == 7){
                            moverSelect(7)
                        }
                        else if (response.data.info.tiposolicitud == 8){
                            moverSelect(8)
                        }
                        else{
                            moverSelect(0)
                        }
                        varGlobalEditorEditar.setData(response.data.info.descripcion);

                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }


        function moverSelect(posicion){
            $('#tiposolicitud-editar').prop('selectedIndex', posicion).change();
        }


        function editarRegistro(){
            var id = document.getElementById('id-editar').value;
            var fecha = document.getElementById('fecha-editar').value;
            var nombre = document.getElementById('nombre-editar').value;
            var dui = document.getElementById('dui-editar').value;
            var telefono = document.getElementById('telefono-editar').value;
            var direccion = document.getElementById('direccion-editar').value;
            var tiposolicitud = document.getElementById('tiposolicitud-editar').value;

            if(fecha === ''){
                toastr.error('Fecha es requerido');
                return;
            }

            if(nombre === ''){
                toastr.error('Nombre es requerido');
                return;
            }

            const editorNuevoE = varGlobalEditorEditar.getData();

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('fecha', fecha);
            formData.append('nombre', nombre);
            formData.append('dui', dui);
            formData.append('telefono', telefono);
            formData.append('direccion', direccion);
            formData.append('editor', editorNuevoE);
            formData.append('tiposolicitud', tiposolicitud);

            axios.post(url+'/secretaria/despacho/editar', formData, {
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


    </script>


@endsection

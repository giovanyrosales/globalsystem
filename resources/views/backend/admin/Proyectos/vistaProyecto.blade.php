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
            <div class="col-sm-6" style="margin-right: 10px;">
                <h1>Control Individual de Proyecto</h1>
            </div>
            <div class="col-sm-2">
                <button type="button" onclick="modalAgregar()" class="btn btn-success btn-sm">
                    <i class="fas fa-pencil-alt"></i>
                    Requisición
                </button>
            </div>
        </div>
    </section>

    <!------------------ INFORMACIÓN DE UN PROYECTO ESPECIFICO ---------------->
    <section class="content">
        <div class="row">
            <div class="col-sm-6 float-left">
                <div class="container-fluid">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title" style="color: white">Datos del Proyecto</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-10">
                                    <table>
                                        <tr>
                                            <td style="font-weight: bold">Código: </td>
                                            <td>{{ $proyecto->codigo }}</td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold">Nombre: </td>
                                            <td>{{ $proyecto->nombre }}</td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold">Dirección: </td>
                                            <td>{{ $proyecto->ubicacion }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!------------------ PRESUPUESTO ---------------->
            <div class="col-sm-6 float-right">
                <div class="container-fluid">
                    <div class="card card-danger">
                        <div class="card-header">
                            <h3 class="card-title">Requerimientos de Proyecto</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <table id="example1" class="table table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th style="width: 10%;">Num.</th>
                                            <th style="width: 15%;">Fecha</th>
                                            <th style="width: 40%;">Opciones</th>
                                        </tr>
                                        </thead>
                                        <tbody>


                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!------------------ BITACORA ---------------->
        <div class="col-sm-6 float-left">
            <div class="container-fluid">
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title" style="margin-top: 5px">BITACORA</h3>
                        <button style="margin-left: 15px; margin-bottom: 10px" type="button" onclick="modalAgregarBitacora()" class="btn btn-success btn-sm">
                            <i class="fas fa-pencil-alt"></i>
                            Agregar Bitacora
                        </button>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="tablaDatatableBitacora">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </section>
</div>

<!-- Modal Agregar Requisicion -->
<div class="modal fade" id="modalAgregar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Agregar Requisicion de Proyecto</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formularion2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha:</label>
                                    <input style="width:50%;" type="date" class="form-control" id="fechan" name="fechan">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Item:</label>
                                    <input  type="text" class="form-control" id="itemn" name="itemn" value="5555" readonly>
                                    <input type="hidden" class="form-control" id="proyecto_idn" name="proyecto_idn" value="{{ $proyecto->id }}">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Destino:</label>
                                    <input  type="text" class="form-control" id="destinon" name="destinon">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label>Necesidad:</label>
                                    <textarea class="form-control" id="necesidadn" name="necesidadn" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <br>
                                    <button type="button" onclick="abrirModalAgregarDet_Req(1)" class="btn btn-primary btn-sm float-right" style="margin-top:10px;">
                                        <i class="fas fa-plus" title="Add"></i>&nbsp; Agregar</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <table  class="table" id="matriz"  data-toggle="table">
                                <thead>
                                <tr>
                                    <th scope="col">Cantidad</th>
                                    <th scope="col">Descripci&oacute;n</th>
                                    <th scope="col">Unidad Medida</th>
                                    <th scope="col">Opciones</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarU" onclick="enviarModalAgregarReq()">Guardar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Editar Requisicion -->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Requisicion</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formularioU2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha:</label>
                                    <input style="width:50%;" type="date" class="form-control" id="fechap" name="fechap">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Num. Req.:</label>
                                    <input  type="text" class="form-control" id="nump" name="nump" readonly>
                                    <input type="hidden" class="form-control" id="idU2" name="idU2">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Destino:</label>
                                    <input style="width:50%;" type="text" class="form-control" id="destinop" name="destinop">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label>Necesidad:</label>
                                    <textarea class="form-control" id="necesidadp" name="necesidadp" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <br><br>
                                    <button type="button" onclick="abrirModalAgregarDet_Req(2)" class="btn btn-primary btn-xs float-right">
                                        <i class="fas fa-plus" title="Add"></i>&nbsp; Agregar</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <table  class="table" id="matrizpar"  data-toggle="table">
                                <thead>
                                <tr>
                                    <th scope="col">Cantidad</th>
                                    <th scope="col">Descripci&oacute;n</th>
                                    <th scope="col">Unidad Medida</th>
                                    <th scope="col">Opciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarU" onclick="enviarModalEditarReq()">Guardar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal agregar detalle de Req -->
<div class="modal fade" id="modalAgregarReq" style="margin-top:3%;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Agregar Detalle de Requisicion</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formularion3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Cantidad:</label>
                                    <input type="number" step="any" class="form-control" id="mcantidad" name="mcantidad">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Unidad de Medida:</label>
                                    <select class="form-control" id="munidadmedida" name="munidadmedida">
                                        <option value="" >Seleccione una opción</option>
                                        <option value="unidad" >C/U</option>
                                        <option value="mts." >mts.</option>
                                        <option value="mts." >plg</option>
                                        <option value="mts." >gal</option>
                                        <option value="mts." >m2</option>
                                        <option value="mts." >m3</option>
                                        <option value="mts." >yds</option>
                                        <option value="mts." >lb</option>
                                        <option value="mts." >Oz</option>
                                        <option value="mts." >Kg</option>
                                        <option value="mts." >cm3</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Descripci&oacute;n:</label>
                                    <input type="text" class="form-control" id="mdescripcion" name="mdescripcion" >
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="add" value="" >Agregar</button>
            </div>
        </div>
    </div>
</div>



<!-- modal agregar bitacora -->
<div class="modal fade" id="modalAgregarBitacora">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nueva Bitacora</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-bitacora-nuevo">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Observaciones</label>
                                    <textarea type="text" maxlength="10000" rows="4" cols="50" class="form-control" id="descripcion-bitacora-nuevo"></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Fecha *</label>
                                    <input type="date" class="form-control" id="fecha-bitacora-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Documento</label>
                                    <input type="file" id="documento-bitacora" class="form-control" accept="image/jpeg, image/jpg, image/png"/>
                                </div>

                                <div class="form-group">
                                    <label>Nombre para Documento</label>
                                    <input type="text" maxlength="300" class="form-control" id="nombre-bitacora-doc-nuevo">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="guardarBitacora()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- modal editar bitacora -->
<div class="modal fade" id="modalEditarBitacora">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Bitacora</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-bitacora-editar">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Observaciones</label>
                                    <input type="hidden" id="id-bitacora-editar">
                                    <textarea type="text" maxlength="10000" rows="4" cols="50" class="form-control" id="descripcion-bitacora-editar"></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Fecha *</label>
                                    <input type="date" class="form-control" id="fecha-bitacora-editar">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editarBitacora()">Actualizar</button>
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
            document.getElementById("divcontenedor").style.display = "block";

            var id = {{ $id }};
            var ruta = "{{ URL::to('/admin/proyecto/vista/bitacora') }}/" + id;
            $('#tablaDatatableBitacora').load(ruta);

        });
    </script>

    <script>

        function recargarBitacora(){
            var id = {{ $id }};
            var ruta = "{{ URL::to('/admin/proyecto/vista/bitacora') }}/" + id;
            $('#tablaDatatableBitacora').load(ruta);
        }

        function modalAgregarBitacora(){
            document.getElementById("formulario-bitacora-nuevo").reset();

            var fecha = new Date();
            document.getElementById('fecha-bitacora-nuevo').value = fecha.toJSON().slice(0,10);

            $('#modalAgregarBitacora').modal('show');
        }

        function guardarBitacora(){

            var fecha = document.getElementById('fecha-bitacora-nuevo').value;
            var observaciones = document.getElementById('descripcion-bitacora-nuevo').value;
            var documento = document.getElementById('documento-bitacora'); // null file
            var nombreDocumento = document.getElementById('nombre-bitacora-doc-nuevo').value;

            if(fecha === ''){
                toastr.error('Fecha para Bitacora es requerida');
                return;
            }

            if(observaciones.length > 10000){
                toastr.error('Descripción máximo 10,000 caracteres');
                return;
            }

            if(documento.files && documento.files[0]){ // si trae doc
                if (!documento.files[0].type.match('image/jpeg|image/jpeg|image/png')){
                    toastr.error('formato para Documento permitido: .png .jpg .jpeg');
                    return;
                }
            }

            if(nombreDocumento.length > 300){
                toastr.error('Nombre para Documento máximo 300 caracteres');
                return;
            }

            // id del proyecto
            var id = {{ $id }};

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('fecha', fecha);
            formData.append('observaciones', observaciones);
            formData.append('documento', documento.files[0]);
            formData.append('nombredocumento', nombreDocumento);

            axios.post(url+'/proyecto/vista/bitacora/registrar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        $('#modalAgregarBitacora').modal('hide');
                        recargarBitacora();
                        toastr.success('Agregado correctamente');
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

        function preguntaBorrarBitacora(id){
            Swal.fire({
                title: 'Borrar Bitacora',
                text: "Se eliminaran los registros",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    borrarBitacora(id);
                }
            })
        }

        function borrarBitacora(id){
            openLoading();

            axios.post(url+'/proyecto/vista/bitacora/borrar', {
                'id': id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        recargarBitacora();
                        toastr.success('Borrado correctamente');
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

        function vistaEditarBitacora(id){

            openLoading();
            document.getElementById("formulario-bitacora-editar").reset();

            axios.post(url+'/proyecto/vista/bitacora/informacion',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalEditarBitacora').modal('show');
                        $('#id-bitacora-editar').val(response.data.bitacora.id);
                        $('#descripcion-bitacora-editar').val(response.data.bitacora.observaciones);
                        $('#fecha-bitacora-editar').val(response.data.bitacora.fecha);

                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }

        function editarBitacora(){
            var id = document.getElementById('id-bitacora-editar').value;
            var fecha = document.getElementById('fecha-bitacora-editar').value;
            var observaciones = document.getElementById('descripcion-bitacora-editar').value;

            if(fecha === ''){
                toastr.error('Fecha para Bitacora es requerida');
                return;
            }

            if(observaciones.length > 10000){
                toastr.error('Descripción máximo 10,000 caracteres');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('fecha', fecha);
            formData.append('observaciones', observaciones);

            axios.post(url+'/proyecto/vista/bitacora/editar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        $('#modalEditarBitacora').modal('hide');
                        recargarBitacora();
                        toastr.success('Actualizado correctamente');
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

        // vista para bitacora detalle
        function vistaBitacora(id){
            window.location.href="{{ url('/admin/proyecto/vista/bitacora-detalle') }}/" + id;
        }


    </script>


@endsection

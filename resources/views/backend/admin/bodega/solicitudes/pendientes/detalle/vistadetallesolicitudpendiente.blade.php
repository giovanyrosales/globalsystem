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

            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Bodega</li>
                    <li class="breadcrumb-item active">Mis Solicitudes - Detalle</li>
                </ol>
            </div>
        </div>
    </section>

    @if($arraySinReferencia->isNotEmpty())

    <section class="content">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">Pendientes de Referencia</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">

                            <table id="tabla" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th style="width: 8%">Nombre</th>
                                    <th style="width: 3%">Prioridad</th>
                                    <th style="width: 2%">U/M</th>
                                    <th style="width: 3%">Estado</th>
                                    <th style="width: 2%">Opciones</th>
                                </tr>
                                </thead>
                                <tbody>

                            @foreach($arraySinReferencia as $fila)
                                <tr>
                                    <td style="width: 3%">{{ $fila->nombre }}</td>
                                    <td style="width: 3%">{{ $fila->nombrePrioridad }}</td>
                                    <td style="width: 2%">{{ $fila->unidadMedida }}</td>
                                    <td style="width: 2%">
                                        @if($fila->estado == 1)
                                            <span class="badge bg-gray-dark">{{ $fila->nombreEstado }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ $fila->nombreEstado }}</span>
                                        @endif
                                    </td>

                                    <td style="width: 2%">
                                        @if($fila->estado == 1)
                                        <button type="button" class="btn btn-success btn-xs"
                                                onclick="vistaAsignar({{ $fila->id }})">
                                            <i class="fas fa-plus" title="Referencia"></i>&nbsp; Referencia
                                        </button>
                                        @endif

                                        @if($fila->estado == 1)
                                            <button type="button" class="btn btn-danger btn-xs"
                                                    onclick="vistaModalDenegar({{ $fila->id }})">
                                                <i class="fas fa-edit" title="Denegar"></i>&nbsp; Denegar
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-info btn-xs"
                                                    onclick="vistaModalPendiente({{ $fila->id }})">
                                                <i class="fas fa-edit" title="Pendiente"></i>&nbsp; Pendiente
                                            </button>
                                        @endif

                                    </td>

                                </tr>
                            @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @endif

    @if($arrayReferencia->isNotEmpty())

    <section class="content">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">Materiales con Referencia</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">

                            <table id="tabla" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th style="width: 2%">#</th>
                                    <th style="width: 7%">Solicitado</th>
                                    <th style="width: 7%">Referencia</th>
                                    <th style="width: 2%">U/M</th>
                                    <th style="width: 2%">Cantidad Solicitado</th>
                                    <th style="width: 3%">Estado</th>
                                    <th style="width: 2%">Cantidad Entregada</th>
                                    <th style="width: 4%">Opciones</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($arrayReferencia as $fila)
                                    <tr>
                                        <td style="width: 2%">{{ $fila->numeralFila }}</td>
                                        <td style="width: 7%">{{ $fila->nombre }}</td>
                                        <td style="width: 7%">{{ $fila->nombreReferencia }}</td>
                                        <td style="width: 2%">{{ $fila->unidadMedida }}</td>
                                        <td style="width: 2%">{{ $fila->cantidad }}</td>
                                        <td style="width: 3%">
                                            @if($fila->estado == 1)
                                                <span class="badge bg-gray-dark">{{ $fila->nombreEstado }}</span>
                                            @elseif($fila->estado == 2)
                                                <span class="badge bg-success">{{ $fila->nombreEstado }}</span>
                                            @elseif($fila->estado == 3)
                                                <span class="badge bg-warning">{{ $fila->nombreEstado }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ $fila->nombreEstado }}</span>
                                            @endif
                                        </td>
                                        <td style="width: 2%">{{ $fila->cantidad_entregada }}</td>
                                        <td style="width: 4%">
                                            <button type="button" class="btn btn-success btn-xs"
                                                    onclick="vistaAgregarMaterial({{ $fila->id }})">
                                                <i class="fas fa-plus" title="Agregar"></i>&nbsp; Agregar
                                            </button>

                                            <button type="button" class="btn btn-info btn-xs" onclick="vistaCambiarEstado({{ $fila->id }})" style="color: white; margin: 3px">
                                                <i class="fas fa-eye" title="Estado" style="color: white;"></i>&nbsp; Estado
                                            </button>

                                            @if($fila->existeSalida == 0)
                                                <button type="button" class="btn btn-warning btn-xs" onclick="vistaCambiarReferencia({{ $fila->id }})" style="color: black; margin: 3px">
                                                    <i class="fas fa-edit" title="Referencia" style="color: black;"></i>&nbsp; Referencia
                                                </button>
                                            @else
                                                <button type="button" style="margin: 3px" class="btn btn-success btn-xs"
                                                        onclick="vistaPDF({{ $fila->id }})">
                                                    <i class="fas fa-file-pdf" title="PDF"></i>&nbsp; PDF
                                                </button>
                                            @endif


                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @endif



    <div class="modal fade" id="modalAgregar">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Asignación</h4>
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
                                        <input type="hidden" disabled class="form-control" id="id-referencia" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input type="text" disabled class="form-control" id="nombre-nuevo" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Unidad de Medida</label>
                                        <input type="text" disabled class="form-control" id="unidad-nuevo" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Asignar Material</label>
                                        <select class="form-control" id="select-materiallote">
                                            @foreach($arrayMateriales as $fila)
                                                <option value="{{ $fila->id }}">{{ $fila->nombre }}</option>
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
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small" onclick="nuevaAsignacion()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEstado">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Asignación de Estado</h4>
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
                                        <input type="hidden" disabled class="form-control" id="id-refestado" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Estado</label>
                                        <select class="form-control" id="select-estadoref" >
                                            <option value="1">Pendiente</option>
                                            <option value="2">Entregado</option>
                                            <option value="3">Entregado/Parcial</option>
                                            <option value="4">Denegado</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Nota</label>
                                        <input type="text" maxlength="300" class="form-control" id="nota-estado" autocomplete="off">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small" onclick="cambiarEstadoFila()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCantidad">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Salida de Material</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-material">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <input type="hidden" disabled class="form-control" id="id-salidamaterial" autocomplete="off">
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label>Fecha de Salida</label>
                                        <input type="date" class="form-control" id="info-fechasalida" autocomplete="off">
                                    </div>

                                    <div class="row">
                                        <div class="form-group">
                                            <label>Cantidad Solicitada</label>
                                            <input type="text" disabled class="form-control" id="info-cantidadsolicitada" autocomplete="off">
                                        </div>

                                        <div class="form-group" style="margin-left: 15px">
                                            <label>Cantidad Entregada</label>
                                            <input type="text" disabled class="form-control" id="info-cantidadentregada" autocomplete="off">
                                        </div>

                                        <div class="form-group" style="margin-left: 15px">
                                            <label>Unidad Medida</label>
                                            <input type="text" disabled class="form-control" id="info-unidadmedida" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Producto</label>
                                        <input type="text" disabled class="form-control" id="info-nombrematerial" autocomplete="off">
                                    </div>

                                    <hr>

                                    <!-- ** TABLA PARA SALIDAS DE MATERIAL POR DIFERENTE LOTE ** -->
                                    <!-- ** CARGARA LOS MATERIALES ASIGNADOS Y DISPONIBLES, MAYOR A CERO ** -->

                                    <table class="table" id="matriz" data-toggle="table" style="margin-right: 15px; margin-left: 15px;">
                                        <thead>
                                        <tr>
                                            <th style="width: 3%">#</th>
                                            <th style="width: 5%">Lote</th>
                                            <th style="width: 5%">Cantidad Actual</th>
                                            <th style="width: 5%">Cantidad Salida</th>

                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>


                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-rounded button-pill button-small" onclick="guardarNuevaSalida()">Guardar Salida</button>
                </div>
            </div>
        </div>
    </div>


    <!-- CUANDO UN ITEM ESTA EQUIVOCADO SE PUEDE DENEGAR -->

    <div class="modal fade" id="modalDenegar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Denegar</h4>
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
                                        <input type="hidden" disabled class="form-control" id="id-itemdenegar" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label>Nota</label>
                                        <input type="text" maxlength="300" class="form-control" id="nota-denegar-v1" autocomplete="off">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white
                     !important;" class="button button-rounded button-pill button-small" onclick="vistaModalDenegarGuardar()">Guardar</button>
                </div>
            </div>
        </div>
    </div>



    <!-- SI SE EQUIVOCA AL COLOCAR UNA REFERENCIA DE MATERIAL : SOLO PUEDE HACERLO SINO HA ENTREGADO NADA -->

    <div class="modal fade" id="modalCambioReferencia">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Cambiar Referencia Material</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-cambioreferencia">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <label style="color: red">Solo podrá cambiar la referencia mientras no ha tenido ninguna salida de Producto</label>

                                    <div class="form-group">
                                        <input type="hidden" disabled class="form-control" id="id-cambioreferencia" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                    <label>Asignar Referencia</label>
                                        <select class="form-control" id="select-nuevareferencia">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white
                     !important;" class="button button-rounded button-pill button-small" onclick="guardarNuevaReferencia()">Guardar</button>
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

            $('#select-materiallote').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });

            $('#select-nuevareferencia').select2({
                theme: "bootstrap-5",
                "language": {
                    "noResults": function(){
                        return "Búsqueda no encontrada";
                    }
                },
            });

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>


        function vistaAsignar(id){
            openLoading();
            document.getElementById("formulario-nuevo").reset();

            axios.post(url+'/bodega/solicitudpendiente/infobodesolituddetalle',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalAgregar').modal('show');
                        $('#id-referencia').val(id);

                        $('#nombre-nuevo').val(response.data.info.nombre);
                        $('#unidad-nuevo').val(response.data.nombreUnidad);
                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }

        function nuevaAsignacion(){

            // id: bodega_solicitud_detalle
            var id = document.getElementById('id-referencia').value;
            var idMaterial = document.getElementById('select-materiallote').value;

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('idmaterial', idMaterial);

            axios.post(url+'/bodega/solicitudpendiente/asignar/referencia', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        // error, ya tenia referencia
                        errorReferencia()
                    }
                    else if(response.data.success === 2){
                        toastr.success('Actualizado correctamente');
                        location.reload();
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

        function errorReferencia(){
            Swal.fire({
                title: 'Error',
                text: "La solicitud de material ya tenia un material asignado.",
                icon: 'info',
                showCancelButton: false,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Recargar'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            })
        }

        function vistaCambiarEstado(id){

            openLoading()

            var formData = new FormData();
            formData.append('id', id);

            document.getElementById('nota-estado').value = "";

            axios.post(url+'/bodega/solicitudpendiente/infobodesolituddetalle', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        $('#id-refestado').val(id);
                        $('#nota-estado').val(response.data.info.nota);

                        const selectElement = document.getElementById('select-estadoref');
                        if(response.data.info.estado === 1){
                            selectElement.selectedIndex = 0;
                        }else if(response.data.info.estado === 2){
                            selectElement.selectedIndex = 1;
                        }else if(response.data.info.estado === 3){
                            selectElement.selectedIndex = 2;
                        }else{
                            // denegado
                            selectElement.selectedIndex = 3;
                        }

                        $('#modalEstado').modal('show');
                    }
                    else {
                        toastr.error('Error');
                    }
                })
                .catch((error) => {
                    toastr.error('Error');
                    closeLoading();
                });
        }


        function cambiarEstadoFila(){

            var id = document.getElementById('id-refestado').value;
            var idestado = document.getElementById('select-estadoref').value;
            var nota = document.getElementById('nota-estado').value;

            openLoading()

            var formData = new FormData();
            formData.append('id', id);
            formData.append('idestado', idestado);
            formData.append('nota', nota);

            axios.post(url+'/bodega/solicitudpendiente/modificar/estadofila', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        location.reload();
                    }
                    else {
                        toastr.error('Error');
                    }
                })
                .catch((error) => {
                    toastr.error('Error');
                    closeLoading();
                });
        }

        // agregar material directamente por fila
        function vistaAgregarMaterial(id){
            openLoading()

            var formData = new FormData();
            formData.append('id', id); // bodega_solicitud_detalle
            $("#matriz tbody tr").remove();

            axios.post(url+'/bodega/solicitudpendiente/infomaterialsalidalote', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        $('#id-salidamaterial').val(id);
                        $('#info-cantidadsolicitada').val(response.data.info.cantidad);
                        $('#info-cantidadentregada').val(response.data.info.cantidad_entregada);
                        $('#info-nombrematerial').val(response.data.nombreMaterial);
                        $('#info-unidadmedida').val(response.data.nombreUnidad);
                        $('#modalCantidad').modal({backdrop: 'static', keyboard: false})

                        $.each(response.data.arrayIngreso, function( key, val ){

                            var nFilas = $('#matriz >tbody >tr').length;
                            nFilas += 1;

                            var markup = "<tr>" +

                                "<td>" +
                                "<p id='fila" + (nFilas) + "' class='form-control' style='max-width: 65px'>" + (nFilas) + "</p>" +
                                "</td>" +

                                "<td>" +
                                "<input disabled value='" + val.lote + "' class='form-control' type='text'>" +
                                "</td>" +

                                "<td>" +
                                "<input name='arrayCantidadActual[]' disabled data-cantidadActualFila='" + val.cantidadActual + "'  value='" + val.cantidadActual + "' class='form-control' type='number'>" +
                                "</td>" +

                                "<td>" +
                                "<input " +
                                "class='form-control' data-idfilaentradadetalle='" + val.id + "' name='arrayCantidadSalida[]' min='0' max='" + val.cantidad + "' " +
                                "type='number' " +
                                "onkeydown=\"return validateInput(event);\" " +
                                "oninput=\"validateCantidadSalida(this, " + val.cantidad + ");\">" +
                                "</td>" +

                                "</tr>";

                            $("#matriz tbody").append(markup);

                        });
                    }
                    else {
                        toastr.error('Error');
                    }
                })
                .catch((error) => {
                    toastr.error('Error');
                    closeLoading();
                });
        }

        function validateCantidadSalida(input, maxCantidad) {
            // Remueve caracteres no numéricos
            input.value = input.value.replace(/[^0-9]/g, '');

            // Convierte el valor a número y verifica el límite
            if (Number(input.value) > maxCantidad) {
                input.value = maxCantidad; // Restringe el valor al máximo permitido
            }
        }

        function validateInput(event) {
            const key = event.key;

            // Permitir teclas de navegación y control
            if (["Backspace", "ArrowLeft", "ArrowRight", "Delete", "Tab"].includes(key)) {
                return true;
            }

            // Bloquear la tecla "e", signos negativos y todos excepto números
            if (key === "e" || key === "E" || key === "-" || isNaN(Number(key))) {
                return false;
            }

            return true;
        }


        function guardarNuevaSalida(){
            Swal.fire({
                title: 'Guardar Salida?',
                text: "",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    guardarNuevaSalidaFinal();
                }
            })
        }

        function guardarNuevaSalidaFinal(){
            var fecha = document.getElementById('info-fechasalida').value;
            var infoCantidadSolicitada = document.getElementById('info-cantidadsolicitada').value;
            var infoCantidadEntregada = document.getElementById('info-cantidadentregada').value;
            let idBodeSolicitudDetalle = document.getElementById('id-salidamaterial').value;

            if(fecha === ''){
                toastr.error('Fecha de Salida es requerida');
                return
            }

            if(idBodeSolicitudDetalle === ''){
                toastr.error('ID bodega solicitud detalle es requerido');
                return
            }

            // id
            var arrayIdEntradaDetalle = $("input[name='arrayCantidadSalida[]']").map(function(){return $(this).attr("data-idfilaentradadetalle");}).get();
            // cantidad salida
            var arrayCantidadSalida = $("input[name='arrayCantidadSalida[]']").map(function(){return $(this).val();}).get();
            // cantidad actual de cada fila
            var arrayCantidadActual = $("input[name='arrayCantidadActual[]']").map(function(){return $(this).attr("data-cantidadActualFila");}).get();

            colorBlancoTabla()
            var cantidadSalida = 0;
            var habraSalida = true;

            // recorrer y verificar
            for(var a = 0; a < arrayCantidadSalida.length; a++){

                let filaCantidad = arrayCantidadSalida[a];
                let infoFilaCantidadActual = arrayCantidadActual[a];

                if(filaCantidad !== ''){
                    if(filaCantidad <= 0){
                        colorRojoTabla(a);
                        alertaMensaje('info', 'Error', 'En la Fila #' + (a+1) + " No se permite ingreso de Cero, por favor borrarlo");
                        return
                    }
                    habraSalida = false;
                    cantidadSalida += Number(filaCantidad);
                }

                // VERIFICAR QUE NO SUPERE CANTIDAD SALIDA AL CANTIDAD ACTUAL DE CADA FILA DE LA TABLA
                if(filaCantidad > Number(infoFilaCantidadActual)){
                    colorRojoTabla(a);
                    alertaMensaje('info', 'Error', 'En la Fila #' + (a+1) + " La cantidad de Salida supera a la Cantidad Actual de ese LOTE");
                    return
                }
            }

            //
            let sumaSalida = Number(infoCantidadEntregada) + cantidadSalida;



            // comprobar que no supere cantidad
            if(sumaSalida > Number(infoCantidadSolicitada)){
                Swal.fire({
                    title: 'Error',
                    text: "La suma de Cantidad de Salida supera a la Cantidad solicitada con la Cantidad ya Entregada",
                    icon: 'info',
                    showCancelButton: false,
                    allowOutsideClick: false,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Aceptar'
                }).then((result) => {
                    if (result.isConfirmed) {

                    }
                })

                return
            }

            if(habraSalida){
                toastr.error('Registrar mínimo 1 salida');
                return
            }



            let formData = new FormData();
            const contenedorArray = [];

            //** PASO VALIDACIONES
            for(var a = 0; a < arrayCantidadSalida.length; a++){

                let filaCantidadSalida = arrayCantidadSalida[a];
                let infoIdEntradaDetalle = arrayIdEntradaDetalle[a];

                if(filaCantidadSalida !== ''){ // evitar vacios
                    contenedorArray.push({ infoIdEntradaDetalle, filaCantidadSalida });
                }
            }

            formData.append('contenedorArray', JSON.stringify(contenedorArray));
            formData.append('fecha', fecha);
            formData.append('idbodesolidetalle', idBodeSolicitudDetalle); // bodega_solicitud_detalle

            axios.post(url+'/bodega/solicitudpendiente/registrarsalida', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        // cuando va vacio la salida
                        toastr.error('Se requiere item de Salida');
                    }
                    else if(response.data.success === 2){
                        // VERIFICACION: No superar la cantidad maxima que hay de ese MATERIAL - LOTE
                        mensajeError("Error, No superar la cantidad maxima que hay de ese MATERIAL - LOTE")
                    }
                    else if(response.data.success === 3){
                        // VERIFICACION: No superar la cantidad que solicito la UNIDAD
                        mensajeError("Error, No superar la cantidad que solicito la UNIDAD")
                    }
                    else if(response.data.success === 10){
                        msgActualizado()
                    }
                    else{
                        toastr.error('error al guardar');
                    }
                })
                .catch((error) => {
                    toastr.error('error al guardar');
                    closeLoading();
                });
        }

        function mensajeError(mensaje){
            Swal.fire({
                title: 'Error',
                text: mensaje,
                icon: 'info',
                showCancelButton: false,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Recargar'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            })
        }

        function msgActualizado(){
            Swal.fire({
                title: 'Actualizado',
                text: "",
                icon: 'success',
                showCancelButton: false,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            })
        }

        function colorRojoTabla(index){
            $("#matriz tr:eq("+(index+1)+")").css('background', '#F1948A');
        }

        function colorBlancoTabla(){
            $("#matriz tbody tr").css('background', 'white');
        }


        // SOLO PODRA CAMBIAR REFERENCIA A MATERIAL SINO TIENE NINGUNA SALIDA
        // OBTENER INFORMACION DE NUEVO CAMBIO
        function vistaCambiarReferencia(id){

            openLoading();
            document.getElementById("formulario-cambioreferencia").reset();

            axios.post(url+'/bodega/solicitudpendiente/infobodesolituddetalle',{
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalCambioReferencia').modal('show');
                        $('#id-cambioreferencia').val(id);

                        document.getElementById("select-nuevareferencia").options.length = 0;

                        $.each(response.data.arrayMateriales, function( key, val ){
                            if(response.data.info.id_referencia == val.id){
                                $('#select-nuevareferencia').append('<option value="' +val.id +'" selected="selected">'+val.nombre+'</option>');
                            }else{
                                $('#select-nuevareferencia').append('<option value="' +val.id +'">'+val.nombre+'</option>');
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


        // Podra cambiar la referencia de material solamente sino ha entregado nada
        function guardarNuevaReferencia(){

            var idBodeSoliDetalle = document.getElementById('id-cambioreferencia').value; // bodega_solicitud_detalle
            var idReferencia = document.getElementById('select-nuevareferencia').value;

            if(idReferencia === ''){
                toastr.error('Referencia Material no encontrada');
                return;
            }

            var formData = new FormData();
            formData.append('id', idBodeSoliDetalle); // bodega_solicitud_detalle
            formData.append('idReferencia', idReferencia);

            openLoading();
            axios.post(url+'/bodega/referencia/cambionuevoid', formData,{
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        mensajeError("Esta solicitud ya tiene una salida de Producto.");
                    }
                    else if(response.data.success === 2) {
                        msgActualizado()
                    }
                    else{
                        toastr.error('Error al actualizar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error al actualizar');
                });
        }



        function vistaModalDenegar(id){
            $('#id-itemdenegar').val(id); // bodega_solicitud_detalle
            $('#modalDenegar').modal('show');
        }

        function vistaModalDenegarGuardar(){

            var id = document.getElementById('id-itemdenegar').value;  // bodega_solicitud_detalle
            var nota = document.getElementById('nota-denegar-v1').value;

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nota', nota);

            axios.post(url+'/bodega/noreferencia/estadodenegado', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        msgActualizado()
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

        function vistaModalPendiente(id){
            Swal.fire({
                title: 'Cambiar Estado',
                text: "Se pasara a estado Pendiente",
                icon: 'info',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: "Cancelar",
                confirmButtonText: 'Guardar'
            }).then((result) => {
                if (result.isConfirmed) {
                    vistaModalPendienteGuardar(id)
                }
            })
        }

        function vistaModalPendienteGuardar(id){

            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post(url+'/bodega/noreferencia/estadopendiente', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        msgActualizado()
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


        function vistaPDF(id){
            // bodega_solicitud_detalle
            window.open("{{ URL::to('admin/bodega/reporte/encargadobodega/item') }}/" + id);
        }


    </script>


@endsection

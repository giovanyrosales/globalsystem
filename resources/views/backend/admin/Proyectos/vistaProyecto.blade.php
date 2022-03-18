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
        </div>
    </section>

    <!------------------ INFORMACION DE UN PROYECTO ESPECIFICO ---------------->
    <section class="content">
        <div class="col-sm-6 float-left">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Datos del Proyecto</h3>
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
        <!------------------ PRESUPUESTO DEL PROYECTO INDIVIDUAL ---------------->
        <div class="col-sm-6 float-right">
            <div class="container-fluid">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title"><strong>Presupuesto de Proyecto</strong></h3>
                        <button style="margin-left: 15px; float: right; margin-bottom: 10px" type="button" onclick="verModalRequisicion()" class="btn btn-secondary btn-sm">
                            Agregar Requisición
                        </button>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="tablaDatatableRequisicion">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!------------------ CONTROL DE BITACORAS ---------------->
        <div class="col-sm-6 float-left">
            <div class="container-fluid">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title"><strong>Control de Bitácoras</strong></h3>
                        <button style="margin-left: 15px; float: right; margin-bottom: 10px" type="button" onclick="modalAgregarBitacora()" class="btn btn-secondary btn-sm">
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


<div class="modal fade" id="modalAgregarRequisicion" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Agregar Requisición de Proyecto</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form id="formulario-requisicion-nuevo">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha *:</label>
                                    <input style="width:50%;" type="date" class="form-control" id="fecha-requisicion-nuevo">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Item:</label>
                                    <input  type="text" class="form-control" id="conteo-requisicion" value="{{ $conteo }}" readonly>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Destino:</label>
                                    <input  type="text" class="form-control" id="destino-requisicion-nuevo">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label>Necesidad:</label>
                                    <textarea class="form-control" id="necesidad-requisicion-nuevo" maxlength="10000" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <br>
                                    <button type="button" onclick="verModalDetalleRequisicion()" class="btn btn-primary btn-sm float-right" style="margin-top:10px;">
                                        <i class="fas fa-plus" title="Agregar"></i>&nbsp; Agregar</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <table class="table" id="matriz-requisicion"  data-toggle="table">
                                <thead>
                                <tr>
                                    <th style="width: 3%">Cantidad</th>
                                    <th style="width: 15%">Descripción</th>
                                    <th style="width: 5%">Opciones</th>
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
                <button type="button" class="btn btn-primary" onclick="preguntaGuardarRequisicion()">Guardar</button>
            </div>
        </div>
    </div>
</div>




<!-- Modal agregar detalle de Req -->
<div class="modal fade" id="modalAgregarRequisicionDeta" style="margin-top:3%;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Agregar Detalle de Requisición</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-requisicion-deta-nuevo">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Cantidad *:</label>
                                    <input type="number" maxlength="10" class="form-control" id="cantidad-deta-requi-nuevo">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Unidad de Medida *:</label>
                                    <select class="form-control" id="select-unidad-requi-deta-nuevo">
                                        @foreach($unidad as $sel)
                                            <option value="{{ $sel->id }}">{{ $sel->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Descripción *:</label>
                                    <input type="text" maxlength="400" class="form-control" id="descrip-requi-deta-nuevo">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="add">Agregar</button>
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
                                    <label>Nombre para Imagen</label>
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

            var rutaR = "{{ URL::to('/admin/proyecto/vista/requisicion') }}/" + id;
            $('#tablaDatatableRequisicion').load(rutaR);
        });
    </script>

    <script type="text/javascript">

        $(document).ready(function () {
            $("#add").on("click", function () {

                var cantidad = document.getElementById('cantidad-deta-requi-nuevo').value;
                var unidadmedida = document.getElementById('select-unidad-requi-deta-nuevo').value;
                var descripcion = document.getElementById('descrip-requi-deta-nuevo').value;

                var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;
                if(cantidad === ''){
                    toastr.error('Cantidad es requerida');
                    return;
                }

                if(!cantidad.match(reglaNumeroDecimal)) {
                    toastr.error('Cantidad debe ser decimal y no negativo');
                    return;
                }

                if(cantidad <= 0){
                    toastr.error('Cantidad no debe ser negativo');
                    return;
                }

                if(cantidad.length > 10){
                    toastr.error('Cantidad máximo 10 caracteres');
                    return;
                }

                if(descripcion === ''){
                    toastr.error('Descripción es requerida');
                    return;
                }

                if(descripcion.length > 400){
                    toastr.error('Descripción máximo 400 caracteres');
                    return;
                }

                var markup = "<tr>"+

                    "<td>"+
                    "<input name='cantidadarray[]' value='"+cantidad+"' maxlength='10' class='form-control' type='number'>"+
                    "</td>"+

                    "<td>"+
                    "<input name='descripcionarray[]' value='"+descripcion+"' maxlength='400' class='form-control' type='text'>"+
                    "<input name='unidadmedidaarray[]' value='"+unidadmedida+"' class='form-control' type='hidden'>"+
                    "</td>"+

                    "<td>"+
                    "<button type='button' class='btn btn-block btn-danger' onclick='borrarFilaRequiDetalle(this)'>Borrar</button>"+
                    "</td>"+

                    "</tr>";

                $("#matriz-requisicion tbody").append(markup);

                $('#modalAgregarRequisicionDeta').modal('hide');
            });
        });

        function borrarFilaRequiDetalle(elemento){
            var tabla = elemento.parentNode.parentNode;
            tabla.parentNode.removeChild(tabla);
        }

    </script>

    <script>

        // recargar tabla solo para bitacoras
        function recargarBitacora(){
            var id = {{ $id }};
            var ruta = "{{ URL::to('/admin/proyecto/vista/bitacora') }}/" + id;
            $('#tablaDatatableBitacora').load(ruta);
        }

        function recargarRequisicion(){
            var id = {{ $id }};
            var rutaR = "{{ URL::to('/admin/proyecto/vista/requisicion') }}/" + id;
            $('#tablaDatatableRequisicion').load(rutaR);
        }

        // modal agregar bitacora
        function modalAgregarBitacora(){
            document.getElementById("formulario-bitacora-nuevo").reset();

            var fecha = new Date();
            document.getElementById('fecha-bitacora-nuevo').value = fecha.toJSON().slice(0,10);

            $('#modalAgregarBitacora').modal('show');
        }

        function verModalRequisicion(){
            document.getElementById("formulario-requisicion-nuevo").reset();
            $('#modalAgregarRequisicion').css('overflow-y', 'auto');
            $('#modalAgregarRequisicion').modal({backdrop: 'static', keyboard: false})
        }

        function verModalDetalleRequisicion(){
            document.getElementById("formulario-requisicion-deta-nuevo").reset();
            $('#modalAgregarRequisicionDeta').modal('show');
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
                    toastr.error('formato para Imagen permitido: .png .jpg .jpeg');
                    return;
                }
            }else{
                // si imagen viene vacio, verificar texto
                if(nombreDocumento.length > 0){
                    toastr.error('Imagen es requerida si ingresa Nombre para Imagen');
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

        function preguntaGuardarRequisicion(){
            Swal.fire({
                title: 'Guardar Requisición',
                text: "",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    verificarRequisicion();
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

        function verificarRequisicion(){

            var fecha = document.getElementById('fecha-requisicion-nuevo').value;
            var destino = document.getElementById('destino-requisicion-nuevo').value; // null
            var necesidad = document.getElementById('necesidad-requisicion-nuevo').value; // text

            if(fecha === ''){
                toastr.error('Fecha requisición es requerido');
                return;
            }

            if(destino.length > 300){
                toastr.error('Destino, máximo 300 caracteres');
                return;
            }

            if(necesidad.length > 15000){
                toastr.error('Necesidad debe tener máximo 15,000 caracteres');
                return;
            }

            var hayRegistro = 0;
            var nRegistro = $('#matriz-requisicion >tbody >tr').length;
            let formData = new FormData();
            var id = {{ $id }};

            if (nRegistro > 0){

                var cantidad = $("input[name='cantidadarray[]']").map(function(){return $(this).val();}).get();
                var descripcion = $("input[name='descripcionarray[]']").map(function(){return $(this).val();}).get();
                var medida = $("input[name='unidadmedidaarray[]']").map(function(){return $(this).val();}).get();

                var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

                for(var a = 0; a < cantidad.length; a++){

                    var datoCantidad = cantidad[a];

                    if(datoCantidad === ''){
                        toastr.error('Cantidad es requerida');
                        return;
                    }

                    if(!datoCantidad.match(reglaNumeroDecimal)) {
                        toastr.error('Cantidad debe ser decimal y no negativo');
                        return;
                    }

                    if(datoCantidad <= 0){
                        toastr.error('Cantidad no debe ser negativo');
                        return;
                    }

                    if(datoCantidad.length > 10){
                        toastr.error('Cantidad máximo 10 caracteres');
                        return;
                    }
                }

                for(var b = 0; b < descripcion.length; b++){

                    var datoDescripcion = descripcion[b];

                    if(datoDescripcion === ''){
                        toastr.error('Descripción es requerida');
                        return;
                    }

                    if(datoDescripcion.length > 400){
                        toastr.error('Una descripción tiene más de 400 caracteres');
                    }
                }

                // como tienen la misma cantidad de filas, podemos recorrer
                // todas las filas de una vez
                for(var p = 0; p < cantidad.length; p++){
                    formData.append('cantidad[]', cantidad[p]);
                    formData.append('descripcion[]', descripcion[p]);
                    formData.append('unidadmedidaarray[]', medida[p]);
                }

                hayRegistro = 1;
            }

            openLoading();
            formData.append('hayregistro', hayRegistro);
            formData.append('fecha', fecha);
            formData.append('destino', destino);
            formData.append('necesidad', necesidad);
            formData.append('id', id);

            axios.post(url+'/proyecto/vista/requisicion/nuevo', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalAgregarRequisicion').modal('hide');
                        toastr.success('Registrado correctamente');
                        recargarRequisicion();
                        limpiarRequisicion(response.data.contador);
                    }
                    else{
                        toastr.error('error al crear requisición');
                    }

                })
                .catch((error) => {
                    toastr.error('error al crear requisición');
                    closeLoading();
                });
        }

        function limpiarRequisicion(contador){
            document.getElementById('conteo-requisicion').value = contador;
            document.getElementById('fecha-requisicion-nuevo').value = '';
            document.getElementById('destino-requisicion-nuevo').value = '';
            document.getElementById('necesidad-requisicion-nuevo').value = '';

            $("#matriz-requisicion tbody tr").remove();
        }


    </script>


@endsection

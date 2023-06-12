@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/select2-bootstrap-5-theme.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
@stop

<style>
    table{
        /*Ajustar tablas*/
        table-layout:fixed;
    }
</style>

<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="col-sm-11">
            <h4>Requisiciones de Agrupados Pendientes</h4>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Listado Agrupados</h3>
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



    <div class="modal fade" id="modalCotizar">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Cotizar</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form id="formulario-cotizar-nuevo">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-5">

                                    <div class="form-group">
                                        <!-- NOMBRE DESTINO DEL AGRUPADO -->

                                        <label>Destino</label>
                                        <input type="text" class="form-control" id="destino" disabled>
                                        <input id="idagrupado" type="hidden" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <!-- JUSTIFICACION DEL AGRUPADO -->

                                        <label>Justificación</label>
                                        <textarea class="form-control" id="justificacion" disabled></textarea>
                                    </div>

                                </div>

                                <div class="col-md-5">

                                    <label>Proveedor</label>
                                    <select class="form-control" id="select-proveedor">
                                        @foreach($proveedores as $data)
                                            <option value="{{ $data->id }}">{{ $data->nombre }}</option>
                                        @endforeach
                                    </select>

                                    <div class="form-group">
                                        <label>Fecha de cotización:</label>
                                        <input type="date" id="fecha-cotizacion" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <!-- Selección del lado izquierdo -->
                                        <div class="col-xs-7 col-md-7 col-sm-7">
                                            <label>Lista de Items de Requisición</label>
                                            <select name="from[]" id="mySideToSideSelect" class="form-control" size="8" multiple="multiple">

                                            </select>
                                        </div>

                                        <!-- Botones de acción -->
                                        <div class="col-xs-2 col-md-2 col-sm-2">

                                            <label>&nbsp;</label>
                                            <button type="button" id="mySideToSideSelect_rightAll" class="btn btn-secondary col-xs-12 col-md-12 col-sm-12 mt-1"><i class="fas fa-forward"></i></button>
                                            <button type="button" id="mySideToSideSelect_rightSelected" class="btn btn-secondary col-md-12 col-sm-12 mt-1"><i class="fas fa-chevron-right"></i></button>
                                            <button type="button" id="mySideToSideSelect_leftSelected" class="btn btn-secondary col-md-12 col-sm-12 mt-1"><i class="fas fa-chevron-left"></i></button>
                                            <button type="button" id="mySideToSideSelect_leftAll" class="btn btn-secondary col-md-12 col-sm-12 mt-1"><i class="fas fa-backward"></i></button>
                                        </div>

                                        <!-- Selección del lado derecho -->
                                        <div class="col-xs-3 col-md-3 col-sm-3">
                                            <label>Lista de Items a cotizar</label>
                                            <select name="to[]" id="mySideToSideSelect_to" class="form-control" size="8" multiple="multiple"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-primary button-rounded button-pill button-small" onclick="detalleCotizacion()">Detalle</button>
                </div>
            </div>
        </div>
    </div>






    <div class="modal fade" id="modalDetalle" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Detalle Cotización</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulariocrearcotizacion">
                        <div class="card-body">
                            <div class="row">
                                <table  class="table" id="matriz-requisicion"  data-toggle="table">
                                    <thead>
                                    <tr>
                                        <th style="width: 3%">#</th>
                                        <th style="width: 5%">Cantidad</th>
                                        <th style="width: 12%">Descripción Material</th>
                                        <th style="width: 5%">Medida</th>
                                        <th style="width: 5%">Precio U. ($)</th>
                                        <th style="width: 5%">Total ($)</th>
                                        <th style="width: 8%">Cod. Presup</th>
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
                    <button type="button" class="btn btn-success" onclick="verificarCotizacion()">Guardar</button>
                </div>
            </div>
        </div>
    </div>







    <!-- CANCELAR TODOS EL REQUERIMIENTO PORQUE FUE DENEGADO POR EL CONCEJO -->


    <div class="modal fade" id="modalCancelamiento">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Denegar Agrupado</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form id="formulario-cancelamiento">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">



                                    <div class="form-group">
                                        <label>Documento (Opcional)</label>
                                        <input type="file" id="documento-denegado" class="form-control" accept="image/jpeg, image/jpg, image/png, .pdf"/>
                                    </div>


                                    <div class="form-group">
                                        <label>Descripción:</label>
                                        <input id="id-denegado" type="hidden">
                                        <input type="text" maxlength="800" id="texto-cancelamiento" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;" class="button button-caution button-rounded button-pill button-small" onclick="cancelarRequerimiento()">Denegar</button>
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
    <script src="{{ asset('js/multiselect.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function(){

            var idanio = {{ $idanio }};
            var ruta = "{{ URL::to('/admin/p/requerimientos/pendiente/unidad/tabla') }}/" + idanio;
            $('#tablaDatatable').load(ruta);

            $('#mySideToSideSelect').multiselect();

            $('#select-proveedor').select2({
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

        function recargar(){
            var idanio = {{ $idanio }};
            var ruta = "{{ URL::to('/admin/p/requerimientos/pendiente/unidad/tabla') }}/" + idanio;
            $('#tablaDatatable').load(ruta);
        }




        function informacionCotizar(id){
            // id agrupado

            document.getElementById("formulario-cotizar-nuevo").reset();
            $('#select-proveedor').prop('selectedIndex', 0).change();
            document.getElementById("mySideToSideSelect").options.length = 0;
            document.getElementById("mySideToSideSelect_to").options.length = 0;

            openLoading();

            axios.post(url+'/p/requerimientos/listado/cotizar/info', {
                'id': id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){
                        $('#modalCotizar').modal('show');

                        $('#idagrupado').val(id);

                        $('#destino').val(response.data.info.destino);
                        $('#justificacion').val(response.data.info.justificacion);

                        var fecha = new Date();
                        document.getElementById('fecha-cotizacion').value = fecha.toJSON().slice(0,10);


                        // LLENAR ARRAY PARA COTIZAR
                        // el id es de requisicion_unidad_detalle
                        $.each(response.data.listado, function( key, val ){
                            $('#mySideToSideSelect').append('<option value='+val.id+'>'+val.materialformat+'</option>');
                        });
                    }
                    else {
                        toastr.error('Error al buscar');
                    }

                })
                .catch((error) => {
                    toastr.error('Error al buscar');
                    closeLoading();
                });
        }


        function removeOptionsFromSelect(selectElement) {
            var i, L = selectElement.options.length - 1;
            for(i = L; i >= 0; i--) {
                selectElement.remove(i);
            }
        }



        function detalleCotizacion(){

            var fecha = document.getElementById('fecha-cotizacion').value;
            var proveedor = document.getElementById('select-proveedor').value;

            if(fecha === ''){
                toastr.error('Fecha es requerido');
                return;
            }

            if(proveedor === ''){
                toastr.error('Proveedor es requerido');
                return;
            }

            $("#matriz-requisicion tbody tr").remove();

            var formData = new FormData();
            var hayLista = true;

            var maximaCantidad = 10;
            var contador = 0;

            // INFORMACION DE LOS QUE VAN A COTIZARCE
            $("#mySideToSideSelect_to option").each(function(){
                contador++;
                hayLista = false;
                let dato = $(this).val();
                formData.append('lista[]', dato);
            });

            if(hayLista){
                Swal.fire({
                    title: 'Nota',
                    text: "Lista de Items de Requisición es Requerido",
                    icon: 'warning',
                    showCancelButton: false,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Aceptar'
                }).then((result) => {
                    if (result.isConfirmed) {

                    }
                });
                return;
            }

            if(contador > maximaCantidad){
                Swal.fire({
                    title: 'Límite superado',
                    text: "Solo puede Cotizar 10 items",
                    icon: 'warning',
                    showCancelButton: false,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Aceptar'
                }).then((result) => {
                    if (result.isConfirmed) {

                    }
                });
                return;
            }

            openLoading();

            axios.post(url+'/p/requerimientos/unidad/verificar', formData,{

            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        // array requisicon unidad detalle
                        var infodetalle = response.data.lista;

                        for (var i = 0; i < infodetalle.length; i++) {

                            var markup = "<tr id='"+infodetalle[i].id+"'>"+

                                "<td>"+
                                "<p id='fila"+(i+1)+"' class='form-control' style='max-width: 65px'>"+(i+1)+"</p>"+
                                "<input type='hidden' name='idfila[]' value='"+infodetalle[i].id+"' class='form-control'>"+
                                "</td>"+

                                "<td>"+
                                "<input name='cantidadfilamate[]' value='"+infodetalle[i].cantidad+"' disabled class='form-control'>"+
                                "</td>"+

                                "<td>"+
                                "<input name='descripmaterial[]' value='"+infodetalle[i].material_descripcion+"' maxlength='300' class='form-control'>"+
                                "</td>"+

                                "<td>"+
                                "<input value='"+infodetalle[i].medida+"' disabled class='form-control'>"+
                                "</td>"+

                                "<td>"+
                                "<input name='unidades[]' class='form-control' type='number' onchange='multiplicar(this)'>"+
                                "</td>"+


                                "<td>"+
                                "<input name='total[]' value='' disabled class='form-control'>"+
                                "</td>"+

                                "<td>"+
                                "<input  value='"+infodetalle[i].codigo+"' disabled class='form-control'>"+
                                "</td>"+


                                "</tr>";

                            $("#matriz-requisicion tbody").append(markup);
                        }


                        var marcador = "<tr>"+

                            "<td>"+
                            "<p class='form-control' style='max-width: 65px'>Total</p>"+
                            "</td>"+

                            "<td>"+
                            "<input value='' disabled class='form-control'>"+
                            "</td>"+

                            "<td>"+
                            "<input value='' disabled class='form-control'>"+
                            "</td>"+

                            "<td>"+
                            "<input value='' disabled class='form-control'>"+
                            "</td>"+

                            "<td>"+
                            "<input disabled class='form-control'>"+
                            "</td>"+

                            "<td>"+
                            "<input id='finaltotal' value='' disabled class='form-control'>"+
                            "</td>"+

                            "<td>"+
                            "<input value='' disabled class='form-control'>"+
                            "</td>"+

                            "</tr>";

                        $("#matriz-requisicion tbody").append(marcador);



                        $('#modalDetalle').css('overflow-y', 'auto');
                        $('#modalDetalle').modal({backdrop: 'static', keyboard: false})
                    }else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }


        function verificarCotizacion(){
            Swal.fire({
                title: 'Guardar',
                text: "Nueva Cotización",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    guardarCotizacion();
                }
            });
        }




        function guardarCotizacion(){

            var fecha = document.getElementById('fecha-cotizacion').value;
            var proveedor = document.getElementById('select-proveedor').value;

            var formData = new FormData();
            formData.append('fecha', fecha);
            formData.append('proveedor', proveedor);

            // ES EL ID DE REQUISICION_AGRUPADA
            formData.append('idagrupado', $('#idagrupado').val());


            // EN LA LISTA VA EL ID: REQUISICION UNIDAD DETALLE
            $("#mySideToSideSelect_to option").each(function(){
                hayLista = false;
            });


            var unidades = $("input[name='unidades[]']").map(function(){return $(this).val();}).get();
            var idfila = $("input[name='idfila[]']").map(function(){return $(this).val();}).get();

            var descripmaterial = $("input[name='descripmaterial[]']").map(function(){return $(this).val();}).get();


            var reglaNumeroDosDecimal = /^([0-9]+\.?[0-9]{0,2})$/;

            for(var a = 0; a < unidades.length; a++){

                var datoUnidades = unidades[a];

                if(datoUnidades == ''){
                    modalMensaje('Fila #' + a+1, 'Precio Unitario es requerido');
                    return;
                }

                if(!datoUnidades.match(reglaNumeroDosDecimal)) {
                    modalMensaje('Fila #' + a+1, 'Precio Unitario debe ser número Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(datoUnidades <= 0){
                    modalMensaje('Fila #' + a+1, 'Precio Unitario no debe ser negativo o cero');
                    return;
                }

                if(datoUnidades > 1000000){
                    modalMensaje('Fila #' + a+1, 'Precio Unitario máximo 1 millón');
                    return;
                }
            }


            for(var d = 0; d < descripmaterial.length; d++){

                var datoDescripcion = descripmaterial[d];

                if(datoDescripcion == ''){
                    modalMensaje('Fila #' + d+1, 'Descripción Material es requerido');
                    return;
                }

                if(datoDescripcion.length > 300){
                    modalMensaje('Fila #' + d+1, 'Descripción Material máximo 1 millón');
                    return;
                }
            }


            for(var z = 0; z < unidades.length; z++){

                // el precio unitario del material a cotizar
                formData.append('unidades[]', unidades[z]);

                // id de requisicion unidad detalle
                formData.append('idfila[]', idfila[z]);

                // la descripcion del material escrito por uaci
                formData.append('descripmate[]', descripmaterial[z]);
            }

            openLoading();

            axios.post(url+'/p/requerimientos/cotizacion/unidad/guardar', formData,{

            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        // EL AGRUPADO ESTA CANCELADO

                        Swal.fire({
                            title: 'Error',
                            text: "Esta Cotización ya fue Cancelada por UCP",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Recargar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    }

                    else if(response.data.success === 2){

                        // UN MATERIAL A COTIZAR ESTA CANCELADO

                        Swal.fire({
                            title: 'Error',
                            text: "Un Material fue Cancelado y no se puede cotizar",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Recargar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    }

                    else if(response.data.success === 3){

                        // UN MATERIAL YA ESTA COTIZADO

                        Swal.fire({
                            title: 'Error',
                            text: "Se detecto que un Material ya tiene una Cotización",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Recargar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    }

                    else if(response.data.success === 4){

                        // ERROR NO SE DETECTO EL MATERIAL

                        Swal.fire({
                            title: 'Error',
                            text: "No se detecto un Material",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Recargar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })



                    }

                    else if(response.data.success === 5){

                        // NO ALCANCA EL DINERO

                        let fila = response.data.fila;
                        let material = response.data.material;
                        let objcodigo = response.data.objcodigo;
                        let disponibleFormat = response.data.disponibleFormat;

                        Swal.fire({
                            title: 'Saldo Insuficiente',
                            html: "En la Fila " + fila + " - " + ", El material: " + material + "<br>"
                                + "De código. " + objcodigo + "<br>"
                                + "El saldo actual es: " + disponibleFormat + "<br>"
                            ,
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }

                    else if(response.data.success === 10){

                        // COTIZACION GUARDADA

                        toastr.success('Guardado correctamente');
                        $('#modalCotizar').modal('hide');
                        $('#modalDetalle').modal('hide');
                        recargar();
                    }


                    else if(response.data.success === 11){

                        // SE DETECTO QUE UN MATERIAL TIENE UNA COTIZACION
                        // YA SEA APROBADA O ESPERANDO RESOLUCION

                        Swal.fire({
                            title: 'Error',
                            text: "Se detecto que un Material ya tiene una Cotización en Proceso",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'Recargar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })

                    }


                    else{
                        toastr.error('Error al guardar');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Error al guardar');
                });
        }


        function modalMensaje(titulo, mensaje){
            Swal.fire({
                title: titulo,
                text: mensaje,
                icon: 'info',
                showCancelButton: false,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {

                }
            });
        }



        function multiplicar(e){

            var table = e.parentNode.parentNode; // fila de la tabla
            var cantidad = table.cells[1].children[0]; // cantidad
            var precio = table.cells[4].children[0]; // precio
            var total = table.cells[5].children[0]; // total

            var boolPasa = false;

            // validar que unidades y periodo existan para calcular total
            var reglaNumeroDosDecimal = /^([0-9]+\.?[0-9]{0,2})$/;

            if(precio.value.length > 0) {
                // validar

                if(!precio.value.match(reglaNumeroDosDecimal)) {
                    modalMensaje('Error', 'Precio Unitario debe ser número Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(precio.value <= 0){
                    modalMensaje('Error', 'Precio Unitario no debe ser negativo o cero');
                    return;
                }

                if(precio.value > 1000000){
                    modalMensaje('Error', 'Precio Unitario máximo 1 millón');
                    return;
                }

                boolPasa = true;
            }

            document.getElementById('finaltotal').value = "";

            if(boolPasa){

                var val1 = cantidad.value;
                var val2 = precio.value;
                var valTotal = (val1 * val2);

                total.value = '$' + Number(valTotal).toFixed(2);
                mostrarTotalFinal();
            }else{
                total.value = '';
            }
        }

        function mostrarTotalFinal() {

            var unidades = $("input[name='unidades[]']").map(function () {
                return $(this).val();
            }).get();
            var cantidadfila = $("input[name='cantidadfilamate[]']").map(function () {
                return $(this).val();
            }).get();

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            for (var a = 0; a < unidades.length; a++) {

                var datoUni = unidades[a];

                if (datoUni == '') {
                    return;
                }

                if (!datoUni.match(reglaNumeroDecimal)) {
                    return;
                }

                if (datoUni <= 0) {
                    return;
                }

                if (datoUni > 1000000) {
                    return;
                }
            }

            // si pasa validaciones, multiplicar

            var multifila = 0;

            for (var z = 0; z < cantidadfila.length; z++) {

                // COMO TIENEN LAS MISMA FILAS, EXACTAMENTE SE ENCONTRARAN
                var datoCantidadq = cantidadfila[z];
                var datoUnidadesq = unidades[z];

                multifila += (datoCantidadq * datoUnidadesq);
            }


            document.getElementById('finaltotal').value = '$' + Number(multifila).toFixed(2);
        }









        // PARA DENEGAR EL AGRUPADO


        // modal para cancelar y dar motivo del cancelamiento
        function informacionCancelar(id){
            // ID: requisicion_unidad

            document.getElementById("formulario-cancelamiento").reset();
            $('#id-denegado').val(id);
            $('#modalCancelamiento').modal('show');
        }

        function cancelarRequerimiento(){

            Swal.fire({
                title: 'Denegar Agrupado',
                text: "",
                icon: 'info',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: "No",
                confirmButtonText: 'Si',
            }).then((result) => {
                if (result.isConfirmed) {
                    peticionDenegarRequerimiento();
                }
            })
        }

        // DENEGAR REQUERIMIENTO POR EJEMPLO POR EL CONCEJO
        function peticionDenegarRequerimiento(){

            var id = document.getElementById('id-denegado').value;
            var documento = document.getElementById('documento-denegado');
            var textodenegado = document.getElementById('texto-cancelamiento').value;



            if(documento.files && documento.files[0]){ // si trae doc
                if (!documento.files[0].type.match('image/jpeg|image/jpeg|image/png|pdf')){
                    toastr.error('formato para Documento permitido: .png .jpg .jpeg .pdf');
                    return;
                }
            }

            if(textodenegado === ''){
                toastr.error('Descripción es requerida');
                return;
            }

            if(textodenegado.length > 800){
                toastr.error('Descripción máximo 800 caracteres');
                return;
            }

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nota', textodenegado);
            formData.append('documento', documento.files[0]);

            axios.post(url+'/p/denegar/completa/requisicion/agrupada', formData, {
            })
                .then((response) => {
                    closeLoading();

                    console.log(response);

                    if(response.data.success === 1){

                        // YA ESTABA DENEGADA EL AGRUPADO

                        Swal.fire({
                            title: "Error",
                            text: "El Agrupado ya estaba Denegado",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Recargar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                recargar();
                            }
                        })

                    }
                    else if(response.data.success === 2) {

                        // UN MATERIAL YA ESTA COTIZADO, NO SE PUEDE DENEGAR YA

                        Swal.fire({
                            title: "Error",
                            text: "Un Material se Encontro que ya esta Cotizado",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Recargar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                recargar();
                            }
                        })
                    }

                    else if(response.data.success === 3){

                        toastr.success('Requisición Denegada correctamente');
                        $('#modalCancelamiento').modal('hide');
                        recargar();
                    }
                    else {
                        toastr.error('Error al denegar');
                        closeLoading();
                    }
                })
                .catch((error) => {
                    toastr.error('Error al denegar');
                    closeLoading();
                });
        }


    </script>


@endsection

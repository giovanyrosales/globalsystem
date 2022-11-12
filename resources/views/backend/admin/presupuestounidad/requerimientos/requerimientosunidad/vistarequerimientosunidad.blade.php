@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/buttons_estilo.css') }}" rel="stylesheet">
@stop

<style>
    table{
        /*Ajustar tablas*/
        table-layout:fixed;
    }
</style>

<!-- VISTA PARA CREAR NUEVOS REQUERIMIENTOS PARA UNIDADES -->

<div id="divcontenedor" style="display: none">


    <div class="container-fluid" style="margin-top: 15px">
        <div class="row">

            <div class="col-md-5">

                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">Información</h3>
                    </div>

                    <form>
                        <div class="card-body">

                            <label>Presupuesto Año: {{ $txtanio }}</label><br>
                            <label>Saldo Aprobado: {{ $monto }}</label><br>

                            <button type="button" style="margin-top: 15px; font-weight: bold; color: white !important;"
                                    class="button button-primary button-3d button-rounded button-pill button-small" onclick="infoModalSaldo()">
                                <i class="fas fa-list-alt" title="Saldos"></i>&nbsp; Saldos
                            </button>
                            <br>
                            <button type="button" style="margin-top: 15px; font-weight: bold; color: white !important;"
                                    class="button button-primary button-3d button-rounded button-pill button-small" onclick="infoMovimientoCuenta()">
                                <i class="fas fa-list-alt" title="Movimiento de Cuenta"></i>&nbsp; Movimiento de Cuenta
                            </button>


                        </div>

                    </form>
                </div>

            </div>


            <div class="col-md-7">

                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">Requisiciones</h3>
                    </div>

                    <form>

                        @if($bloqueo == 1)
                            <button type="button" style="margin: 20px; font-weight: bold; background-color: #28a745; color: white !important;"
                                    class="button button-3d button-rounded button-pill button-small" onclick="verModalRequisicion()">
                                <i class="fas fa-plus-square" title="Agregar Requisición"></i>&nbsp; Agregar Requisición
                            </button>
                        @else
                            <label style="margin: 20px">Sin permiso para Crear Requisición</label>
                        @endif


                        <div class="card-body">




                            <div class="row">
                                <div class="col-md-12">
                                    <div id="tablaDatatableRequisicion">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>



            </div>

        </div>

    </div>




    <!------------------ MODAL PARA AGREGAR REQUISICION ---------------->
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
                                        <label>Número Req.:</label>
                                        <input  type="text" class="form-control" id="conteo-requisicion" value="{{ $conteo }}" readonly>
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Destino:</label>
                                        <input  type="text" class="form-control" autocomplete="off" id="destino-requisicion-nuevo">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <label>Necesidad:</label>
                                        <textarea class="form-control" id="necesidad-requisicion-nuevo" autocomplete="off" maxlength="15000" rows="2"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <br>
                                        <button type="button" onclick="addAgregarFilaNuevaRequisicion()" class="button button-3d button-rounded button-pill button-small"
                                                style="margin-top:10px; font-weight: bold; background-color: #17a2b8; color: white !important;">
                                            <i class="fas fa-plus" title="Agregar"></i>&nbsp; Agregar</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <table class="table" id="matriz-requisicion"  data-toggle="table">
                                    <thead>
                                    <tr>
                                        <th style="width: 3%">#</th>
                                        <th style="width: 5%">Cantidad</th>
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
                    <button type="button" class="button button-3d button-rounded button-pill button-small" style="font-weight: bold; background-color: #28a745; color: white !important;"
                            onclick="preguntaGuardarRequisicion()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal para ver saldo del proyecto -->
    <div class="modal fade" id="modalSaldo">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Saldo Disponible</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="tablaSaldo">
                                </div>
                            </div>
                        </div>
                    </div>

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
            let idpresup = {{ $idpresubunidad }};
            var ruta = "{{ URL::to('/admin/p/requerimientos/tabla') }}/" + idpresup;
            $('#tablaDatatableRequisicion').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";

            // variable global para setear input al buscar nuevo material
            window.txtContenedorGlobal = this;
            window.seguroBuscador = true;

            $(document).click(function(){
                $(".droplista").hide();
                $(".droplistaeditar").hide();
                $(".droplistapresupuesto").hide();
                $(".droplistapresupuestoEditar").hide();
            });

            $(document).ready(function() {
                $('[data-toggle="popover"]').popover({
                    placement: 'top',
                    trigger: 'hover'
                });
            });
        });
    </script>


    <script>

        function recargar(){
            let idpresup = {{ $idpresubunidad }};
            var ruta = "{{ URL::to('/admin/p/requerimientos/tabla') }}/" + idpresup;
            $('#tablaDatatableRequisicion').load(ruta);
        }

        function modalAgregar(){
            document.getElementById("formulario-nuevo").reset();
            $('#modalAgregar').modal('show');
        }

        function infoModalSaldo(){
            let idpresup = {{ $idpresubunidad }};
            var ruta = "{{ URL::to('/admin/p/modal/saldo/unidad') }}/" + idpresup;
            $('#tablaSaldo').load(ruta);
            $('#modalSaldo').modal('show');
        }

        function infoMovimientoCuenta(){
            let idpresup = {{ $idpresubunidad }};
            window.location.href="{{ url('/admin/p/requerimientos/movicuentaunidad/index') }}/" + idpresup;
        }



        // para modal agregar Requisicion por parte de administradora
        function buscarMaterialRequisicion(e){

            let idpresubuni = {{ $idpresubunidad }};

            // seguro para evitar errores de busqueda continua
            if(seguroBuscador){
                seguroBuscador = false;

                var row = $(e).closest('tr');
                txtContenedorGlobal = e;

                let texto = e.value;

                if(texto === ''){
                    // si se limpia el input, setear el atributo id
                    $(e).attr('data-info', 0);
                }

                axios.post(url+'/buscar/material/requisicion/unidad', {
                    'query' : texto,
                    'idpresuunidad': idpresubuni,
                })
                    .then((response) => {
                        seguroBuscador = true;
                        $(row).each(function (index, element) {
                            $(this).find(".droplista").fadeIn();
                            $(this).find(".droplista").html(response.data);
                        });

                    })
                    .catch((error) => {
                        seguroBuscador = true;
                    });
            }
        }

        function addAgregarFilaNuevaRequisicion(){

            var nFilas = $('#matriz-requisicion >tbody >tr').length;
            nFilas += 1;

            var markup = "<tr>"+

                "<td>"+
                "<p id='fila"+(nFilas)+"' class='form-control' style='max-width: 65px'>"+(nFilas)+"</p>"+
                "</td>"+

                "<td>"+
                "<input name='cantidadarray[]' maxlength='10' class='form-control' type='number'>"+
                "</td>"+

                "<td>"+
                "<input name='descripcionarray[]' data-info='0' autocomplete='off' class='form-control' style='width:100%' onkeyup='buscarMaterialRequisicion(this)' maxlength='400'  type='text'>"+
                "<div class='droplista' style='position: absolute; z-index: 9; width: 75% !important;'></div>"+
                "</td>"+

                "<td>"+
                "<button type='button' class='btn btn-block btn-danger' onclick='borrarFilaRequiDetalle(this)'>Borrar</button>"+
                "</td>"+

                "</tr>";

            $("#matriz-requisicion tbody").append(markup);
        }

        // borrar fila para tabla nueva requisicion material
        function borrarFilaRequiDetalle(elemento){
            var tabla = elemento.parentNode.parentNode;
            tabla.parentNode.removeChild(tabla);
            setearFilaRequisicion();
        }

        // borrar fila para tabla editar requisicion material
        function borrarFilaRequiEditar(elemento){
            var tabla = elemento.parentNode.parentNode;
            tabla.parentNode.removeChild(tabla);
            setearFilaRequisicionEditar()
        }

        // cambiar # de fila cada vez que se borra la fila de
        // tabla nuevo material
        function setearFilaRequisicion(){

            var table = document.getElementById('matriz-requisicion');
            var conteo = 0;
            for (var r = 1, n = table.rows.length; r < n; r++) {
                conteo +=1;
                var element = table.rows[r].cells[0].children[0];
                document.getElementById(element.id).innerHTML = ""+conteo;
            }
        }

        function setearFilaRequisicionEditar(){

            var table = document.getElementById('matriz-requisicion-editar');
            var conteo = 0;
            for (var r = 1, n = table.rows.length; r < n; r++) {
                conteo +=1;
                var element = table.rows[r].cells[0].children[0];
                document.getElementById(element.id).innerHTML = ""+conteo;
            }
        }

        // recargar tabla de requisiciones
        function recargarRequisicion(){
            let idpresup = {{ $idpresubunidad }};
            var ruta = "{{ URL::to('/admin/p/requerimientos/tabla') }}/" + idpresup;
            $('#tablaDatatableRequisicion').load(ruta);
        }

        // ver modal requisición
        function verModalRequisicion(){
            document.getElementById("formulario-requisicion-nuevo").reset();

            colorBlancoTablaRequisicion();
            $('#modalAgregarRequisicion').css('overflow-y', 'auto');
            $('#modalAgregarRequisicion').modal({backdrop: 'static', keyboard: false})
        }

        // ver modal detalle requisicon
        function verModalDetalleRequisicion(){
            document.getElementById("formulario-requisicion-deta-nuevo").reset();
            $('#modalAgregarRequisicionDeta').modal('show');
        }

        // preguntar si quiere guardar la nueva requisicion
        function preguntaGuardarRequisicion(){
            colorBlancoTablaRequisicion();

            Swal.fire({
                title: 'Guardar Requisición',
                text: "Se Reservara Monto en Saldo Retenido",
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

        // preguntar si quiere guardar la editada de requisicion
        function preguntaGuardarRequisicionEditar(){
            colorBlancoTablaRequisicionEditar();

            Swal.fire({
                title: 'Actualizar Requisición',
                text: "",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    verificarRequisicionEditar();
                }
            })
        }

        // verificar la requisicin para agregar a la base
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

            var nRegistro = $('#matriz-requisicion >tbody >tr').length;
            let formData = new FormData();


            if (nRegistro <= 0) {
                toastr.error('Detalle es requerido');
                return;
            }

            var cantidad = $("input[name='cantidadarray[]']").map(function(){return $(this).val();}).get();
            var descripcion = $("input[name='descripcionarray[]']").map(function(){return $(this).val();}).get();
            var descripcionAtributo = $("input[name='descripcionarray[]']").map(function(){return $(this).attr("data-info");}).get();
            var reglaNumeroDosDecimal = /^([0-9]+\.?[0-9]{0,2})$/;

            for(var a = 0; a < cantidad.length; a++){
                let detalle = descripcionAtributo[a];
                let datoCantidad = cantidad[a];

                // identifica si el 0 es tipo number o texto
                if(detalle == 0){
                    colorRojoTablaRequisicion(a);
                    alertaMensaje('info', 'No encontrado', 'En la Fila #' + (a+1) + " El material no se encuentra. Por favor buscar de nuevo el Material. Recordar que debe hacer clic en el Material para Seleccionarlo");
                    return;
                }

                if(datoCantidad === ''){
                    colorRojoTablaRequisicion(a);
                    toastr.error('Fila #' + (a+1) + ' Cantidad es requerida');
                    return;
                }

                if(!datoCantidad.match(reglaNumeroDosDecimal)) {
                    colorRojoTablaRequisicion(a);
                    toastr.error('Fila #' + (a+1) + ' Cantidad debe ser Número Decimal Positivo. Solo se permite 2 Decimales');
                    return;
                }

                if(datoCantidad <= 0){
                    colorRojoTablaRequisicion(a);
                    toastr.error('Fila #' + (a+1) + ' Cantidad no debe ser negativo o igual a Cero');
                    return;
                }

                if(datoCantidad > 1000000){
                    colorRojoTablaRequisicion(a);
                    toastr.error('Fila #' + (a+1) + ' Cantidad máximo 1 millón');
                    return;
                }
            }

            // SOLO TIENE QUE IR UNA LETRA, ESTO NO SE ENVÍA A SERVER
            for(var b = 0; b < descripcion.length; b++){

                var datoDescripcion = descripcion[b];

                if(datoDescripcion === ''){
                    colorRojoTablaRequisicion(b);
                    toastr.error('Fila #' + (b+1) + ' la descripción es requerida');
                    return;
                }

                // cantidad de caracteres no se valida, ya que no se envía
            }

            // como tienen la misma cantidad de filas, podemos recorrer
            // todas las filas de una vez
            for(var p = 0; p < cantidad.length; p++){

                formData.append('cantidad[]', cantidad[p]);
                formData.append('datainfo[]', descripcionAtributo[p]);
            }

            let idpresubuni = {{ $idpresubunidad }};
            let idanio = {{ $idanio }};

            openLoading();
            formData.append('fecha', fecha);
            formData.append('destino', destino);
            formData.append('necesidad', necesidad);
            formData.append('idpresubuni', idpresubuni); // id presup unidad
            formData.append('idanio', idanio);

            axios.post(url+'/p/regisrar/requisicion/unidades', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1){

                        let fila = response.data.fila;
                        let obj = response.data.obj; // codigo especifico
                        let restanteFormat = response.data.restanteFormat;
                        let retenidoFormat = response.data.retenidoFormat;
                        let retenido = response.data.retenido;
                        let solicita = response.data.solicita;

                        colorRojoTablaRequisicion(fila);

                        var texto = '';

                        // en true, mostramos el saldo Retenido
                        if(retenido > 0){
                            texto = "Fila #" + (fila+1) + ", el objeto específico: " + obj + "<br>" +
                                "Tiene Saldo Restante $" + restanteFormat + "<br>" +
                                "Saldo Retenido $" + retenidoFormat + "<br>" +
                                " Y se esta solicitando $" + solicita;
                        }else{
                            texto = "Fila #" + (fila+1) + ", el objeto específico: " + obj + "<br>" +
                                "Tiene Saldo Restante $" + restanteFormat + "<br>" +
                                " Y se esta solicitando $" + solicita + "<br>";
                        }

                        Swal.fire({
                            title: 'Cantidad No Disponible',
                            html: texto,
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }
                    else if(response.data.success === 2){
                        $('#modalAgregarRequisicion').modal('hide');
                        toastr.success('Registrado correctamente');
                        recargarRequisicion();
                        limpiarRequisicion(response.data.contador);
                    }
                    else if(response.data.success === 3){
                        // no hay permiso para crear requisiciones

                        Swal.fire({
                            title: 'Permiso Denegado',
                            text: "Para el Presente Año no es permitido realizar una Requisición",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                               location.reload();
                            }
                        })

                    }
                    else{
                        toastr.error('Error al crear requisición');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al crear requisición');
                    closeLoading();
                });
        }

        // cambio de color de fila tabla a rojo
        function colorRojoTablaRequisicion(index){
            $("#matriz-requisicion tr:eq("+(index+1)+")").css('background', '#F1948A');
        }

        // cambio de color de fila tabla a blanco
        function colorBlancoTablaRequisicion(){
            $("#matriz-requisicion tbody tr").css('background', 'white');
        }

        // cambio de color de fila tabla a rojo
        function colorRojoTablaRequisicionEditar(index){
            $("#matriz-requisicion-editar tr:eq("+(index+1)+")").css('background', '#F1948A');
        }

        // cambio de color de fila tabla a blanco
        function colorBlancoTablaRequisicionEditar(){
            $("#matriz-requisicion-editar tbody tr").css('background', 'white');
        }

        // limpiar modal requisicion y su tabla
        function limpiarRequisicion(contador){
            document.getElementById('conteo-requisicion').value = contador;
            document.getElementById('fecha-requisicion-nuevo').value = '';
            document.getElementById('destino-requisicion-nuevo').value = '';
            document.getElementById('necesidad-requisicion-nuevo').value = '';

            $("#matriz-requisicion tbody tr").remove();
        }


        function vistaEditarRequisicion(dato){

            let id = dato.id;
            let conteo = dato.numero;

            openLoading();
            document.getElementById("formulario-requisicion-editar").reset();
            $("#matriz-requisicion-editar tbody tr").remove();

            axios.post(url+'/proyecto/vista/requisicion/informacion', {
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        $('#id-requisicion-editar').val(response.data.info.id);
                        $('#fecha-requisicion-editar').val(response.data.info.fecha);
                        $('#conteo-requisicion-editar').val(conteo);
                        $('#destino-requisicion-editar').val(response.data.info.destino);
                        $('#necesidad-requisicion-editar').val(response.data.info.necesidad);

                        let nopuedoEditar = response.data.btneditar;

                        if(nopuedoEditar){
                            // ocultar botón
                            document.getElementById("botonGuardarRequiDetalle").style.display = "none";

                            document.getElementById("fecha-requisicion-editar").disabled = true;
                            document.getElementById("destino-requisicion-editar").disabled = true;
                            document.getElementById("necesidad-requisicion-editar").disabled = true;
                        }else{
                            // mostrar botón
                            document.getElementById("botonGuardarRequiDetalle").style.display = "block";

                            document.getElementById("fecha-requisicion-editar").disabled = false;
                            document.getElementById("destino-requisicion-editar").disabled = false;
                            document.getElementById("necesidad-requisicion-editar").disabled = false;
                        }

                        var infodetalle = response.data.detalle;

                        for (var i = 0; i < infodetalle.length; i++) {
                            // id requi detalle
                            var markup = "<tr id='"+infodetalle[i].id+"'>";

                            markup += "<td>"+
                                "<p id='fila"+(i+1)+"' class='form-control' style='max-width: 65px'>"+(i+1)+"</p>"+
                                "</td>"+

                                "<td>"+
                                "<input name='cantidadarrayeditar[]' disabled value='"+infodetalle[i].cantidad+"' class='form-control'>"+
                                "</td>"+

                                "<td>"+
                                "<input value='$"+infodetalle[i].dinero+"' disabled class='form-control'>"+
                                "</td>"+

                                "<td>"+
                                "<input value='$"+infodetalle[i].multiplicado+"' disabled class='form-control'>"+
                                "</td>"+

                                "<td>"+
                                "<input value='"+infodetalle[i].codigo+"' disabled class='form-control'>"+
                                "</td>"+

                                "<td>"+
                                "<input class='form-control' disabled value='"+infodetalle[i].descripcion+"' style='width:100%' type='text'>"+
                                "</td>";

                            // si hay cotización
                            if(infodetalle[i].haycoti){

                                // cotizacion aprobada, no se puede borrar
                                if(infodetalle[i].cotizado === 1){
                                    markup += "<td>" +
                                        "<span class='badge bg-success'>Material Aprobado</span>"+
                                        "</td>"+
                                        "</tr>";

                                    // cotizacion denegada, puede CANCELAR
                                }else if(infodetalle[i].cotizado === 2){

                                    if(infodetalle[i].cancelado === 0){
                                        markup += "<td>"+
                                            "<button type='button' class='btn btn-block btn-danger' onclick='cancelarFilaRequiEditar(this)'>Cancelar</button>"+
                                            "</td>"+

                                            "</tr>";
                                    }else { // cuando material esta cancelado
                                        markup += "<td>"+
                                            "<span class='badge bg-danger'>Material Cancelado</span>"+
                                            "</tr>";
                                    }

                                }

                            }else{

                                if(nopuedoEditar){
                                    markup += "<td>"+
                                        "<button type='button' class='btn btn-block btn-danger' onclick='modalBorrarFilaRequiEditar(this)'>Borrar</button>"+
                                        "</td>"+

                                        "</tr>";
                                }else{
                                    // no tiene cotizacion, asi que puede BORRAR
                                    markup += "<td>"+
                                        "<button type='button' class='btn btn-block btn-danger' onclick='borrarFilaRequiEditar(this)'>Borrar</button>"+
                                        "</td>"+

                                        "</tr>";
                                }
                            }

                            // cotizacion aprobada, no puede borrar

                            $("#matriz-requisicion-editar tbody").append(markup);
                        }

                        $('#modalEditarRequisicion').css('overflow-y', 'auto');
                        $('#modalEditarRequisicion').modal({backdrop: 'static', keyboard: false})
                    }
                    else{
                        toastr.error('error buscar información');
                    }
                })
                .catch((error) => {
                    toastr.error('error buscar información');
                    closeLoading();
                });
        }

        // cancelar un material si fue denegada la cotizacion
        function cancelarFilaRequiEditar(e){

            // ID REQUI_DETALLE
            var id = $(e).closest('tr').attr('id');

            Swal.fire({
                title: 'Cancelar Material',
                text: "Si el material no puede ser Cotizado. Se cancelara y se libera el saldo Retenido",
                icon: 'info',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Salir',
                confirmButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    cancelarMaterialCotizado(id);
                }
            })
        }

        // borrar material requi detalle, aquí se hace especificamente a un material Fila
        // porque el boton guardar desparece porque otros materiales ya tiene cotización
        function modalBorrarFilaRequiEditar(e){
            // ID REQUI_DETALLE
            var id = $(e).closest('tr').attr('id');

            Swal.fire({
                title: 'Borrar Material',
                text: "Este material no tiene Cotización aun. Se puede eliminar",
                icon: 'info',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Borrar'
            }).then((result) => {
                if (result.isConfirmed) {
                    borrarRequiDetalleFila(id);
                }
            })
        }

        // solo elimina una fila
        function borrarRequiDetalleFila(id){
            openLoading();

            axios.post(url+'/proyecto/requisicion/material/borrarfila', {
                'id': id
            })
                .then((response) => {
                    closeLoading();

                    // el material ya tiene una cotización
                    if(response.data.success === 1) {

                        Swal.fire({
                            title: "Cotización Encontrada",
                            text: "No se puede Borrar el Material. Se encontro una cotización en Proceso",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#modalEditarRequisicion').modal('hide');
                                recargarRequisicion();
                            }
                        })

                    }else if(response.data.success === 2){
                        toastr.success('Borrado correctamente');
                        recargarRequisicion();
                    }
                    else{
                        toastr.error('Error al borrar');
                    }
                })
                .catch((error) => {
                    toastr.error('Error al borrar');
                    closeLoading();
                });
        }

        function cancelarMaterialCotizado(id){
            openLoading();

            axios.post(url+'/proyecto/requisicion/material/cancelar', {
                'id': id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1) {

                        // si es 1, la coti fue aprobada, sino se esta esperando que sea
                        // aprobada o denegada
                        let tipo = response.data.tipo;

                        var mensaje = '';
                        var titulo = '';
                        if(tipo > 0){
                            titulo = "Cotización Aprobada";
                            mensaje = "El material fue aprobado. No se puede cancelar";
                        }else{
                            titulo = "Material en Espera";
                            mensaje = "El material esta esperando que su cotización sea Aprobado o Denegada. No se puede Cancelar por el momento.";
                        }

                        Swal.fire({
                            title: titulo,
                            text: mensaje,
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Recargar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#modalEditarRequisicion').modal('hide');
                                recargarRequisicion();
                            }
                        })

                    }else if(response.data.success === 2){
                        toastr.success('Cancelado correctamente');
                        $('#modalEditarRequisicion').modal('hide');
                        recargarRequisicion();
                    }
                    else{
                        toastr.error('error al actualizar');
                    }
                })
                .catch((error) => {
                    toastr.error('error al actualizar');
                    closeLoading();
                });
        }

        // ver modal para detalle requisicion editar
        function verModalDetalleRequisicionEditar(){
            document.getElementById("formulario-requisicion-deta-editar").reset();
            $('#modalAgregarRequisicionDetaEditar').modal('show');
        }

        // verificar la editada de requisicion
        function verificarRequisicionEditar(){

            var fecha = document.getElementById('fecha-requisicion-editar').value;
            var idrequisicion = document.getElementById('id-requisicion-editar').value;
            var destino = document.getElementById('destino-requisicion-editar').value; // null
            var necesidad = document.getElementById('necesidad-requisicion-editar').value; // text

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

            var nRegistro = $('#matriz-requisicion-editar >tbody >tr').length;
            let formData = new FormData();

            if (nRegistro <= 0){
                toastr.error('Detalle Requisición es requerida');
                return;
            }

            var cantidad = $("input[name='cantidadarrayeditar[]']").map(function(){return $(this).val();}).get();

            // como tienen la misma cantidad de filas, podemos recorrer
            // todas las filas de una vez
            for(var p = 0; p < cantidad.length; p++){
                // obtener el id de la fila, si el id fila es 0, significa que sera nuevo registro
                var id = $("#matriz-requisicion-editar tr:eq("+(p+1)+")").attr('id');
                formData.append('idarray[]', id); // ID REQUI DETALLE
            }

            openLoading();
            formData.append('fecha', fecha);
            formData.append('destino', destino);
            formData.append('necesidad', necesidad);
            formData.append('idrequisicion', idrequisicion);

            axios.post(url+'/proyecto/vista/requisicion/editar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1) {

                        let nombre = response.data.nombre;

                        Swal.fire({
                            title: 'Material Ya Cotizado',
                            text: "El material " + nombre + " Ya fue cotizado. Recargar Tabla",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Recargar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#modalEditarRequisicion').modal('hide');
                                recargarRequisicion();
                            }
                        })

                    }else if(response.data.success === 2){
                        toastr.success('Actualizado correctamente');
                        recargarRequisicion();
                        $('#modalEditarRequisicion').modal('hide');
                    }
                    else if(response.data.success === 3){

                        let mensaje = response.data.mensaje;

                        Swal.fire({
                            title: 'Estado Proyecto',
                            html: mensaje,
                            icon: 'info',
                            showCancelButton: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {

                            }
                        })
                    }


                    else{
                        toastr.error('error al actualizar');
                    }
                })
                .catch((error) => {
                    toastr.error('error al actualizar');
                    closeLoading();
                });
        }

        // cuando se busca un material en requisición y se hace clic en material se modifica el valor
        function modificarValorRequisicion(edrop){

            // obtener texto del li
            let texto = $(edrop).text();
            // setear el input de la descripcion
            $(txtContenedorGlobal).val(texto);

            // agregar el id al atributo del input descripcion
            $(txtContenedorGlobal).attr('data-info', edrop.id);
            //$(txtContenedorGlobal).data("info");
        }

        function modalBorrarRequisicion(id){
            Swal.fire({
                title: 'Borrar Requisición',
                text: "",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar',
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    borrarRequisicion(id);
                }
            })
        }

        function borrarRequisicion(id){

            openLoading();

            let idanio = {{ $idanio }};

            let formData = new FormData();
            formData.append('id', id);
            formData.append('idanio', idanio);

            axios.post(url+'/p/requisicion/unidad/borrar/todo', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        Swal.fire({
                            title: 'Permiso Denegado',
                            text: "Para el Presente Año no es permitido realizar una Requisición",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })


                    }
                    else if(response.data.success === 2){

                        Swal.fire({
                            title: 'Ya hay Cotización',
                            text: "Uno o todos los materiales ya tiene una cotización en proceso",
                            icon: 'info',
                            showCancelButton: false,
                            allowOutsideClick: false,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // quitara el boton borrar de la requisición
                                recargarRequisicion();
                            }
                        })
                    }
                    else if(response.data.success === 3) {

                        // cotización borrada
                        toastr.success('Cotización Borrada');
                        recargarRequisicion();
                    }
                    else{
                        toastr.error('error al borrar');
                    }
                })
                .catch((error) => {
                    toastr.error('error al borrar');
                    closeLoading();
                });
        }

    </script>


@endsection

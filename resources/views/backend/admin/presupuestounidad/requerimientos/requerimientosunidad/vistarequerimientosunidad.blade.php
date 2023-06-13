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


    .modal-xl { max-width: 95% !important; }

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

                            <br>
                            <button type="button" style="margin-top: 15px; font-weight: bold; color: white !important;"
                                    class="button button-primary button-3d button-rounded button-pill button-small" onclick="verSolicitudes()">
                                <i class="fas fa-list-alt" title="Solicitud Materiales"></i>&nbsp; Solicitud Materiales
                            </button>

                            <br>
                            <button type="button" style="margin-top: 20px; font-weight: bold; background-color: #6c757d; color: white !important;"
                                    class="button button-3d button-rounded button-pill button-small" onclick="vistaCatalogoMaterial()">
                                <i class="fas fa-list-alt" title="Materiales Presupuesto"></i>&nbsp; Materiales Presupuesto
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
                    <h4 class="modal-title">Agregar Requisición de Unidad</h4>
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
                                        <label>Destino *:</label>
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
                                        <button type="button" onclick="modalNuevaSolicitud()" class="button button-3d button-rounded button-pill button-small"
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
                                        <th style="width: 4%">Cantidad</th>
                                        <th style="width: 10%">Material</th>
                                        <th style="width: 12%">Descripción</th>
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

    <!-- modal para ver saldo de cuenta unidad -->
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


    <!------------------ MODAL PARA EDITAR REQUISICIÓN ---------------->
    <div class="modal fade" id="modalEditarRequisicion" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Requisición de Unidad</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form id="formulario-requisicion-editar">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Fecha *:</label>
                                        <input type="hidden" id="id-requisicion-editar">
                                        <input style="width:50%;" type="date" class="form-control" id="fecha-requisicion-editar">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Número Req.:</label>
                                        <input  type="text" class="form-control" id="conteo-requisicion-editar" readonly>
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Destino:</label>
                                        <input  type="text" class="form-control" id="destino-requisicion-editar">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <label>Necesidad:</label>
                                        <textarea class="form-control" id="necesidad-requisicion-editar" maxlength="15000" rows="2"></textarea>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <table class="table" id="matriz-requisicion-editar"  data-toggle="table">
                                    <thead>
                                    <tr>
                                        <th style="width: 3%">#</th>
                                        <th style="width: 4%">Cantidad</th>
                                        <th style="width: 4%">Precio</th>
                                        <th style="width: 4%">Total</th>
                                        <th style="width: 7%">Material</th>
                                        <th style="width: 15%">Descripción Material</th>
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
                    <button type="button" style="font-weight: bold; background-color: #28a745; color: white !important;"
                            class="button button-rounded button-pill button-small" id="botonGuardarRequiDetalle" onclick="preguntaGuardarRequisicionEditar()">Guardar</button>
                </div>
            </div>
        </div>
    </div>



    <!-- ************************************************ -->


    <div class="modal fade" id="modalNuevoSolicitud">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Solicitud de Material</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo-material">
                        <div class="card-body">

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Cantidad</label>
                                            <input type="number" class="form-control" autocomplete="off" id="cantidad-material-nuevo">
                                        </div>

                                        <label>Material del Presupuesto</label>
                                        <table class="table" id="matriz-busqueda" data-toggle="table">
                                            <tbody>
                                            <tr style="width: 100%">
                                                <td >
                                                    <input type='text' id="materialnuevosolicitado" autocomplete="off" data-info='0' class='form-control' onkeyup='buscarMaterialRequisicion(this)' maxlength='300'  >
                                                    <div class='droplistado' style='position: absolute; z-index: 9; width: 85% !important;'></div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>

                                        <div class="form-group">
                                            <label>Descripción del Material</label>
                                            <input type="text" class="form-control" autocomplete="off" id="descripcion-material-nuevo">
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="verificarNuevaFila()">Agregar Fila</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalCatalogoMaterial">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Catálogo de Materiales</h4>

                    <button type="button" style="margin-left: 15px; font-weight: bold; color: white !important;"
                            class="button button-primary button-3d button-rounded button-pill button-small" onclick="pdfMaterialesPresupuesto()">
                        <i class="fas fa-file-pdf" title="Documento PDF"></i>&nbsp; PDF
                    </button>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="tablaCatalogoMaterial">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>



    <!-- MUESTRA EL ESTADO DEL PROCESO QUE VA EL MATERIAL -->

    <div class="modal fade" id="modalEstado">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Estados de Material</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="tablaEstado">
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
                $(".droplistado").hide();
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


        // exportar PDF de catalogo de materiales
        function pdfMaterialesPresupuesto(){
            let idpresup = {{ $idpresubunidad }};

            window.open("{{ URL::to('admin/p/generador/pdf/catalogomaterial/unidad') }}/" + idpresup);
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
                            $(this).find(".droplistado").fadeIn();
                            $(this).find(".droplistado").html(response.data);
                        });

                    })
                    .catch((error) => {
                        seguroBuscador = true;
                    });
            }
        }

        function verificarNuevaFila(){

            var cantidad = document.getElementById('cantidad-material-nuevo').value;
            var descripcion = document.getElementById('descripcion-material-nuevo').value;
            var idmaterial = document.querySelector('#materialnuevosolicitado');
            var nomMaterial = document.getElementById('materialnuevosolicitado').value;

            if(idmaterial.dataset.info == 0){
                toastr.error("Se debe seleccionar un Material del Buscador");
                return;
            }

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            //*************

            if(cantidad === ''){
                toastr.error('Cantidad es requerida');
                return;
            }

            if(!cantidad.match(reglaNumeroDecimal)) {
                toastr.error('Cantidad debe ser número Decimal y no Negativo. Solo 2 decimales');
                return;
            }

            if(cantidad <= 0){
                toastr.error('Cantidad no debe ser negativo o cero');
                return;
            }

            if(cantidad > 9000000){
                toastr.error('Cantidad máximo 9 millones');
                return;
            }

            if(descripcion === ''){
                toastr.error('Descripción es requerido');
                return;
            }

            if(descripcion.length > 300){
                toastr.error('Descripción máximo 300 caracteres');
                return;
            }


            var nFilas = $('#matriz-requisicion >tbody >tr').length;
            nFilas += 1;

            var markup = "<tr>"+

                "<td>"+
                "<p id='fila"+(nFilas)+"' class='form-control' style='max-width: 65px'>"+(nFilas)+"</p>"+
                "</td>"+

                "<td>"+
                "<input name='cantidadarray[]' disabled class='form-control' value='" + cantidad + "'>"+
                "</td>"+

                "<td>"+
                "<input name='descripcionarray[]' disabled data-info='" + idmaterial.dataset.info + "'  value='" + nomMaterial + "' class='form-control'>"+
                "</td>"+

                "<td>"+
                "<input name='materialdescripcionarray[]' disabled value='" + descripcion + "' class='form-control'>"+
                "</td>"+

                "<td>"+
                "<button type='button' class='btn btn-block btn-danger' onclick='borrarFilaRequiDetalle(this)'>Borrar</button>"+
                "</td>"+

                "</tr>";

            $('#modalNuevoSolicitud').modal('hide');
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

            // una descripción mas descriptiva del material
            var materialDescriptivo = $("input[name='materialdescripcionarray[]']").map(function(){return $(this).val();}).get();

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
                formData.append('materialDescriptivo[]', materialDescriptivo[p]);
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
                        let saldoCodigo = response.data.saldoactual;
                        let solicita = response.data.solicita; // multiplicado (cantidad * monto)

                        colorRojoTablaRequisicion(fila);

                        var texto = "Fila #" + (fila+1) + ", el objeto específico: " + obj + "<br>" +
                                "Tiene Saldo Restante " + saldoCodigo + "<br>" +
                                " Y se esta solicitando " + solicita;

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

            let id = dato.id; // id requisicion unidad
            let conteo = dato.numero;

            openLoading();
            document.getElementById("formulario-requisicion-editar").reset();
            $("#matriz-requisicion-editar tbody tr").remove();

            axios.post(url+'/p/requisicion/unidad/informacion', {
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

                        let puedoEditar = response.data.btneditar;

                        // CON UN SOLO MATERIAL QUE ESTE AGRUPADO, YA NO PODRA CAMBIAR DESCRIPCION NI NADA
                        if(puedoEditar === 1){
                            // mostrar botón
                            document.getElementById("botonGuardarRequiDetalle").style.display = "block";

                            document.getElementById("fecha-requisicion-editar").disabled = false;
                            document.getElementById("destino-requisicion-editar").disabled = false;
                            document.getElementById("necesidad-requisicion-editar").disabled = false;
                        }else{
                            // ocultar botón
                            document.getElementById("botonGuardarRequiDetalle").style.display = "none";

                            document.getElementById("fecha-requisicion-editar").disabled = true;
                            document.getElementById("destino-requisicion-editar").disabled = true;
                            document.getElementById("necesidad-requisicion-editar").disabled = true;
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
                                "<input value='"+infodetalle[i].descripcion+"' disabled class='form-control'>"+
                                "</td>"+

                                "<td>"+
                                "<input class='form-control' disabled value='"+infodetalle[i].material_descripcion+"' style='width:100%' type='text'>"+
                                "</td>";



                            if(infodetalle[i].cancelado === 1){

                                markup += "<td>"+
                                    "<span class='badge bg-danger'>Material Cancelado</span>"+
                                    "</tr>";
                            }else{

                                if(infodetalle[i].agrupado === 1){
                                    markup += "<td>"+

                                        "</tr>";
                                }else{
                                    markup += "<td>"+
                                        "<button type='button' class='btn btn-block btn-danger' onclick='cancelarFilaRequiEditar(this)'>Cancelar</button>"+
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
                text: "Se devolvera el Monto ($) a su código",
                icon: 'info',
                showCancelButton: true,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                cancelButtonText: 'No',
                confirmButtonText: 'Si'
            }).then((result) => {
                if (result.isConfirmed) {
                    cancelarMaterialCotizado(id);
                }
            })
        }



        // CANCELAR MATERIAL SINO HA SIDO AGRUPADO Y SE DEVOLVERA EL DINERO A SU CODIGO
        function cancelarMaterialCotizado(id){
            openLoading();

            axios.post(url+'/p/requisicion/unidad/material/cancelar', {
                'id': id
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1) {


                        // NO SE PUEDE CANCELAR PORQUE MATERIAL YA HA SIDO AGRUPADO

                        Swal.fire({
                            title: "Denegado",
                            text: "No se puede cancelar porque el material ya ha sido Agrupado por Consolidador",
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

                        // SE CANCELO Y SE DEVOLVIO EL DINERO A SU CODIGO

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

            let idanio = {{ $idanio }};

            openLoading();
            formData.append('fecha', fecha);
            formData.append('destino', destino);
            formData.append('necesidad', necesidad);
            formData.append('idrequisicion', idrequisicion);
            formData.append('idanio', idanio);

            axios.post(url+'/p/requisicion/unidad/editar', formData, {
            })
                .then((response) => {
                    closeLoading();

                    if(response.data.success === 1) {
                        Swal.fire({
                            title: 'Permiso Denegado',
                            text: "Para el Presente Año no es permitido realizar modificaciones",
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

                    }else if(response.data.success === 2){

                        // ACTUALIZADO

                        Swal.fire({
                            title: 'NO ACTUALIZADO',
                            text: "Un Material ya se encuentra Agrupado por el Consolidador",
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
                    }
                    else if(response.data.success === 3){


                        toastr.success('Actualizado correctamente');
                        recargarRequisicion();
                        $('#modalEditarRequisicion').modal('hide');


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
                            text: "Para el Presente Año no es permitido realizar modificaciones",
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

        //*********************************************

        function modalNuevaSolicitud(){
            document.getElementById("formulario-nuevo-material").reset();
            $('#modalNuevoSolicitud').modal('show');
        }

        function vistaCatalogoMaterial(){
            let idpresubuni = {{ $idpresubunidad }};

            var ruta = "{{ URL::to('/admin/p/listado/materiales/presupuestounidad') }}/" + idpresubuni;
            $('#tablaCatalogoMaterial').load(ruta);
            $('#modalCatalogoMaterial').modal('show');
        }

        // vista para solicitar material que no esta en mi presupuesto y quitara
        // dinero de un código
        function verSolicitudes(){
            let id = {{ $idpresubunidad }}; // ID presup_unidad
            window.location.href="{{ url('/admin/p/movicuentaunidad/solicitud/material') }}/" + id;
        }


        function vistaPDFRequerimiento(id){
            // ID DE requisicion_unidad

            window.open("{{ URL::to('admin/p/generador/pdf/requisicion') }}/" + id);
        }


        // MUESTRA EL ESTADO DEL PROCESO DEL MATERIAL
        // PENDIENTE
        // AGRUPADO
        // COTIZADO (SI FUE DENEGADA PASA AL AGRUPADO)
        // ORDEN GENERADA (SI FUE DENEGADA PASA A CANCELADO)
        // CANCELADO


        function vistaModalEstadoMaterial(id){
            var ruta = "{{ URL::to('/admin/p/modal/material/estados') }}/" + id;
            $('#tablaEstado').load(ruta);
            $('#modalEstado').modal('show');
        }


        function infoEstados(){
            Swal.fire({
                title: 'Tipo de Estados',
                html: "Pendiente" + "<br>"
                    + "Agrupado por Consolidador" + "<br>"
                    + "Cotizado por UCP" + "<br>"
                    + "Orden de Compra Generada" + "<br>"
                    + "Requisición Denegada por UCP" + "<br>"
                ,
                icon: 'info',
                showCancelButton: false,
                allowOutsideClick: false,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar',
            }).then((result) => {
                if (result.isConfirmed) {

                }
            })

        }




    </script>


@endsection

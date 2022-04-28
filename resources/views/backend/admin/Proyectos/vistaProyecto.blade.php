@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/bootstrap-select.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/jquery-ui.min.css') }}" type="text/css" rel="stylesheet" />
@stop

<style>
    table{
        /*Ajustar tablas*/
        table-layout:fixed;
    }

    .dropdown-menu {
        max-height: 280px;
        overflow-y: auto;
        width: 75%;
    }

</style>

<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6" style="margin-right: 10px;">
                <h1>Control Individual de Proyecto</h1>
            </div>
            <button type="button" onclick="modalGenerarPresupuesto()" class="btn btn-success btn-sm">
                <i class="fas fa-file-pdf"></i>
                Generar Presupuesto
            </button>
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
        <!--  <div class="col-sm-6 float-right">
              <div class="container-fluid">
                  <div class="card card-default">
                      <div class="card-header">
                          <h3 class="card-title"><strong>Requisiciones de Proyecto</strong></h3>
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
          </div>     -->



        <!-- ******************** MODULO DE INGENIERIA ************************ -->

        <!------------------ PRESUPUESTO DE PROYECTO ---------------->
        <div class="col-sm-6 float-right">
            <div class="container-fluid">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title"><strong>Presupuesto de Proyecto</strong></h3>
                        <button style="margin-left: 15px; float: right; margin-bottom: 10px" type="button" onclick="verModalPresupuesto()" class="btn btn-secondary btn-sm">
                            Agregar Partida
                        </button>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="tablaDatatablePresupuesto">
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
                                    <input  type="text" class="form-control" id="destino-requisicion-nuevo">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label>Necesidad:</label>
                                    <textarea class="form-control" id="necesidad-requisicion-nuevo" maxlength="15000" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <br>
                                    <button type="button" onclick="addAgregarFilaNueva()" class="btn btn-primary btn-sm float-right" style="margin-top:10px;">
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
                <button type="button" class="btn btn-primary" onclick="preguntaGuardarRequisicion()">Guardar</button>
            </div>
        </div>
    </div>
</div>



<!------------------ MODAL AGREGAR BITACORA ---------------->
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

<!------------------ MODAL EDITAR BITACORA ---------------->
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

<!------------------ MODAL PARA EDITAR REQUISICION ---------------->
<div class="modal fade" id="modalEditarRequisicion" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Requisición de Proyecto</h4>
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
                            <div class="col-md-2">
                                <div class="form-group">
                                    <br>
                                    <button type="button" onclick="addAgregarFilaNuevaEditar()" class="btn btn-primary btn-sm float-right" style="margin-top:10px;">
                                        <i class="fas fa-plus" title="Agregar"></i>&nbsp; Agregar</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <table class="table" id="matriz-requisicion-editar"  data-toggle="table">
                                <thead>
                                <tr>
                                    <th style="width: 3%">#</th>
                                    <th style="width: 6%">Cantidad</th>
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
                <button type="button" class="btn btn-primary" onclick="preguntaGuardarRequisicionEditar()">Guardar</button>
            </div>
        </div>
    </div>
</div>


<!-- ****** INGENIERIA MODALES ******* !-->
<!-- modal agregar nuevo presupuesto -->
<div class="modal fade" id="modalAgregarPresupuesto" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Agregar Presupuesto de Proyecto</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form id="formulario-presupuesto-nuevo">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tipo Partida:</label>
                                    <select id="select-partida-nuevo" class="form-control">
                                        <option value="1">Materiales</option>
                                        <option value="2">Herramientas (2% de Materiales)</option>
                                        <option value="3">Mano de obra (Por Administración)</option>
                                        <option value="4">Aporte Mano de Obra</option>
                                        <option value="5">Alquiler de Maquinaria</option>
                                        <option value="6">Transporte de Concreto Fresco</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Item:</label>
                                    <input  type="text" class="form-control" id="conteo-partida" value="{{ $conteoPartida }}" readonly>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Cantidad C/ Unidad *:</label>
                                    <input class="form-control" id="cantidad-partida-nuevo">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Partida *:</label>
                                    <input class="form-control" id="nombre-partida-nuevo" maxlength="300">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <br>
                                    <button type="button" onclick="addAgregarFilaPresupuestoNueva()" class="btn btn-primary btn-sm float-right" style="margin-top:10px;">
                                        <i class="fas fa-plus" title="Agregar"></i>&nbsp; Agregar</button>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <table class="table" id="matriz-presupuesto"  data-toggle="table">
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
                <button type="button" class="btn btn-primary" onclick="preguntaGuardarPresupuesto()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- modal editar presupuesto -->
<div class="modal fade" id="modalEditarPresupuesto" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Presupuesto de Proyecto</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form id="formulario-presupuesto-editar">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tipo Partida:</label>
                                    <select id="select-partida-editar" class="form-control">
                                        <option value="1">Materiales</option>
                                        <option value="2">Herramientas (2% de Materiales)</option>
                                        <option value="3">Mano de obra (Por Administración)</option>
                                        <option value="4">Aporte Mano de Obra</option>
                                        <option value="5">Alquiler de Maquinaria</option>
                                        <option value="6">Transporte de Concreto Fresco</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Item:</label>
                                    <input  type="text" class="form-control" id="conteo-partida-editar" readonly>
                                    <input  type="hidden" id="id-partida-editar">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Cantidad C/ Unidad *:</label>
                                    <input class="form-control" id="cantidad-partida-editar">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Partida *:</label>
                                    <input class="form-control" id="nombre-partida-editar" maxlength="300">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <br>
                                    <button type="button" onclick="addAgregarFilaPresupuestoEditar()" class="btn btn-primary btn-sm float-right" style="margin-top:10px;">
                                        <i class="fas fa-plus" title="Agregar"></i>&nbsp; Agregar</button>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <table class="table" id="matriz-presupuesto-editar"  data-toggle="table">
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
                <button type="button" class="btn btn-primary" onclick="preguntaEditarPresupuestoEditar()">Guardar</button>
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
    <script src="{{ asset('js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            document.getElementById("divcontenedor").style.display = "block";

            // variable global para setear input al buscar nuevo material
            window.txtContenedorGlobal = this;
            window.seguroBuscador = true;

            var id = {{ $id }};
            var ruta = "{{ URL::to('/admin/proyecto/vista/bitacora') }}/" + id;
            $('#tablaDatatableBitacora').load(ruta);

            // vista otros
            var rutaR = "{{ URL::to('/admin/proyecto/vista/requisicion') }}/" + id;
            $('#tablaDatatableRequisicion').load(rutaR);

            // vista ingeniera
            var rutaP = "{{ URL::to('/admin/proyecto/vista/presupuesto') }}/" + id;
            $('#tablaDatatablePresupuesto').load(rutaP);


            $(document).click(function(){
                $(".droplista").hide();
                $(".droplistaeditar").hide();
                $(".droplistapresupuesto").hide();
                $(".droplistapresupuestoEditar").hide();
            });

        });
    </script>

    <script type="text/javascript">

        function buscarMaterial(e){

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

                axios.post(url+'/proyecto/buscar/material', {
                    'query' : texto
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

        function buscarMaterialEditar(e){

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

                axios.post(url+'/proyecto/buscar/material', {
                    'query' : texto
                })
                    .then((response) => {
                        seguroBuscador = true;
                        $(row).each(function (index, element) {
                            $(this).find(".droplistaeditar").fadeIn();
                            $(this).find(".droplistaeditar").html(response.data);
                        });
                    })
                    .catch((error) => {
                        seguroBuscador = true;
                    });
            }
        }

        // al hacer clic en material buscado
        function modificarValor(edrop){

            // obtener texto del li
            let texto = $(edrop).text();
            // setear el input de la descripcion
            $(txtContenedorGlobal).val(texto);

            // agregar el id al atributo del input descripcion
            $(txtContenedorGlobal).attr('data-info', edrop.id);
            //$(txtContenedorGlobal).data("info");
        }

        function addAgregarFilaNueva(){

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
                "<input name='descripcionarray[]' data-info='0' class='form-control' style='width:100%' onkeyup='buscarMaterial(this)' maxlength='400'  type='text'>"+
                "<div class='droplista' style='position: absolute; z-index: 9;'></div>"+
                "</td>"+

                "<td>"+
                "<button type='button' class='btn btn-block btn-danger' onclick='borrarFilaRequiDetalle(this)'>Borrar</button>"+
                "</td>"+

                "</tr>";

            $("#matriz-requisicion tbody").append(markup);
        }

        function addAgregarFilaNuevaEditar(){

            var nFilas = $('#matriz-requisicion-editar >tbody >tr').length;
            nFilas += 1;

            // el id 0 significa que sera un nuevo registro a la hora de editar
            var markup = "<tr id='0'>"+

                "<td>"+
                "<p id='fila"+(nFilas)+"' class='form-control' style='max-width: 65px'>"+(nFilas)+"</p>"+
                "</td>"+

                "<td>"+
                "<input name='cantidadarrayeditar[]' maxlength='10' class='form-control' type='number'>"+
                "</td>"+

                "<td>"+
                "<input name='descripcionarrayeditar[]' data-info='0' class='form-control' style='width:100%' onkeyup='buscarMaterialEditar(this)' maxlength='400'  type='text'>"+
                "<div class='droplistaeditar' style='position: absolute; z-index: 9;'></div>"+
                "</td>"+

                "<td>"+
                "<button type='button' class='btn btn-block btn-danger' onclick='borrarFilaRequiEditar(this)'>Borrar</button>"+
                "</td>"+

                "</tr>";

            $("#matriz-requisicion-editar tbody").append(markup);
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

        // recargar tabla solo para bitacoras
        function recargarBitacora(){
            var id = {{ $id }};
            var ruta = "{{ URL::to('/admin/proyecto/vista/bitacora') }}/" + id;
            $('#tablaDatatableBitacora').load(ruta);
        }

        // recargar tabla de requisiciones
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

        // ver modal requisicion
        function verModalRequisicion(){
            document.getElementById("formulario-requisicion-nuevo").reset();
            $('#modalAgregarRequisicion').css('overflow-y', 'auto');
            $('#modalAgregarRequisicion').modal({backdrop: 'static', keyboard: false})
        }

        // ver modal detalle requisicon
        function verModalDetalleRequisicion(){
            document.getElementById("formulario-requisicion-deta-nuevo").reset();
            $('#modalAgregarRequisicionDeta').modal('show');
        }

        // registro de bitacora
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

        // preguntar si quiere borrar la bitacora
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

        // borrar la bitacora
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

        // ver modal para editar bitacora
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

        // editar registro de bitacora
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

            var hayRegistro = 0;
            var nRegistro = $('#matriz-requisicion >tbody >tr').length;
            let formData = new FormData();
            var id = {{ $id }};

            if (nRegistro > 0){

                var cantidad = $("input[name='cantidadarray[]']").map(function(){return $(this).val();}).get();
                var descripcion = $("input[name='descripcionarray[]']").map(function(){return $(this).val();}).get();
                var descripcionAtributo = $("input[name='descripcionarray[]']").map(function(){return $(this).attr("data-info");}).get();
                var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

                for(var a = 0; a < cantidad.length; a++){
                    let detalle = descripcionAtributo[a];
                    let datoCantidad = cantidad[a];

                    // identifica si el 0 es tipo number o texto
                    if(detalle == 0){
                        colorRojoTablaRequisicion(a);
                        alertaMensaje('info', 'No encontrado', 'En la Fila #' + (a+1) + " El material no se encuentra. Por favor buscar de nuevo el Material");
                        return;
                    }

                    if(datoCantidad === ''){
                        colorRojoTablaRequisicion(a);
                        toastr.error('Fila #' + (a+1) + ' Cantidad es requerida');
                        return;
                    }

                    if(!datoCantidad.match(reglaNumeroDecimal)) {
                        colorRojoTablaRequisicion(a);
                        toastr.error('Fila #' + (a+1) + ' Cantidad debe ser decimal y no negativo');
                        return;
                    }

                    if(datoCantidad <= 0){
                        colorRojoTablaRequisicion(a);
                        toastr.error('Fila #' + (a+1) + ' Cantidad no debe ser negativo');
                        return;
                    }

                    if(datoCantidad.length > 10){
                        colorRojoTablaRequisicion(a);
                        toastr.error('Fila #' + (a+1) + ' Cantidad máximo 10 caracteres');
                        return;
                    }
                }

                for(var b = 0; b < descripcion.length; b++){

                    var datoDescripcion = descripcion[b];

                    if(datoDescripcion === ''){
                        colorRojoTablaRequisicion(b);
                        toastr.error('Fila #' + (b+1) + ' la descripción es requerida');
                        return;
                    }

                    if(datoDescripcion.length > 400){
                        colorRojoTablaRequisicion(b);
                        toastr.error('Fila #' + (b+1) + ' la descripción tiene más de 400 caracteres');
                    }
                }

                // como tienen la misma cantidad de filas, podemos recorrer
                // todas las filas de una vez
                for(var p = 0; p < cantidad.length; p++){
                    formData.append('cantidad[]', cantidad[p]);
                    formData.append('datainfo[]', descripcionAtributo[p]);
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


        //******* VISTA EDITAR REQUISICION *********

        function vistaEditarRequisicion(id, conteo){

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

                        var infodetalle = response.data.detalle;
                        for (var i = 0; i < infodetalle.length; i++) {

                            var markup = "<tr id='"+infodetalle[i].id+"'>"+

                                "<td>"+
                                "<p id='fila"+(i+1)+"' class='form-control' style='max-width: 65px'>"+(i+1)+"</p>"+
                                "</td>"+

                                "<td>"+
                                "<input name='cantidadarrayeditar[]' value='"+infodetalle[i].cantidad+"' maxlength='10' class='form-control' type='number'>"+
                                "</td>"+

                                "<td>"+
                                "<input name='descripcionarrayeditar[]' disabled class='form-control' data-info='"+infodetalle[i].material_id+"' value='"+infodetalle[i].descripcion+"' style='width:100%' type='text'>"+
                                "<div class='droplistaeditar' style='position: absolute; z-index: 9;'></div>"+
                                "</td>"+

                                "<td>"+
                                "<button type='button' class='btn btn-block btn-danger' onclick='borrarFilaRequiEditar(this)'>Borrar</button>"+
                                "</td>"+

                                "</tr>";

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

            var hayRegistro = 0;
            var nRegistro = $('#matriz-requisicion-editar >tbody >tr').length;
            let formData = new FormData();

            if (nRegistro > 0){

                var cantidad = $("input[name='cantidadarrayeditar[]']").map(function(){return $(this).val();}).get();
                var descripcion = $("input[name='descripcionarrayeditar[]']").map(function(){return $(this).val();}).get();
                var descripcionAtributo = $("input[name='descripcionarrayeditar[]']").map(function(){return $(this).attr("data-info");}).get();
                var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

                for(var a = 0; a < cantidad.length; a++){
                    let detalle = descripcionAtributo[a];
                    let datoCantidad = cantidad[a];

                    // identifica si el 0 es tipo number o texto
                    if(detalle == 0){
                        colorRojoTablaRequisicionEditar(a);
                        alertaMensaje('info', 'No encontrado', 'En la Fila #' + (a+1) + " El material no se encuentra. Por favor buscar de nuevo el Material");
                        return;
                    }

                    if(datoCantidad === ''){
                        colorRojoTablaRequisicionEditar(a);
                        toastr.error('Fila #' + (a+1) + ' Cantidad es requerida');
                        return;
                    }

                    if(!datoCantidad.match(reglaNumeroDecimal)) {
                        colorRojoTablaRequisicionEditar(a);
                        toastr.error('Fila #' + (a+1) + ' Cantidad debe ser decimal y no negativo');
                        return;
                    }

                    if(datoCantidad <= 0){
                        colorRojoTablaRequisicionEditar(a);
                        toastr.error('Fila #' + (a+1) + ' Cantidad no debe ser negativo');
                        return;
                    }

                    if(datoCantidad.length > 10){
                        colorRojoTablaRequisicionEditar(a);
                        toastr.error('Fila #' + (a+1) + ' Cantidad máximo 10 caracteres');
                        return;
                    }
                }

                for(var b = 0; b < descripcion.length; b++){

                    var datoDescripcion = descripcion[b];

                    if(datoDescripcion === ''){
                        colorRojoTablaRequisicionEditar(b);
                        toastr.error('Fila #' + (b+1) + ' la descripción es requerida');
                        return;
                    }

                    if(datoDescripcion.length > 400){
                        colorRojoTablaRequisicionEditar(b);
                        toastr.error('Fila #' + (b+1) + ' la descripción tiene más de 400 caracteres');
                    }
                }

                // como tienen la misma cantidad de filas, podemos recorrer
                // todas las filas de una vez
                for(var p = 0; p < cantidad.length; p++){
                    // obtener el id de la fila, si el id fila es 0, significa que sera nuevo registro
                    var id = $("#matriz-requisicion-editar tr:eq("+(p+1)+")").attr('id');
                    formData.append('idarray[]', id);
                    formData.append('datainfo[]', descripcionAtributo[p]);
                    formData.append('cantidad[]', cantidad[p]);
                }

                hayRegistro = 1;
            }

            openLoading();
            formData.append('hayregistro', hayRegistro);
            formData.append('fecha', fecha);
            formData.append('destino', destino);
            formData.append('necesidad', necesidad);
            formData.append('idrequisicion', idrequisicion);

            axios.post(url+'/proyecto/vista/requisicion/editar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
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

        // vista a ver cotizacion
        function vistaCotizacion(id){
            // id tabla requisicion
            window.location.href="{{ url('/admin/proyecto/vista/cotizacion') }}/" + id;
        }

    </script>

    <script>

        // **** INGENIERIA  ****

        function verModalPresupuesto(){
            document.getElementById("formulario-presupuesto-nuevo").reset();
            $('#modalAgregarPresupuesto').css('overflow-y', 'auto');
            $('#modalAgregarPresupuesto').modal({backdrop: 'static', keyboard: false})
        }

        function addAgregarFilaPresupuestoNueva(){

            var nFilas = $('#matriz-presupuesto >tbody >tr').length;
            nFilas += 1;

            var markup = "<tr>"+

                "<td>"+
                "<p id='fila"+(nFilas)+"' class='form-control' style='max-width: 65px'>"+(nFilas)+"</p>"+
                "</td>"+

                "<td>"+
                "<input name='cantidadPresupuestoArray[]' maxlength='10' class='form-control' type='number'>"+
                "</td>"+

                "<td>"+
                "<input name='descripcionPresupuestoArray[]' data-infopresupuesto='0' class='form-control' style='width:100%' onkeyup='buscarMaterialPresupuesto(this)' maxlength='400'  type='text'>"+
                "<div class='droplistaPresupuesto' style='position: absolute; z-index: 9;'></div>"+
                "</td>"+

                "<td>"+
                "<button type='button' class='btn btn-block btn-danger' onclick='borrarFilaPresupuestoDetalle(this)'>Borrar</button>"+
                "</td>"+

                "</tr>";

            $("#matriz-presupuesto tbody").append(markup);
        }

        // borrar fila para tabla editar requisicion material
        function borrarFilaPresupuestoDetalle(elemento){
            var tabla = elemento.parentNode.parentNode;
            tabla.parentNode.removeChild(tabla);
            setearFilaPresupuesto()
        }

        // cambiar # de fila cada vez que se borra la fila de
        // tabla nuevo material
        function setearFilaPresupuesto(){

            var table = document.getElementById('matriz-presupuesto');
            var conteo = 0;
            for (var r = 1, n = table.rows.length; r < n; r++) {
                conteo +=1;
                var element = table.rows[r].cells[0].children[0];
                document.getElementById(element.id).innerHTML = ""+conteo;
            }
        }


        function buscarMaterialPresupuesto(e){

            // seguro para evitar errores de busqueda continua
            if(seguroBuscador){
                seguroBuscador = false;

                var row = $(e).closest('tr');
                txtContenedorGlobal = e;

                let texto = e.value;

                if(texto === ''){
                    // si se limpia el input, setear el atributo id
                    $(e).attr('data-infopresupuesto', 0);
                }

                axios.post(url+'/proyecto/buscar/material-presupuesto', {
                    'query' : texto
                })
                    .then((response) => {
                        seguroBuscador = true;
                        $(row).each(function (index, element) {
                            $(this).find(".droplistaPresupuesto").fadeIn();
                            $(this).find(".droplistaPresupuesto").html(response.data);
                        });
                    })
                    .catch((error) => {
                        seguroBuscador = true;
                    });
            }
        }

        // al hacer clic en material buscado
        function modificarValorPresupuesto(edrop){

            // obtener texto del li
            let texto = $(edrop).text();
            // setear el input de la descripcion
            $(txtContenedorGlobal).val(texto);

            // agregar el id al atributo del input descripcion
            $(txtContenedorGlobal).attr('data-infopresupuesto', edrop.id);

            //$(txtContenedorGlobal).data("info");
        }

        function preguntaGuardarPresupuesto(){
            colorBlancoTablaPresupuesto();

            Swal.fire({
                title: 'Guardar Presupuesto',
                text: "",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    verificarPresupuesto();
                }
            })
        }

        function colorBlancoTablaPresupuesto(){
            $("#matriz-presupuesto tbody tr").css('background', 'white');
        }

        // cambio de color de fila tabla a rojo
        function colorRojoTablaPresupuesto(index){
            $("#matriz-presupuesto tr:eq("+(index+1)+")").css('background', '#F1948A');
        }

        function verificarPresupuesto(){

            var cantidadPartida = document.getElementById('cantidad-partida-nuevo').value; // decimal
            var nombre = document.getElementById('nombre-partida-nuevo').value; // 300 caracteres
            var contador = document.getElementById('conteo-partida').value;
            var tipopartida = document.getElementById('select-partida-nuevo').value;

            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            if(cantidadPartida === ''){
                toastr.error('Cantidad C/ Unidad es requerido');
                return;
            }

            if(!cantidadPartida.match(reglaNumeroDecimal)) {
                toastr.error('Cantidad Partida debe ser decimal y no negativo');
                return;
            }

            if(cantidadPartida <= 0){
                toastr.error('Cantidad partida no debe ser negativo');
                return;
            }

            if(cantidadPartida.length > 10){
                toastr.error('Cantidad Partida debe tener máximo 10 caracteres');
                return;
            }

            if(nombre === ''){
                toastr.error('Partida es requerida');
                return;
            }

            if(nombre.length > 300){
                toastr.error('Partida debe tener máximo 300 caracteres');
                return;
            }

            var hayRegistro = 0;
            var nRegistro = $('#matriz-presupuesto > tbody >tr').length;
            let formData = new FormData();
            var id = {{ $id }}; // id proyecto

            if (nRegistro > 0){

                var cantidad = $("input[name='cantidadPresupuestoArray[]']").map(function(){return $(this).val();}).get();
                var descripcion = $("input[name='descripcionPresupuestoArray[]']").map(function(){return $(this).val();}).get();
                var descripcionAtributo = $("input[name='descripcionPresupuestoArray[]']").map(function(){return $(this).attr("data-infopresupuesto");}).get();

                for(var a = 0; a < cantidad.length; a++){

                    let detalle = descripcionAtributo[a];

                    let datoCantidad = cantidad[a];

                    // identifica si el 0 es tipo number o texto
                    if(detalle == 0){
                        colorRojoTablaPresupuesto(a);
                        alertaMensaje('info', 'No encontrado', 'En la Fila #' + (a+1) + " El material no se encuentra. Por favor buscar de nuevo el Material");
                        return;
                    }

                    if(datoCantidad === ''){
                        colorRojoTablaPresupuesto(a);
                        toastr.error('Fila #' + (a+1) + ' Cantidad es requerida');
                        return;
                    }

                    if(!datoCantidad.match(reglaNumeroDecimal)) {
                        colorRojoTablaPresupuesto(a);
                        toastr.error('Fila #' + (a+1) + ' Cantidad debe ser decimal y no negativo');
                        return;
                    }

                    if(datoCantidad <= 0){
                        colorRojoTablaPresupuesto(a);
                        toastr.error('Fila #' + (a+1) + ' Cantidad no debe ser negativo');
                        return;
                    }

                    if(datoCantidad.length > 10){
                        colorRojoTablaPresupuesto(a);
                        toastr.error('Fila #' + (a+1) + ' Cantidad máximo 10 caracteres');
                        return;
                    }
                }

                for(var b = 0; b < descripcion.length; b++){

                    var datoDescripcion = descripcion[b];

                    if(datoDescripcion === ''){
                        colorRojoTablaPresupuesto(b);
                        toastr.error('Fila #' + (b+1) + ' la descripción es requerida');
                        return;
                    }

                    if(datoDescripcion.length > 400){
                        colorRojoTablaPresupuesto(b);
                        toastr.error('Fila #' + (b+1) + ' la descripción tiene más de 400 caracteres');
                    }
                }

                // como tienen la misma cantidad de filas, podemos recorrer
                // todas las filas de una vez
                for(var p = 0; p < cantidad.length; p++){
                    formData.append('cantidad[]', cantidad[p]);
                    formData.append('datainfo[]', descripcionAtributo[p]);
                }

                hayRegistro = 1;
            }

            openLoading();
            formData.append('hayregistro', hayRegistro);
            formData.append('cantidadpartida', cantidadPartida);
            formData.append('nombrepartida', nombre);
            formData.append('contador', contador);
            formData.append('id', id);
            formData.append('tipopartida', tipopartida);

            axios.post(url+'/proyecto/agregar/presupuesto', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        $('#modalAgregarPresupuesto').modal('hide');
                        toastr.success('Registrado correctamente');
                        recargarPresupuesto();
                        limpiarPresupuesto(response.data.contador);
                    }
                    else{
                        toastr.error('error al crear presupuesto');
                    }
                })
                .catch((error) => {
                    toastr.error('error al crear presupuesto');
                    closeLoading();
                });
        }

        function recargarPresupuesto(){
            var id = {{ $id }};
            var rutaP = "{{ URL::to('/admin/proyecto/vista/presupuesto') }}/" + id;
            $('#tablaDatatablePresupuesto').load(rutaP);
        }

        function limpiarPresupuesto(contador){
            document.getElementById('conteo-partida').value = contador;
            document.getElementById('cantidad-partida-nuevo').value = '';
            document.getElementById('nombre-partida-nuevo').value = '';

            $("#matriz-presupuesto tbody tr").remove();
        }


        function informacionPresupuesto(id, numero, tipo){
            // tipo:  1- ver, 2-editar



            openLoading();
            document.getElementById("formulario-presupuesto-editar").reset();
            $("#matriz-presupuesto-editar tbody tr").remove();

            axios.post(url+'/proyecto/vista/presupuesto/informacion', {
                'id': id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        $('#id-partida-editar').val(response.data.info.id);
                        $('#cantidad-partida-editar').val(response.data.info.cantidadp);
                        $('#nombre-partida-editar').val(response.data.info.nombre);

                        $('#conteo-partida-editar').val(numero);

                        document.getElementById("select-partida-editar").value = response.data.info.tipo_partida;

                        var infodetalle = response.data.detalle;
                        for (var i = 0; i < infodetalle.length; i++) {

                            var markup = "<tr id='"+infodetalle[i].id+"'>"+

                                "<td>"+
                                "<p id='fila"+(i+1)+"' class='form-control' style='max-width: 65px'>"+(i+1)+"</p>"+
                                "</td>"+

                                "<td>"+
                                "<input name='cantidadPresupuestoEditar[]' value='"+infodetalle[i].cantidad+"' maxlength='10' class='form-control' type='number'>"+
                                "</td>"+

                                "<td>"+
                                "<input name='descripcionPresupuestoEditar[]' disabled class='form-control' data-infopresupuestoeditar='"+infodetalle[i].material_id+"' value='"+infodetalle[i].descripcion+"' style='width:100%' type='text'>"+
                                "<div class='dropListaPresupuestoEditar' style='position: absolute; z-index: 9;'></div>"+
                                "</td>"+

                                "<td>"+
                                "<button type='button' class='btn btn-block btn-danger' onclick='borrarFilaPresupuestoEditar(this)'>Borrar</button>"+
                                "</td>"+

                                "</tr>";

                            $("#matriz-presupuesto-editar tbody").append(markup);
                        }

                        $('#modalEditarPresupuesto').css('overflow-y', 'auto');
                        $('#modalEditarPresupuesto').modal({backdrop: 'static', keyboard: false})
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

        function borrarFilaPresupuestoEditar(elemento){
            var tabla = elemento.parentNode.parentNode;
            tabla.parentNode.removeChild(tabla);
            setearFilaPresupuestoEditar();
        }

        // cambiar # de fila cada vez que se borra la fila de
        // tabla nuevo material
        function setearFilaPresupuestoEditar(){

            var table = document.getElementById('matriz-presupuesto-editar');
            var conteo = 0;
            for (var r = 1, n = table.rows.length; r < n; r++) {
                conteo +=1;
                var element = table.rows[r].cells[0].children[0];
                document.getElementById(element.id).innerHTML = ""+conteo;
            }
        }

        function addAgregarFilaPresupuestoEditar(){

            var nFilas = $('#matriz-presupuesto-editar >tbody >tr').length;
            nFilas += 1;

            var markup = "<tr>"+

                "<td>"+
                "<p id='fila"+(nFilas)+"' class='form-control' style='max-width: 65px'>"+(nFilas)+"</p>"+
                "</td>"+

                "<td>"+
                "<input name='cantidadPresupuestoEditar[]' maxlength='10' class='form-control' type='number'>"+
                "</td>"+

                "<td>"+
                "<input name='descripcionPresupuestoEditar[]' data-infopresupuestoeditar='0' class='form-control' style='width:100%' onkeyup='buscarMaterialPresupuestoEditar(this)' maxlength='400'  type='text'>"+
                "<div class='dropListaPresupuestoEditar' style='position: absolute; z-index: 9;'></div>"+
                "</td>"+

                "<td>"+
                "<button type='button' class='btn btn-block btn-danger' onclick='borrarFilaPresupuestoEditar(this)'>Borrar</button>"+
                "</td>"+

                "</tr>";

            $("#matriz-presupuesto-editar tbody").append(markup);
        }

        function buscarMaterialPresupuestoEditar(e){

            // seguro para evitar errores de busqueda continua
            if(seguroBuscador){
                seguroBuscador = false;

                var row = $(e).closest('tr');
                txtContenedorGlobal = e;

                let texto = e.value;

                if(texto === ''){
                    // si se limpia el input, setear el atributo id
                    $(e).attr('data-infopresupuestoeditar', 0);
                }

                axios.post(url+'/proyecto/buscar/material-presupuesto-editar', {
                    'query' : texto
                })
                    .then((response) => {
                        seguroBuscador = true;
                        $(row).each(function (index, element) {
                            $(this).find(".droplistaPresupuestoEditar").fadeIn();
                            $(this).find(".droplistaPresupuestoEditar").html(response.data);
                        });
                    })
                    .catch((error) => {
                        seguroBuscador = true;
                    });
            }
        }

        function modificarValorPresupuestoEditar(edrop){

            // obtener texto del li
            let texto = $(edrop).text();
            // setear el input de la descripcion
            $(txtContenedorGlobal).val(texto);

            // agregar el id al atributo del input descripcion
            $(txtContenedorGlobal).attr('data-infopresupuestoeditar', edrop.id);
            //$(txtContenedorGlobal).data("info");
        }

        function preguntaEditarPresupuestoEditar(){
            colorBlancoTablaPresupuestoEditar();

            Swal.fire({
                title: 'Editar Presupuesto',
                text: "",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    verificarPresupuestoEditado();
                }
            })
        }

        function verificarPresupuestoEditado(){

            var tipopartida = document.getElementById('select-partida-editar').value;
            var idpartida = document.getElementById('id-partida-editar').value;
            var cantidadPartida = document.getElementById('cantidad-partida-editar').value; // decimal
            var nombre = document.getElementById('nombre-partida-editar').value; // 300 caracteres
            var reglaNumeroDecimal = /^[0-9]\d*(\.\d+)?$/;

            if(cantidadPartida === ''){
                toastr.error('Cantidad C/ Unidad es requerido');
                return;
            }

            if(!cantidadPartida.match(reglaNumeroDecimal)) {
                toastr.error('Cantidad Partida debe ser decimal y no negativo');
                return;
            }

            if(cantidadPartida <= 0){
                toastr.error('Cantidad partida no debe ser negativo');
                return;
            }

            if(cantidadPartida.length > 10){
                toastr.error('Cantidad Partida debe tener máximo 10 caracteres');
                return;
            }

            if(nombre === ''){
                toastr.error('Partida es requerida');
                return;
            }

            if(nombre.length > 300){
                toastr.error('Partida debe tener máximo 300 caracteres');
                return;
            }

            var hayRegistro = 0;
            var nRegistro = $('#matriz-presupuesto-editar >tbody >tr').length;
            let formData = new FormData();

            if (nRegistro > 0){

                var cantidad = $("input[name='cantidadPresupuestoEditar[]']").map(function(){return $(this).val();}).get();
                var descripcion = $("input[name='descripcionPresupuestoEditar[]']").map(function(){return $(this).val();}).get();
                var descripcionAtributo = $("input[name='descripcionPresupuestoEditar[]']").map(function(){return $(this).attr("data-infopresupuestoeditar");}).get();

                for(var a = 0; a < cantidad.length; a++){
                    let detalle = descripcionAtributo[a];
                    let datoCantidad = cantidad[a];

                    // identifica si el 0 es tipo number o texto
                    if(detalle == 0){
                        colorRojoTablaPresupuestoEditar(a);
                        alertaMensaje('info', 'No encontrado', 'En la Fila #' + (a+1) + " El material no se encuentra. Por favor buscar de nuevo el Material");
                        return;
                    }

                    if(datoCantidad === ''){
                        colorRojoTablaPresupuestoEditar(a);
                        toastr.error('Fila #' + (a+1) + ' Cantidad es requerida');
                        return;
                    }

                    if(!datoCantidad.match(reglaNumeroDecimal)) {
                        colorRojoTablaPresupuestoEditar(a);
                        toastr.error('Fila #' + (a+1) + ' Cantidad debe ser decimal y no negativo');
                        return;
                    }

                    if(datoCantidad <= 0){
                        colorRojoTablaPresupuestoEditar(a);
                        toastr.error('Fila #' + (a+1) + ' Cantidad no debe ser negativo');
                        return;
                    }

                    if(datoCantidad.length > 10){
                        colorRojoTablaPresupuestoEditar(a);
                        toastr.error('Fila #' + (a+1) + ' Cantidad máximo 10 caracteres');
                        return;
                    }
                }

                for(var b = 0; b < descripcion.length; b++){

                    var datoDescripcion = descripcion[b];

                    if(datoDescripcion === ''){
                        colorRojoTablaPresupuestoEditar(b);
                        toastr.error('Fila #' + (b+1) + ' la descripción es requerida');
                        return;
                    }

                    if(datoDescripcion.length > 400){
                        colorRojoTablaPresupuestoEditar(b);
                        toastr.error('Fila #' + (b+1) + ' la descripción tiene más de 400 caracteres');
                    }
                }

                // como tienen la misma cantidad de filas, podemos recorrer
                // todas las filas de una vez
                for(var p = 0; p < cantidad.length; p++){
                    // obtener el id de la fila, si el id fila es 0, significa que sera nuevo registro
                    var id = $("#matriz-presupuesto-editar tr:eq("+(p+1)+")").attr('id');
                    formData.append('idarray[]', id);
                    formData.append('datainfo[]', descripcionAtributo[p]);
                    formData.append('cantidad[]', cantidad[p]);
                }

                hayRegistro = 1;
            }

            openLoading();
            formData.append('hayregistro', hayRegistro);
            formData.append('cantidadpartida', cantidadPartida);
            formData.append('nombrepartida', nombre);
            formData.append('idpartida', idpartida);
            formData.append('tipopartida', tipopartida);

            axios.post(url+'/proyecto/vista/presupuesto/editar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Actualizado correctamente');
                        recargarPresupuesto();
                        $('#modalEditarPresupuesto').modal('hide');
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

        function colorBlancoTablaPresupuestoEditar(){
            $("#matriz-presupuesto-editar tbody tr").css('background', 'white');
        }

        // cambio de color de fila tabla a rojo
        function colorRojoTablaPresupuestoEditar(index){
            $("#matriz-presupuesto-editar tr:eq("+(index+1)+")").css('background', '#F1948A');
        }

        function modalGenerarPresupuesto(){

            Swal.fire({
                title: 'Generar Presupuesto',
                text: "",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                   generarPresupuesto();
                }
            })
        }

        function generarPresupuesto(){
            let id = {{ $id }};  // id proyecto
            window.open("{{ URL::to('admin/generar/pdf/presupuesto') }}/"+id);
        }


    </script>


@endsection

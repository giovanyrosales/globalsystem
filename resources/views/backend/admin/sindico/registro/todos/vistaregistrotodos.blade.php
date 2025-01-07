@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/estiloToggle.css') }}" type="text/css" rel="stylesheet" />
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

            <div class="col-md-6">
                <select id="select-tiposolicitudglobal" class="form-control" onchange="cargarTabla(this)">
                    <option value="0">Seleccionar opción</option>
                    @foreach($listado as $dd)
                        <option value="{{ $dd->id }}">{{ $dd->nombre }}</option>
                    @endforeach
                </select>
            </div>



        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">Listado de Registros</h3>
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


    <!-- ID GLOBAL PARA EDITAR -->
    <div class="form-group">
        <input type="hidden" id="id-global" class="form-control" />
        <input type="hidden" id="id-global-vista" class="form-control" />
    </div>


    <div class="modal fade" id="modal1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Registro</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo1">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label>Fecha Registro</label>
                                        <input type="date" id="fecha-general-1" autocomplete="off" class="form-control" />
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label>Fecha de Reunión</label>
                                        <input type="date" id="fecha-reunion-1" autocomplete="off" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Asesoría jurídica</label>
                                        <input type="text" id="asesoria-1" autocomplete="off" maxlength="500" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Estado</label>
                                        <select id="select-estado-1" class="form-control">

                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Fecha Informe</label>
                                        <input type="date" id="fecha-informe-1" autocomplete="off" class="form-control" />
                                    </div>


                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="actualizarCampos1()">Guardar</button>
                </div>
            </div>
        </div>
    </div>




    <div class="modal fade" id="modal2">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Registro</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label>Fecha Registro</label>
                                        <input type="date" id="fecha-general-2" autocomplete="off" class="form-control" />
                                    </div>

                                    <hr>


                                    <div class="form-group">
                                        <label>Fecha inscripción</label>
                                        <input type="date" id="fecha-inscripcion-2" autocomplete="off" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Ubicación</label>
                                        <input type="text" id="ubicacion-2" autocomplete="off" maxlength="500" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Zonas pendientes</label>
                                        <input type="text" id="zonas-pendientes-2" autocomplete="off" maxlength="500" class="form-control" />
                                    </div>


                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="actualizarCampos2()">Guardar</button>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="modal3">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Registro</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label>Fecha Registro</label>
                                        <input type="date" id="fecha-general-3" autocomplete="off" class="form-control" />
                                    </div>

                                    <hr>


                                    <div class="form-group">
                                        <label>Matricula</label>
                                        <input type="text" id="matricula-3" autocomplete="off" maxlength="500" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Fecha de inicio de trámites</label>
                                        <input type="date" id="fecha-inicio-3" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Estado</label>
                                        <select id="select-estado-3" class="form-control">
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Fecha de legalización</label>
                                        <input type="date" id="fecha-legalizacion-3" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Inmueble pendientes de legalizar</label>
                                        <input type="text" id="inmueble-3" autocomplete="off" maxlength="500" class="form-control" />
                                    </div>



                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="actualizarCampos3()">Guardar</button>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="modal4">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Registro</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label>Fecha Registro</label>
                                        <input type="date" id="fecha-general-4" autocomplete="off" class="form-control" />
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label>Inmueble</label>
                                        <input type="text" id="inmueble-4" autocomplete="off" maxlength="500" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Fecha realización de avalúo</label>
                                        <input type="date" id="fecha-realizacion-4" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Realizado por</label>
                                        <input type="text" id="realizado-por-4" maxlength="500" autocomplete="off" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Monto</label>
                                        <input type="number" id="monto-4" maxlength="10" min="0" max="1000000" autocomplete="off" class="form-control" />
                                    </div>


                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="actualizarCampos4()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal5">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Registro</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo5">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label>Fecha Registro</label>
                                        <input type="date" id="fecha-general-5" autocomplete="off" class="form-control" />
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label>Tipo de diligencia</label>
                                        <select id="select-diligencia-5" class="form-control">
                                        </select>
                                    </div>


                                    <div class="form-group">
                                        <label>Fecha de recepción de documentación</label>
                                        <input type="date" id="fecha-recepcion-5" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Nombre solicitante</label>
                                        <input type="text" id="nombre-solicitante-5" maxlength="500" autocomplete="off" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>DUI Solicitante</label>
                                        <input type="text" id="dui-solicitante-5" maxlength="50" autocomplete="off" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Fecha de revisión</label>
                                        <input type="date" id="fecha-revision-5" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Observación</label>
                                        <input type="text" id="observacion-5" maxlength="500" autocomplete="off" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Fecha de emisión</label>
                                        <input type="date" id="fecha-emision-5" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Fecha de entrega</label>
                                        <input type="date" id="fecha-entrega-5" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Recibe</label>
                                        <input type="text" id="recibe-5" maxlength="100" autocomplete="off" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input type="text" id="nombre-5" maxlength="100" autocomplete="off" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>DUI</label>
                                        <input type="text" id="dui-5" maxlength="50" autocomplete="off" class="form-control" />
                                    </div>


                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="actualizarCampos5()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal6">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Registro</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo6">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label>Fecha Registro</label>
                                        <input type="date" id="fecha-general-6" autocomplete="off" class="form-control" />
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label>Adesco</label>
                                        <select id="select-adesco-6" class="form-control">
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Estado</label>
                                        <select id="select-estado-6" class="form-control">
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Fecha de finalización</label>
                                        <input type="date" id="fecha-finalizacion-6" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Observación</label>
                                        <input type="text" id="observacion-6" maxlength="500" autocomplete="off" class="form-control" />
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="actualizarCampos6()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal7">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Registro</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo7">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label>Fecha Registro</label>
                                        <input type="date" id="fecha-general-7" autocomplete="off" class="form-control" />
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label>Tipo de diligencia</label>
                                        <select id="select-tipodiligencia-7" class="form-control">
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Fecha de recepción de solicitud</label>
                                        <input type="date" id="fecha-recepcion-7" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input type="text" id="nombre-7" maxlength="100" autocomplete="off" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>DUI</label>
                                        <input type="text" id="dui-7" maxlength="50" autocomplete="off" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Fecha de inspección</label>
                                        <input type="date" id="fecha-inspeccion-7" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Nombre del técnico que realiza la inspección</label>
                                        <input type="text" id="nombretecnico-7" maxlength="100" autocomplete="off" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Resultado de inspección</label>
                                        <input type="text" id="resultado-7" maxlength="500" autocomplete="off" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Fecha de emisión de resolución</label>
                                        <input type="date" id="fecha-emision-7" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Fecha de entrega de diligencia</label>
                                        <input type="date" id="fecha-diligencia-7" class="form-control" />
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="actualizarCampos7()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal8">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Registro</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo8">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label>Fecha Registro</label>
                                        <input type="date" id="fecha-general-8" autocomplete="off" class="form-control" />
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label>Fecha de recepción de solicitud</label>
                                        <input type="date" id="fecha-recepcion-8" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Nombre del encargado de recuperación de Mora</label>
                                        <input type="text" id="nombre-8" maxlength="100" autocomplete="off" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Informe del meses de recaudados</label>
                                        <input type="text" id="informemeses-8" maxlength="500" autocomplete="off" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Monto</label>
                                        <input type="number" id="monto-8" min="0" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Observaciones</label>
                                        <input type="text" id="observacion-8" maxlength="500" autocomplete="off" class="form-control" />
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="actualizarCampos8()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal9">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Registro</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo9">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label>Fecha Registro</label>
                                        <input type="date" id="fecha-general-9" autocomplete="off" class="form-control" />
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label>Fecha de recepción de informe</label>
                                        <input type="date" id="fecha-recepcion-9" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Encargado de remitir informe</label>
                                        <input type="text" id="encargado-9" maxlength="500" autocomplete="off" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Número de empresas para cobro judicial</label>
                                        <input type="text" id="numeroempresa-9" maxlength="50" autocomplete="off" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Número de inmuebles para cobro judicial</label>
                                        <input type="text" id="numeroinmueble-9" maxlength="50" autocomplete="off" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Monto total adeudado</label>
                                        <input type="number" id="monto-9" min="0" class="form-control" />
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="actualizarCampos9()">Guardar</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modal10">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Registro</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-nuevo10">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label>Fecha Registro</label>
                                        <input type="date" id="fecha-general-10" autocomplete="off" class="form-control" />
                                    </div>

                                    <hr>

                                    <div class="form-group">
                                        <label>Fecha de revisión de documentos contables</label>
                                        <input type="date" id="fecha-revision-10" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Total de documentos</label>
                                        <input type="text" id="totaldoc-10" maxlength="50" autocomplete="off" class="form-control" />
                                    </div>

                                    <div class="form-group">
                                        <label>Total de documentos aprobados</label>
                                        <input type="text" id="totaldocapro-10" maxlength="50" autocomplete="off" class="form-control" />
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="actualizarCampos10()">Guardar</button>
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



            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function cargarTabla(){
            var id = document.getElementById('select-tiposolicitudglobal').value;

            if(id == '0'){
                $('#tablaDatatable').load('');
                return
            }

            openLoading()
            var ruta = "{{ URL::to('/admin/sindico/registrotodos/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);
        }

        function recargar(){
            var id = document.getElementById('select-tiposolicitudglobal').value;
            openLoading()
            var ruta = "{{ URL::to('/admin/sindico/registrotodos/tabla') }}/" + id;
            $('#tablaDatatable').load(ruta);
        }

        function tipoVista(id, tipo){

            openLoading();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('tipo', tipo);

            axios.post(url+'/sindico/registrotodos/informacion', formData, {
            })
                .then((response) => {
                    closeLoading();

                    $('#id-global').val(id);
                    $('#id-global-vista').val(tipo);

                    if(tipo == '1'){

                        $('#fecha-general-1').val(response.data.info.fecha_general);
                        $('#fecha-reunion-1').val(response.data.info.fecha_reunion);
                        $('#asesoria-1').val(response.data.info.asesoria);
                        $('#fecha-informe-1').val(response.data.info.fecha_informe);

                        document.getElementById("select-estado-1").options.length = 0;

                        $.each(response.data.arrayEstado, function( key, val ){
                            if(response.data.info.id_estado === val.id){
                                $('#select-estado-1').append('<option value="' +val.id +'" selected="selected">'+val.nombre+'</option>');
                            }else{
                                $('#select-estado-1').append('<option value="' +val.id +'">'+val.nombre+'</option>');
                            }
                        });

                        $('#modal1').modal('show');
                    }
                    else if(tipo == '2'){

                        $('#fecha-general-2').val(response.data.info.fecha_general);
                        $('#fecha-inscripcion-2').val(response.data.info.fecha_inspeccion);
                        $('#ubicacion-2').val(response.data.info.ubicacion);
                        $('#zonas-pendientes-2').val(response.data.info.zonas_pendientes);

                        $('#modal2').modal('show');
                    }
                    else if(tipo == '3'){

                        $('#fecha-general-3').val(response.data.info.fecha_general);
                        $('#matricula-3').val(response.data.info.matricula);
                        $('#fecha-inicio-3').val(response.data.info.fecha_reunion);
                        $('#fecha-legalizacion-3').val(response.data.info.fecha_legalizacion);
                        $('#inmueble-3').val(response.data.info.inmueble);

                        document.getElementById("select-estado-3").options.length = 0;

                        $.each(response.data.arrayEstado, function( key, val ){
                            if(response.data.info.id_estado === val.id){
                                $('#select-estado-3').append('<option value="' +val.id +'" selected="selected">'+val.nombre+'</option>');
                            }else{
                                $('#select-estado-3').append('<option value="' +val.id +'">'+val.nombre+'</option>');
                            }
                        });

                        $('#modal3').modal('show');
                    }
                    else if(tipo == '4'){

                        $('#fecha-general-4').val(response.data.info.fecha_general);
                        $('#inmueble-4').val(response.data.info.inmueble);
                        $('#fecha-realizacion-4').val(response.data.info.fecha_legalizacion);
                        $('#realizado-por-4').val(response.data.info.realizado_por);
                        $('#monto-4').val(response.data.info.monto);

                        $('#modal4').modal('show');
                    }
                    else if(tipo == '5'){

                        $('#fecha-general-5').val(response.data.info.fecha_general);


                        document.getElementById("select-diligencia-5").options.length = 0;

                        $.each(response.data.arrayDiligencia, function( key, val ){
                            if(response.data.info.id_tipodeligencia === val.id){
                                $('#select-diligencia-5').append('<option value="' +val.id +'" selected="selected">'+val.nombre+'</option>');
                            }else{
                                $('#select-diligencia-5').append('<option value="' +val.id +'">'+val.nombre+'</option>');
                            }
                        });

                        $('#fecha-recepcion-5').val(response.data.info.fecha_recepcion);
                        $('#nombre-solicitante-5').val(response.data.info.nombre_solicitante);
                        $('#dui-solicitante-5').val(response.data.info.dui_solicitante);
                        $('#fecha-revision-5').val(response.data.info.fecha_revision);
                        $('#observacion-5').val(response.data.info.observacion);
                        $('#fecha-emision-5').val(response.data.info.fecha_emision_diligencia);
                        $('#fecha-entrega-5').val(response.data.info.fecha_entrega);
                        $('#recibe-5').val(response.data.info.recibe);
                        $('#nombre-5').val(response.data.info.nombre);
                        $('#dui-5').val(response.data.info.dui);

                        $('#modal5').modal('show');
                    }
                    else if(tipo == '6'){

                        $('#fecha-general-6').val(response.data.info.fecha_general);

                        document.getElementById("select-adesco-6").options.length = 0;
                        document.getElementById("select-estado-6").options.length = 0;

                        $.each(response.data.arrayAdesco, function( key, val ){
                            if(response.data.info.id_adesco === val.id){
                                $('#select-adesco-6').append('<option value="' +val.id +'" selected="selected">'+val.nombre+'</option>');
                            }else{
                                $('#select-adesco-6').append('<option value="' +val.id +'">'+val.nombre+'</option>');
                            }
                        });

                        $.each(response.data.arrayEstado, function( key, val ){
                            if(response.data.info.id_estado === val.id){
                                $('#select-estado-6').append('<option value="' +val.id +'" selected="selected">'+val.nombre+'</option>');
                            }else{
                                $('#select-estado-6').append('<option value="' +val.id +'">'+val.nombre+'</option>');
                            }
                        });

                        $('#fecha-finalizacion-6').val(response.data.info.fecha_finalizacion);
                        $('#observacion-6').val(response.data.info.observacion);

                        $('#modal6').modal('show');
                    }
                    else if(tipo == '7'){

                        $('#fecha-general-7').val(response.data.info.fecha_general);

                        document.getElementById("select-tipodiligencia-7").options.length = 0;


                        $.each(response.data.arrayDiligencia, function( key, val ){
                            if(response.data.info.id_tipodeligencia === val.id){
                                $('#select-tipodiligencia-7').append('<option value="' +val.id +'" selected="selected">'+val.nombre+'</option>');
                            }else{
                                $('#select-tipodiligencia-7').append('<option value="' +val.id +'">'+val.nombre+'</option>');
                            }
                        });


                        $('#fecha-recepcion-7').val(response.data.info.fecha_recepcion);
                        $('#nombre-7').val(response.data.info.nombre);
                        $('#dui-7').val(response.data.info.dui);
                        $('#fecha-inspeccion-7').val(response.data.info.fecha_inspeccion);
                        $('#nombretecnico-7').val(response.data.info.nombre_tecnico);
                        $('#resultado-7').val(response.data.info.resultado);
                        $('#fecha-emision-7').val(response.data.info.fecha_emision_diligencia);
                        $('#fecha-diligencia-7').val(response.data.info.fecha_entrega);
                        $('#modal7').modal('show');
                    }
                    else if(tipo == '8'){

                        $('#fecha-general-8').val(response.data.info.fecha_general);

                        $('#fecha-recepcion-8').val(response.data.info.fecha_recepcion);
                        $('#nombre-8').val(response.data.info.nombre_tecnico);
                        $('#informemeses-8').val(response.data.info.informe_meses);
                        $('#monto-8').val(response.data.info.monto);
                        $('#observacion-8').val(response.data.info.observacion);

                        $('#modal8').modal('show');
                    }
                    else if(tipo == '9'){

                        $('#fecha-general-9').val(response.data.info.fecha_general);

                        $('#fecha-recepcion-9').val(response.data.info.fecha_recepcion);
                        $('#encargado-9').val(response.data.info.asesoria);
                        $('#numeroempresa-9').val(response.data.info.numero_empresas);
                        $('#numeroinmueble-9').val(response.data.info.numero_inmuebles);
                        $('#monto-9').val(response.data.info.monto);


                        $('#modal9').modal('show');
                    }
                    else if(tipo == '10'){

                        $('#fecha-general-10').val(response.data.info.fecha_general);

                        $('#fecha-revision-10').val(response.data.info.fecha_revision);
                        $('#totaldoc-10').val(response.data.info.total_doc);
                        $('#totaldocapro-10').val(response.data.info.total_doc_aprobados);

                        $('#modal10').modal('show');
                    }
                    else{
                        toastr.error('Información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('Información no encontrada');
                });
        }


        function actualizarCampos1(){
            openLoading();

            var idGlobal = document.getElementById('id-global').value;
            var idGlobalVista = document.getElementById('id-global-vista').value;
            var formData = new FormData();

            var fechaRegistro = document.getElementById('fecha-general-1').value;
            var fechaReunion = document.getElementById('fecha-reunion-1').value;
            var asesoria = document.getElementById('asesoria-1').value;
            var fechaInforme = document.getElementById('fecha-informe-1').value;
            var estado = document.getElementById('select-estado-1').value;

            formData.append('idGlobal', idGlobal);
            formData.append('idTipoVista', idGlobalVista);
            formData.append('fechaRegistro', fechaRegistro);
            formData.append('fechaReunion', fechaReunion);
            formData.append('asesoria', asesoria);
            formData.append('fechaInforme', fechaInforme);
            formData.append('estado', estado);

            axios.post(url+'/sindico/registrotodos/editar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Actualizado');
                        $('#modal1').modal('hide');
                        recargar();
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

        function actualizarCampos2(){

            openLoading();

            var idGlobal = document.getElementById('id-global').value;
            var idGlobalVista = document.getElementById('id-global-vista').value;
            var formData = new FormData();

            var fechaRegistro = document.getElementById('fecha-general-2').value;
            var fechaInscripcion = document.getElementById('fecha-inscripcion-2').value;
            var ubicacion = document.getElementById('ubicacion-2').value;
            var zonasPendientes = document.getElementById('zonas-pendientes-2').value;

            formData.append('idGlobal', idGlobal);
            formData.append('idTipoVista', idGlobalVista);
            formData.append('fechaRegistro', fechaRegistro);
            formData.append('fechaInscripcion', fechaInscripcion);
            formData.append('ubicacion', ubicacion);
            formData.append('zonasPendientes', zonasPendientes);

            axios.post(url+'/sindico/registrotodos/editar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Actualizado');
                        $('#modal2').modal('hide');
                        recargar();
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

        function actualizarCampos3(){

            openLoading();

            var idGlobal = document.getElementById('id-global').value;
            var idGlobalVista = document.getElementById('id-global-vista').value;
            var formData = new FormData();

            var fechaRegistro = document.getElementById('fecha-general-3').value;
            var matricula = document.getElementById('matricula-3').value;
            var fechaInicio = document.getElementById('fecha-inicio-3').value;
            var estado = document.getElementById('select-estado-3').value;
            var fechaLegalizacion = document.getElementById('fecha-legalizacion-3').value;
            var inmueble = document.getElementById('inmueble-3').value;

            formData.append('idGlobal', idGlobal);
            formData.append('idTipoVista', idGlobalVista);
            formData.append('fechaRegistro', fechaRegistro);
            formData.append('matricula', matricula);
            formData.append('fechaInicio', fechaInicio);
            formData.append('estado', estado);
            formData.append('fechaLegalizacion', fechaLegalizacion);
            formData.append('inmueble', inmueble);

            axios.post(url+'/sindico/registrotodos/editar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Actualizado');
                        $('#modal3').modal('hide');
                        recargar();
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

        function actualizarCampos4(){

            openLoading();

            var idGlobal = document.getElementById('id-global').value;
            var idGlobalVista = document.getElementById('id-global-vista').value;
            var formData = new FormData();

            var fechaRegistro = document.getElementById('fecha-general-4').value;
            var inmueble = document.getElementById('inmueble-4').value;
            var fechaRealizacion = document.getElementById('fecha-realizacion-4').value;
            var realizadoPor = document.getElementById('realizado-por-4').value;
            var montoAvaluo = document.getElementById('monto-4').value;

            formData.append('idGlobal', idGlobal);
            formData.append('idTipoVista', idGlobalVista);
            formData.append('fechaRegistro', fechaRegistro);
            formData.append('inmueble', inmueble);
            formData.append('fechaRealizacion', fechaRealizacion);
            formData.append('realizadoPor', realizadoPor);
            formData.append('monto', montoAvaluo);

            axios.post(url+'/sindico/registrotodos/editar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Actualizado');
                        $('#modal4').modal('hide');
                        recargar();
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

        function actualizarCampos5(){

            openLoading();

            var idGlobal = document.getElementById('id-global').value;
            var idGlobalVista = document.getElementById('id-global-vista').value;
            var formData = new FormData();

            var fechaRegistro = document.getElementById('fecha-general-5').value;
            var tipoDeligencia = document.getElementById('select-diligencia-5').value;
            var fechaRecepcion = document.getElementById('fecha-recepcion-5').value;
            var nombreSolicitante = document.getElementById('nombre-solicitante-5').value;
            var duiSolicitante = document.getElementById('dui-solicitante-5').value;
            var fechaRevision = document.getElementById('fecha-revision-5').value;
            var observacion = document.getElementById('observacion-5').value;
            var fechaEmision = document.getElementById('fecha-emision-5').value;
            var fechaEntrega = document.getElementById('fecha-entrega-5').value;
            var recibe = document.getElementById('recibe-5').value;
            var nombre = document.getElementById('nombre-5').value;
            var dui = document.getElementById('dui-5').value;

            formData.append('idGlobal', idGlobal);
            formData.append('idTipoVista', idGlobalVista);
            formData.append('fechaRegistro', fechaRegistro);
            formData.append('tipoDeligencia', tipoDeligencia);
            formData.append('fechaRecepcion', fechaRecepcion);
            formData.append('nombreSolicitante', nombreSolicitante);
            formData.append('duiSolicitante', duiSolicitante);
            formData.append('fechaRevision', fechaRevision);
            formData.append('observacion', observacion);
            formData.append('fechaEmision', fechaEmision);
            formData.append('fechaEntrega', fechaEntrega);
            formData.append('recibe', recibe);
            formData.append('nombre', nombre);
            formData.append('dui', dui);

            axios.post(url+'/sindico/registrotodos/editar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Actualizado');
                        $('#modal5').modal('hide');
                        recargar();
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

        function actualizarCampos6(){

            openLoading();

            var idGlobal = document.getElementById('id-global').value;
            var idGlobalVista = document.getElementById('id-global-vista').value;
            var formData = new FormData();

            var fechaRegistro = document.getElementById('fecha-general-6').value;
            var adesco = document.getElementById('select-adesco-6').value;
            var estadoProceso = document.getElementById('select-estado-6').value;
            var fechaFinalizacion = document.getElementById('fecha-finalizacion-6').value;
            var observacion = document.getElementById('observacion-6').value;

            formData.append('idGlobal', idGlobal);
            formData.append('idTipoVista', idGlobalVista);
            formData.append('fechaRegistro', fechaRegistro);
            formData.append('adesco', adesco);
            formData.append('estado', estadoProceso);
            formData.append('fechaFinalizacion', fechaFinalizacion);
            formData.append('observacion', observacion);

            axios.post(url+'/sindico/registrotodos/editar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Actualizado');
                        $('#modal6').modal('hide');
                        recargar();
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

        function actualizarCampos7(){

            openLoading();

            var fechaRegistro = document.getElementById('fecha-general-7').value;
            var idGlobal = document.getElementById('id-global').value;
            var idGlobalVista = document.getElementById('id-global-vista').value;

            var tipoDiligencia = document.getElementById('select-tipodiligencia-7').value;
            var fechaRecepcion = document.getElementById('fecha-recepcion-7').value;
            var nombre = document.getElementById('nombre-7').value;
            var dui = document.getElementById('dui-7').value;
            var fechaInspeccion = document.getElementById('fecha-inspeccion-7').value;
            var nombreTecnico = document.getElementById('nombretecnico-7').value;
            var resultadoInspeccion = document.getElementById('resultado-7').value;
            var fechaEmision = document.getElementById('fecha-emision-7').value;
            var fechaDiligencia = document.getElementById('fecha-diligencia-7').value;

            var formData = new FormData();

            formData.append('idGlobal', idGlobal);
            formData.append('idTipoVista', idGlobalVista);
            formData.append('fechaRegistro', fechaRegistro);
            formData.append('tipoDiligencia', tipoDiligencia);
            formData.append('fechaRecepcion', fechaRecepcion);
            formData.append('nombre', nombre);
            formData.append('dui', dui);
            formData.append('fechaInspeccion', fechaInspeccion);
            formData.append('nombreTecnico', nombreTecnico);
            formData.append('resultadoInspeccion', resultadoInspeccion);
            formData.append('fechaEmision', fechaEmision);
            formData.append('fechaDiligencia', fechaDiligencia);

            axios.post(url+'/sindico/registrotodos/editar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Actualizado');
                        $('#modal7').modal('hide');
                        recargar();
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

        function actualizarCampos8(){

            openLoading();

            var fechaRegistro = document.getElementById('fecha-general-8').value;
            var idGlobal = document.getElementById('id-global').value;
            var idGlobalVista = document.getElementById('id-global-vista').value;

            var fechaRecepcion = document.getElementById('fecha-recepcion-8').value;
            var nombreEncargado = document.getElementById('nombre-8').value;
            var informeMeses = document.getElementById('informemeses-8').value;
            var monto = document.getElementById('monto-8').value;
            var observaciones = document.getElementById('observacion-8').value;

            var formData = new FormData();

            formData.append('idGlobal', idGlobal);
            formData.append('idTipoVista', idGlobalVista);
            formData.append('fechaRegistro', fechaRegistro);
            formData.append('fechaRecepcion', fechaRecepcion);
            formData.append('nombreEncargado', nombreEncargado);
            formData.append('informeMeses', informeMeses);
            formData.append('monto', monto);
            formData.append('observaciones', observaciones);

            axios.post(url+'/sindico/registrotodos/editar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Actualizado');
                        $('#modal8').modal('hide');
                        recargar();
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

        function actualizarCampos9(){

            openLoading();

            var fechaRegistro = document.getElementById('fecha-general-9').value;
            var idGlobal = document.getElementById('id-global').value;
            var idGlobalVista = document.getElementById('id-global-vista').value;

            var fechaRecepcion = document.getElementById('fecha-recepcion-9').value;
            var encargadoRemitir = document.getElementById('encargado-9').value;
            var numeroEmpresa = document.getElementById('numeroempresa-9').value;
            var numeroInmueble = document.getElementById('numeroinmueble-9').value;
            var montoTotal = document.getElementById('monto-9').value;

            var formData = new FormData();

            formData.append('idGlobal', idGlobal);
            formData.append('idTipoVista', idGlobalVista);
            formData.append('fechaRegistro', fechaRegistro);
            formData.append('fechaRecepcion', fechaRecepcion);
            formData.append('encargadoRemitir', encargadoRemitir);
            formData.append('numeroEmpresa', numeroEmpresa);
            formData.append('numeroInmueble', numeroInmueble);
            formData.append('montoTotal', montoTotal);

            axios.post(url+'/sindico/registrotodos/editar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Actualizado');
                        $('#modal9').modal('hide');
                        recargar();
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

        function actualizarCampos10(){

            openLoading();

            var fechaRegistro = document.getElementById('fecha-general-10').value;
            var idGlobal = document.getElementById('id-global').value;
            var idGlobalVista = document.getElementById('id-global-vista').value;

            var fechaRevision = document.getElementById('fecha-revision-10').value;
            var totalDoc = document.getElementById('totaldoc-10').value;
            var totalDocApro = document.getElementById('totaldocapro-10').value;

            var formData = new FormData();

            formData.append('idGlobal', idGlobal);
            formData.append('idTipoVista', idGlobalVista);
            formData.append('fechaRegistro', fechaRegistro);
            formData.append('fechaRevision', fechaRevision);
            formData.append('totalDoc', totalDoc);
            formData.append('totalDocApro', totalDocApro);

            axios.post(url+'/sindico/registrotodos/editar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Actualizado');
                        $('#modal10').modal('hide');
                        recargar();
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
        //*********************************************************


        function infoBorrar(id){

            Swal.fire({
                title: 'Borrar',
                text: "",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Borrar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    borrar(id)
                }
            })
        }

        function borrar(id){

            openLoading();
            var formData = new FormData();
            formData.append('id', id);

            axios.post(url+'/sindico/registrotodos/borrar', formData, {
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){
                        toastr.success('Borrado');
                        recargar();
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




    </script>


@endsection

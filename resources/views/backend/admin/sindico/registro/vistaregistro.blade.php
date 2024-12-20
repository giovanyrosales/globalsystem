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

    <section class="content" style="margin-top: 25px">
        <div class="container-fluid">
            <div class="card card-gray-dark">
                <div class="card-header">
                    <h3 class="card-title">Registro</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="form-group" style="padding: 16px">
                                <label class="control-label">Tipo de Solicitud: </label>
                                <select id="select-tiposolicitud" class="form-control" style="max-width: 50%">
                                    @foreach($arrayTipoSoli as $item)
                                        <option value="{{$item->id}}">{{ $item->nombre }}</option>
                                    @endforeach
                                </select>

                                <div style="margin-top: 20px">
                                    <button type="button" onclick="modalAgregar()" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus-square"></i>
                                        Nuevo registro
                                    </button>
                                </div>

                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



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
                                            @foreach($arrayEstados as $item)
                                                <option value="{{$item->id}}">{{ $item->nombre }}</option>
                                            @endforeach
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
                    <button type="button" class="btn btn-primary btn-sm" onclick="nuevoRegistro1()">Guardar</button>
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
                    <button type="button" class="btn btn-primary btn-sm" onclick="nuevoRegistro2()">Guardar</button>
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
                                            @foreach($arrayEstados as $item)
                                                <option value="{{$item->id}}">{{ $item->nombre }}</option>
                                            @endforeach
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
                    <button type="button" class="btn btn-primary btn-sm" onclick="nuevoRegistro3()">Guardar</button>
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
                    <button type="button" class="btn btn-primary btn-sm" onclick="nuevoRegistro4()">Guardar</button>
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
                                        <label>Tipo Deligencia</label>
                                        <select id="select-deligencia-5" class="form-control">
                                            @foreach($arrayTipoDeligencia as $item)
                                                <option value="{{$item->id}}">{{ $item->nombre }}</option>
                                            @endforeach
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
                    <button type="button" class="btn btn-primary btn-sm" onclick="nuevoRegistro5()">Guardar</button>
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
                                        <label>Adesco</label>
                                        <select id="select-adesco-6" class="form-control">
                                            @foreach($arrayAdesco as $item)
                                                <option value="{{$item->id}}">{{ $item->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Estado</label>
                                        <select id="select-estado-6" class="form-control">
                                            @foreach($arrayEstados as $item)
                                                <option value="{{$item->id}}">{{ $item->nombre }}</option>
                                            @endforeach
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
                    <button type="button" class="btn btn-primary btn-sm" onclick="nuevoRegistro6()">Guardar</button>
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
                                        <label>Tipo de diligencia</label>
                                        <select id="select-tipodiligencia-7" class="form-control">
                                            @foreach($arrayTipoDeligencia as $item)
                                                <option value="{{$item->id}}">{{ $item->nombre }}</option>
                                            @endforeach
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
                    <button type="button" class="btn btn-primary btn-sm" onclick="nuevoRegistro7()">Guardar</button>
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
                    <button type="button" class="btn btn-primary btn-sm" onclick="nuevoRegistro8()">Guardar</button>
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
                    <button type="button" class="btn btn-primary btn-sm" onclick="nuevoRegistro9()">Guardar</button>
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
                    <button type="button" class="btn btn-primary btn-sm" onclick="nuevoRegistro10()">Guardar</button>
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

    <script src="{{ asset('js/alertaPersonalizada.js') }}"></script>

    <script>


        function modalAgregar(){
            var id = document.getElementById('select-tiposolicitud').value;

            const numeros = Array.from({ length: 10 }, (_, index) => index + 1);

            numeros.forEach((numero) => {
                let idFormulario = "formulario-nuevo" + numero;
                //document.getElementById(idFormulario).reset();
            });

            if(id === '1'){
                $('#modal1').modal('show');
            }else if(id === '2'){
                $('#modal2').modal('show');
            }else if(id === '3'){
                $('#modal3').modal('show');
            }else if(id === '4'){
                $('#modal4').modal('show');
            }else if(id === '5'){
                $('#modal5').modal('show');
            }else if(id === '6'){
                $('#modal6').modal('show');
            }else if(id === '7'){
                $('#modal7').modal('show');
            }else if(id === '8'){
                $('#modal8').modal('show');
            }else if(id === '9'){
                $('#modal9').modal('show');
            }else if(id === '10'){
                $('#modal10').modal('show');
            }

        }
    </script>


    @extends('backend.admin.sindico.registro.codigoregistro')


@endsection

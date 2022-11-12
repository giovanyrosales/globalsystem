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
                                    class="button button-3d button-rounded button-pill button-small" onclick="infoModalSaldo()">
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



    </script>


@endsection

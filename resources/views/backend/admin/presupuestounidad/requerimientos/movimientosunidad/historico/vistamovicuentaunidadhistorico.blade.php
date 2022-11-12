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

<div id="divcontenedor" style="display: none">

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-6">
                <div class="col-sm-5">
                    <h1>Histórico</h1>
                </div>

            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Listado</h3>
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

    <!-- CARGAR REFORMA -->
    <div class="modal fade" id="modalReforma" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Agregar Documento</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form id="formulario-repuesto">
                        <div class="card-body">

                            <div class="form-group">
                                <label>Reforma</label>
                                <input id="id-reforma" type="hidden">
                                <input type="file" id="documento" class="form-control" accept="image/jpeg, image/jpg, image/png, .pdf"/>
                            </div>

                        </div>
                    </form>

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarDocumento()">Guardar</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalAgregar">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nuevo Movimiento</h4>
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
                                        <input type="hidden" class="form-control" id="id-control">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Fecha:</label>
                                        <input type="text" disabled class="form-control" id="fecha-control">
                                    </div>

                                    <label>Cuenta a Aumentar</label>
                                    <hr style="height:1px;border:none;color:#333;background-color:#333;">

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Objeto Específico</label>
                                            <input type="text" disabled class="form-control" id="objeto-aumenta-control">
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Cuenta</label>
                                            <input type="text" disabled class="form-control" id="cuenta-aumenta-control">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Saldo Aumentar</label>
                                            <input type="text" disabled placeholder="0.00" class="form-control" id="saldo-aumentar-control">
                                        </div>
                                    </div>


                                    <label>Cuenta a Disminuir</label>
                                    <hr style="height:1px;border:none;color:#333;background-color:#333;">

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Objeto Específico</label>
                                            <input type="text" disabled class="form-control" id="objeto-baja-control">
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Cuenta</label>
                                            <input type="text" disabled class="form-control" id="cuenta-baja-control">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Saldo Restante Actualmente</label>
                                            <p style="color: red">Se resta también Saldo Retenido</p>
                                            <input type="text" disabled placeholder="0.00" class="form-control" id="saldo-restante-actual">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Saldo Disminuir</label>
                                            <input type="text" disabled placeholder="0.00" class="form-control" id="saldo-baja-control">
                                        </div>
                                    </div>

                                    <hr style="height:1px;border:none;color:#333;background-color:#333;">

                                    <div class="form-group">
                                        <label>Reforma</label>
                                        <input type="file" id="documento-control" class="form-control" accept="image/jpeg, image/jpg, image/png, .pdf"/>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-danger" onclick="verificarDenegar()">Denegar</button>
                    <button type="button" class="btn btn-success" onclick="verificarAutorizar()">Autorizar</button>
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
            let id = {{ $id }}; // id PRESUP UNIDAD
            var ruta = "{{ URL::to('/admin/p/movicuentaunidad/tablahistorico') }}/" + id;
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";

        });
    </script>


@endsection

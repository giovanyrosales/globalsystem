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
            <div class="row mb-10">
                <div class="col-sm-10">
                    <h1>Historial de Descargos Directos</h1>
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

    <!-- PROVEEDOR -->

    <div class="modal fade" id="modalProveedor">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tipo Descargo: Proveedor</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-proveedor">
                        <div class="card-body">

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Fecha</label>
                                            <input type="text" class="form-control" disabled id="fecha-1">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Número orden</label>
                                            <input type="text" class="form-control" disabled id="numorden-1">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Número de Acuerdo</label>
                                            <input type="text" class="form-control" disabled id="numacuerdo-1">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Cuenta Unidad</label>
                                            <input type="text" class="form-control" disabled id="cuentaunidad-1">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Saldo Anterior antes de Bajar</label>
                                            <input type="text" class="form-control" disabled id="saldoanterior-1">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Proveedor</label>
                                            <input type="text" class="form-control" disabled id="proveedor-1">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Línea de Trabajo</label>
                                            <input type="text" class="form-control" disabled id="lineatrabajo-1">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Fuente de Financiamiento</label>
                                            <input type="text" class="form-control" disabled id="fuentef-1">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Concepto</label>
                                            <input type="text" class="form-control" disabled id="concepto-1">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Monto Descontar</label>
                                            <input type="text" class="form-control" disabled id="montodescontar-1">
                                        </div>


                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- PROYECTO -->

    <div class="modal fade" id="modalProyecto">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tipo Descargo: Proyecto</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-proyecto">
                        <div class="card-body">

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Fecha</label>
                                            <input type="text" class="form-control" disabled id="fecha-2">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Número orden</label>
                                            <input type="text" class="form-control" disabled id="numorden-2">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Número de Acuerdo</label>
                                            <input type="text" class="form-control" disabled id="numacuerdo-2">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Cuenta Proyecto</label>
                                            <input type="text" class="form-control" disabled id="cuentaproyecto-2">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Saldo Anterior antes de Bajar</label>
                                            <input type="text" class="form-control" disabled id="saldoanterior-2">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Línea de Trabajo</label>
                                            <input type="text" class="form-control" disabled id="lineatrabajo-2">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Fuente de Financiamiento</label>
                                            <input type="text" class="form-control" disabled id="fuentef-2">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Concepto</label>
                                            <input type="text" class="form-control" disabled id="concepto-2">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Monto Descontar</label>
                                            <input type="text" class="form-control" disabled id="montodescontar-2">
                                        </div>


                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- CONTRIBUCIÓN -->

    <div class="modal fade" id="modalContribucion">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tipo Descargo: Contribución</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario-contribucion">
                        <div class="card-body">

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Fecha</label>
                                            <input type="text" class="form-control" disabled id="fecha-3">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Número orden</label>
                                            <input type="text" class="form-control" disabled id="numorden-3">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Número de Acuerdo</label>
                                            <input type="text" class="form-control" disabled id="numacuerdo-3">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Cuenta Unidad</label>
                                            <input type="text" class="form-control" disabled id="cuentaunidad-3">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Saldo Anterior antes de Bajar</label>
                                            <input type="text" class="form-control" disabled id="saldoanterior-3">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Beneficiario</label>
                                            <input type="text" class="form-control" disabled id="beneficiario-3">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Línea de Trabajo</label>
                                            <input type="text" class="form-control" disabled id="lineatrabajo-3">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Fuente de Financiamiento</label>
                                            <input type="text" class="form-control" disabled id="fuentef-3">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Concepto</label>
                                            <input type="text" class="form-control" disabled id="concepto-3">
                                        </div>

                                        <div class="form-group" style="margin-top: 15px">
                                            <label>Monto Descontar</label>
                                            <input type="text" class="form-control" disabled id="montodescontar-3">
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
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

            var ruta = "{{ URL::to('/admin/descargos/directos/historial/tabla') }}";
            $('#tablaDatatable').load(ruta);

            document.getElementById("divcontenedor").style.display = "block";
        });
    </script>

    <script>

        function informacion(id){

            openLoading();

            document.getElementById("formulario-proveedor").reset();
            document.getElementById("formulario-proyecto").reset();
            document.getElementById("formulario-contribucion").reset();

            axios.post(url+'/descargos/directos/historial/informacion', {
                'id' : id
            })
                .then((response) => {
                    closeLoading();
                    if(response.data.success === 1){

                        if(response.data.datos.tipodescargo === 1){

                            $('#fecha-1').val(response.data.fecha);
                            $('#numorden-1').val(response.data.datos.numero_orden);
                            $('#numacuerdo-1').val(response.data.datos.numero_acuerdo);
                            $('#cuentaunidad-1').val(response.data.cuentaunidad);
                            $('#saldoanterior-1').val(response.data.saldocuentaunidad);
                            $('#proveedor-1').val(response.data.proveedor);
                            $('#lineatrabajo-1').val(response.data.lineatrabajo);
                            $('#fuentef-1').val(response.data.fuentef);
                            $('#concepto-1').val(response.data.datos.concepto);
                            $('#montodescontar-1').val(response.data.montodescargo);


                            $('#modalProveedor').modal('show');
                        }
                        else if(response.data.datos.tipodescargo === 2){

                            $('#fecha-2').val(response.data.fecha);
                            $('#numorden-2').val(response.data.datos.numero_orden);
                            $('#numacuerdo-2').val(response.data.datos.numero_acuerdo);
                            $('#cuentaproyecto-2').val(response.data.cuentaproy);
                            $('#saldoanterior-2').val(response.data.saldocuentaproy);
                            $('#proveedor-2').val(response.data.proveedor);
                            $('#lineatrabajo-2').val(response.data.lineatrabajo);
                            $('#fuentef-2').val(response.data.fuentef);
                            $('#concepto-2').val(response.data.datos.concepto);
                            $('#montodescontar-2').val(response.data.montodescargo);


                            $('#modalProyecto').modal('show');
                        }
                        else{

                            $('#fecha-3').val(response.data.fecha);
                            $('#numorden-3').val(response.data.datos.numero_orden);
                            $('#numacuerdo-3').val(response.data.datos.numero_acuerdo);
                            $('#cuentaunidad-3').val(response.data.cuentaunidad);
                            $('#saldoanterior-3').val(response.data.saldocuentaunidad);
                            $('#lineatrabajo-3').val(response.data.lineatrabajo);
                            $('#fuentef-3').val(response.data.fuentef);
                            $('#concepto-3').val(response.data.datos.concepto);
                            $('#montodescontar-3').val(response.data.montodescargo);
                            $('#beneficiario-3').val(response.data.datos.beneficiario);

                            $('#modalContribucion').modal('show');
                        }


                    }else{
                        toastr.error('información no encontrada');
                    }
                })
                .catch((error) => {
                    closeLoading();
                    toastr.error('información no encontrada');
                });
        }



    </script>


@endsection
